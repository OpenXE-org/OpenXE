<?php

namespace Xentral\Modules\TransferSmartyTemplate\TemplateHelper;

final class XmlHelper
{
    /**
     * @param string $string
     * @param string $charset
     *
     * @return string
     */
    public function escapeXml($string, $charset = 'UTF-8')
    {
        $string = $this->stripControlChars($string);

        return htmlspecialchars($string, ENT_XML1 | ENT_QUOTES, $charset, true);
    }

    /**
     * CDATA-Abschnitt erzeugen
     *
     * @param string $string
     *
     * @return string
     */
    public function createXmlCdataSection($string)
    {
        return sprintf('<![CDATA[%s]]>', (string)$string);
    }

    /**
     * Filtert alle Steuerzeichen aus; außer Zeilenumbrüche, Tabs und Leerzeichen
     *
     * @see https://www.ascii-code.com/
     *
     * @param string $string
     *
     * @return string
     */
    private function stripControlChars($string)
    {
        // Erlaubte Steuerzeichen
        // \x09 = Horintal tab (HT) = \t
        // \x0a = Line feed (LF) = \n
        // \x0d = Carriage return (CR) = \r
        $string = (string)preg_replace('/[\x00-\x08]/', '', (string)$string); // Steuerzeichen bis HT und LF
        $string = (string)preg_replace('/[\x0b\x0c]/', '', (string)$string); // Steuerzeichen zwischen LF und CR
        $string = (string)preg_replace('/[\x0e-\x1f]/', '', (string)$string); // Steuerzeichen ab CR
        $string = (string)preg_replace('/[\x7f]/', '', (string)$string); // DELETE

        return $string;
    }
}
