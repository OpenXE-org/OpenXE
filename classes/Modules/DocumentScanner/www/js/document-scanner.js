var DocscanFiles = function ($) {
    'use strict';

    var me = {
        elem: {},

        storage: {
            fileId: null,
            previewFileId: null,
            documentType: 'verbindlichkeit',
            dataTableSettings: {},
            dragTimer: null,
            uploadData: []
        },

        selector: {
            filesTable: '#docscan_files',
            preview: '#preview-iframe',
            dialog: '#docscan-files-dialog',
            dialogTabs: '#filetabs',
            dialogContent: '#docscan-files-content',
            documentTableContainer: '.document-table-container',
            documentFilterCheckbox: '.document-filter-checkbox',
            documentAssignButton: '.document-assign-action',
            createLiabilityButton: '.create-liability-button',
            uploadDropzoneWrapper: '#dropzone-wrapper',
            uploadDropzone: '#dropzone'
        },

        init: function () {
            me.elem.$docscanDialog = $(me.selector.dialog);
            me.elem.$previewIframe = $(me.selector.preview);
            me.elem.$dropzone = $(me.selector.uploadDropzone);
            me.elem.$dropzoneWrapper = $(me.selector.uploadDropzoneWrapper);

            me.initDialog();
            me.attachEvents();
        },

        /**
         * Benötigte Events attachen
         */
        attachEvents: function () {

            // Tabellenzeile wird angeklickt
            $(document).on('click', 'table#docscan_files tr', function () {
                me.onClickFileTableRow(this);
            });

            // Hinzufügen-Button wird angeklickt
            $(document).on('click', 'table#docscan_files .docscan-add-button', function (e) {
                e.stopPropagation(); // Blockt Click-Event auf Tabellenzeile
                e.preventDefault();
                me.onClickAddButton(this);
            });

            // Löschen-Button wird angeklickt
            $(document).on('click', 'table#docscan_files .docscan-delete-button', function (e) {
                e.stopPropagation(); // Blockt Click-Event auf Tabellenzeile
                e.preventDefault();
                me.onClickDeleteButton(this);
            });

            // Im Dialog-Modal wird der Tab gewechselt
            $(document).on('tabsactivate', me.selector.dialogTabs, function (event, ui) {
                me.onChangeFileTab(ui.newTab, ui.oldTab);
            });

            // "Datei zuweisen"-Button wurde in UI-Dialog-Table angeklickt
            $(document).on('click', me.selector.documentAssignButton, function (e) {
                e.preventDefault();
                me.onClickDocumentAssignButton(this);
            });

            // DataTable-Filter wird geändert
            $(document).on('change', me.selector.documentFilterCheckbox, function () {
                me.onChangeTableFilter(this);
            });

            // Neue Verbindlichkeit anlegen
            $(document).on('click', me.selector.createLiabilityButton, function () {
                me.createNewLiability();
            });

            // Dropzone einblenden
            $(document).on('dragover', function (e) {
                var dt = e.originalEvent.dataTransfer;
                if (typeof dt.types && dt.types.indexOf('Files') !== -1) {
                    me.showDropzone();
                    window.clearTimeout(me.dragTimer);
                }
            });

            // Dropzone ausblenden
            $(document).on('dragleave', function () {
                me.dragTimer = window.setTimeout(function () {
                    if (me.storage.previewFileId !== null) {
                        me.hideDropzone();
                    }
                }, 25);
            });

            // Dateien werden in Dropzone gedropped
            me.elem.$dropzoneWrapper.on('drop', function (e) {
                e.preventDefault();
                me.handleDroppedFiles(e.dataTransfer.files);
                me.elem.$dropzone.css('borderColor', '#CCC');
            });

            // Dropzone: Rahmenfarbe ändern bei Drag
            me.elem.$dropzoneWrapper
              .on('dragover', function (e) {
                  e.preventDefault();
                  me.elem.$dropzone.css('borderColor', 'darkred');
              })
              .on('dragleave', function () {
                  me.elem.$dropzone.css('borderColor', '#CCC');
              });
        },

        /**
         * DataTable-Filter wurde geändert
         *
         * @param {HTMLElement} element
         */
        onChangeTableFilter: function (element) {
            var isActive = $(element).prop('checked');
            var filterColumn = $(element).data('filter-column');
            if (typeof isActive === 'undefined' || typeof filterColumn === 'undefined') {
                return;
            }

            // Prüfen ob DataTable vorhanden
            if (!$.fn.DataTable.isDataTable(me.elem.$currentDataTable)) {
                return;
            }
            var dataTable = me.elem.$currentDataTable.DataTable();

            // Filter anwenden
            if (isActive) {
                dataTable.column(filterColumn).search('true', false, false, false).draw();
            } else {
                dataTable.column(filterColumn).search('', false, false, false).draw();
            }
        },

        /**
         * Tabellenzeile wird angeklickt
         *
         * @param {HTMLElement} element Angeklickte Tabellenzeile
         */
        onClickFileTableRow: function (element) {

            // Datei-ID aus Hinzufügen-Button lesen
            var $docscanDataItem = $(element).find('.docscan-add-button');
            var fileId = parseInt($docscanDataItem.data('file'));
            if (isNaN(fileId) || fileId === 0) {
                return;
            }

            // Passende Datei-Vorschau wird bereits angezeigt
            if (me.storage.previewFileId === fileId) {
                return;
            }

            // Dateivorschau anzeigen
            me.previewFileInIframe(fileId);
        },


        /**
         * @param {HTMLElement} element
         */
        onClickAddButton: function (element) {
            var $element = $(element);
            var fileId = parseInt($element.data('file'));
            if (isNaN(fileId) || fileId === 0) {
                return;
            }

            // UI-Dialog anzeigen
            me.storage.fileId = fileId;
            me.displayDialog();

            var fileType = $element.data('type');
            if (fileType !== '' && fileType !== null) {
                var types = {
                    'verbindlichkeit': 'verbindlichkeit',
                    'kassenbuch': 'kasse',
                    'reisekosten': 'reisekosten',
                    'bestellung': 'bestellung',
                    'adresse': 'adressen'
                };
                me.storage.documentType = types[fileType];
                $(me.selector.dialogTabs).tabs('option', 'active', Object.keys(types).indexOf(fileType));
            }
        },

        /**
         * @param {HTMLElement} element
         */
        onClickDeleteButton: function (element) {
            var fileId = parseInt($(element).data('file'));
            if (isNaN(fileId) || fileId === 0) {
                return;
            }

            var confirm = window.confirm('Möchten Sie die Datei wirklich löschen?');
            if (confirm === false) {
                return;
            }

            // Datei löschen
            me.deleteFile(fileId);
        },

        /**
         * Im UI-Dialog wird der Tab gewechselt
         *
         * @param {HTMLElement} newTab Tab-Element das aktiviert wird
         * @param {HTMLElement} oldTab Tab-Element das vorher aktiv war
         */
        onChangeFileTab: function (newTab, oldTab) {
            var docTypeBefore = $(oldTab).data('type');
            me.destroyDataTable(docTypeBefore);

            me.storage.documentType = $(newTab).data('type');
            me.loadDataTable();
        },

        /**
         * "Datei zuweisen"-Button wurde angeklickt
         *
         * @param {HTMLElement} element Angeklickter Button
         */
        onClickDocumentAssignButton: function (element) {
            var $tableRow = $(element).parents('tr');
            if (!$.fn.DataTable.isDataTable(me.elem.$currentDataTable)) {
                alert('Unbekannter Fehler #1: DataTable konnte nicht gefunden werden.');
                return;
            }
            var $dataTable = me.elem.$currentDataTable.DataTable();
            var data = $dataTable.row($tableRow).data();

            // ID aus DataTable-Datensatz holen
            var objectId = parseInt(data.id);
            if (isNaN(objectId)) {
                alert('Unbekannter Fehler #2: ID für Zuweisung konnte nicht gefunden werden.');
                return;
            }

            // Datei-Stichwort zuweisen
            me.assignDocumentKeyword(me.storage.documentType, objectId);
        },

        /**
         * Datei-Stichwort zuweisen
         *
         * @param {string} documentType
         * @param {number} objectId
         */
        assignDocumentKeyword: function (documentType, objectId) {
            $.ajax({
                url: 'index.php?module=docscan&action=edit&cmd=assign-file',
                data: {keyword: documentType, file: me.storage.fileId, object: objectId},
                type: 'POST',
                dataType: 'json',
                success: function (result) {
                    if (result.success === false) {
                        alert('Unbekannter Fehler #3. Datei konnte nicht zugewiesen werden.');
                    }
                    me.reloadFilesTable();
                    me.showDropzone();
                    me.closeDialog();
                }
            });
        },

        /**
         * Dateivorschau anzeigen
         *
         * @param {number} fileId
         */
        previewFileInIframe: function (fileId) {
            $.ajax({
                url: 'index.php?module=docscan&action=preview&id=' + fileId,
                type: 'GET',
                dataType: 'json',
                success: function (data) {
                    /** @namespace data.iframe_src */
                    if (typeof data.iframe_src === 'undefined') {
                        alert('Unbekannter Fehler #4. Datei konnte nicht abgerufen werden.');
                        return;
                    }

                    // Dropzone ausblenden
                    me.hideDropzone();

                    // IFrame-Inhalt ändern
                    me.elem.$previewIframe.attr('src', data.iframe_src);

                    // Merken welche Datei angezeigt wird
                    me.storage.previewFileId = fileId;
                }
            });
        },

        /**
         * Datei löschen
         *
         * @param {number} fileId
         */
        deleteFile: function (fileId) {
            $.ajax({
                url: 'index.php?module=docscan&action=edit&cmd=delete-file',
                data: {file: fileId},
                type: 'POST',
                dataType: 'json',
                success: function (data) {
                    if (typeof data.success === 'undefined' || data.success === false) {
                        if (data.hasOwnProperty('error')) {
                            alert(data.error);
                        } else {
                            alert('Unbekannter Fehler #7. Datei konnte nicht gelöscht werden.');
                        }
                    }

                    me.reloadFilesTable();
                    me.showDropzone();
                }
            });
        },

        /**
         * UI-Dialog initialisieren
         */
        initDialog: function () {
            me.elem.$docscanDialog.dialog({
                title: 'Datei-Zuordnung',
                modal: true,
                minWidth: 900,
                closeOnEscape: false,
                autoOpen: false,
                resizable: false
            });

            // Tabs initialisieren
            $(me.selector.dialogTabs).tabs();
        },

        /**
         * UI-Dialog anzeigen (vorher DataTable laden)
         */
        displayDialog: function () {
            me.loadDataTable().then(me.openDialog);
        },

        /**
         * HTML-Template und DataTable-Einstellungen laden
         *
         * @return {Deferred} jQuery Promise Object
         */
        loadDataTable: function () {
            return me.fetchDataTableHtmlTemplate()
                     .then(me.fetchDataTableSettings)
                     .then(me.initDataTable);
        },

        /**
         * UI-Dialog öffnen
         */
        openDialog: function () {
            window.setTimeout(function () {
                // Kurz warten bis DataTable aufgebaut ist, damit Höhe berechnet werden kann
                me.elem.$docscanDialog.dialog('open');
                me.elem.$docscanDialog.dialog('option', 'height', 'auto');
            }, 150);
        },

        /**
         * UI-Dialog schließen
         */
        closeDialog: function () {
            me.elem.$docscanDialog.dialog('close');
        },

        /**
         * DataTable mit Docscan-Dateien neuladen
         */
        reloadFilesTable: function () {
            var $files = $(me.selector.filesTable);
            if ($.fn.DataTable.isDataTable($files)) {
                $files.DataTable().ajax.reload(null, false); // false = hold the current paging position
            }
            me.storage.previewFileId = null;
        },

        /**
         * DataTable initialisieren
         */
        initDataTable: function () {
            var $tabContent = me.elem.$docscanDialog.find('#' + me.storage.documentType + '-tab');
            me.elem.$currentDataTable = $tabContent.find('table.display');
            me.elem.$currentDataTable.css('width', '100%');

            // DataTable ist bereist initialisiert > Instanz zerstören
            if ($.fn.DataTable.isDataTable(me.elem.$currentDataTable)) {
                me.elem.$currentDataTable.DataTable().destroy();
            }

            // DataTable initialisieren
            var table = me.elem.$currentDataTable.DataTable(me.storage.dataTableSettings);
            table.on('init.dt', function () {
                $(me.selector.documentTableContainer).show();
            });
        },

        /**
         * Zerstört eine DataTable-Instanz
         *
         * @param {string} docType
         */
        destroyDataTable: function (docType) {
            var $tabContent = me.elem.$docscanDialog.find('#' + docType + '-tab');
            var $table = $tabContent.find('table.display');

            if ($.fn.DataTable.isDataTable($table)) {
                $table.DataTable().destroy();
                me.elem.$currentDataTable = null;
            }
        },

        /**
         * DataTable-HTML-Tabelle laden
         *
         * @return {jqXHR} jQuery jqXHR-Objekt
         */
        fetchDataTableHtmlTemplate: function () {
            return $.ajax({
                url: 'index.php?module=docscan&action=edit&cmd=table-html',
                data: {'type': me.storage.documentType, 'id': me.storage.fileId},
                type: 'GET',
                dataType: 'html',
                success: function (data) {
                    me.elem.$docscanDialog.find('#' + me.storage.documentType + '-tab').html(data);
                    me.elem.$docscanDialog.find(me.selector.documentTableContainer).hide();
                }
            });
        },

        /**
         * DataTable-Einstellungen laden
         *
         * @return {jqXHR|null} jQuery jqXHR-Objekt oder null wenn Modul nicht zur Verfügung steht
         */
        fetchDataTableSettings: function () {
            var $tabContent = me.elem.$docscanDialog.find('#' + me.storage.documentType + '-tab');
            var $disabledContainer = $tabContent.find('.module-disabled');
            if ($disabledContainer.length > 0) {
                return null; // Manche Module stehen erst ab der Business-Version zur Verfügung
            }

            return $.ajax({
                url: 'index.php?module=docscan&action=edit&cmd=table-settings',
                data: {'type': me.storage.documentType, 'id': me.storage.fileId},
                type: 'GET',
                dataType: 'json',
                success: function (data) {
                    me.storage.dataTableSettings = data;
                }
            });
        },

        /**
         * Neue Verbindlichkeit anlegen + Datei zuweisen
         *
         * Bei Erfolg wird die Verbindlichkeit geöffnet.
         */
        createNewLiability: function () {
            $.ajax({
                url: 'index.php?module=docscan&action=edit&cmd=create-liability',
                data: {'id': me.storage.fileId},
                type: 'GET',
                dataType: 'json',
                success: function (data) {
                    if (typeof data.success === 'undefined' || data.success === false) {
                        alert('Unbekannter Fehler #5. Verbindlichkeit konnte nicht angelegt werden.');
                        return;
                    }
                    /** @namespace data.liability */
                    if (typeof data.liability === 'undefined') {
                        alert('Unbekannter Fehler #6. Verbindlichkeit konnte nicht angelegt werden.');
                        return;
                    }

                    window.location.href = './index.php?module=verbindlichkeit&action=edit&id=' + data.liability;
                }
            });
        },

        /**
         * @param {FileList} files
         */
        handleDroppedFiles: function (files) {
            $.each(files, function (index, file) {

                // Dateien einlesen
                var fileReader = new FileReader();
                fileReader.onload = (function (file) {
                    return function () {
                        if (file.size === 0 || file.type === '') {
                            return;
                        }

                        var isImage = file.type.substr(0, 6) === 'image/';
                        var isPdf = file.type === 'application/pdf';
                        if (!isImage && !isPdf) {
                            alert('Dieser Dateityp wird nicht unterstützt. Bitte laden Sie nur PDFs und Bilder hoch.');
                            return;
                        }

                        // Datei einzeln hochladen
                        me.uploadDroppedFiles(file.name, this.result);
                    };

                })(files[index]);
                fileReader.readAsDataURL(file);
            });
        },

        /**
         * In Dropzone abgelegte Datei hochladen
         *
         * @param {string} name Dateiname
         * @param {string} data Uploaddaten (base64-kodiert)
         */
        uploadDroppedFiles: function (name, data) {
            if (typeof name === 'undefined' || typeof data === 'undefined') {
                return;
            }

            $.ajax({
                type: 'POST',
                url: 'index.php?module=docscan&action=list&cmd=drop-file',
                data: {name: name, data: data},
                dataType: 'json',
                success: function (result) {
                    if (result && result.success && result.success === true) {
                        me.debounce(function () {

                            // DataTable aktualisieren
                            me.reloadFilesTable();

                            // Vorschau zur zuletzt hochgeladenen Datei in IFrame anzeigen
                            if (typeof result.file !== 'undefined') {
                                me.previewFileInIframe(result.file);
                            }
                        }, 250);
                    }
                }
            });
        },

        /**
         * Dropzone anzeigen (Vorschau ausblenden)
         */
        showDropzone: function () {
            me.elem.$dropzone.addClass('active');
        },

        /**
         * Dropzone ausblenden (Vorschau anzeigen)
         */
        hideDropzone: function () {
            me.elem.$dropzone.removeClass('active');
        },

        /**
         * Puffer-Funktion um Events erst nach einer bestimmten Zeit auszuführen
         *
         * @param {function} callback
         * @param {number}   delay
         */
        debounce: function (callback, delay) {
            var context = this;
            var args = arguments;

            window.clearTimeout(me.storage.buffer);
            me.storage.buffer = window.setTimeout(function () {
                callback.apply(context, args);
            }, delay || 250);
        }
    };

    return {
        init: me.init
    };

}(jQuery);


$(document).ready(function () {
    if ($('#docscan-module').length === 0) {
        return;
    }
    DocscanFiles.init();
});
