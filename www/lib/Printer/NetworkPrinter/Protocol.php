<?php
/**
 * Netzwerk-Druckprotokolle.
 * Ersatz fuer PHP 8.1 Enums — PHP 7.4 kompatibel.
 */
class Protocol
{
    const IPP = 'ipp';
    const RAW = 'raw';
    const ESCPOS = 'escpos';
    const LPR = 'lpr';

    /** @var array<string, int> Default-Ports pro Protokoll */
    const DEFAULT_PORTS = [
        self::IPP => 631,
        self::RAW => 9100,
        self::ESCPOS => 9100,
        self::LPR => 515,
    ];

    /**
     * @param string $protocol
     * @return bool
     */
    public static function isValid(string $protocol): bool
    {
        return in_array($protocol, [self::IPP, self::RAW, self::ESCPOS, self::LPR], true);
    }

    /**
     * @param string $protocol
     * @return int
     */
    public static function getDefaultPort(string $protocol): int
    {
        return self::DEFAULT_PORTS[$protocol] ?? 9100;
    }
}
