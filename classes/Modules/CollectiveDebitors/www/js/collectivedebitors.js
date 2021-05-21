/**
 * Für die Bedienung der Modul-Oberfläche
 */
var CollectiveDebitorsUi = (function ($) {
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
            me.storage.$table = $('#collectivedebitors_list');
            me.storage.dataTable = me.storage.$table.dataTable();
            me.storage.$editDialog = $('#editCollectiveDebitor');

            if (me.storage.$table.length === 0 || me.storage.$editDialog.length === 0) {
                throw 'Could not initialize CollectiveDebitorUi. Required elements are missing.';
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
                minHeight: 420,
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
                    $('#collectivedebitor_paymentmethod').trigger('focus');
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
            $(document).on('click', '.collectivedebitor-edit', function (e) {
                e.preventDefault();
                var fieldId = $(this).data('collectivedebitor-id');
                me.editItem(fieldId);
            });

            $(document).on('click', '.collectivedebitor-delete', function (e) {
                e.preventDefault();
                var fieldId = $(this).data('collectivedebitor-id');
                me.deleteItem(fieldId);
            });

            $(document).on('click', '.collectivedebitor-down', function (e) {
                e.preventDefault();
                var fieldId = $(this).data('collectivedebitor-id');
                me.sortItem(fieldId, 'down');
            });

            $(document).on('click', '.collectivedebitor-up', function (e) {
                e.preventDefault();
                var fieldId = $(this).data('collectivedebitor-id');
                me.sortItem(fieldId, 'up');
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
                url: 'index.php?module=collectivedebitors&action=edit&cmd=get',
                data: {
                    id: fieldId
                },
                method: 'post',
                dataType: 'json',
                beforeSend: function () {
                    App.loading.open();
                },
                success: function (data) {
                    me.storage.$editDialog.find('#collectivedebitor_id').val(data.id);
                    me.storage.$editDialog.find('#collectivedebitor_paymentmethod').val(data.paymentmethod_id);
                    me.storage.$editDialog.find('#collectivedebitor_channel').val(data.channel_id);
                    me.storage.$editDialog.find('#collectivedebitor_country').val(data.country);
                    me.storage.$editDialog.find('#collectivedebitor_project').val(data.project);
                    me.storage.$editDialog.find('#collectivedebitor_group').val(data.group);
                    me.storage.$editDialog.find('#collectivedebitor_account').val(data.account);
                    me.storage.$editDialog.find('#collectivedebitor_storeaddress0').prop('checked', Number(data.store_in_address) === 0);
                    me.storage.$editDialog.find('#collectivedebitor_storeaddress1').prop('checked', Number(data.store_in_address) === 1);
                    me.storage.$editDialog.find('#collectivedebitor_storeaddress2').prop('checked', Number(data.store_in_address) === 2);
                    App.loading.close();
                    me.storage.$editDialog.dialog('open');
                }
            });
        },

        /**
         * @param {number} fieldId
         * @param {string} direction
         *
         * @return {void}
         */
        sortItem: function (fieldId, direction) {
            fieldId = parseInt(fieldId);
            if (isNaN(fieldId) || fieldId <= 0) {
                return;
            }

            direction = direction.toString();
            if (direction !== 'up' && direction !== 'down') {
                return;
            }

            $.ajax({
                url: 'index.php?module=collectivedebitors&action=edit&cmd=sort',
                data: {
                    id: fieldId,
                    direction: direction
                },
                method: 'post',
                dataType: 'json',
                beforeSend: function () {
                    App.loading.open();
                },
                success: function() {
                    me.reloadDataTable();
                }
            });
        },

        /**
         * @return {void}
         */
        saveItem: function () {
            var storeAddress = 0;
            if ($('#collectivedebitor_storeaddress1').prop('checked')) {
                storeAddress = 1;
            }
            if ($('#collectivedebitor_storeaddress2').prop('checked')) {
                storeAddress = 2;
            }

            $.ajax({
                url: 'index.php?module=collectivedebitors&action=save',
                data: {
                    //Alle Felder die fürs editieren vorhanden sind
                    id: $('#collectivedebitor_id').val(),
                    paymentmethod: $('#collectivedebitor_paymentmethod').val(),
                    channel: $('#collectivedebitor_channel').val(),
                    country: $('#collectivedebitor_country').val(),
                    project: $('#collectivedebitor_project').val(),
                    group: $('#collectivedebitor_group').val(),
                    account: $('#collectivedebitor_account').val(),
                    store_in_address: storeAddress
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
                url: 'index.php?module=collectivedebitors&action=delete',
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
            me.storage.$editDialog.find('#collectivedebitor_id').val('');
            me.storage.$editDialog.find('#collectivedebitor_paymentmethod').val('');
            me.storage.$editDialog.find('#collectivedebitor_channel').val('');
            me.storage.$editDialog.find('#collectivedebitor_country').val('');
            me.storage.$editDialog.find('#collectivedebitor_project').val('');
            me.storage.$editDialog.find('#collectivedebitor_group').val('');
            me.storage.$editDialog.find('#collectivedebitor_account').val('');
            me.storage.$editDialog.find('#collectivedebitor_storeaddress0').prop('checked', true);
            me.storage.$editDialog.find('#collectivedebitor_storeaddress1').prop('checked', false);
            me.storage.$editDialog.find('#collectivedebitor_storeaddress2').prop('checked', false);
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
    CollectiveDebitorsUi.init();
});
