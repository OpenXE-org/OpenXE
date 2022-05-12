<?php

namespace Xentral\Core\ErrorHandler;

use RuntimeException;

class PhpErrorException extends RuntimeException
{
    /**
     * @param string $file
     *
     * @return void
     */
    public function setFile($file)
    {
        $this->file = $file;
    }

    /**
     * @param int $line
     *
     * @return void
     */
    public function setLine($line)
    {
        $this->line = $line;
    }
}
