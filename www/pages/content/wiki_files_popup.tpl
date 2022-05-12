<script>
    var WikiFilesBrowser = (function ($) {
        'use strict';

        var me = {

            init: function () {
                $('#wiki_files').on('click', '.wiki-select-file-button', me.onClickSelectFileButton);
                $('#wiki_files').on('click', '.wiki-delete-file-button', me.onClickDeleteFileButton);
            },

            /**
             * @param {Event} event
             */
            onClickSelectFileButton: function (event) {
                event.preventDefault();
                var fileId = $(this).data('fileId');

                me.assignFileUrltoEditorInstance(fileId);
            },

            onClickDeleteFileButton: function (event) {
                event.preventDefault();
                var fileId = $(this).data('fileId');

                if(confirm('Datei wirklich löschen?')) {
                    $.ajax({
                        url: 'index.php?module=wiki&action=dateien&subcmd=delete',
                        type: 'POST',
                        dataType: 'json',
                        data: {
                            fileid: fileId
                        },
                        success: function(data) {
														$('#wiki_files').DataTable( ).ajax.reload();
                        },
                        beforeSend: function() {

                        }
                    });
                }
            },

						/**
             * @see https://ckeditor.com/docs/ckeditor4/latest/guide/dev_file_browser_api.html#example-2
						 *
						 * @param {Number} fileId
						 */
            assignFileUrltoEditorInstance: function (fileId) {
                var fileUrl = me.getFileUrl(fileId);
                var funcNum = me.getUrlParam('CKEditorFuncNum');
                window.opener.CKEDITOR.tools.callFunction(funcNum, fileUrl);
                window.close();
            },

						/**
						 * @param {Number} fileId
						 *
						 * @return {String}
						 */
						getFileUrl: function (fileId) {
                if (typeof fileId === 'undefined' || fileId === null || fileId === '') {
                    alert('Can not generate file url. Required attribute "fileId" is missing.');
                    return '';
                }

                return './index.php?module=dateien&action=send&id=' + fileId;
            },

            /**
             * Helper function to get parameters from the query string.
             *
             * @param {String} paramName
             *
             * @return {String|null}
             */
            getUrlParam: function (paramName) {
                var reParam = new RegExp('(?:[\?&]|&)' + paramName + '=([^&]+)', 'i');
                var match = window.location.search.match(reParam);

                return (match && match.length > 1) ? match[1] : null;
            }
        };

        return {
            init: me.init
        };

    })(jQuery);

    $(document).ready(function () {
        WikiFilesBrowser.init();
    });
</script>
<div>
	[DATATABLE]
</div>
