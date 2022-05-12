<?php

namespace Xentral\Modules\HocrParser\Finder;

use Xentral\Modules\HocrParser\Exception\InvalidArgumentException;

class PatternMatcher
{
    public const PATTERN_DOCUMENT_NUMBER = 'documentnumber';
    public const PATTERN_MONEY = 'money';
    public const PATTERN_DATE = 'date';
    public const PATTERN_DEFAULT = 'default';

    /** @var array $validPatterns */
    private static $validPatterns = [
        self::PATTERN_DOCUMENT_NUMBER,
        self::PATTERN_MONEY,
        self::PATTERN_DATE,
        self::PATTERN_DEFAULT,
    ];

    /** @var string $pattern */
    private $pattern;

    /**
     * @param string $pattern
     */
    public function __construct($pattern = self::PATTERN_DEFAULT)
    {
        if (!in_array($pattern, self::$validPatterns, true)) {
            throw new InvalidArgumentException(sprintf('Pattern "%s" is not allowed.', $pattern));
        }

        $this->pattern = $pattern;
    }

    /**
     * @param string $value
     *
     * @return bool
     */
    public function Match($value)
    {
        $value = trim((string)$value);

        if (empty($value)) {
            return false;
        }

        switch ($this->pattern) {
            case self::PATTERN_DATE:
                return $this->IsDateLikeValue($value);
                break;
            case self::PATTERN_MONEY:
                return $this->IsMoneyLikeValue($value);
                break;
            case self::PATTERN_DOCUMENT_NUMBER:
                return $this->IsDocumentNumberLikeValue($value);
                break;
            case self::PATTERN_DEFAULT:
                return $this->IsCandidateValue($value);
                break;
        }

        return false;
    }

    /**
     * @param string $value
     *
     * @return bool
     */
    private function IsDateLikeValue($value)
    {
        return (bool)preg_match('/\d{1,2}\.\d{1,2}\.\d{2,4}/', $value);
    }

    /**
     * @param string $value
     *
     * @return bool
     */
    private function IsMoneyLikeValue($value)
    {
        // Mit Tausendertrenner: z.B.: 11.111,11 oder 11,111.11
        $withThousands = (bool)preg_match('/\d+[\.,]\d{3}[\.,]{1}\d{2}$/', $value);
        if ($withThousands) {
            return true;
        }

        // Ohne Tausendertrenner: z.B.: 1111111,11 oder 1111111.11
        return (bool)preg_match('/^\d+[\.,]{1}\d{2}$/', $value);
    }

    /**
     * @param $value
     *
     * @return bool
     */
    private function IsDocumentNumberLikeValue($value)
    {
        // Nur Grossbuchstaben, Zahlen, Minus und Unterstrich sind erlaubt
        $containsInvalidChars = (bool)preg_match('/[^A-Z0-9\-_]+/', $value);
        if ($containsInvalidChars) {
            return false;
        }

        return (bool)preg_match('/\d{4,}/', $value);
    }

    /**
     * @param string $value
     *
     * @return bool
     */
    private function IsCandidateValue($value)
    {
        return $this->IsDateLikeValue($value)
            || $this->IsDocumentNumberLikeValue($value)
            || $this->IsMoneyLikeValue($value);
    }
}

