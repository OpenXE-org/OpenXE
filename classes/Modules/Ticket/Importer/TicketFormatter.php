<?php

declare(strict_types=1);

namespace Xentral\Modules\Ticket\Importer;

class TicketFormatter
{
    /**
     * @param string $rawEmail
     *
     * @return string
     */
    public function formatEmail(string $rawEmail): string
    {
    }

    /**
     * @param string $string
     *
     * @return string
     */
    public function encodeToUtf8(string $string): string
    {
        $encoding = mb_detect_encoding($string, 'UTF-8, ISO-8859-1, ISO-8859-15', true);

        return mb_convert_encoding(
            $string,
            'UTF-8',
            $encoding
        );
    }
}
