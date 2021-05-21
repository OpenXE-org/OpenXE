
/**
 * Für die Bedienung der Modul-Oberfläche
 */
var MandatoryFieldsUi = (function ($) {
    'use strict';

    var me = {

        isInitialized: false,

        storage: {
            $table: null,
            dataTable: null,
            $editDialog: null
        },

        /**
         * @return void
         */
        init: function () {
            if (me.isInitialized === true) {
                return;
            }

            me.storage.$table = $('#mandatoryfields_list');
            me.storage.dataTable = me.storage.$table.dataTable();
            me.storage.$editDialog = $('#editMandatoryFields');

            if (me.storage.$table.length === 0 || me.storage.$editDialog.length === 0) {
                throw 'Could not initialize MandatoryFieldsUi. Required elements are missing.';
            }

            me.initDialog();
            me.registerEvents();

            me.isInitialized = true;
        },

        /**
         * @return {void}
         */
        initDialog: function () {
            me.storage.$editDialog.dialog({
                modal: true,
                bgiframe: true,
                closeOnEscape: false,
                minWidth: 550,
                maxHeight: 700,
                autoOpen: false,
                buttons: [{
                    text: 'ABBRECHEN',
                    click: function () {
                        me.resetEditDialog();
                        me.closeEditDialog();
                    }
                }, {
                    text: 'SPEICHERN',
                    click: function () {
                        me.saveItem();
                    }
                }],
                open: function () {
                    $('#mandatoryfield_module').focus(); // Focus first input element
                },
                close: function () {
                    me.resetEditDialog();
                }
            });
        },

        /**
         * @return {void}
         */
        registerEvents: function () {

            // Eintrag bearbeiten
            $(document).on('click', '.mandatoryfields-edit', function (e) {
                e.preventDefault();
                var fieldId = $(this).data('mandatoryfields-id');
                me.editItem(fieldId);
            });

            // Eintrag löschen
            $(document).on('click', '.mandatoryfields-delete', function (e) {
                e.preventDefault();
                var fieldId = $(this).data('mandatoryfields-id');
                me.deleteItem(fieldId);
            });

            $(document).on('change', '#mandatoryfield_type', function (e) {
                me.showHideElements('mandatoryfield_type',$(this).val());
            });
        },

        /**
         * @return {void}
         */
        createItem: function () {
            if (me.isInitialized === false) {
                me.init();
            }
            me.resetEditDialog();
            me.openEditDialog();
        },

        /**
         *
         * @param {string} elementName
         * @param {string} elementValue
         */
        showHideElements: function (elementName, elementValue) {

            if(elementName==='mandatoryfield_type'){
                if(elementValue==='text'){
                    $('.min_max_length').show();
                }
                else{
                    $('.min_max_length').hide();
                }

                if(
                    elementValue==='ganzzahl' ||
                    elementValue==='dezimalzahl'
                ){
                    $('.comparator').show();
                }
                else{
                    $('.comparator').hide();
                }
            }
        },

        /**
         * @param {number} fieldId
         *
         * @return {void}
         */
        editItem: function (fieldId) {
            fieldId = parseInt(fieldId);
            if (isNaN(fieldId) || fieldId <= 0) {
                return;
            }

            $.ajax({
                url: 'index.php?module=mandatoryfields&action=edit&cmd=get',
                data: {
                    id: fieldId
                },
                method: 'post',
                dataType: 'json',
                beforeSend: function () {
                    App.loading.open();
                },
                success: function (data) {
                    me.storage.$editDialog.find('#mandatoryfield_entry_id').val(data.id);
                    me.storage.$editDialog.find('#mandatoryfield_module').val(data.module);
                    me.storage.$editDialog.find('#mandatoryfield_action').val(data.action);
                    me.storage.$editDialog.find('#mandatoryfield_field_id').val(data.field_id);
                    me.storage.$editDialog.find('#mandatoryfield_error_message').val(data.error_message);
                    me.storage.$editDialog.find('#mandatoryfield_type').val(data.type);
                    me.storage.$editDialog.find('#mandatoryfield_min_length').val(data.min_length);
                    me.storage.$editDialog.find('#mandatoryfield_max_length').val(data.max_length);
                    me.storage.$editDialog.find('#mandatoryfield_mandatory').prop('checked', data.mandatory == 1);
                    me.storage.$editDialog.find('#mandatoryfield_comparator').val(data.comparator);
                    me.storage.$editDialog.find('#mandatoryfield_compareto').val(data.compareto);

                    me.showHideElements('mandatoryfield_type',data.type);

                    App.loading.close();
                    me.storage.$editDialog.dialog('open');
                }
            });
        },

        /**
         * @return {void}
         */
        saveItem: function () {
            $.ajax({
                url: 'index.php?module=mandatoryfields&action=save',
                data: {
                    // Alle Felder die fürs Editieren vorhanden sind
                    id: $('#mandatoryfield_entry_id').val(),
                    module: $('#mandatoryfield_module').val(),
                    action: $('#mandatoryfield_action').val(),
                    fieldId: $('#mandatoryfield_field_id').val(),
                    errorMessage: $('#mandatoryfield_error_message').val(),
                    type: $('#mandatoryfield_type').val(),
                    minLength: $('#mandatoryfield_min_length').val(),
                    maxLength: $('#mandatoryfield_max_length').val(),
                    mandatory: $('#mandatoryfield_mandatory').prop('checked') ? 1 : 0,
                    comparator: $('#mandatoryfield_comparator').val(),
                    compareto: $('#mandatoryfield_compareto').val(),
                },
                method: 'post',
                dataType: 'json',
                beforeSend: function () {
                    App.loading.open();
                },
                success: function (data) {
                    App.loading.close();
                    if (data.success === true) {
                        me.resetEditDialog();
                        me.reloadDataTable();
                        me.closeEditDialog();
                    }
                    if (data.success === false) {
                        alert(data.error);
                    }
                }
            });
        },

        /**
         * @param {number} fieldId
         *
         * @return {void}
         */
        deleteItem: function (fieldId) {
            var confirmValue = confirm('Wirklich löschen?');
            if (confirmValue === false) {
                return;
            }

            $.ajax({
                url: 'index.php?module=mandatoryfields&action=delete',
                data: {
                    id: fieldId
                },
                method: 'post',
                dataType: 'json',
                beforeSend: function () {
                    App.loading.open();
                },
                success: function (data) {
                    if (data.success === true) {
                        me.reloadDataTable();
                    }
                    if (data.success === false) {
                        alert('Unbekannter Fehler beim Löschen.');
                    }
                    App.loading.close();
                }
            });
        },

        /**
         * @return void
         */
        openEditDialog: function () {
            me.storage.$editDialog.dialog('open');
        },

        /**
         * @return void
         */
        closeEditDialog: function () {
            me.storage.$editDialog.dialog('close');
        },

        /**
         * @return void
         */
        resetEditDialog: function () {
            me.storage.$editDialog.find('#mandatoryfield_entry_id').val('');
            me.storage.$editDialog.find('#mandatoryfield_module').val('');
            me.storage.$editDialog.find('#mandatoryfield_action').val('');
            me.storage.$editDialog.find('#mandatoryfield_field_id').val('');
            me.storage.$editDialog.find('#mandatoryfield_error_message').val('');
            var type = document.getElementById('mandatoryfield_type');
            type.selectedIndex = 0;
            me.storage.$editDialog.find('#mandatoryfield_min_length').val('');
            me.storage.$editDialog.find('#mandatoryfield_max_length').val('');
            me.storage.$editDialog.find('#mandatoryfield_mandatory').prop('checked', true);
            me.storage.$editDialog.find('#mandatoryfield_comparator').val('');
            me.storage.$editDialog.find('#mandatoryfield_compareto').val('');

            me.showHideElements('mandatoryfield_type','text');
        },

        /**
         * @return {void}
         */
        reloadDataTable: function () {
            me.storage.dataTable.api().ajax.reload();
        }
    };

    return {
        init: me.init,
        createItem: me.createItem
    };

})(jQuery);


