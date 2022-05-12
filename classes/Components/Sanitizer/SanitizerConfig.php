<?php

namespace Xentral\Components\Sanitizer;

use Xentral\Components\Sanitizer\Helper\InternalUriWhitelistChecker;
use Xentral\Components\Sanitizer\Helper\UriParser;
use Xentral\Components\Sanitizer\HtmlPurifier\InternalUrlWhitelist;

final class SanitizerConfig
{
    /** @var bool $disableExternal */
    private $disableExternal;

    /** @var bool $disableExternalResources */
    private $disableExternalResources;

    /** @var string|null $hostname */
    private $hostname;

    /** @var string|null $redirectUrl */
    private $redirectUrl;

    /** @var array $moduleActionWhitelist */
    private $moduleActionWhitelist;

    /** @var string|null $tempDir */
    private $tempDir;

    /**
     * @param bool        $disableExternal          Removes all external links and resources
     * @param bool        $disableExternalResources Removes only external resources; external links are allowed
     * @param string|null $hostname                 Domain name of the server; without http/https
     * @param string|null $redirectUrl              Munges all browsable (usually http, https and ftp) absolute URIs
     *                                              into another URI; example `http://my-redirect-service.com/?url=%s`
     * @param array $moduleActionWhitelist          Example `[ ['module' => 'welcome', 'action' => 'redirect'] ]`
     */
    public function __construct(
        $disableExternal = false,
        $disableExternalResources = false,
        $hostname = null,
        $redirectUrl = null,
        $moduleActionWhitelist = []
    ) {
        $this->disableExternal = (bool)$disableExternal;
        $this->disableExternalResources = (bool)$disableExternalResources;
        $this->moduleActionWhitelist = (array)$moduleActionWhitelist;
        if (is_string($hostname) && !empty($hostname)) {
            $this->hostname = $hostname;
        }
        if (is_string($redirectUrl) && !empty($redirectUrl)) {
            $this->redirectUrl = $redirectUrl;
        }
    }

    /**
     * @param string $tempDir
     *
     * @return void
     */
    public function setTempDir($tempDir)
    {
        $this->tempDir = $tempDir;
    }

    /**
     * @return array
     */
    public function toHtmlPurifierConfig()
    {
        $config = $this->getPurifierDefaults();
        $config['URI']['DisableExternal'] = $this->disableExternal;
        $config['URI']['DisableExternalResources'] = $this->disableExternalResources;

        if ($this->redirectUrl !== null) {
            $config['URI']['Munge'] = $this->redirectUrl;
        }
        if ($this->hostname !== null) {
            $config['URI']['Host'] = $this->hostname;
        }
        if ($this->tempDir !== null && is_dir($this->tempDir)) {
            $config['Cache'] = [
                'DefinitionImpl' => 'Serializer',
                'SerializerPath' => $this->tempDir,
            ];
        }

        // Nur bestimmte interne URLs zulassen; alle anderen entfernen
        if (!empty($this->hostname) && !empty($this->moduleActionWhitelist)) {
            $checker = new InternalUriWhitelistChecker($this->hostname, $this->moduleActionWhitelist);
            $autoFormatter = new InternalUrlWhitelist(new UriParser(), $checker);

            if (!isset($config['AutoFormat']['Custom'])) {
                $config['AutoFormat']['Custom'] = [];
            }
            $config['AutoFormat']['Custom'][] = $autoFormatter;
        }

        return $config;
    }

    /**
     * @see http://htmlpurifier.org/live/configdoc/plain.html
     *
     * @return array
     */
    private function getPurifierDefaults()
    {
        return [
            'URI'  => [
                'DisableExternal'          => false,
                'DisableExternalResources' => false,
                'Munge'                    => null,
                'Host'                     => null,
            ],
            'HTML' => [
                'TidyLevel'        => 'medium',
                'TargetBlank'      => true,
                'TargetNoopener'   => true,
                'TargetNoreferrer' => true,
            ],
        ];
    }
}
