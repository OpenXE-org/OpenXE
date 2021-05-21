/**
 * Confirm and delete all logs
 */
var DeleteLogs = (function ($) {
    'use strict';

    var me = {
        isInitialized: false,

        url: {
            ajaxDeleteLogs: 'index.php?module=log&action=deleteall'
        },

        selector: {
            deleteButton: '#btn_delete'
        },

        /**
         * @return void
         */
        init: function () {
            if (me.isInitialized === true) {
                return;
            }
            if ($(me.selector.deleteButton).length === 0) {
                return;
            }
            me.registerEvents();
            me.isInitialized = true;
        },

        /**
         * @return {void}
         */
        registerEvents: function () {
            $(me.selector.deleteButton).on('click', function (event) {
                event.preventDefault();
                me.dialogDelete();
            });
        },

        /**
         * @return {void}
         */
        dialogDelete: function () {
            var confirmValue = confirm('Alle Log-Einträge löschen?');
            if (confirmValue === false) {
                return;
            }
            me.ajaxDeleteLogs();
        },

        /**
         * @return {void}
         */
        ajaxDeleteLogs: function() {
            $.ajax({
                url: me.url.ajaxDeleteLogs,
                data:{
                    delete: true
                },
                dataType: 'json',
                method: 'post',
                beforeSend: function () {
                    App.loading.open();
                },
                success: function (data) {
                    App.loading.close();
                    alert('Log-Einträge wurden erfolgreich gelöscht.');
                },
                error: function (xhr, status, httpStatus) {
                    console.log('Fehler: ' + httpStatus);
                    alert('Log-Einträge konnten nicht gelöscht werden.');
                }
            });
        }
    };

    return {
        init: me.init
    };

})(jQuery);

$(document).ready(function () {
    DeleteLogs.init();
});
