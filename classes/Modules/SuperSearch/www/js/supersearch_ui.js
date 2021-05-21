var SuperSearchUi = (function ($) {
    'use strict';

    var me = {

        init: function () {
            me.registerEvents();
        },

        registerEvents: function () {
            $('#supersearch-fullindex-task-trigger').on('click', function (e) {
                e.preventDefault();
                me.onClickExecuteFullIndexTask();
            });

            $('.button-provider-activate').on('click', function (e) {
                e.preventDefault();
                var indexName = $(this).data('indexName');
                me.onClickActivateProvider(indexName);
            });

            $('.button-provider-deactivate').on('click', function (e) {
                e.preventDefault();
                var indexName = $(this).data('indexName');
                me.onClickDeactivateProvider(indexName);
            });
        },

        onClickExecuteFullIndexTask: function () {
            var $container = $('#supersearch-fullindex-task-wrapper');

            $.ajax({
                url: 'index.php?module=supersearch&action=settings',
                data: {
                    cmd: 'run-full-index-task'
                },
                method: 'post',
                dataType: 'json',
                beforeSend: function () {
                    $container.loadingOverlay();
                },
                success: function () {
                    var message = '<div class="success">Such-Index wurde erfolgreich neu aufgebaut.</div>';
                    message += '<a class="button button-primary" href="index.php?module=supersearch&action=settings">';
                    message += 'Seite neuladen</a>';
                    $container.html(message);
                },
                error: function (jqXhr) {
                    var errorMessage = 'Unbekannter Fehler';
                    if (jqXhr.hasOwnProperty('responseJSON') && jqXhr.responseJSON.hasOwnProperty('error')) {
                        errorMessage = jqXhr.responseJSON.error;
                    }
                    var message = '<div class="warning">';
                    message += 'Fehler beim Aufbau des Such-Indexes: ';
                    message += errorMessage;
                    message += '</div>';
                    $container.html(message);
                },
                complete: function () {
                    $container.loadingOverlay('remove');
                }
            });
        },

        /**
         * @param {string} indexName
         */
        onClickActivateProvider: function (indexName) {
            $.ajax({
                url: 'index.php?module=supersearch&action=settings',
                data: {
                    cmd: 'activate-provider',
                    index_name: indexName
                },
                method: 'post',
                dataType: 'json',
                success: function () {
                    window.location.reload();
                },
                error: function (jqXhr) {
                    var errorMessage = 'Unbekannter Fehler';
                    if (jqXhr.hasOwnProperty('responseJSON') && jqXhr.responseJSON.hasOwnProperty('error')) {
                        errorMessage = jqXhr.responseJSON.error;
                    }
                    alert('Fehler beim Aktivieren des Providers: ' + errorMessage);
                }
            });
        },

        /**
         * @param {string} indexName
         */
        onClickDeactivateProvider: function (indexName) {
            $.ajax({
                url: 'index.php?module=supersearch&action=settings',
                data: {
                    cmd: 'deactivate-provider',
                    index_name: indexName
                },
                method: 'post',
                dataType: 'json',
                success: function () {
                    window.location.reload();
                },
                error: function (jqXhr) {
                    var errorMessage = 'Unbekannter Fehler';
                    if (jqXhr.hasOwnProperty('responseJSON') && jqXhr.responseJSON.hasOwnProperty('error')) {
                        errorMessage = jqXhr.responseJSON.error;
                    }
                    alert('Fehler beim Deaktivieren des Providers: ' + errorMessage);
                }
            });
        }
    };

    return {
        init: me.init
    };

})(jQuery);

$(document).ready(function () {
    SuperSearchUi.init();
});
