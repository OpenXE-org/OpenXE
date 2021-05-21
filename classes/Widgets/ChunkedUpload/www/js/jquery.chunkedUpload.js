/**
 * # ChunkedUpload
 *
 * ## Initialisierung
 *
 * ```html
 * <input type="file" id="chunky">
 * <div id="files"></div>
 *
 * <script type="application/javascript">
 * $('#chunky').chunkedUpload({
 *      chunkSize: 6291456, // 6MB
 *      upload: {
 *          url: 'index.php?module=foo&action=bar',
 *      },
 *      filesContainer: '#files'
 * });
 * </script>
 * ```
 *
 * ## Callback
 *
 * ### `fileComplete`
 *
 * ```javascript
 * $('#chunky').chunkedUpload({
 *     fileComplete: function(fileInfo) {
 *         // Do something when file upload is completed
 *         // fileInfo.id   = Unique id; example: 'chunked_upload_2377390993'
 *         // fileInfo.name = Client file name
 *         // fileInfo.type = File mime type
 *         // fileInfo.size = File size in bytes
 *     }
 * });
 * ```
 *
 */
(function ($) {
    'use strict';

    var ChunkedUpload = function ($elem, options) {

        var STATUS = {
            WAITING: 'waiting', // Datei wurde hinzugefügt; Es wird gewartet dass Benutzer den Upload startet
            UPLOADING: 'uploading', // Datei wird gerade hochgeladen
            FINISHED: 'finished', // Upload verarbeitet
            FAILURE: 'failure' // Fehler beim Upload
        };

        var me = {

            /** @property {Object} me.options Default configuration */
            options: {
                chunkSize: 6291456, // 6291456 = 6MB
                upload: {
                    url: null,
                    view: 'standard',
                    formData: {}
                },
                filesContainer: '#chunked-upload-files',

                /**
                 * Callback wenn Datei erfolgreich hochgeladen wurde
                 *
                 * @param {FileInfo} fileInfo
                 */
                fileComplete: function (fileInfo) {}
            },

            /** @property {Object} me.storage Runtime storage */
            storage: {
                $fileInput: null,
                $filesContainer: null,
                $filesList: null,
                uploads: {}
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
                if (typeof me.options.upload.url === 'undefined' || me.options.upload.url === null) {
                    throw 'Initialisierung nicht möglich. Upload-URL fehlt.';
                }
                if (typeof me.options.upload.formData === 'undefined' || me.options.upload.formData === null) {
                    me.options.upload.formData = {};
                }

                var $fileInput = $(element);
                if ($fileInput.length !== 1) {
                    throw 'File-Input Element wurde nicht gefunden.';
                }
                if (!$fileInput.is('input[type=file]')) {
                    alert('Init-Element muss ein "input"-Element vom Typ "file" sein.');
                    return;
                }

                $fileInput.on('change', me.onSelectFilesEventHandler);
                me.storage.$fileInput = $fileInput;

                me.createFilesContainer();
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

                // File-Input leeren
                var $input = $(this);
                $input.replaceWith($input.val('').clone(true));
            },

            /**
             * @param {File} fileObject
             */
            addFileUpload: function (fileObject) {
                var fileId = me.generateRandomId();
                var fileSize = me.formatBytes(fileObject.size);

                var uploadObject = {
                    id: fileId,
                    file: fileObject,
                    reader: new FileReader(),
                    status: STATUS.WAITING,
                    elements: {
                        $progressBar: null,
                        $statusInfo: null,
                        $actionCell: null
                    }
                };

                var $removeFileButton = $('<a>');
                $removeFileButton.attr('href', '#').text('Entfernen');
                $removeFileButton.addClass('chuncked-removefile-trigger').addClass('button');
                $removeFileButton.on('click', function (event) {
                    event.preventDefault();
                    var $link = $(this);
                    var $row = $link.parents('tr');
                    var fileId = $row.data('fileId');

                    me.removeFile(fileId);
                });

                var $uploadFileButton = $('<a>');
                $uploadFileButton.attr('href', '#').text('Hochladen');
                $uploadFileButton.addClass('chuncked-startupload-trigger').addClass('button');
                $uploadFileButton.on('click', function (event) {
                    event.preventDefault();
                    var $link = $(this);
                    var $row = $link.parents('tr');
                    var fileId = $row.data('fileId');

                    me.startUpload(fileId);
                });

                var $progressBar = $('<progress>').attr('min', '0').attr('max', '100').val(0);
                var $row = $('<tr>').attr('id', fileId).data('fileId', fileId);
                if(me.options.upload.view === 'sidebar') {
                    var $td = $('<td>').appendTo($row);
                    var $table = $('<table>').appendTo($td);
                    $('<td>').addClass('filename').html(fileObject.name).appendTo($('<tr>').appendTo($table)).before('<td>Dateiname:</td>');
                    $('<td>').addClass('filesize').html(fileSize).appendTo($('<tr>').appendTo($table)).before('<td>Gr&ouml;&szlig;e:</td>');
                    $('<td>').addClass('fileprogress').html($progressBar).appendTo($('<tr>').appendTo($table)).before('<td>Fortschritt:</td>');
                    var $statusInfo = $('<td>').addClass('filestatus').html('Bereit zum Hochladen').appendTo($('<tr>').appendTo($table)).before('<td>Status:</td>');
                    var $actionsCell = $('<td>').addClass('fileaction').appendTo($('<tr>').data('fileId', fileId).appendTo($table));
                }
                else {
                    $('<td>').addClass('filename').html(fileObject.name).appendTo($row);
                    $('<td>').addClass('filesize').html(fileSize).appendTo($row);
                    $('<td>').addClass('fileprogress').html($progressBar).appendTo($row);
                    var $statusInfo = $('<td>').addClass('filestatus').html('Bereit zum Hochladen').appendTo($row);
                    var $actionsCell = $('<td>').addClass('fileaction').appendTo($row);
                }
                $actionsCell.append($removeFileButton);
                $actionsCell.append($uploadFileButton);
                $row.appendTo(me.storage.$filesList);

                uploadObject.elements.$progressBar = $progressBar;
                uploadObject.elements.$statusInfo = $statusInfo;
                uploadObject.elements.$actionCell = $actionsCell;
                me.storage.uploads[fileId] = uploadObject;

                me.storage.$filesContainer.show();
                me.storage.$filesContainer.find('table').show();
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

                // Datei existiert nicht (mehr?)
                if (!me.storage.uploads.hasOwnProperty(uploadId)) {
                    return;
                }

                // Upload läuft gerade
                if (me.storage.uploads[uploadId].status === STATUS.UPLOADING) {
                    return; // @todo Laufenden Upload abbrechen
                }

                // Tabellenzeile entfernen
                $row.remove();
                delete me.storage.uploads[uploadId];
            },

            /**
             * @ŧodo Upload starten
             *
             * @param {String} uploadId
             */
            startUpload: function (uploadId) {
                if (me.storage.uploads.hasOwnProperty(uploadId)) {
                    var upload = me.storage.uploads[uploadId];
                    me.startSingleUpload(upload);
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

                var $tableRow = $('#' + fileId);
                var $statusCell = $tableRow.find('.filestatus');
                $statusCell.html('Bitte warten...');
                upload.elements.$progressBar.val(0);

                me.uploadFileChunk(upload, 0);
            },

            /**
             * @param {object} upload
             * @param {number} start
             */
            uploadFileChunk: function (upload, start) {
                var chunkSize = me.options.chunkSize;

                // ChunkSize kleiner 10KB macht keinen Sinn; Upload verhindern
                if (chunkSize < 10240) {
                    me.displayUploadError(upload.id, 'ChunkSize ist zu gering (<= 10KB). Upload nicht möglich.');
                    return;
                }

                // Im allerersten Upload die ChunkSize auf 100KB stellen
                // Server schickt in seiner Antwort das PHP-Upload-Limit mit
                if (start === 0) {
                    chunkSize = 102400; // 102400 = 100KB
                }

                var offset = start + chunkSize + 1;
                var chunkBlob = upload.file.slice(start, offset);

                var fileId = upload.id;
                if (typeof fileId === 'undefined') {
                    throw 'Upload fehlgeschlagen. Unique-ID is missing.';
                }

                upload.status = STATUS.UPLOADING;
                upload.elements.$statusInfo.html('Hochladen...');
                upload.elements.$actionCell.html('');

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
                    ajaxData.file_id = upload.id;
                    ajaxData.file_offset = start;

                    upload.xhr = $.ajax({
                        url: me.options.upload.url,
                        type: 'POST',
                        dataType: 'json',
                        cache: false,
                        data: ajaxData,
                        error: function (jqXHR, textStatus, errorThrown) {
                            var errorMessage = 'Unbekannter Fehler #21: ' + errorThrown;

                            // User hat "Abbrechen" geklickt
                            if (textStatus === 'abort') {
                                errorMessage = 'Upload abgebrochen';
                            }

                            // PHP-Skript hat Fehler geliefert (z.b. 404)
                            if (textStatus === 'error') {
                                errorMessage = 'Unbekannter Server-Fehler';
                            }

                            // PHP-Skript liefer JSON-Error-Response
                            if (jqXHR.hasOwnProperty('responseJSON') && jqXHR.responseJSON.hasOwnProperty('error')) {
                                errorMessage = 'Server-Fehler: ' + jqXHR.responseJSON.error;
                            }

                            upload.elements.$statusInfo.html('<strong>' + errorMessage + '</strong>');
                            upload.elements.$progressBar.val(null);
                        },
                        success: function (data) {
                            if (data.hasOwnProperty('success') && data.success === false) {
                                upload.elements.$progressBar.val(null);
                                upload.elements.$statusInfo.html('<strong>Server-Fehler: ' + data.error + '</strong>');
                                return;
                            }

                            if (!data.hasOwnProperty('file') && !data.file.hasOwnProperty('bytes')) {
                                alert('Fehlerhafte Antwort vom Server. #1');
                                return;
                            }

                            if (data.file.bytes === false) {
                                alert('Fehlerhafte Antwort vom Server. #2');
                                return;
                            }

                            var bytesSend = data.file.bytes;
                            var sizeDone = start + bytesSend;
                            var sizeTotal = upload.file.size;
                            var percentDone = Math.floor((sizeDone / sizeTotal) * 100);
                            upload.elements.$progressBar.val(percentDone);

                            // Die erste Response vom Server enthält das PHP-Upload-Limit
                            // => ChunkSize anpassen falls diese über dem PHP-Limit liegt
                            if (data.hasOwnProperty('uploadLimit')
                                && typeof data.uploadLimit === 'number'
                                && data.uploadLimit > 0
                            ) {
                                var uploadLimit = Math.floor(data.uploadLimit / 100 * 95); // 5% als Reserve freihalten
                                var transferSize = me.calculateBase64SizeFromRawSize(me.options.chunkSize);
                                if (uploadLimit < transferSize) {
                                    var maxChunkSize = me.calculateRawSizeFromBase64Size(uploadLimit);
                                    me.options.chunkSize = maxChunkSize;
                                    console.warn('ChunkedUpload: PHP upload limit is ' + data.uploadLimit + ' bytes.');
                                    console.warn('ChunkedUpload: Chunk size set to ' + maxChunkSize + ' bytes.');
                                }
                            }

                            if (offset < sizeTotal) {
                                me.uploadFileChunk(upload, offset);
                            } else {
                                me.finishUpload(upload.id);
                            }
                        }
                    });
                };

                // Datei einlesen starten
                upload.reader.readAsDataURL(chunkBlob);
            },

            /**
             * Erfolgreichen Upload abschließen
             *
             * @param {String} uploadId
             */
            finishUpload: function (uploadId) {
                if (me.storage.uploads.hasOwnProperty(uploadId)) {
                    var upload = me.storage.uploads[uploadId];
                    upload.elements.$statusInfo.html('Upload erfolgreich');
                    upload.elements.$actionCell.html('');
                    upload.status = STATUS.FINISHED;

                    // Callback aufrufen
                    var fileInfo = new FileInfo(upload.id, upload.file.name, upload.file.type, upload.file.size);
                    me.options.fileComplete(fileInfo);
                }
            },

            /**
             * Upload als fehlerhaft markieren
             *
             * @param {String} uploadId
             * @param {String} errorMessage
             */
            displayUploadError: function (uploadId, errorMessage) {
                if (me.storage.uploads.hasOwnProperty(uploadId)) {
                    var upload = me.storage.uploads[uploadId];
                    upload.elements.$statusInfo.html('Upload-Fehler: ' + errorMessage);
                    upload.elements.$actionCell.html('');
                    upload.status = STATUS.FAILURE;
                }
            },

            /**
             * Datei-Tabelle erzeugen
             */
            createFilesContainer: function () {
                var template = '<table>';
                if(me.options.upload.view === 'sidebar') {
                    template +=
                        '<thead><th></th><th></th>' +
                        '<tbody></tbody>' +
                        '</table>';
                }
                else {
                    template +=
                        '<thead><th align="left">Dateiname</th><th>Gr&ouml;&szlig;e</th>' +
                        '<th>Fortschritt</th><th>Status</th><th>Aktionen</th></tr></thead>' +
                        '<tbody></tbody>' +
                        '</table>';
                }
                var $list = $(template).hide();

                var $filesContainer = $(me.options.filesContainer);
                if ($filesContainer.length === 0) {
                    $filesContainer = $('<div>').insertAfter(me.storage.$fileInput);
                }

                $filesContainer.append($list);
                $filesContainer.addClass('chunked-file-upload-container');

                me.storage.$filesContainer = $filesContainer;
                me.storage.$filesList = $list.find('tbody');
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
                return 'chunked_upload_' + Math.floor(Math.random() * Math.floor(9999999999));
            },

            /**
             * @param {string|number} value
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
            },

            /**
             * @param {number} rawSize Größe der Binärdaten (in Bytes)
             *
             * @return {number} Größe in Bytes wenn Base64-kodiert
             */
            calculateBase64SizeFromRawSize: function (rawSize) {
                return Math.floor(rawSize / 3 * 4);
            },

            /**
             *
             * @param {number} encodedSize Größe eines Base64-kodierten Strings (in Bytes)
             *
             * @return {number} Größe in Bytes nach Base64-Dekodierung
             */
            calculateRawSizeFromBase64Size: function (encodedSize) {
                return Math.floor(encodedSize / 4 * 3);
            }
        };

        /**
         * @param {string} id   Unique id; example: 'chunked_upload_2377390993'
         * @param {string} name file name
         * @param {string} type Mime type
         * @param {number} size File size in bytes
         * @constructor
         */
        var FileInfo = function (id, name, type, size) {
            this.id = id;
            this.name = name;
            this.type = type;
            this.size = size;
        };

        me.init($elem, options);

        /**
         * Return public api
         */
        return {};
    };

    // Dokumentation: Siehe Dateianfang
    $.fn.chunkedUpload = function (options) {
        return this.each(function () {
            var $elem = $(this);

            if (!$elem.data('chunkedUpload')) {
                var api = new ChunkedUpload(this, options);
                $elem.data('chunkedUpload', api);
            }
        });
    };

}(jQuery));
