<?php

namespace Xentral\Components\Util;

use Exception;
use Xentral\Components\Util\Exception\InsecureRandomStringException;
use Xentral\Components\Util\Exception\InvalidArgumentException;
use Xentral\Components\Util\Exception\StringUtilException;

final class StringUtil
{
    /** @var array $translate */
    private static $transliteration = [
        '/m\x{00B2}/u' => 'qm',
        '/m\x{00B3}/u' => 'm3',
        '/ä/'          => 'ae',
        '/ö/'          => 'oe',
        '/ü/'          => 'ue',
        '/Ä/'          => 'Ae',
        '/Ö/'          => 'Oe',
        '/Ü/'          => 'Ue',
        '/\x{00DF}/u'  => 'ss',     //ß
        '/\x{20AC}/u'  => 'EURO',   //€
        '/\x{0026}/u'  => 'und',    //&
    ];

    /**
     * Returns true if string starts with needle.
     *
     * @example startsWith('Apple', 'A') -> true
     *
     * @param string $haystack
     * @param string $needle
     *
     * @return bool
     */
    public static function startsWith($haystack, $needle)
    {
        self::ensureString($haystack);
        self::ensureString($needle);

        return $needle === '' || strpos($haystack, $needle) === 0;
    }

    /**
     * Returns true if string ends with needle.
     *
     * @example endsWith('Apple', 'e') -> true
     *
     * @param string $haystack
     * @param string $needle
     *
     * @return bool
     */
    public static function endsWith($haystack, $needle)
    {
        self::ensureString($haystack);
        self::ensureString($needle);

        return $needle === substr($haystack, strlen($haystack) - strlen($needle), strlen($needle));
    }

    /**
     * Returns true if needle appears anywhere in string.
     *
     * @example contains('Apple', 'pp') -> true
     *
     * @param string $haystack
     * @param string $needle
     *
     * @return bool
     */
    public static function contains($haystack, $needle)
    {
        self::ensureString($haystack);
        self::ensureString($needle);

        return $needle === '' || strpos($haystack, $needle) !== false;
    }

    /**
     * Pads a string up to a specific length.
     *
     * @example padLeft('a', 4, '-') -> '---a'
     * @example padLeft('a', 4, '-+') -> '-+-a'
     *
     * @param string $string
     * @param int    $length
     * @param string $pad
     *
     * @return string
     */
    public static function padLeft($string, $length, $pad = ' ')
    {
        self::ensureString($string);
        self::ensureInteger($length);
        self::ensureString($pad);

        return self::generatePadding($length - mb_strlen($string), $pad) . $string;
    }

    /**
     * Pads a string up to a specific length.
     *
     * @example padRight('a', 4, '-') -> 'a---'
     * @example padRight('a', 4, '-+') -> 'a-+-'
     *
     * @param string $string
     * @param int    $length
     * @param string $pad
     *
     * @return string
     */
    public static function padRight($string, $length, $pad = ' ')
    {
        self::ensureString($string);
        self::ensureInteger($length);
        self::ensureString($pad);

        return $string . self::generatePadding($length - mb_strlen($string), $pad);
    }

    /**
     * Generates a filesystem-friendly (win, mac, linux) file name from a given string
     *
     * @param string $value
     *
     * @return string file name
     */
    public static function toFilename($value)
    {
        self::ensureString($value);

        $sanitize = [
            '/\s+/'                  => '_',    //space
            '/:\\\/'                 => '-',    //drive name
            '/\\\\\\\/'              => '-',    //unc path
            '/[\\/\\\<>:|]/'         => '-',    //seperators and reserved
            '/"/'                    => '\'',
            '/[\*\?&]+/'             => '',     //other special chars
            '/[\x{0000}-\x{001F}]/u' => '',
            '/[\x{0080}-\x{FFFF}]/u' => '',     //only ascii
        ];

        $filename = preg_replace(array_keys(self::$transliteration), array_values(self::$transliteration), $value);
        $filename = preg_replace(array_keys($sanitize), array_values($sanitize), $filename);
        $filename = trim($filename, '.');

        if ($filename === '') {
            throw new StringUtilException(
                sprintf('The specified string "%s" cannot be converted to a valid file name', $value)
            );
        }

        return $filename;
    }

    /** Deletes all non-ASCII non-printable characters from a string
     *
     * Only characters between 32 to 127 (inclusive) remain.
     *
     * @param string $value
     *
     * @return string empty if whole string was non-ASCII
     */
    public static function toAscii($value)
    {
        self::ensureString($value);

        $value = preg_replace(['/[\x{0000}-\x{001F}]/u', '/[\x{0080}-\x{FFFF}]/u'], ['', ''], $value);

        return $value;
    }

    /**
     * Transforms a String to Title case.
     *
     * Use delimiter='' to only capitalize the first character.
     *
     * @example "foo Bar BaZ" => "Foo Bar Baz"
     *
     * @param string $value
     * @param string $delimiters all characters that seperate words
     *
     * @return string
     */
    public static function toTitleCase($value, $delimiters = '\s\v')
    {
        self::ensureString($value);
        self::ensureString($delimiters);

        $inWords = [$value];
        $outWords = [];

        if ($delimiters !== '') {
            $regex = sprintf('/[%s]/', $delimiters);
            $inWords = preg_split($regex, $value);
        }

        foreach ($inWords as $word) {
            if ($word !== '') {
                $outWords[] = mb_strtoupper(mb_substr($word, 0, 1))
                    . mb_strtolower(mb_substr($word, 1, mb_strlen($word)));
            }
        }

        return implode(' ', $outWords);
    }

