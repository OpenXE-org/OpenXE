/**
 * Für die Bedienung der Modul-Oberfläche
 */
var GoogleApiUI = (function ($) {
    'use strict';

    var me = {

        isInitialized: false,

        storage: {
            $table: null,
            dataTable: null
        },

        /**
         * @return void
         */
        init: function () {
            if (me.isInitialized === true) {
                return;
            }
            me.storage.$table = $('#googleapi_list');
            me.storage.dataTable = me.storage.$table.dataTable();
            me.registerEvents();

            me.isInitialized = true;
        },

        /**
         * @return {void}
         */
        registerEvents: function () {

            $(document).on('click', '.googleapi-delete', function (e) {
                e.preventDefault();
                var fieldId = $(this).data('googleapi-id');
                me.deleteItem(fieldId);
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
                url: 'index.php?module=googleapi&action=delete',
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
         * @return {void}
         */
        reloadDataTable: function () {
            me.storage.dataTable.api().ajax.reload();
        }
    };

    return {
        init: me.init,
    };

})(jQuery);


$(document).ready(function () {
    GoogleApiUI.init();
});
