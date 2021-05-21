/**
 * Für die Bedienung der Modul-Oberfläche
 */
var EangeneratorUi = (function ($) {
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

            me.storage.$table = $('#eangenerator_pool');
            me.storage.dataTable = me.storage.$table.dataTable();
            me.storage.$editDialog = $('#editEanGenerator');

            if (me.storage.$table.length === 0 || me.storage.$editDialog.length === 0) {
                throw 'Could not initialize EangeneratorUi. Required elements are missing.';
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
                minWidth: 500,
                maxHeight: 700,
                autoOpen: false,
                buttons: [
                    {
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
                    $('#eangenerator_ean').trigger('focus');
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
            $(document).on('click', '#eangenerator_create_button', function (e) {
                e.preventDefault();
                me.createItem();
            });

            $(document).on('click', '.eangenerator-edit', function (e) {
                e.preventDefault();
                var fieldId = $(this).data('eangeneratorId');
                me.editItem(fieldId);
            });

            $(document).on('click', '.eangenerator-delete', function (e) {
                e.preventDefault();
                var fieldId = $(this).data('eangeneratorId');
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
                url: 'index.php?module=eangenerator&action=edit&cmd=get',
                data: {
                    id: fieldId
                },
                method: 'post',
                dataType: 'json',
                beforeSend: function () {
                    App.loading.open();
                },
                success: function (data) {
                    data.available = data.available === '1' || data.available === 1 || data.available === true;

                    // Liste mit zugewiesenen Artikel zusammenstellen
                    var $articles = me.storage.$editDialog.find('#eangenerator_articles');
                    if (typeof data.articles === 'object' && data.articles.length > 0) {
                        $articles.html('');
                        $.each(data.articles, function (index, articleData) {
                            if (articleData.hasOwnProperty('id') && articleData.hasOwnProperty('title')) {
                                $('<a>', {
                                    text: articleData.title,
                                    href: 'index.php?module=artikel&action=edit&id=' + articleData.id,
                                    target: '_blank'
                                }).appendTo($articles);
                                $('<br/>').appendTo($articles);
                            }
                        });
                    } else {
                        $articles.html('keine');
                    }

                    me.storage.$editDialog.find('#eangenerator_id').val(data.id);
                    me.storage.$editDialog.find('#eangenerator_ean').val(data.ean);
                    me.storage.$editDialog.find('#eangenerator_available').prop('checked', data.available);

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
                url: 'index.php?module=eangenerator&action=save',
                data: {
                    //Alle Felder die fürs editieren vorhanden sind
                    id: $('#eangenerator_id').val(),
                    ean: $('#eangenerator_ean').val(),
                    available: $('#eangenerator_available').prop('checked') ? 1 : 0
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
                url: 'index.php?module=eangenerator&action=delete',
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
            me.storage.$editDialog.find('#eangenerator_id').val('');
            me.storage.$editDialog.find('#eangenerator_ean').val('');
            me.storage.$editDialog.find('#eangenerator_available').prop('checked', true);
            me.storage.$editDialog.find('#eangenerator_articles').html('keine');
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


$(document).ready(function () {
    EangeneratorUi.init();
});
