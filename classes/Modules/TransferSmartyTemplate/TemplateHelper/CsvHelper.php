<?php

namespace Xentral\Modules\TransferSmartyTemplate\TemplateHelper;

use Xentral\Modules\TransferSmartyTemplate\Exception\InvalidArgumentException;

final class CsvHelper
{
    /**
     * Quotes and escapes an CSV value
     *
     * @param string $string
     * @param string $enclosure
     *
     * @throws InvalidArgumentException
     *
     * @return string
     */
    public function quoteCsv($string, $enclosure = '"')
    {
        if (!is_string($enclosure)) {
            throw new InvalidArgumentException('Invalid argument. Enclosure need to be type string.');
        }
        if (strlen($enclosure) !== 1) {
            throw new InvalidArgumentException('Invalid argument. Enclosure can only be a single character.');
        }

        $escapedString = (string)str_replace($enclosure, $enclosure . $enclosure, $string);

        return $enclosure . $escapedString . $enclosure;
    }
}
