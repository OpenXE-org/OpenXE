<?php

namespace Xentral\Components\Sanitizer;

use HTMLPurifier;
use HTMLPurifier_Config;
use HTMLPurifier_Exception;
use Xentral\Components\Sanitizer\Exception\InitialisationFailedException;
use Xentral\Components\Sanitizer\Exception\SanitationFailedException;

final class HtmlMailSanitizer
{
    /** @var HTMLPurifier $purifier */
    private $purifier;

    /**
     * @param SanitizerConfig $config
     *
     * @throws InitialisationFailedException
     */
    public function __construct(SanitizerConfig $config)
    {
        try {
            $purifierConf = HTMLPurifier_Config::create($config->toHtmlPurifierConfig());
            $this->purifier = new HTMLPurifier($purifierConf);
        } catch (HTMLPurifier_Exception $exception) {
            throw new InitialisationFailedException('Failed to initialize HtmlMailSanitizer', 0, $exception);
        }
    }

    /**
     * @return HTMLPurifier_Config
     */
    public function getConfig()
    {
        return $this->purifier->config;
    }

    /**
     * @param string          $mailContent
     * @param SanitizerConfig $config
     *
     * @throws SanitationFailedException
     *
     * @return string
     */
    public function sanitize($mailContent, SanitizerConfig $config = null)
    {
        $purifierConf = $config !== null ? HTMLPurifier_Config::create($config->toHtmlPurifierConfig()) : null;

        try {
            $cleanContent = $this->purifier->purify($mailContent, $purifierConf);
        } catch (HTMLPurifier_Exception $exception) {
            throw new SanitationFailedException($exception->getMessage(), $exception->getCode(), $exception);
        }

        return $cleanContent;
    }
}
