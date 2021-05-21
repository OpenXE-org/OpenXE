<?php

namespace Xentral\Modules\TransferSmartyTemplate\TemplateHelper;

final class CommonHelper
{
    /**
     * @param string $string
     * @param string $replaceChar
     *
     * @return string
     */
    public function replaceLineBreaks($string, $replaceChar = ' ')
    {
        return (string)preg_replace("/\r\n|\r|\n/", (string)$replaceChar, (string)$string);
    }

    /**
     * Dumps a template variable
     *
     * @example {$var|dump}
     *
     * @param mixed $data
     *
     * @return string
     */
    public function dumpVariable($data)
    {
        return var_export($data, true);
    }
}
