/**
 * # DropzoneUpload
 *
 * ## Initialisierung
 *
 * `$('#dropzone').dropzoneUpload(options);`
 *
 * ## Initialisierungsoptionen
 *
 * * debug (bool):
 *   Debugging aktivieren/deaktivieren
 *   Default: `false`
 *
 * * upload.url (string):
 *   URL die zum Hochladen von Dateien verwendet wird.
 *   Default: `'index.php?module=dateibrowser&action=upload&cmd=upload-file'`
 *
 * * upload.formData (object):
 *   Zusätzliche POST-Daten für den Datei-Upload.
 *   Default: `{}`
 *
 * * upload.simultaneous (number)
 *   Anzahl der simultanen Upload-Requests.
 *   Default: `5`
 *
 * ## DropzoneUpload-API
 *
 * API-Objekt holen: `$('#dropzone').DropZoneUploadApi()`
 *
 * ### API-Methoden
 *
 * * `startUploads()`
 *   Startet alle offenen Uploads
 *
 * * `cancelUploads()`
 *   Bricht alle laufenden und wartenden Uploads ab
 *
 * * `reset()`
 *   Setzt alles auf den Ausgangszustand zurück
 */
(function ($) {
    'use strict';

    var DropZoneUpload = function ($elem, options) {

        var STATUS = {
            INITAL: 'inital', // Ausgangszustand: Dateiliste leer
            WAITING: 'waiting', // Uploads wurden hinzugefügt; Es wird gewartet dass Benutzer den Upload startet
            UPLOADING: 'uploading', // Upload werden gerade hochgeladen
            FINISHED: 'finished' // Alle Uploads verarbeitet
        };

        var me = {

            /** @property {Object} me.options Default configuration */
            options: {
                debug: false,
                upload: {
                    url: 'index.php?module=dateibrowser&action=upload&cmd=upload-file',
                    formData: {},
                    simultaneous: 5
                }
            },

            /** @property {Object} me.storage Runtime storage */
            storage: {
                $container: null,
                $dropzone: null,
                $filesContainer: null,
                $filesList: null,
                $statusInfo: null,
                buttons: {
                    $container: null,
                    $start: null,
                    $cancel: null,
                    $reset: null
                },
                uploadLoop: null,
                currentStatus: STATUS.INITAL,
                waiting: [], // Wartende Uploads
                uploads: [], // Laufende Uploads
                canceled: [] // Abgebrochene Uploads
            },

            /**
             * @param {HTMLElement} element
             * @param {Object} options
             */
            init: function (element, options) {

                // Prüfen ob HTML5-File-API verfügbar
                if (!me.isFileApiAvailable()) {
                    alert(
                        'Die HTML5 File-API wird von ihrem Browser nicht unterstützt. ' +
                        'Bitte verwenden sie einen moderneren Browser.'
                    );
                    return;
                }

                // Optionen mit Defaults mergen
                me.options = $.extend({}, me.options, options);
                if (typeof me.options.upload.simultaneous !== 'number') {
                    throw 'Invalid option "upload.simultaneous".';
                }

                var $container = $(element);
                if ($container.length !== 1) {
                    throw 'Container-Elemente wurde nicht gefunden.';
                }

                $container.addClass('dropzone-upload');
                me.storage.$container = $container;

                me.createDropzone();
                me.createButtons();
                me.createFilesContainer();
            },

            /**
             * @param {string} status
             */
            changeStatus: function (status) {
                switch (status) {
                    case STATUS.INITAL:
                        me.storage.$filesList.html('');
                        me.storage.$filesContainer.hide();
                        me.storage.buttons.$container.hide();
                        me.storage.buttons.$start.hide();
                        me.storage.buttons.$cancel.hide();
                        me.storage.buttons.$reset.hide();
                        break;

                    case STATUS.WAITING:
                        me.storage.$filesContainer.show();
                        me.storage.buttons.$container.show();
                        me.storage.buttons.$start.show();
                        me.storage.buttons.$cancel.hide();
                        me.storage.buttons.$reset.show();
                        break;

                    case STATUS.UPLOADING:
                        me.storage.$statusInfo.html('Dateien werden hochgeladen');
                        me.storage.$filesContainer.show();
                        me.storage.buttons.$container.show();
                        me.storage.buttons.$start.show();
                        me.storage.buttons.$cancel.show();
                        me.storage.buttons.$reset.hide();
                        break;

                    case STATUS.FINISHED:
                        me.storage.$statusInfo.html('Upload abgeschlossen');
                        me.storage.$filesContainer.show();
                        me.storage.buttons.$container.show();
                        me.storage.buttons.$start.hide();
                        me.storage.buttons.$cancel.hide();
                        me.storage.buttons.$reset.show();
                        break;

                    default:
                        status = STATUS.INITAL;
                        me.changeStatus(status);
                        break;
                }

                me.storage.currentStatus = status;
            },

            /**
             * @param {Event} event
             */
            onClickDropboxEventHandler: function (event) {
                event.stopPropagation();
                var $fileInput = me.storage.$container.find('input[type=file]');
                $fileInput.trigger('click');
            },

            /**
             * @param {Event} event
             */
            onDragEventHandler: function (event) {
                event.preventDefault();
                event.stopPropagation();
                if (event.type === 'dragover') {
                    $(this).addClass('dragging');
                }
                if (event.type === 'dragleave') {
                    $(this).removeClass('dragging');
                }
            },

            /**
             * @param {Event} event
             */
            onDropFilesEventHandler: function (event) {
                event.preventDefault();
                event.stopPropagation();

                var files = event.originalEvent.dataTransfer.files;
                $.each(files, function (index, file) {
                    me.addFileUpload(file);
                });

                me.storage.$dropzone.removeClass('dragging');
            },

            /**
             * @param {Event} event
             */
            onSelectFilesEventHandler: function (event) {
                /** @var {FileList} files */
                var files = event.target.files;
                $.each(files, function (index, file) {
                    me.addFileUpload(file);
                });
            },

            /**
             * @param {File}   fileObject
             */
            addFileUpload: function (fileObject) {
                var fileId = me.generateRandomId();
                var fileSize = me.formatBytes(fileObject.size);

                me.storage.waiting.push({
                    id: fileId,
                    file: fileObject,
                    reader: new FileReader()
                });

                var $selectKeywordTemplate = $('#select-keyword-template').clone();
                var selectedKeyword = $('#select-keyword-template').val();
                $selectKeywordTemplate.removeAttr('id');
                $selectKeywordTemplate.val(selectedKeyword);
                $selectKeywordTemplate.trigger('change');
                var $inputTitleTemplate = $('<input>').attr('type', 'text');

                var $removeFileLink = $('<a>');
                $removeFileLink.attr('href', '#').text('Entfernen');
                $removeFileLink.addClass('dropzone-removefile-trigger').addClass('btn');
                $removeFileLink.on('click', function (event) {
                    event.preventDefault();
                    var $link = $(this);
                    var $row = $link.parents('tr');
                    var fileId = $row.data('fileId');

                    me.removeFile(fileId);
                });

                var $row = $('<tr>').attr('id', fileId).data('fileId', fileId);
                $('<td>').addClass('filepreview').html('&nbsp;').appendTo($row);
                $('<td>').addClass('filename').html(fileObject.name).appendTo($row);
                $('<td>').addClass('filesize').html(fileSize).appendTo($row);
                $('<td>').addClass('filetitle').html($inputTitleTemplate).appendTo($row);
                $('<td>').addClass('filekeyword').html($selectKeywordTemplate).appendTo($row);
                $('<td>').addClass('filestatus').html('Bereit zum Hochladen').appendTo($row);
                $('<td>').addClass('fileaction').html($removeFileLink).appendTo($row);

                $row.appendTo(me.storage.$filesList);

                if (me.storage.waiting.length > 0) {
                    if (me.storage.uploads.length > 0) {
                        me.changeStatus(STATUS.UPLOADING);
                    } else {
                        me.changeStatus(STATUS.WAITING);
                    }
                }
            },

            /**
             * @param {String} uploadId
             */
            removeFile: function (uploadId) {
                var $row = $('#' + uploadId);
                if ($row.length === 0) {
                    alert('Can not remove file upload. Element "#' + uploadId + '" not found.');
                    return;
                }

                // Tabellenzeile entfernen
                $row.remove();

                var uploads = me.storage.uploads;
                $.each(uploads, function (index, upload) {
                    if (typeof upload === 'undefined' || upload === null) {
                        return; // Upload wurde bereits verarbeitet
                    }

                    if (uploadId === upload.id) {
                        me.storage.uploads.splice(index, 1);
                        me.storage.canceled.push(upload);
                        me.cancelSingleUpload(upload);
                    }
                });

                var waiting = me.storage.waiting;
                $.each(waiting, function (index, upload) {
                    if (typeof upload === 'undefined' || upload === null) {
                        return; // Upload wurde bereits verarbeitet
                    }

                    if (uploadId === upload.id) {
                        me.storage.waiting.splice(index, 1);
                        me.storage.canceled.push(upload);
                        me.cancelSingleUpload(upload);
                    }
                });
            },

            /**
             * Alle offenen (unverarbeiteten) Uploads starten
             */
            startUploads: function () {
                me.storage.uploadLoop = window.setInterval(me.processUploadQueue, 100);
            },

            /**
             * Warteschlange mit unverarbeiteten Uploads abarbeiten
             */
            processUploadQueue: function () {
                if (me.options.debug === true) {
                    console.log('processUploadQueue', {
                        waitingCount: me.storage.waiting.length,
                        uploadsCount: me.storage.uploads.length,
                        canceledCount: me.storage.canceled.length,
                        simultaneous: me.options.upload.simultaneous
                    });
                }

                if (me.storage.uploads.length <= 0 && me.storage.waiting.length <= 0) {
                    window.clearInterval(me.storage.uploadLoop);
                    return; // Queue leer; keine unverarbeiteten oder laufenden Uploads
                }

                // Starte einzelnen Upload, wenn Platz frei
                if (me.storage.uploads.length < me.options.upload.simultaneous) {
                    var uploadObject = me.storage.waiting.shift();
                    if (typeof uploadObject !== 'undefined' && uploadObject !== null) {
                        me.startSingleUpload(uploadObject);
                    }
                }
            },

            /**
             * @param {Object} upload Einzelner Wert aus me.storage.waiting
             */
            startSingleUpload: function (upload) {
                if (upload === null) {
                    return;
                }

                var fileId = upload.id;
                if (typeof fileId === 'undefined') {
                    throw 'Upload fehlgeschlagen. Unique-ID is missing.';
                }

                // Upload-Queue füllen
                me.storage.uploads.push(upload);

                var $tableRow = $('#' + fileId);
                var $inputTitle = $tableRow.find('.filetitle input');
                var $selectKeyword = $tableRow.find('.filekeyword select');
                var $statusCell = $tableRow.find('.filestatus');
                $statusCell.html('Bitte warten...');

                if (me.options.debug === true) {
                    console.log('startSingleUpload', {
                        waitingCount: me.storage.waiting.length,
                        uploadsCount: me.storage.uploads.length,
                        canceledCount: me.storage.canceled.length,
                        simultaneous: me.options.upload.simultaneous
                    });
                }

                // Datei-Inhalt fertig eingelesen => Upload starten
                upload.reader.onloadend = function (event) {
                    if (event.target.readyState !== FileReader.DONE) {
                        return;
                    }

                    var ajaxData = me.options.upload.formData;
                    ajaxData.file_data = event.target.result;
                    ajaxData.file_name = upload.file.name;
                    ajaxData.file_type = upload.file.type;
                    ajaxData.file_size = upload.file.size;
                    ajaxData.file_title = $inputTitle.val();
                    ajaxData.file_keyword = $selectKeyword.val();

                    upload.xhr = $.ajax({
                        url: me.options.upload.url,
                        type: 'POST',
                        dataType: 'json',
                        cache: false,
                        data: ajaxData,
                        beforeSend: function () {
                            $statusCell.html('Hochladen...');
                            $inputTitle.prop('disabled', true);
                            $selectKeyword.prop('disabled', true);
                        },
                        error: function (jqXHR, textStatus, errorThrown) {
                            var errorMessage = 'Unbekannter Fehler #21: ' + errorThrown;

                            // User hat "Abbrechen" geklickt
                            if (textStatus === 'abort') {
                                errorMessage = 'Upload abgebrochen';
                            }

                            // PHP-Skript hat Fehler geliefert (z.b. 404)
                            if (textStatus === 'error') {
                                errorMessage =
                                    jqXHR.hasOwnProperty('responseJSON') &&
                                    jqXHR.responseJSON.hasOwnProperty('message')
                                        ? 'Fehler: ' + jqXHR.responseJSON.message
                                        : 'Unbekannter Fehler';
                            }

                            $statusCell.html('<span class="message-failure">' + errorMessage + '</span>');
                        },
                        success: function (data) {
                            var successMessage = data.hasOwnProperty('message') ? data.message : 'Hochgeladen';
                            $statusCell.html('<span class="message-success">' + successMessage + '</span>');

                            if (data.hasOwnProperty('file') && data.file.hasOwnProperty('preview')) {
                                me.createImagePreview(fileId, data.file.preview);
                            }
                        },
                        complete: function () {
                            me.finishUpload(fileId);
                        }
                    });
                };

                // Datei einlesen starten
                upload.reader.readAsDataURL(upload.file);

                me.changeStatus(STATUS.UPLOADING);
            },

            /**
             * Erfolgreichen Upload aus der Upload-Queue entfernen
             *
             * @param {String} uploadId
             */
            finishUpload: function (uploadId) {
                $.each(me.storage.uploads, function (index, upload) {
                    if (typeof upload === 'undefined') {
                        return;
                    }
                    if (upload.id === uploadId) {
                        me.storage.uploads.splice(index, 1);
                    }
                });

                if (me.storage.uploads.length <= 0 &&
                    me.storage.waiting.length <= 0) {
                    me.changeStatus(STATUS.FINISHED);
                }
            },

            /**
             * Bricht alle laufenden und wartenden Uploads ab
             */
            cancelUploads: function () {
                var index;

                // Erstmal Alles (offene und wartende Uploads) in Canceled-Queue schieben
                for (index = 0; index < me.storage.waiting.length; index++) {
                    me.storage.canceled.push(me.storage.waiting[index]);
                }
                for (index = 0; index < me.storage.uploads.length; index++) {
                    me.storage.canceled.push(me.storage.uploads[index]);
                }

                // Warteschlangen leeren
                me.storage.waiting = [];
                me.storage.uploads = [];

                if (me.options.debug === true) {
                    console.log('cancelUploads', {
                        waitingCount: me.storage.waiting.length,
                        uploadsCount: me.storage.uploads.length,
                        canceledCount: me.storage.canceled.length,
                        simultaneous: me.options.upload.simultaneous
                    });
                }

                // Uploads abbrechen
                $.each(me.storage.canceled, function (index, upload) {
                    me.cancelSingleUpload(upload);
                });

                me.changeStatus(STATUS.FINISHED);
            },

            /**
             * Bricht einzelnen (laufenden) Uploads ab
             *
             * @param {object} uploadObject
             */
            cancelSingleUpload: function (uploadObject) {
                if (typeof uploadObject === 'undefined' || uploadObject === null) {
                    return;
                }

                var fileId = uploadObject.id;
                var $tableRow = $('#' + fileId);
                var $statusCell = $tableRow.find('.filestatus');
                $statusCell.html('<span class="message-failure">Upload abgebrochen</span>');

                if (uploadObject.hasOwnProperty('reader')) {
                    uploadObject.reader.abort();
                }

                if (uploadObject.hasOwnProperty('xhr')) {
                    uploadObject.xhr.abort();
                }
            },

            /**
             * Dateiliste leeren
             */
            reset: function () {
                me.cancelUploads();
                me.storage.uploads = [];
                me.storage.waiting = [];
                me.storage.canceled = [];
                me.changeStatus(STATUS.INITAL);
            },

            /**
             * Dropzone-Element erzeugen
             */
            createDropzone: function () {
                var $dropzone = $('<div>');
                $dropzone.addClass('dropzone');
                $dropzone.html('<span>Dateien hier ablegen</span>');
                $dropzone.on('dragover dragleave', me.onDragEventHandler);
                $dropzone.on('drop', me.onDropFilesEventHandler);
                $dropzone.on('click', me.onClickDropboxEventHandler);
                $dropzone.appendTo(me.storage.$container);

                var $fileInput = $('<input>');
                $fileInput.attr('type', 'file');
                $fileInput.prop('multiple', true);
                $fileInput.on('change', me.onSelectFilesEventHandler);
                $('<div>').addClass('hidden-upload').html($fileInput).appendTo(me.storage.$container);

                me.storage.$dropzone = $dropzone;
            },

            /**
             * Datei-Tabelle erzeugen
             */
            createFilesContainer: function () {
                var tableTemplate =
                    '<table>' +
                    '<thead><th></th><th>Dateiname</th><th>Gr&ouml;&szlig;e</th>' +
                    '<th>Titel</th><th>Stichwort</th><th>Status</th><th>Aktionen</th></tr></thead>' +
                    '<tbody></tbody>' +
                    '</table>';
                var $files = $('<div>').addClass('files');
                var $list = $(tableTemplate);
                $files.appendTo(me.storage.$container).hide();
                $list.appendTo($files);

                me.storage.$filesContainer = $files;
                me.storage.$filesList = $list.find('tbody');
            },

            /**
             * Buttons erzeugen
             */
            createButtons: function () {
                var $buttons = $('<div>').addClass('buttons');
                $buttons.appendTo(me.storage.$container).hide();

                var $uploadButton = $('<input>');
                $uploadButton.attr('type', 'button');
                $uploadButton.addClass('upload-files-trigger');
                $uploadButton.addClass('btnGreen');
                $uploadButton.val('Upload starten');
                $uploadButton.on('click', function (e) {
                    e.preventDefault();
                    me.startUploads();
                });
                $uploadButton.appendTo($buttons);

                var $cancelButton = $('<input>');
                $cancelButton.attr('type', 'button');
                $cancelButton.addClass('stop-upload-trigger');
                $cancelButton.addClass('btnBlue');
                $cancelButton.val('Upload abbrechen');
                $cancelButton.on('click', function (e) {
                    e.preventDefault();
                    me.cancelUploads();
                });
                $cancelButton.appendTo($buttons);

                var $resetButton = $('<input>');
                $resetButton.attr('type', 'button');
                $resetButton.addClass('clear-files-trigger');
                $resetButton.addClass('btnBlue');
                $resetButton.val('Liste leeren');
                $resetButton.on('click', function (e) {
                    e.preventDefault();
                    me.reset();
                });
                $resetButton.appendTo($buttons);

                var $statusInfo = $('<span class="status-info"></span>');
                $statusInfo.appendTo($buttons);

                // Referenzen wegspeichern
                me.storage.buttons.$container = $buttons;
                me.storage.buttons.$start = $uploadButton;
                me.storage.buttons.$cancel = $cancelButton;
                me.storage.buttons.$reset = $resetButton;
                me.storage.$statusInfo = $statusInfo;
            },

            /**
             * @param {String} fileId
             * @param {String} previewUrl
             */
            createImagePreview: function (fileId, previewUrl) {
                var $row = me.storage.$filesList.find('#' + fileId);
                if ($row.length === 0) {
                    return;
                }

                var $img = $('<img src="" alt="">').attr({
                    'alt': 'Vorschau',
                    'src': previewUrl
                });
                $row.find('.filepreview').html($img);
            },

            /**
             * HTML5 File-API vorhanden? Oder Uralt-Browser?
             *
             * @return {boolean}
             */
            isFileApiAvailable: function () {
                return typeof window.File !== 'undefined' &&
                    typeof window.FileList !== 'undefined' &&
                    typeof window.FileReader !== 'undefined';
            },

            /**
             * Zufällige ID generieren
             *
             * @return {string}
             */
            generateRandomId: function () {
                return 'upload_' + Math.floor(Math.random() * Math.floor(9999999999));
            },

            /**
             * @param {string} value
             *
             * @return {string}
             */
            formatBytes: function (value) {
                var bytes = parseInt(value, 10);
                if (bytes === 0) {
                    return '0&nbsp;Bytes';
                }

                var sizes = ['Bytes', 'KB', 'MB', 'GB', 'TB'];
                var exponent = Math.floor(Math.log(bytes) / Math.log(1024));
                var decimalString = (bytes / Math.pow(1024, exponent)).toFixed(1) + '';

                return decimalString.replace('.', ',') + '&nbsp;' + sizes[exponent];
            }
        };

        me.init($elem, options);

        /**
         * Return public api
         */
        return {
            startUploads: me.startUploads,
            cancelUploads: me.cancelUploads,
            reset: me.reset
        };
    };

    // Dokumentation: Siehe Dateianfang
    $.fn.dropzoneUpload = function (options) {
        return this.each(function () {
            var $elem = $(this);

            if (!$elem.data('dropzoneUpload')) {
                var api = new DropZoneUpload(this, options);

                $elem.init.prototype.DropZoneUploadApi = function () {
                    return api;
                };

                $elem.data('dropzoneUpload', api);
            }
        });
    };

}(jQuery));
