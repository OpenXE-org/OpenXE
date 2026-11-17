<?php
/**
 * Druckertypen — korrespondiert mit drucker.art in der Datenbank.
 */
class PrinterType
{
    const DOCUMENT = 'document';
    const LABEL = 'label';
    const RECEIPT = 'receipt';

    /** @var array<string, string> Empfohlenes Protokoll pro Typ */
    const DEFAULT_PROTOCOLS = [
        self::DOCUMENT => Protocol::IPP,
        self::LABEL => Protocol::RAW,
        self::RECEIPT => Protocol::ESCPOS,
    ];

    /** @var array<int, string> Mapping von drucker.art (DB) auf PrinterType */
    const FROM_DB_ART = [
        0 => self::DOCUMENT,
        2 => self::LABEL,
    ];

    /**
     * @param string $type
     * @return string
     */
    public static function getDefaultProtocol(string $type): string
    {
        return self::DEFAULT_PROTOCOLS[$type] ?? Protocol::RAW;
    }
}