    /**
     * Format a nubmer of Bytes to KB, MB etc.
     *
     * @example formatBytes(1024, ' ') -> '1,0 KB'
     *
     * @param integer $bytes
     * @param string  $separator
     *
     * @return string
     */
    public static function formatBytes($bytes, $separator = '')
    {
        self::ensureInteger($bytes);
        $bytes = (int)$bytes;
        if ($bytes <= 0) {
            return sprintf('0%sBytes', $separator);
        }

        $units = ['Bytes', 'KB', 'MB', 'GB', 'TB'];
        $exponent = (int)floor(log($bytes) / log(1024));
        if ($exponent > count($units) - 1) {
            $exponent = count($units) - 1;
        }
        $size = $bytes / pow(1024, $exponent);

        return sprintf('%s%s%s',
            number_format($size, 1, ',', '.'),
            $separator,
            $units[$exponent]
        );
    }

    /**
     * Parse PHP byte size Values to number of bytes.
     *
     * Shorthand Format is used in php_ini (e.g. post_max_size).
     *
     * @example parsePhpShorthandByte('1M') -> 1048576
     *
     * @param string $phpSize php shorthand byte format
     *
     * @return int
     */
    public static function parsePhpByteSize($phpSize)
    {
        self::ensureString($phpSize);

        preg_match('/^(\d+)([kKmMgG]?)$/', $phpSize, $matches);
        if (empty($matches)) {
            throw new InvalidArgumentException(sprintf('"%s" is not a PHP size format.', $phpSize));
        }

        $sizes = ['' => 0, 'K' => 1, 'M' => 2, 'G' => 3];
        $number = (int)$matches[1];
        $unit = strtoupper($matches[2]);
        $exponent = $sizes[$unit];
        $bytes = $number * pow(1024, $exponent);

        return (int)$bytes;
    }

    /**
     * Generates a random String with specified length
     *
     * @param int  $length
     * @param bool $secure true=create cryptographically secure string
     *
     * @throws InsecureRandomStringException
     *
     * @return string
     */
    public static function random($length, $secure = false)
    {
        $random = self::randomByOpenSsl($length, $secure);
        if ($random === false) {
            $random = self::randomByRandomBytes($length);
        }
        if ($random !== false) {
            return $random;
        }
        if ($random === false && $secure === true) {
            throw new InsecureRandomStringException('Could not generate cryptographically secure random string.');
        }

        return self::randomByMd5($length);
    }

    /**
     * @param int  $length
     * @param bool $secure
     *
     * @return string|false
     */
    private static function randomByOpenSsl($length, $secure = false)
    {
        if (function_exists('openssl_random_pseudo_bytes')) {
            $bytes = openssl_random_pseudo_bytes(ceil($length / 2), $cryptoStrong);
            if ($bytes === false || ($secure === true && $cryptoStrong === false)) {
                return false;
            }

            return substr(bin2hex($bytes), 0, $length);
        }

        return false;
    }

    /**
     * random_bytes steht erst ab PHP7 zur Verfügung
     *
     * @param int $length
     *
     * @return string|false
     */
    private static function randomByRandomBytes($length)
    {
        $random = false;
        if (function_exists('random_bytes')) {
            try {
                $bytes = random_bytes(ceil($length / 2));
                $random = substr(bin2hex($bytes), 0, $length);
            } catch (Exception $e) {
            }
        }

        return $random;
    }

    /**
     * @param int $length
     *
     * @return string|false
     */
    private static function randomByMd5($length)
    {
        $random = '';
        for ($i = 0; $i * 32 <= $length; $i++) {
            $val = mt_rand();
            $random .= md5((string)$val);
        }

        $random = substr($random, 0, $length);

        return $random;
    }

    /**
     * @param int    $lengthDelta
     * @param string $sequence
     *
     * @return string
     */
    private static function generatePadding($lengthDelta, $sequence)
    {
        if ($sequence === '' || $lengthDelta < 1) {
            return '';
        }

        $padding = '';
        for ($i = 0; $i < $lengthDelta; $i++) {
            $delta = $lengthDelta - mb_strlen($padding);
            $subSequence = mb_substr($sequence, 0, $delta);
            $padding .= $subSequence;
        }

        return $padding;
    }

    /**
     * @param mixed $value
     *
     * @throws InvalidArgumentException
     *
     * @return void
     */
    private static function ensureString($value)
    {
        $type = gettype($value);
        if ($type !== 'string') {
            throw new InvalidArgumentException(sprintf('Wrong type "%s". Only "string" is allowed.', $type));
        }
    }

    /**
     * @param mixed $value
     *
     * @throws InvalidArgumentException
     *
     * @return void
     */
    private static function ensureInteger($value)
    {
        $type = gettype($value);
        if ($type !== 'integer') {
            throw new InvalidArgumentException(sprintf('Wrong type "%s". Only "integer" allowed.', $type));
        }
    }
}
