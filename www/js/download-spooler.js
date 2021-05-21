/**
 * Modul stellt das Modal für den Download-Spooler bereit
 */
var DownloadSpoolerDialog = (function ($) {

    var me = {

        storage: {
            printerId: null,
            $dialog: null,
            $dataTable: null,
            dataTableId: 'downloadspooler-table'
        },

        /**
         * DownloadSpoolerDialog initialisieren und anzeigen
         *
         * @param {Number} printerId
         */
        show: function (printerId) {
            me.storage.printerId = parseInt(printerId);
            if (isNaN(me.storage.printerId)) {
                throw 'DownloadSpoolerDialog: Required parameter "printerId" is empty or invalid: ' + printerId;
            }

            me.init();
            me.displayDialog();
        },

        init: function () {
            // Ist bereits initialisiert?
            if (me.storage.$dialog !== null) {
                return;
            }

            me.initDialog();
            me.registerEventListener();
        },

        registerEventListener: function () {

            // Nach Klick auf Download > Tabelle neuladen
            me.storage.$dialog.on('click', 'a.spooler-action', function () {
                setTimeout(function () {
                    me.reloadDataTable();
                }, 1000);
            });

            // Datei in Tabelle löschen
            me.storage.$dialog.on('click', 'a.spooler-action-delete', function (e) {
                e.preventDefault();
                var fileId = $(this).data('file');
                me.deleteFile(fileId);
            });

            // Alle Zeilen in LiveTabelle markieren
            me.storage.$dialog.on('change', '#markall_trigger', function () {
                var isChecked = $(this).prop('checked');
                me.storage.$dialog.find('input.spooler-selection:checkbox').each(function () {
                    $(this).prop('checked', isChecked);
                });
            });

            // DataTable-Filter wird geändert
            me.storage.$dialog.on('change', '.spooler-filter-checkbox', function () {
                me.onChangeDataTableFilter(this);
            });

            // Auf Button-Klicks in Benachrichtigungen horchen
            $(document).on('notification-button:clicked', function (e, button) {
                var $button = $('#' + button.id);
                var printerId = $button.data('printer');
                var action = $button.data('action');

                if (typeof printerId === 'undefined' || typeof action === 'undefined') {
                    return; // Kein Spooler-Button
                }

                // Spooler-Modal anzeigen
                if (action === 'open-dialog') {
                    e.preventDefault();
                    me.show(printerId);
                }
            });
        },

        /**
         * UI-Dialog initialisieren
         */
        initDialog: function () {
            me.storage.$dialog = $('<div id="download-spooler-dialog"></div>').appendTo('body');
            me.storage.$dialog.dialog({
                title: 'Download-Spooler',
                modal: true,
                minWidth: 900,
                autoOpen: false,
                resizable: false,
                closeOnEscape: false
            });
        },

        /**
         * UI-Dialog anzeigen (vorher DataTable laden)
         */
        displayDialog: function () {
            me.loadDataTable().then(me.openDialog);
        },

        /**
         * UI-Dialog öffnen
         */
        openDialog: function () {
            window.setTimeout(function () {
                // Kurz warten bis DataTable aufgebaut ist, damit Höhe berechnet werden kann
                me.storage.$dialog.dialog('open');
                me.storage.$dialog.dialog('option', 'height', 'auto');
            }, 150);
        },

        /**
         * UI-Dialog schließen
         */
        closeDialog: function () {
            me.storage.$dialog.dialog('close');
        },

        /**
         * HTML-Template und DataTable-Einstellungen laden
         *
         * @return {Deferred} jQuery Promise Object
         */
        loadDataTable: function () {
            return me.fetchDataTableHtml()
                     .then(me.initDataTable);
        },

        /**
         * DataTable-HTML-Tabelle laden
         *
         * @return {jqXHR} jQuery jqXHR-Objekt
         */
        fetchDataTableHtml: function () {
            return $.ajax({
                url: 'index.php?module=welcome&action=spooler&cmd=datatable-html',
                data: {'id': me.storage.printerId},
                type: 'GET',
                dataType: 'html',
                success: function (data) {
                    me.storage.$dialog.html(data);
                }
            });
        },

        /**
         * DataTable initialisieren
         */
        initDataTable: function () {
            var $table = me.storage.$dialog.find('table#downloadspooler-table').css('width', '100%');
            var settingsJson = me.storage.$dialog.find('#downloadspooler-table-settings').html();

            try {
                var settingsObject = JSON.parse(settingsJson);
                settingsObject.columns.forEach(function (column, index) {
                    // Menü
                    if (column.data === null) {
                        settingsObject.columns[index].render = function (data, type, row) {
                            return '<a class="spooler-action" ' +
                                'href="./index.php?module=welcome&action=spooler&cmd=download-file&file=' + row.id + '">' +
                                '<img src="./themes/new/images/download.svg" border="0" alt="Download"></a>&nbsp;' +
                                '<a class="spooler-action spooler-action-delete" href="#" data-file="' + row.id + '">' +
                                '<img src="./themes/new/images/delete.svg" border="0" alt="Löschen"></a>';
                        };
                    }

                    // ID
                    if (column.data === 'id') {
                        settingsObject.columns[index].render = function (data) {
                            return '<input type="checkbox" class="spooler-selection" name="selection[]" value="' + data + '">';
                        };
                    }
                });
            }
            catch (e) {
                console.error('DownloadSpoolerDialog-DataTable konnte nicht initialisiert werden: ' + e.message);
            }

            // Vor jedem DataTable-AJAX-Request > Filter-Checkboxen verarbeiten
            $table.on('preXhr.dt', function (e, settings) {
                if (settings.sTableId !== me.storage.dataTableId) {
                    return; // Falsche DataTable
                }
                var dataTableApi = new $.fn.dataTable.Api(settings);
                me.appendDataTableFilters(dataTableApi);
            });

            // DataTable initialisieren
            me.storage.$dataTable = $table.DataTable(settingsObject);
        },

        /**
         * Filter-Checkboxen in DataTable-Suche übergeben
         *
         * @param {object} dataTableApi
         */
        appendDataTableFilters: function (dataTableApi) {
            me.storage.$dialog.find('.spooler-filter-checkbox').each(function (index, element) {
                var $filter = $(element);
                var isActive = $filter.prop('checked');
                var filterColumn = $filter.data('filter-column');
                if (typeof isActive === 'undefined' || typeof filterColumn === 'undefined') {
                    return;
                }

                var dataTableColumn = dataTableApi.column(filterColumn);
                if (typeof dataTableColumn !== 'object') {
                    console.warn('Filter column "' + filterColumn + '" not found.');
                    return;
                }

                // Filter anwenden
                var searchValue = isActive ? 'true' : '';
                dataTableColumn.search(searchValue, false, false, false);
            });
        },

        /**
         * DataTable-Filter wurde geändert
         *
         * @param {HTMLElement} element
         */
        onChangeDataTableFilter: function (element) {
            var isActive = $(element).prop('checked');
            var filterColumn = $(element).data('filter-column');
            if (typeof isActive === 'undefined' || typeof filterColumn === 'undefined') {
                return;
            }

            var dataTableColumn = me.storage.$dataTable.column(filterColumn);
            if (typeof dataTableColumn !== 'object') {
                console.warn('Filter column "' + filterColumn + '" not found.');
                return;
            }

            // Filter anwenden
            var searchValue = isActive ? 'true' : '';
            dataTableColumn.search(searchValue, false, false, false).draw();
        },

        /**
         * DataTable-Inhalte neu laden
         */
        reloadDataTable: function () {
            me.storage.$dataTable.ajax.reload();
        },

        /**
         * Datei in Tabelle löschen
         *
         * @param {number} fileId
         *
         * @return {boolean}
         */
        deleteFile: function (fileId) {
            $.ajax({
                url: 'index.php?module=welcome&action=spooler&cmd=delete-file',
                data: {'id': me.storage.printerId, 'file': fileId},
                type: 'GET',
                dataType: 'json',
                success: function (data) {
                    if (typeof data === 'object' && typeof data.success === 'boolean') {
                        return data.success
                    }
                }
            });
        }
    };

    return {
        init: me.init,
        show: me.show
    };

})(jQuery);

$(document).ready(function () {
    DownloadSpoolerDialog.init();
});
