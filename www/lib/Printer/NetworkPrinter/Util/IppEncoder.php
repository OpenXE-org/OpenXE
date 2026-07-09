<?php

require_once __DIR__ . '/../Exception/PrinterProtocolException.php';
require_once __DIR__ . '/../Exception/PrinterConnectionException.php';

/**
 * IPP Binaerformat Encoder/Decoder nach RFC 8010/8011.
 *
 * Implementiert das Binaerformat zum Zusammenbau und Parsen von
 * IPP-Requests und -Responses ueber HTTP POST an Port 631.
 */
class IppEncoder
{
    // Value-tag constants (RFC 8010 Section 3.5)
    const TAG_OPERATION_ATTRS  = 0x01;
    const TAG_JOB_ATTRS        = 0x02;
    const TAG_END_ATTRS        = 0x03;
    const TAG_PRINTER_ATTRS    = 0x04;

    const TAG_INTEGER          = 0x21;
    const TAG_BOOLEAN          = 0x22;
    const TAG_ENUM             = 0x23;
    const TAG_TEXT_WITHOUT_LANG = 0x41;
    const TAG_NAME_WITHOUT_LANG = 0x42;
    const TAG_KEYWORD          = 0x44;
    const TAG_URI              = 0x45;
    const TAG_CHARSET          = 0x47;
    const TAG_NATURAL_LANG     = 0x48;
    const TAG_MIME_TYPE        = 0x49;

    // IPP operations
    const OP_PRINT_JOB            = 0x0002;
    const OP_GET_PRINTER_ATTRS    = 0x000B;

    // IPP status codes
    const STATUS_OK                       = 0x0000;
    const STATUS_OK_IGNORED_SUBSTITUTED   = 0x0001;

    // Finishings enum values
    const FINISHING_NONE   = 3;
    const FINISHING_STAPLE = 20;

    // Orientation enum values
    const ORIENTATION_PORTRAIT  = 3;
    const ORIENTATION_LANDSCAPE = 4;

    /**
     * Baut einen Print-Job IPP-Request.
     *
     * @param string $printerUri Vollstaendiger IPP-URI z.B. ipp://host:631/ipp/print
     * @param array  $options    Druck-Optionen (copies, duplex, color, media, tray, staple, orientation, job-name)
     *
     * @return string Binaerer IPP-Request-Header
     */
    public static function buildPrintJobRequest(string $printerUri, array $options = []): string
    {
        static $requestId = 0;
        $requestId++;

        // IPP/1.1 version + operation-id + request-id
        $body  = pack('CCnN', 0x01, 0x01, self::OP_PRINT_JOB, $requestId);

        // Operation attributes group
        $body .= pack('C', self::TAG_OPERATION_ATTRS);
        $body .= self::encodeAttribute(self::TAG_CHARSET,      'attributes-charset',          'utf-8');
        $body .= self::encodeAttribute(self::TAG_NATURAL_LANG, 'attributes-natural-language', 'en');
        $body .= self::encodeAttribute(self::TAG_URI,          'printer-uri',                 $printerUri);
        $body .= self::encodeAttribute(self::TAG_NAME_WITHOUT_LANG, 'requesting-user-name',   'OpenXE');

        $jobName = isset($options['job-name']) ? (string)$options['job-name'] : 'OpenXE-Print-Job';
        $body .= self::encodeAttribute(self::TAG_NAME_WITHOUT_LANG, 'job-name', $jobName);
        $body .= self::encodeAttribute(self::TAG_MIME_TYPE, 'document-format', 'application/pdf');

        // Job attributes group
        $body .= pack('C', self::TAG_JOB_ATTRS);

        // copies
        if (isset($options['copies']) && (int)$options['copies'] > 1) {
            $body .= self::encodeIntegerAttribute('copies', (int)$options['copies']);
        }

        // duplex -> sides
        if (!empty($options['duplex'])) {
            $sides = 'two-sided-long-edge';
            $body .= self::encodeAttribute(self::TAG_KEYWORD, 'sides', $sides);
        } else {
            $body .= self::encodeAttribute(self::TAG_KEYWORD, 'sides', 'one-sided');
        }

        // color -> print-color-mode
        if (isset($options['color'])) {
            $colorMode = $options['color'] ? 'color' : 'monochrome';
            $body .= self::encodeAttribute(self::TAG_KEYWORD, 'print-color-mode', $colorMode);
        }

        // media
        if (!empty($options['media'])) {
            $body .= self::encodeAttribute(self::TAG_KEYWORD, 'media', (string)$options['media']);
        }

        // tray -> media-source
        if (!empty($options['tray'])) {
            $body .= self::encodeAttribute(self::TAG_KEYWORD, 'media-source', (string)$options['tray']);
        }

        // staple -> finishings (enum)
        if (!empty($options['staple'])) {
            $body .= self::encodeEnumAttribute('finishings', self::FINISHING_STAPLE);
        }

        // orientation -> orientation-requested (enum)
        if (!empty($options['orientation'])) {
            $orientation = (strtolower((string)$options['orientation']) === 'landscape')
                ? self::ORIENTATION_LANDSCAPE
                : self::ORIENTATION_PORTRAIT;
            $body .= self::encodeEnumAttribute('orientation-requested', $orientation);
        }

        // End of attributes
        $body .= pack('C', self::TAG_END_ATTRS);

        return $body;
    }

