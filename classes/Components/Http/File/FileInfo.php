<?php

namespace Xentral\Components\Http\File;

use SplFileInfo;
use Xentral\Components\Http\Exception\FileNotFoundException;

class FileInfo extends SplFileInfo
{
    /**
     * @param string $filePath
     * @param bool   $checkExistence
     *
     * @throws FileNotFoundException
     */
    public function __construct($filePath, $checkExistence = true)
    {
        if ($checkExistence === true && !is_file($filePath)) {
            throw new FileNotFoundException(sprintf('File "%s" not found.', $filePath));
        }

        parent::__construct($filePath);
    }

    /**
     * Returns the mime type of the file.
     *
     * @example CSV file -> 'text/plain'
     * @example PDF file -> 'application/pdf'
     *
     * @return string Mime type
     */
    public function getMimeType()
    {
        $finfo = finfo_open(FILEINFO_MIME);
        $mimetype = finfo_file($finfo, $this->getRealPath());
        finfo_close($finfo);

        if ($mimetype !== false) {
            $mimetype = preg_replace('/^(.+);.+$/', '\1', $mimetype);
        } else {
            $mimetype =  'application/octet-stream';
        }

        return $mimetype;
    }
}
