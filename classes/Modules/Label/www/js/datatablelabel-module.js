/**
 * Für die Bedienung der Modul-Oberfläche
 */
var DataTableLabelsUi = (function ($) {
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

            me.storage.$table = $('#datatablelabels_list');
            me.storage.$editDialog = $('#datatablelabels_edit');
            me.storage.dataTableApi = me.storage.$table.dataTable().api();

            if (me.storage.$table.length === 0 || me.storage.$editDialog.length === 0) {
                throw 'Could not initialize DataTableLabelsUi. Required elements are missing.';
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
                open: function () {
                    var $colorInput = $('#datatablelabel_hexcolor');
                    var $titleInput = $('#datatablelabel_title');
                    var $typeInput = $('#datatablelabel_type');
                    var isTypeInputEmpty = ($typeInput.val().length === 0);

                    // Fokus auf erstes Eingabefeld setzen
                    $titleInput.trigger('focus');

                    // Default-Farbe setzen, wenn leer
                    if ($colorInput.val().length === 0) {
                        $colorInput.val('#000000').trigger('change');
                    }

                    // Kennung automatisch aus Titel füllen
                    $titleInput.on('keyup', function () {
                        if (!isTypeInputEmpty) {
                            return;
                        }
                        var titleVal = $(this).val();
                        var typeVal = titleVal.toLowerCase().replace(/[^a-z0-9_]+/g, '').substr(0, 24);
                        $typeInput.val(typeVal);
                    });
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
            $(document).on('click', '.datatablelabels-edit', function (e) {
                e.preventDefault();
                var fieldId = $(this).data('id');
                me.editItem(fieldId);
            });

            // Eintrag löschen
            $(document).on('click', '.datatablelabels-delete', function (e) {
                e.preventDefault();
                var fieldId = $(this).data('id');
                me.deleteItem(fieldId);
            });

            // Farb-Vorschau in LiveTabelle anzeigen
            $(document).on('draw.dt', function (e, settings) {
                var tableName = settings.sTableId;
                var $table = $('#' + tableName);

                $table.find('.label-color-preview').each(function (index, element) {
                    var $element = $(element);
                    var hexColor = $element.data('hexcolor');
                    $element.css('background-color', hexColor);
                });
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
         * @param {number} labelTypeId
         *
         * @return {void}
         */
        editItem: function (labelTypeId) {
            labelTypeId = parseInt(labelTypeId);
            if (isNaN(labelTypeId) || labelTypeId <= 0) {
                return;
            }

            $.ajax({
                url: 'index.php?module=datatablelabels&action=edit&cmd=get',
                data: {
                    id: labelTypeId
                },
                method: 'post',
                dataType: 'json',
                beforeSend: function () {
                    App.loading.open();
                },
                success: function (result) {
                    me.storage.$editDialog.find('#datatablelabel_id').val(result.data.id);
                    me.storage.$editDialog.find('#datatablelabel_type').val(result.data.type);
                    me.storage.$editDialog.find('#datatablelabel_title').val(result.data.title);
                    me.storage.$editDialog.find('#datatablelabel_group').val(result.data.group_id);
                    me.storage.$editDialog.find('#datatablelabel_hexcolor').val(result.data.hexcolor).trigger('change');
                    me.storage.$editDialog.dialog('open');
                },
                error: function (jqXhr) {
                    alert('Fehler: ' + jqXhr.responseJSON.error);
                },
                complete: function () {
                    App.loading.close();
                }
            });
        },

        /**
         * @return {void}
         */
        saveItem: function () {
            $.ajax({
                url: 'index.php?module=datatablelabels&action=edit&cmd=save',
                data: {
                    id: $('#datatablelabel_id').val(),
                    type: $('#datatablelabel_type').val(),
                    title: $('#datatablelabel_title').val(),
                    group: $('#datatablelabel_group').val(),
                    hexcolor: $('#datatablelabel_hexcolor').val()
                },
                method: 'post',
                dataType: 'json',
                beforeSend: function () {
                    App.loading.open();
                },
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
                },
                complete: function () {
                    App.loading.close();
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
                url: 'index.php?module=datatablelabels&action=edit&cmd=delete',
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
                },
                error: function (jqXhr) {
                    alert('Fehler: ' + jqXhr.responseJSON.error);
                },
                complete: function () {
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
            me.storage.$editDialog.find('#datatablelabel_id').val('');
            me.storage.$editDialog.find('#datatablelabel_type').val('');
            me.storage.$editDialog.find('#datatablelabel_title').val('').off('keyup');
            me.storage.$editDialog.find('#datatablelabel_group').val(0);
            me.storage.$editDialog.find('#datatablelabel_hexcolor').val('');
        },

        /**
         * @return {void}
         */
        reloadDataTable: function () {
            me.storage.dataTableApi.ajax.reload();
        }
    };

    return {
        init: me.init,
        createItem: me.createItem
    };

})(jQuery);


/**
 * Für die Bedienung der Modul-Oberfläche
 */
var DataTableLabelsAutomaticLabelsUi = (function ($) {
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

            me.storage.$table = $('#datatablelabels_automaticlabelslist');
            me.storage.$editDialog = $('#datatablelabels_automaticlabelsedit');
            me.storage.dataTableApi = me.storage.$table.dataTable().api();

            if (me.storage.$table.length === 0 || me.storage.$editDialog.length === 0) {
                throw 'Could not initialize DataTableLabelsUi. Required elements are missing.';
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
                open: function () {
                    // Fokus auf erstes Eingabefeld setzen
                    $('#datatablelabel_automaticlabelname').trigger('focus');
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
            $(document).on('click', '.datatablelabels-automaticlabeledit', function (e) {
                e.preventDefault();
                var fieldId = $(this).data('id');
                me.editItem(fieldId);
            });

            // Eintrag löschen
            $(document).on('click', '.datatablelabels-automaticlabeldelete', function (e) {
                e.preventDefault();
                var fieldId = $(this).data('id');
                me.deleteItem(fieldId);
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
         * @param {number} automaticLabelId
         *
         * @return {void}
         */
        editItem: function (automaticLabelId) {
            automaticLabelId = parseInt(automaticLabelId);
            if (isNaN(automaticLabelId) || automaticLabelId <= 0) {
                return;
            }

            $.ajax({
                url: 'index.php?module=datatablelabels&action=automaticlabelsedit&cmd=get',
                data: {
                    id: automaticLabelId
                },
                method: 'post',
                dataType: 'json',
                beforeSend: function () {
                    App.loading.open();
                },
                success: function (result) {
                    me.storage.$editDialog.find('#datatablelabel_automaticlabelid').val(result.data.id);
                    me.storage.$editDialog.find('#datatablelabel_automaticlabelname').val(result.data.labelname);
                    me.storage.$editDialog.find('#datatablelabel_automaticlabelaction').val(result.data.action);
                    me.storage.$editDialog.find('#datatablelabel_automaticlabelselection').val(result.data.selection);
                    me.storage.$editDialog.find('#datatablelabel_automaticlabelproject').val(result.data.project);
                    me.storage.$editDialog.dialog('open');
                },
                error: function (jqXhr) {
                    alert('Fehler: ' + jqXhr.responseJSON.error);
                },
                complete: function () {
                    App.loading.close();
                }
            });
        },

        /**
         * @return {void}
         */
        saveItem: function () {
            $.ajax({
                url: 'index.php?module=datatablelabels&action=automaticlabelsedit&cmd=save',
                data: {
                    id: $('#datatablelabel_automaticlabelid').val(),
                    labelname: $('#datatablelabel_automaticlabelname').val(),
                    action: $('#datatablelabel_automaticlabelaction').val(),
                    selection: $('#datatablelabel_automaticlabelselection').val(),
                    project: $('#datatablelabel_automaticlabelproject').val()
                },
                method: 'post',
                dataType: 'json',
                beforeSend: function () {
                    App.loading.open();
                },
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
                },
                complete: function () {
                    App.loading.close();
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
                url: 'index.php?module=datatablelabels&action=automaticlabelsedit&cmd=delete',
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
                },
                error: function (jqXhr) {
                    alert('Fehler: ' + jqXhr.responseJSON.error);
                },
                complete: function () {
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
            me.storage.$editDialog.find('#datatablelabel_automaticlabelid').val('');
            me.storage.$editDialog.find('#datatablelabel_automaticlabelname').val('');
            var action = document.getElementById('datatablelabel_automaticlabelaction');
            action.selectedIndex = 0;
            var selection = document.getElementById('datatablelabel_automaticlabelselection');
            selection.selectedIndex = 0;
            me.storage.$editDialog.find('#datatablelabel_automaticlabelproject').val('');
        },

        /**
         * @return {void}
         */
        reloadDataTable: function () {
            me.storage.dataTableApi.ajax.reload();
        }
    };

    return {
        init: me.init,
        createItem: me.createItem
    };

})(jQuery);

$(document).ready(function () {
    if ($('#datatablelabels_list').length > 0) {
        DataTableLabelsUi.init();
    }

    if ($('#datatablelabels_automaticlabelslist').length > 0) {
        DataTableLabelsAutomaticLabelsUi.init();
    }
});