    /**
     * Baut einen Get-Printer-Attributes IPP-Request.
     *
     * @param string $printerUri         Vollstaendiger IPP-URI
     * @param array  $requestedAttributes Liste der angeforderten Attributnamen (leer = alle)
     *
     * @return string Binaerer IPP-Request
     */
    public static function buildGetPrinterAttributesRequest(string $printerUri, array $requestedAttributes = []): string
    {
        static $requestId = 100;
        $requestId++;

        $body  = pack('CCnN', 0x01, 0x01, self::OP_GET_PRINTER_ATTRS, $requestId);

        $body .= pack('C', self::TAG_OPERATION_ATTRS);
        $body .= self::encodeAttribute(self::TAG_CHARSET,      'attributes-charset',          'utf-8');
        $body .= self::encodeAttribute(self::TAG_NATURAL_LANG, 'attributes-natural-language', 'en');
        $body .= self::encodeAttribute(self::TAG_URI,          'printer-uri',                 $printerUri);
        $body .= self::encodeAttribute(self::TAG_NAME_WITHOUT_LANG, 'requesting-user-name',   'OpenXE');

        // requested-attributes (multi-value: first has full name, subsequent have name-length=0)
        if (!empty($requestedAttributes)) {
            $first = true;
            foreach ($requestedAttributes as $attrName) {
                if ($first) {
                    $body .= self::encodeAttribute(self::TAG_KEYWORD, 'requested-attributes', (string)$attrName);
                    $first = false;
                } else {
                    // Additional values: value-tag + name-length=0 + value
                    $val = (string)$attrName;
                    if (strlen($val) > 65535) {
                        $val = substr($val, 0, 65535);
                    }
                    $body .= pack('C', self::TAG_KEYWORD);
                    $body .= pack('n', 0);                   // name-length = 0 (additional value)
                    $body .= pack('n', strlen($val)) . $val;
                }
            }
        }

        $body .= pack('C', self::TAG_END_ATTRS);

        return $body;
    }

    /**
     * Sendet einen IPP-Request per HTTP POST.
     *
     * @param string $host     Druckerhostname oder IP-Adresse
     * @param int    $port     TCP-Port (normalerweise 631)
     * @param string $path     HTTP-Pfad (z.B. '/ipp/print')
     * @param string $ippData  Roher binaerer IPP-Datenstrom
     * @param string $username Optionaler Benutzername fuer HTTP-Auth
     * @param string $password Optionales Passwort fuer HTTP-Auth
     * @param int    $timeout  Request-Timeout in Sekunden
     *
     * @return string Roher HTTP-Response-Body
     *
     * @throws PrinterConnectionException Bei Curl-Fehler
     * @throws PrinterProtocolException   Bei nicht-200 HTTP-Status
     */
    public static function sendRequest(
        string $host,
        int $port,
        string $path,
        string $ippData,
        string $username = '',
        string $password = '',
        int $timeout = 30
    ): string {
        $url = sprintf('http://%s:%d%s', $host, $port, $path);

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $ippData);
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/ipp']);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);

