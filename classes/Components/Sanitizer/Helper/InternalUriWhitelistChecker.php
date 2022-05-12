<?php

namespace Xentral\Components\Sanitizer\Helper;

use Xentral\Components\Sanitizer\Exception\InvalidArgumentException;

final class InternalUriWhitelistChecker
{
    /** @var string $hostname */
    private $hostname;

    /** @var array $hostnameParts */
    private $hostnameParts;

    /** @var array $moduleActionWhitelist */
    private $moduleActionWhitelist;

    /**
     * @example `$whitelist = [
     *     ['module' => 'testinger', 'action' => 'example'],
     *     ['module' => 'welcome', 'action' => 'redirect'],
     * ]`
     *
     * @param string $hostname Der eigene Hostname; nur Domain ohne http
     * @param array  $moduleActionWhitelist Erlaubte Module-Action-Kombinationen
     *
     * @throws InvalidArgumentException
     */
    public function __construct($hostname, $moduleActionWhitelist = [])
    {
        if (empty($hostname)) {
            throw new InvalidArgumentException(sprintf('Hostname "%s" is invalid.', $hostname));
        }

        $this->hostname = (string)$hostname;
        $this->hostnameParts = array_reverse(explode('.', $this->hostname));
        $this->moduleActionWhitelist = (array)$moduleActionWhitelist;
    }

    /**
     * @param string|null $uriHostname
     *
     * @return bool
     */
    public function isOwnHost($uriHostname = null)
    {
        if ($uriHostname === null) {
            return true; // Domain fehlt => Relative URL './index.php?module=...'
        }
        if ($uriHostname === $this->hostname) {
            return true; // Domain stimmt 1:1 überein
        }

        $uriHostParts = array_reverse(explode('.', $uriHostname));
        foreach ($this->hostnameParts as $index => $hostnamePart) {
            if (!isset($uriHostParts[$index])) {
                return false;
            }
            if ($this->hostnameParts[$index] !== $uriHostParts[$index]) {
                return false;
            }
        }

        return true; // Geprüfte Domain ist Subdomain von $this->hostname > OK
    }

    /**
     * Achtung: Methode prüft nicht ob Domain übereinstimmt; nur in Kombination mit `$this->isOwnHost` verwenden!
     *
     * @param string|null $module
     * @param string|null $action
     *
     * @return bool
     */
    public function isAllowedAction($module = null, $action = null)
    {
        if ($module === null || $action === null) {
            return false;
        }

        foreach ($this->moduleActionWhitelist as $params) {
            if ($params['module'] === $module && $params['action'] === $action) {
                return true;
            }
        }

        return false;
    }
}
