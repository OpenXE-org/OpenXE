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

        $converted = mb_convert_encoding(
            $string,
            'UTF-8',
            'auto'
        );

        // Fallback
        if ($converted === false) {
            $converted = mb_convert_encoding(
                $string,
                'UTF-8',
                'iso-8859-1'
            );
        }

        return ($converted);
    }
}