        if ($username !== '') {
            curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC | CURLAUTH_DIGEST);
            curl_setopt($ch, CURLOPT_USERPWD, $username . ':' . $password);
        }

        $response = curl_exec($ch);
        $httpCode = (int)curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $curlError = curl_error($ch);
        $curlErrno = curl_errno($ch);
        curl_close($ch);

        if ($response === false) {
            throw new PrinterConnectionException(
                sprintf('IPP curl-Fehler fuer %s: %s (%d)', $url, $curlError, $curlErrno)
            );
        }

        if ($httpCode !== 200) {
            throw new PrinterProtocolException(
                sprintf('IPP HTTP-Fehler: Status %d fuer %s', $httpCode, $url)
            );
        }

        return $response;
    }

    /**
     * Parst eine IPP-Response und gibt Status + Attribute zurueck.
     *
     * @param string $response Rohe binaere IPP-Response
     *
     * @return array ['status_code' => int, 'status_ok' => bool, 'attributes' => array]
     */
    public static function parseResponse(string $response): array
    {
        if (strlen($response) < 8) {
            return ['status_code' => -1, 'status_ok' => false, 'attributes' => []];
        }

        $offset = 0;

        // Version: 2 bytes
        $versionMajor = ord($response[$offset]);
        $versionMinor = ord($response[$offset + 1]);
        $offset += 2;

        // Status-code: 2 bytes big-endian
        $unpacked   = unpack('n', substr($response, $offset, 2));
        $statusCode = $unpacked[1];
        $offset    += 2;

        // Request-id: 4 bytes (skip)
        $offset += 4;

        $attributes  = [];
        $currentGroup = '';

        while ($offset < strlen($response)) {
            $tag = ord($response[$offset]);
            $offset++;

            // Group delimiter tags
            if ($tag === self::TAG_OPERATION_ATTRS) {
                $currentGroup = 'operation';
                continue;
            }
            if ($tag === self::TAG_JOB_ATTRS) {
                $currentGroup = 'job';
                continue;
            }
            if ($tag === self::TAG_PRINTER_ATTRS) {
                $currentGroup = 'printer';
                continue;
            }
            if ($tag === self::TAG_END_ATTRS) {
                break;
            }

            // Need at least 2 bytes for name-length
            if ($offset + 2 > strlen($response)) {
                break;
            }

            $nameLenUnpacked = unpack('n', substr($response, $offset, 2));
            $nameLen         = $nameLenUnpacked[1];
            $offset         += 2;

            $attrName = '';
            if ($nameLen > 0) {
                if ($offset + $nameLen > strlen($response)) {
                    break;
                }
                $attrName = substr($response, $offset, $nameLen);
                $offset  += $nameLen;
            }

            // Value-length: 2 bytes
            if ($offset + 2 > strlen($response)) {
                break;
            }
            $valLenUnpacked = unpack('n', substr($response, $offset, 2));
            $valLen         = $valLenUnpacked[1];
            $offset        += 2;

            $rawValue = '';
            if ($valLen > 0) {
                if ($offset + $valLen > strlen($response)) {
                    break;
                }
                $rawValue = substr($response, $offset, $valLen);
                $offset  += $valLen;
            }

            $decodedValue = self::decodeValue($tag, $rawValue);

            if ($attrName !== '') {
                // First occurrence of this attribute name
                $key = $currentGroup . '.' . $attrName;
                if (isset($attributes[$key])) {
                    // Already exists -> convert to array
                    if (!is_array($attributes[$key])) {
                        $attributes[$key] = [$attributes[$key]];
                    }
                    $attributes[$key][] = $decodedValue;
                } else {
                    $attributes[$key] = $decodedValue;
                }
                // Also store without group prefix for convenience
                if (!isset($attributes[$attrName])) {
                    $attributes[$attrName] = $decodedValue;
                } elseif (!is_array($attributes[$attrName])) {
                    $attributes[$attrName] = [$attributes[$attrName], $decodedValue];
                } else {
                    $attributes[$attrName][] = $decodedValue;
                }
            } else {
                // Additional value for previous attribute (name-length=0)
                // Append to the last known attribute
                end($attributes);
                $lastKey = key($attributes);
                if ($lastKey !== null) {
                    if (!is_array($attributes[$lastKey])) {
                        $attributes[$lastKey] = [$attributes[$lastKey]];
                    }
                    $attributes[$lastKey][] = $decodedValue;
                }
            }
        }

        $statusOk = ($statusCode === self::STATUS_OK || $statusCode === self::STATUS_OK_IGNORED_SUBSTITUTED);

        return [
            'status_code' => $statusCode,
            'status_ok'   => $statusOk,
            'attributes'  => $attributes,
        ];
    }

    // -------------------------------------------------------------------------
    // Private helpers
    // -------------------------------------------------------------------------

    /**
     * Encodes a single IPP attribute (string value) as binary.
     *
     * Format: value-tag (1B) + name-length (2B BE) + name + value-length (2B BE) + value
     *
     * @param int    $tag   Value-tag byte
     * @param string $name  Attribute name
     * @param string $value Attribute value (string)
     *
     * @return string Binary encoded attribute
     */
    private static function encodeAttribute(int $tag, string $name, string $value): string
    {
        // IPP-Spec: Attribute-Werte duerfen max 1023 Bytes lang sein (name-without-language)
        // pack('n', ...) erlaubt max 65535 — Overflow verhindern
        if (strlen($value) > 65535) {
            $value = substr($value, 0, 65535);
        }
        if (strlen($name) > 65535) {
            $name = substr($name, 0, 65535);
        }

        return chr($tag)
            . pack('n', strlen($name))
            . $name
            . pack('n', strlen($value))
            . $value;
    }

    /**
     * Encodes an IPP integer attribute (4 bytes big-endian).
     *
     * @param string $name  Attribute name
     * @param int    $value Integer value
     *
     * @return string Binary encoded attribute
     */
    private static function encodeIntegerAttribute(string $name, int $value): string
    {
        return pack('C', self::TAG_INTEGER)
            . pack('n', strlen($name)) . $name
            . pack('n', 4)
            . pack('N', $value);
    }

    /**
     * Encodes an IPP enum attribute (4 bytes big-endian, same wire format as integer).
     *
     * @param string $name  Attribute name
     * @param int    $value Enum value
     *
     * @return string Binary encoded attribute
     */
    private static function encodeEnumAttribute(string $name, int $value): string
    {
        return pack('C', self::TAG_ENUM)
            . pack('n', strlen($name)) . $name
            . pack('n', 4)
            . pack('N', $value);
    }

    /**
     * Decodes a raw binary value based on its value-tag.
     *
     * @param int    $tag      Value-tag byte
     * @param string $rawValue Raw binary value
     *
     * @return mixed Decoded PHP value (int, bool, or string)
     */
    private static function decodeValue(int $tag, string $rawValue)
    {
        switch ($tag) {
            case self::TAG_INTEGER:
                if (strlen($rawValue) < 4) {
                    return 0;
                }
                $u = unpack('N', $rawValue);
                $val = (int)$u[1];
                // Handle signed 32-bit
                if ($val >= 0x80000000) {
                    $val -= 0x100000000;
                }
                return $val;

            case self::TAG_ENUM:
                if (strlen($rawValue) < 4) {
                    return 0;
                }
                $u = unpack('N', $rawValue);
                return (int)$u[1];

            case self::TAG_BOOLEAN:
                return strlen($rawValue) > 0 && ord($rawValue[0]) !== 0x00;

            case self::TAG_TEXT_WITHOUT_LANG:
            case self::TAG_NAME_WITHOUT_LANG:
            case self::TAG_KEYWORD:
            case self::TAG_URI:
            case self::TAG_CHARSET:
            case self::TAG_NATURAL_LANG:
            case self::TAG_MIME_TYPE:
                return $rawValue;

            default:
                return $rawValue;
        }
    }
}
