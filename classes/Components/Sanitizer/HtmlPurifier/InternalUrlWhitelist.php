<?php

namespace Xentral\Components\Sanitizer\HtmlPurifier;

use HTMLPurifier_Injector;
use HTMLPurifier_Token;
use HTMLPurifier_Token_Empty;
use HTMLPurifier_Token_Start;
use Xentral\Components\Sanitizer\Exception\SanitizerExceptionInterface;
use Xentral\Components\Sanitizer\Helper\InternalUriWhitelistChecker;
use Xentral\Components\Sanitizer\Helper\UriParser;

class InternalUrlWhitelist extends HTMLPurifier_Injector
{
    /** @var string $name */
    public $name = 'InternalUrlWhitelist';

    /** @var array $needed */
    public $needed = ['a', 'img'];

    /** @var InternalUriWhitelistChecker $checker */
    protected $checker;

    /** @var UriParser $parser */
    protected $parser;

    /**
     * @param UriParser                   $parser
     * @param InternalUriWhitelistChecker $checker
     */
    public function __construct(UriParser $parser, InternalUriWhitelistChecker $checker)
    {
        $this->parser = $parser;
        $this->checker = $checker;
    }

    /**
     * Image-URLs verarbeiten
     *
     * @param HTMLPurifier_Token_Empty $token
     */
    public function handleElement(&$token)
    {
        if ($token->name !== 'img' || !isset($token->attr['src'])) {
            return;
        }

        try {
            $url = $token->attr['src'];
            $uri = $this->parser->parse($url);
        } catch (SanitizerExceptionInterface $exception) {
            unset($token->attr['src']);
            return;
        }

        if ($this->checker->isOwnHost($uri->getHost())) {
            $module = $uri->getQueryParam('module');
            $action = $uri->getQueryParam('action');
            if (!$this->checker->isAllowedAction($module, $action)) {
                unset($token->attr['src']);
            }
        }
    }

    /**
     * Hyperlink-URLs verarbeiten
     *
     * @param HTMLPurifier_Token $token
     */
    public function handleEnd(&$token)
    {
        /** @var HTMLPurifier_Token_Start $startToken */
        $startToken = $token->start;
        if ($startToken->name !== 'a' || !isset($startToken->attr['href'])) {
            return;
        }

        try {
            $url = $startToken->attr['href'];
            $uri = $this->parser->parse($url);
        } catch (SanitizerExceptionInterface $exception) {
            unset($startToken->attr['href']);
            return;
        }

        if ($this->checker->isOwnHost($uri->getHost())) {
            $module = $uri->getQueryParam('module');
            $action = $uri->getQueryParam('action');
            if (!$this->checker->isAllowedAction($module, $action)) {
                unset($startToken->attr['href']);
            }
        }
    }
}
