<?php

namespace Xentral\Modules\TransferSmartyTemplate\Smarty;

use Smarty_Internal_Template;
use Xentral\Modules\TransferSmartyTemplate\Exception\TransferTemplateUserException;
use Xentral\Modules\TransferSmartyTemplate\TemplateHelper\CommonHelper;
use Xentral\Modules\TransferSmartyTemplate\TemplateHelper\CsvHelper;
use Xentral\Modules\TransferSmartyTemplate\TemplateHelper\HtmlUrlHelper;
use Xentral\Modules\TransferSmartyTemplate\TemplateHelper\XmlHelper;

final class SmartyTemplateHelper
{
    /** @var XmlHelper $xmlHelper */
    private $xmlHelper;

    /** @var CsvHelper $csvHelper */
    private $csvHelper;

    /** @var HtmlUrlHelper $htmlHelper */
    private $htmlHelper;

    /** @var CommonHelper $commonHelper */
    private $commonHelper;

    /**
     */
    public function __construct()
    {
        $this->xmlHelper = new XmlHelper();
        $this->csvHelper = new CsvHelper();
        $this->htmlHelper = new HtmlUrlHelper();
        $this->commonHelper = new CommonHelper();
    }

    /**
     * @example {escapeXml}{$evilString}{/escapeXml}
     * @example {escapeXml charset="UTF-32"}{$evilString}{/escapeXml}
     *
     * @param array                    $params
     * @param mixed                    $content
     * @param Smarty_Internal_Template $template
     * @param bool                     $repeat
     *
     * @return string
     */
    public function compileBlockEscapeXml($params, $content, $template, &$repeat)
    {
        // Only output on closing tag @see https://www.smarty.net/docs/en/plugins.block.functions.tpl
        if ($repeat === true) {
            return null;
        }

        $charset = isset($params['charset']) && !empty($params['charset']) ? (string)$params['charset'] : 'UTF-8';

        return $this->xmlHelper->escapeXml($content, $charset);
    }

    /**
     * @example {escapeXml}{$evilString}{/escapeXml}
     * @example {escapeXml charset="UTF-32"}{$evilString}{/escapeXml}
     *
     * @param array                    $params
     * @param mixed                    $content
     * @param Smarty_Internal_Template $template
     * @param bool                     $repeat
     *
     * @throws TransferTemplateUserException
     */
    public function compileBlockError($params, $content, $template, &$repeat)
    {
        if ($repeat === true) {
            return null;
        }

        throw new TransferTemplateUserException($content);
    }

    /**
     * @example `{cdata}Long text data{/cdata}` => Ausgabe `<![CDATA[Long text data]]>`
     *
     * @param array                    $params
     * @param mixed                    $content
     * @param Smarty_Internal_Template $template
     * @param bool                     $repeat
     *
     * @return string
     */
    public function compileBlockCdata($params, $content, $template, &$repeat)
    {
        // Only output on closing tag @see https://www.smarty.net/docs/en/plugins.block.functions.tpl
        if ($repeat === true) {
            return null;
        }

        return $this->xmlHelper->createXmlCdataSection($content);
    }

    /**
     * @example {$url|urlEncode}
     *
     * @param mixed $content
     *
     * @return string
     */
    public function compileModifierEncodeUrl($content)
    {
        return $this->htmlHelper->encodeUrl($content);
    }

    /**
     * @example {$url|urlDecode}
     *
     * @param mixed $content
     *
     * @return string
     */
    public function compileModifierDecodeUrl($content)
    {
        return $this->htmlHelper->decodeUrl($content);
    }

    /**
     * @example `{"Inhalt"|cdata}` => Ausgabe `<![CDATA[Inhalt]]>`
     *
     * @param string $content
     *
     * @return string
     */
    public function compileModifierCdata($content)
    {
        return $this->xmlHelper->createXmlCdataSection($content);
    }

    /**
     * @param string $content
     */
    public function compileModifierError($content)
    {
        throw new TransferTemplateUserException($content);
    }

    /**
     * @example {$value|escapeXml}
     *
     * @param string $content
     *
     * @return string
     */
    public function compileModifierEscapeXml($content)
    {
        return $this->xmlHelper->escapeXml($content);
    }

    /**
     * @example {$value|quoteCsv}
     * @example {$value|quoteCsv:"'"}
     *
     *
     * @param string $content
     * @param string $quoteChar
     *
     * @return string
     */
    public function compileModifierQuoteCsv($content, $quoteChar = '"')
    {
        if (empty($quoteChar)) {
            $quoteChar = '"';
        }

        return $this->csvHelper->quoteCsv($content, (string)$quoteChar);
    }

    /**
     * @example {$value|htmlEntitiesDecode}
     *
     * @param string $content
     *
     * @return string
     */
    public function compileModifierDecodeHtmlEntities($content)
    {
        return $this->htmlHelper->decodeHtmlEntities($content);
    }

    /**
     * @example {$value|htmlSpecialCharsDecode}
     *
     * @param string $content
     *
     * @return string
     */
    public function compileModifierDecodeHtmlSpecialChars($content)
    {
        return $this->htmlHelper->decodeHtmlSpecialChars($content);
    }

    /**
     * Converts HTML-BR-Tags to line breaks
     *
     * @example {$value|br2nl}
     *
     * @param string $content
     *
     * @return string
     */
    public function compileModifierBr2Nl($content)
    {
        return $this->htmlHelper->convertBr2LineBreak($content);
    }

    /**
     * @example {$var|replaceLineBreaks}
     * @example {$var|replaceLineBreaks:"ZEILENUMBRUCH"}
     *
     * @param string $string
     * @param string $replaceChar
     *
     * @return string
     */
    public function compileModifierReplaceLineBreaks($string, $replaceChar = ' ')
    {
        return $this->commonHelper->replaceLineBreaks($string, $replaceChar);
    }

    /**
     * Dumps a template variable
     *
     * @example {$var|dump}
     *
     * @param mixed $data
     *
     * @return string
     */
    public function compileModifierDumpVariable($data)
    {
        return $this->commonHelper->dumpVariable($data);
    }
}
