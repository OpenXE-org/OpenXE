class XentralUploadAdapter {
    constructor( loader ) {
        // The file loader instance to use during the upload.
        this.loader = loader;
    }

    upload() {
        window.console.log(this);
        return this.loader.file
                   .then( file => new Promise( ( resolve, reject ) => {
                       this._initRequest();
                       this._initListeners( resolve, reject, file );
                       this._sendRequest( file );
                   } ) );
    }

    // Aborts the upload process.
    abort() {
        if ( this.xhr ) {
            this.xhr.abort();
        }
    }

    _initRequest() {
        const xhr = this.xhr = new XMLHttpRequest();

        // Note that your request may look different. It is up to you and your editor
        // integration to choose the right communication channel. This example uses
        // a POST request with JSON as a data structure but your configuration
        // could be different.
        xhr.open( 'POST', './index.php?module=ajax&action=upload&fromurl='+encodeURIComponent(window.location.href), true );
        xhr.responseType = 'json';
    }

    _initListeners( resolve, reject, file ) {
        const xhr = this.xhr;
        const loader = this.loader;
        const genericErrorText = `Couldn't upload file: ${ file.name }.`;

        xhr.addEventListener( 'error', () => reject( genericErrorText ) );
        xhr.addEventListener( 'abort', () => reject() );
        xhr.addEventListener( 'load', () => {
            const response = xhr.response;

            // This example assumes the XHR server's "response" object will come with
            // an "error" which has its own "message" that can be passed to reject()
            // in the upload promise.
            //
            // Your integration may handle upload errors in a different way so make sure
            // it is done properly. The reject() function must be called when the upload fails.
            if ( !response || response.error ) {
                return reject( response && response.error ? response.error.message : genericErrorText );
            }

            // If the upload is successful, resolve the upload promise with an object containing
            // at least the "default" URL, pointing to the image on the server.
            // This URL will be used to display the image in the content. Learn more in the
            // UploadAdapter#upload documentation.
            resolve( {
                default: response.url
            } );
        } );

        // Upload progress when it is supported. The file loader has the #uploadTotal and #uploaded
        // properties which are used e.g. to display the upload progress bar in the editor
        // user interface.
        if ( xhr.upload ) {
            xhr.upload.addEventListener( 'progress', evt => {
                if ( evt.lengthComputable ) {
                    loader.uploadTotal = evt.total;
                    loader.uploaded = evt.loaded;
                }
            } );
        }
    }

    // Prepares the data and sends the request.
    _sendRequest( file ) {
        // Prepare the form data.
        const data = new FormData();

        data.append( 'upload', file );

        // Important note: This is the right place to implement security mechanisms
        // like authentication and CSRF protection. For instance, you can use
        // XMLHttpRequest.setRequestHeader() to set the request headers containing
        // the CSRF token generated earlier by your application.

        // Send the request.
        this.xhr.send( data );
    }
}

function XentralUploadAdapterPlugin( editor ) {
    editor.plugins.get( 'FileRepository' ).createUploadAdapter = ( loader ) => {
        // Configure the URL to the upload script in your back-end here!
        return new XentralUploadAdapter( loader );
    };
}
var CkEditor5Helper = function ($, ClassicEditor) {
    "use strict";

    var me = {
        storage: {
            editor : []
        },
        init: function () {
            $('.json_ckeditor5').each(function() {
                var jsontext = $(this).html();
                if(jsontext) {
                    var obj = JSON.parse(jsontext);
                    window.console.log(obj);
                    if (typeof obj.data != 'undefined' && typeof obj.element != 'undefined') {
                        window.console.log(obj.data);
                        if(typeof obj.data.extraPlugins != 'undefined') {
                            obj.data.extraPlugins.push(XentralUploadAdapterPlugin);
                        }
                        else{
                            obj.data.extraPlugins = [XentralUploadAdapterPlugin];
                        }
                        ClassicEditor.create( document.querySelector( '#'+obj.element ),
                            obj.data
                        ).
                        then(editor => {
                           me.storage.editor[obj.element] = editor;
                        });
                    }
                    $(this).remove();
                }
            });
        },
        update: function(element) {
            if(typeof me.storage.editor[element] != 'undefined') {
                me.storage.editor[element].updateSourceElement();
                window.console.log(me.storage.editor[element]);
            }
        },
        setData: function (element, data) {
            if(typeof me.storage.editor[element] != 'undefined') {
                me.storage.editor[element].setData(data);
            }
        }
    };

    return {
        init: me.init,
        update: me.update,
        setData: me.setData
    };

}(jQuery, ClassicEditor);

$(document).ready(function () {
    CkEditor5Helper.init();
});

