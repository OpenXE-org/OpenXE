<?php

namespace Xentral\Modules\SuperSearch\SearchEngine;

use Xentral\Components\Util\StringUtil;

final class SearchTermParser
{
    /**
     * @internal Operators in Boolean search mode: +, -, > <, ( ), ~, *, ", @distance
     *
     * @param string $searchTerm
     *
     * @return string
     */
    public function parse($searchTerm)
    {
        // Remove unused (not supported by us) search operators: *, > <, ( ), @distance
        $searchTerm = preg_replace('/[><()*@]+/', '', $searchTerm);

        $searchWords = preg_split('/([\s]+)/um', $searchTerm, -1, PREG_SPLIT_NO_EMPTY);
        if ($searchWords === false) {
            return '';
        }

        foreach ($searchWords as &$searchWord) {

            if (strlen($searchWord) < 3) {
                $searchWord = '';
                continue;
            }

            $hasLeadingTilde = StringUtil::startsWith($searchWord, '~');
            $searchWord = ltrim($searchWord, '~');

            $hasLeadingMinus = StringUtil::startsWith($searchWord, '-');
            $searchWord = trim($searchWord, '-'); // Trim both sides; InnoDB only supports leading minus signs

            $hasLeadingPlus = StringUtil::startsWith($searchWord, '+');
            $searchWord = trim($searchWord, '+'); // Trim both sides; InnoDB only supports leading plus signs

            $hasLeadingQuote = StringUtil::startsWith($searchWord, '"');
            $hasTrailingQuote = StringUtil::endsWith($searchWord, '"');
            $searchWord = trim($searchWord, '"');

            // Operatoren innerhalb eines Suchworts entfernen
            $searchWord = preg_replace('/[+\-~\"]+/', '', $searchWord);

            $searchWord = trim($searchWord);
            if (strlen($searchWord) < 3) {
                $searchWord = ''; // Zu kurze Suchwörter ignorieren
                continue;
            }

            // Beispiel: `"foobar"`
            // Kombination mit Operatoren möglich: `+"foobar"` oder `-"foobar"` oder `~"foobar"`
            if ($hasLeadingQuote && $hasTrailingQuote) {
                $searchWord = '"' . $searchWord . '"';
            }

            // Operatoren vorne anfügen
            if ($hasLeadingTilde) {
                $searchWord = '~' . $searchWord;
            }
            if ($hasLeadingMinus) {
                $searchWord = '-' . $searchWord;
            }
            if ($hasLeadingPlus) {
                $searchWord = '+' . $searchWord;
            }

            // Default: Wildcard hinten, nur wenn keine Anführungszeichen
            if (!$hasLeadingQuote && !$hasTrailingQuote) {
                $searchWord .= '*';
            }
        }
        unset($searchWord);

        return trim(implode(' ', $searchWords));
    }
}
