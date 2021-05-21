/**
 * Für die Bedienung der Freifelder-Konfigurations-Oberfläche
 */
var ResubmissionTextFieldConfig = (function ($) {
    'use strict';

    var me = {

        isInitialized: false,

        storage: {
            $table: null,
            $editDialog: null,
            dataTableApi: null
        },

        /**
         * @return void
         */
        init: function () {
            if (me.isInitialized === true) {
                return;
            }

            me.storage.$table = $('#resubmission_textfield_datatable');
            me.storage.$editDialog = $('#resubmission_textfield_edit');
            if (me.storage.$table.length === 0 || me.storage.$editDialog.length === 0) {
                throw 'Could not initialize ResubmissionTextFieldConfig. Required elements are missing.';
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
                maxHeight: 400,
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
            $(document).on('click', '.resubmissiontextfield-edit-button', function (e) {
                e.preventDefault();
                var textfieldConfigId = $(this).data('textfieldConfigId');
                me.editItem(textfieldConfigId);
            });

            // Eintrag löschen
            $(document).on('click', '.resubmissiontextfield-delete-button', function (e) {
                e.preventDefault();
                var textfieldConfigId = $(this).data('textfieldConfigId');
                me.deleteItem(textfieldConfigId);
            });

            // Neuen Eintrag anlegen
            $('#resubmissiontextfield-create-button').on('click', function (e) {
                e.preventDefault();
                me.createItem();
            })
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
         * @param {number} textfieldConfigId
         *
         * @return {void}
         */
        editItem: function (textfieldConfigId) {
            $.ajax({
                url: 'index.php?module=wiedervorlage&action=settings&cmd=textfields-detail',
                data: {
                    id: textfieldConfigId
                },
                method: 'post',
                dataType: 'json',
                success: function (result) {
                    me.storage.$editDialog.find('#resubmissiontextfield-id').val(result.data.id);
                    me.storage.$editDialog.find('#resubmissiontextfield-title').val(result.data.title);
                    me.storage.$editDialog.find('#resubmissiontextfield-availablestage').val(result.data.available_from_stage_id);
                    me.storage.$editDialog.find('#resubmissiontextfield-requiredstage').val(result.data.required_from_stage_id);
                    me.storage.$editDialog.find('#resubmissiontextfield-showinpipeline').prop('checked', result.data.show_in_pipeline);
                    me.storage.$editDialog.find('#resubmissiontextfield-showintables').prop('checked', result.data.show_in_tables);
                    me.storage.$editDialog.dialog('open');
                },
                error: function (jqXhr) {
                    alert('Fehler: ' + jqXhr.responseJSON.error);
                }
            });
        },

        /**
         * @return {void}
         */
        saveItem: function () {
            $.ajax({
                url: 'index.php?module=wiedervorlage&action=settings&cmd=textfields-save',
                data: {
                    id: $('#resubmissiontextfield-id').val(),
                    title: $('#resubmissiontextfield-title').val(),
                    available_from_stage_id: $('#resubmissiontextfield-availablestage').val(),
                    required_from_stage_id: $('#resubmissiontextfield-requiredstage').val(),
                    show_in_pipeline: $('#resubmissiontextfield-showinpipeline').prop('checked'),
                    show_in_tables: $('#resubmissiontextfield-showintables').prop('checked')
                },
                method: 'post',
                dataType: 'json',
                success: function (data) {
                    if (data.success === true) {
                        me.resetEditDialog();
                        me.reloadDataTable();
                        me.closeEditDialog();
                    }
                    if (data.success === false) {
                        alert(data.error);
                    }
                },
                error: function (jqXhr) {
                    alert('Fehler: ' + jqXhr.responseJSON.error);
                }
            });
        },

        /**
         * @param {number} textfieldConfigId
         *
         * @return {void}
         */
        deleteItem: function (textfieldConfigId) {
            var confirmValue = confirm('Möchten Sie das Freitextfeld wirklich löschen?');
            if (confirmValue === false) {
                return;
            }

            $.ajax({
                url: 'index.php?module=wiedervorlage&action=settings&cmd=textfields-delete',
                data: {
                    id: textfieldConfigId
                },
                method: 'post',
                dataType: 'json',
                success: function (data) {
                    if (data.success === true) {
                        me.reloadDataTable();
                    }
                    if (data.success === false) {
                        alert('Unbekannter Fehler beim Löschen.');
                    }
                },
                error: function (jqXhr) {
                    alert('Fehler: ' + jqXhr.responseJSON.error);
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
            me.storage.$editDialog.find('#resubmissiontextfield-id').val('');
            me.storage.$editDialog.find('#resubmissiontextfield-title').val('');
            me.storage.$editDialog.find('#resubmissiontextfield-availablestage').val('0');
            me.storage.$editDialog.find('#resubmissiontextfield-requiredstage').val('0');
            me.storage.$editDialog.find('#resubmissiontextfield-showinpipeline').prop('checked', false);
            me.storage.$editDialog.find('#resubmissiontextfield-showintables').prop('checked', false);
        },

        /**
         * Lädt die DataTable-Inhalte neu; per AJAX
         *
         * @return {void}
         */
        reloadDataTable: function () {
            if (!$.fn.DataTable.isDataTable(me.storage.$table)) {
                return; // DataTable ist noch nicht initalisiert
            }

            var dataTableApi = $(me.storage.$table).dataTable().api();
            dataTableApi.ajax.reload();
        }
    };

    return {
        init: me.init
    };

})(jQuery);

$(document).ready(function () {
    if ($('#resubmission_textfield_datatable').length > 0) {
        ResubmissionTextFieldConfig.init();
    }
});
