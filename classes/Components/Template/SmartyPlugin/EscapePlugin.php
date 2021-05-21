<?php

namespace Xentral\Components\Template\SmartyPlugin;

use Smarty_Internal_Template;
use Xentral\Components\Template\Exception\TemplateException;

final class EscapePlugin
{
    /** @var string $charset */
    private $charset = 'UTF-8';

    /**
     * @example {escape format='html'}{$evilString}{/escape}
     *
     * Default format is 'html'
     *
     * @param array                    $params
     * @param mixed                    $content
     * @param Smarty_Internal_Template $template
     * @param bool                     $repeat
     *
     * @throws TemplateException On missing params
     *
     * @return string|null
     */
    public function compileEscapeBlock($params, $content, $template, &$repeat)
    {
        // Only output on closing tag @see https://www.smarty.net/docs/en/plugins.block.functions.tpl
        if ($repeat === true) {
            return null;
        }

        $format = isset($params['format']) ? $params['format'] : 'html';

        switch ($format) {
            case 'none':
            case 'null':
                return $content;
                break;

            case 'entitites':
                return $this->escapeHtmlEntities($content);
                break;

            case 'quotes':
                return $this->escapeQuotes($content);
                break;

            case 'url':
                return $this->escapeUrl($content);
                break;

            case 'html':
            default:
                return $this->escapeHtml($content);
                break;
        }
    }

    /**
     * @example {escapeHtml}{$evilString}{/escapeHtml}
     *
     * @param array                    $params
     * @param mixed                    $content
     * @param Smarty_Internal_Template $template
     * @param bool                     $repeat
     *
     * @return string
     */
    public function compileEscapeHtmlBlock($params, $content, $template, &$repeat)
    {
        // Only output on closing tag @see https://www.smarty.net/docs/en/plugins.block.functions.tpl
        if ($repeat === true) {
            return null;
        }

        return $this->escapeHtml($content);
    }

    /**
     * @example {$evilString|escape:'html'}
     *
     * @param string $content
     * @param string $format
     *
     * @return string
     */
    public function compileEscapeModifier($content, $format = 'html')
    {
        switch ($format) {
            case 'none':
            case 'null':
                return $content;
                break;

            case 'entitites':
                return $this->escapeHtmlEntities($content);
                break;

            case 'quotes':
                return $this->escapeQuotes($content);
                break;

            case 'url':
                return $this->escapeUrl($content);
                break;

            case 'html':
            default:
                return $this->escapeHtml($content);
                break;
        }
    }

    /**
     * @example {$string|escapeEntities}
     *
     * @param string $content
     *
     * @return string
     */
    public function compileEscapeEntitiesModifier($content)
    {
        return $this->escapeHtmlEntities($content);
    }
    
    /**
     * Escapes unescaped single quotes
     *
     * @example {$quotedString|escapeQuotes}
     *
     * @param mixed $content
     *
     * @return string
     */
    public function compileEscapeQuotesModifier($content)
    {
        return $this->escapeQuotes($content);
    }

    /**
     * @example {$evilString|escapeHtml}
     *
     * @param mixed $content
     *
     * @return string
     */
    public function compileEscapeHtmlModifier($content)
    {
        return $this->escapeHtml($content);
    }

    /**
     * @example {$url|escapeUrl}
     *
     * @param mixed $content
     *
     * @return string
     */
    public function compileEscapeUrlModifier($content)
    {
        return $this->escapeUrl($content);
    }

    /**
     * @param string $string
     *
     * @return string
     */
    private function escapeHtml($string)
    {
        return htmlspecialchars($string, ENT_QUOTES, $this->charset, true);
    }

    /**
     * @param string $string
     *
     * @return string
     */
    private function escapeHtmlEntities($string)
    {
        return htmlentities($string, ENT_QUOTES, $this->charset, true);
    }

    /**
     * @param string $string
     *
     * @return string
     */
    private function escapeQuotes($string)
    {
        return preg_replace("%(?<!\\\\)'%", "\\'", $string);
    }

    /**
     * @param string $string
     *
     * @return string
     */
    private function escapeUrl($string)
    {
        return rawurlencode($string);
    }
}
