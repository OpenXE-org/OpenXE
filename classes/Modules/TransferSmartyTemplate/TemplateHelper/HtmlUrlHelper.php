<?php

namespace Xentral\Modules\TransferSmartyTemplate\TemplateHelper;

final class HtmlUrlHelper
{
    /**
     * @param string $string
     * @param string $charset
     *
     * @return string
     */
    public function decodeHtmlEntities($string, $charset = 'UTF-8')
    {
        // ENT_HTML5 wird benötigt für Umwandlung von &apos;
        return html_entity_decode($string, ENT_QUOTES | ENT_HTML5, $charset);
    }

    /**
     * @param string $string
     *
     * @return string
     */
    public function decodeHtmlSpecialChars($string)
    {
        // ENT_HTML5 wird benötigt für Umwandlung von &apos;
        return htmlspecialchars_decode($string, ENT_QUOTES | ENT_HTML5);
    }

    /**
     * @param string $string
     *
     * @return string
     */
    public function encodeUrl($string)
    {
        $string = $this->stripUrlControlChars($string);

        return rawurlencode($string);
    }

    /**
     * @param string $string
     *
     * @return string
     */
    public function decodeUrl($string)
    {
        $string = $this->stripUrlControlChars($string);

        return rawurldecode($string);
    }

    /**
     * @param string $string
     * @param bool   $lineFeed
     * @param bool   $carriageReturn
     *
     * @return string
     */
    public function convertBr2LineBreak($string, $lineFeed = true, $carriageReturn = true)
    {
        $breakChars = '';
        if ((bool)$carriageReturn === true) {
            $breakChars .= "\r";
        }
        if ((bool)$lineFeed === true) {
            $breakChars .= "\n";
        }

        $string = str_replace(['<br>', '<br/>', '<br />'], $breakChars, $string);

        return $string;
    }

    /**
     * Filter alle Steuerzeichen aus; auch Zeilenumbrüche
     *
     * @see https://www.ascii-code.com/l
     *
     * @param string $string
     *
     * @return string
     */
    private function stripUrlControlChars($string)
    {
        return (string)preg_replace('/[\x00-\x1f\x7f]/', '', (string)$string);
    }
}
