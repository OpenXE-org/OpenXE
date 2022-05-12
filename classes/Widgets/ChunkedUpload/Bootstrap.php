<?php

namespace Xentral\Widgets\ChunkedUpload;

final class Bootstrap
{
    /**
     * @return array
     */
    public static function registerJavascript()
    {
        return [
            'chunkedupload' => [
                './classes/Widgets/ChunkedUpload/www/js/jquery.chunkedUpload.js',
            ],
        ];
    }

    /**
     * @return array
     */
    public static function registerStylesheets()
    {
        return [
            'chunkedupload' => [
                './classes/Widgets/ChunkedUpload/www/css/chunked_upload.css',
            ],
        ];
    }

    /**
     * @return array
     */
    public static function registerServices()
    {
        return [
            'ChunkedUploadRequestHandler' => 'onInitChunkedUploadRequestHandler',
        ];
    }

    /**
     * @return ChunkedUploadRequestHandler
     */
    public static function onInitChunkedUploadRequestHandler()
    {
        return new ChunkedUploadRequestHandler();
    }
}