/**
 * Prüft Formular-Pflichtfelder
 */
var MandatoryFieldsValidator = (function () {
    'use strict';

    var me = {

        storage: {
            $fields: [],
            fieldConfig: [],
            debugging: false,
            $notificationKeys:[],
        },

        init: function () {
            me.log('init');
            var $jsonElem = $('#mandatory-fields-json');
            if ($jsonElem.length === 0) {
                return;
            }

            try {
                var jsonData = JSON.parse($jsonElem.html());
            } catch (e) {
                console.error('Could not initialize MandatoryFieldsValidator. JSON data is malformed.');
                return;
            }

            me.log('init jsonData', jsonData);
            me.storage.fieldConfig = jsonData;
            if (me.storage.fieldConfig.length === 0) {
                return;
            }

            me.registerFields();
            me.registerEvents();
        },

        registerEvents: function () {

            var selectors = [
                'input[type=\'button\']',
                '.publish',
                '.ui-dialog-buttonset .ui-button'
            ];

            $(document).on('click', selectors.join(','), function(e) {
                if(!me.validateAll(e)){
                    e.preventDefault();
                }
            });

            $(document).on('submit', 'form', function (e) {
                return me.validateAll(e);
            });
        },

        /**
         * Pflichtfelder mit Data-Attributen und Events für Validierung versehen
         */
        registerFields: function () {
            var fields = Array.from(me.storage.fieldConfig);
            $.each(fields, function (index, field) {
                var $elem = $(field.cssSelector);
                if ($elem.length === 0) {
                    console.warn('MandatoryFieldsValidator: Could not find element "' + field.cssSelector + '"');
                    return;
                }

                // Feld-Instanz merken
                me.storage.$fields.push($elem);

                // Data-Attribute setzen
                $elem.data('validator-id', field.id);
                $elem.data('validator-rule', field.rule);

                // Blur-Event setzen
                $elem.on('blur', function () {
                    me.validateField($(this));
                });
                if(field.isMandatory){
                    me.validateFieldOnServerSide($elem).done(function (validationResult) {
                        var isValid = !validationResult.error
                        if (!isValid) {
                            $elem.addClass('validator_field_warning');
                        }
                    });
                }
            });
        },

        /**
         * @param {Event} event
         */
        validateAll: function (event) {
            me.log('validateAll', event);
            var allValid = true;
            var $lastInvalidField;

            // Registrierte Felder durchlaufen und einzeln prüfen
            $.each(me.storage.$fields, function (index, $field) {

                // Feld benötigt Validierung
                if (me.validateField($field) === false) {
                    $lastInvalidField = $field;
                    allValid = false;
                }
            });

            // Bei Fehler > Event abbrechen und fehlerhaftes Feld anscrollen
            if (allValid === false) {
                me.scrollToField($lastInvalidField);
                return false;
            }

            me.log('validateAll done', {allValid:allValid});
            return true;
        },

        /**
         * @param {jQuery} $field
         *
         * @return {boolean} true if valid; false if invalid
         */
        validateField: function ($field) {
            me.log('validateField #' + $field.attr('id'));
            var isValid = true;

            me.validateFieldOnServerSide($field).done(function (validationResult) {
                me.log('validateFieldOnServerSide doneXHR #' + $field.attr('id'), validationResult);
                isValid = !validationResult.error
                if (!isValid) {
                    me.displayErrorMessage($field, validationResult.message);
                }
            });

            // Keine Fehler > Vorherige Fehlermeldung entfernen
            if (isValid === true) {
                me.hideErrorMessage($field);
                $field.removeClass('validator_field_warning');
            }

            return isValid;
        },

        /**
         * @param {jQuery} $field
         *
         * @return {jqXHR}
         */
        validateFieldOnServerSide: function ($field) {
            me.log('validateFieldOnServerSide preXHR #' + $field.attr('id'));
            var fieldValue = $field.val();
            var fieldId = $field.data('validator-id');
            var rule = $field.data('validator-rule');

            return jQuery.ajax({
                type: 'POST',
                url: 'index.php?module=ajax&action=validator',
                data: {rule: rule, value: fieldValue, mandatoryid: fieldId},
                dataType: 'json',
                async: false
            });
        },

        /**
         * @param {jQuery} $field
         * @param {string} message
         */
        displayErrorMessage: function ($field, message) {

            var keys = Notify.keys();
            var notificationKey = me.storage.$notificationKeys[$field.attr('id')];

            if ($field.hasClass('validator_field_error') && keys.includes(notificationKey)) {
                me.log('displayErrorMessage cancelled #' + $field.attr('id'));
                return; // Fehlermeldung wird schon angezeigt
            }
            me.log('displayErrorMessage #' + $field.attr('id'), {message:message});

            Notify.create('error', 'Pflichtfeld', message,true);
            keys = Notify.keys();

            me.storage.$notificationKeys[$field.attr('id')] = keys[keys.length-1];

            $field.removeClass('validator_field_warning');
            $field.addClass('validator_field_error');
        },

        /**
         * @param {jQuery} $field
         */
        hideErrorMessage: function ($field) {
            me.log('hideErrorMessage #' + $field.attr('id'));
            $field.removeClass('validator_field_error');

            var key = me.storage.$notificationKeys[$field.attr('id')];
            Notify.close(key);
        },

        /**
         * @param {jQuery} $field
         */
        scrollToField: function ($field) {
            if (typeof $field !== 'object' || !$field instanceof jQuery) {
                return;
            }

            me.log('scrollToField #' + $field.attr('id'));
            var offsetTop = $field.offset().top;
            var windowHeight = $(window).height();
            $('html, body').clearQueue().animate({
                scrollTop: offsetTop - (windowHeight / 2)
            }, 'slow');
        },

        /**
         * @param {string} message
         * @param {object} context
         */
        log: function (message, context) {
            if (typeof me.storage.debugging === 'undefined' || me.storage.debugging !== true) {
                return;
            }

            if (typeof context !== 'undefined') {
                console.log('MandatoryFieldsValidator: ' + message, context);
            } else {
                console.log('MandatoryFieldsValidator: ' + message);
            }
        }
    };

    return {
        init: me.init
    };

})(jQuery);


$(document).ready(function () {
    if ($('#mandatoryfields_list').length > 0) {
        MandatoryFieldsUi.init();
    }

    MandatoryFieldsValidator.init();
});
