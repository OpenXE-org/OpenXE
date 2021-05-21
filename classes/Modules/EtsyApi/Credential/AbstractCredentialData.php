<?php

namespace Xentral\Modules\EtsyApi\Credential;

use Xentral\Modules\EtsyApi\Exception\CredentialException;

abstract class AbstractCredentialData implements CredentialDataInterface
{
    /** @var string $identifier */
    private $identifier;

    /** @var string $secret */
    private $secret;

    /**
     * @param string $identifier
     * @param string $secret
     */
    public function __construct($identifier, $secret)
    {
        $this->identifier = $identifier;
        $this->secret = $secret;
    }

    /**
     * @param string $string
     *
     * @throws CredentialException
     *
     * @return static
     */
    public static function fromString($string)
    {
        $base64Raw = strtr($string, '-_', '+/');
        $jsonString = base64_decode($base64Raw, true);

        $data = json_decode($jsonString, true);

        if (!isset($data['class'])) {
            throw new CredentialException('Can not create credentials object from string. Class property is missing.');
        }
        if ($data['class'] !== static::class) {
            throw new CredentialException('Can not create credentials object from string. Class does not match.');
        }

        return static::fromArray($data);
    }

    /**
     * @param array $credentials
     *
     * @return static
     */
    public static function fromArray(array $credentials)
    {
        return new static($credentials['identifier'], $credentials['secret']);
    }

    /**
     * @return string
     */
    public function getIdentifier()
    {
        return $this->identifier;
    }

    /**
     * @return string
     */
    public function getSecret()
    {
        return $this->secret;
    }

    /**
     * @return string
     */
    public function toString()
    {
        $data = [
            'identifier' => $this->identifier,
            'secret'     => $this->secret,
            'class'      => static::class,
        ];
        $base64Raw = base64_encode(json_encode($data, JSON_HEX_TAG| JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT));
        $base64Url = strtr($base64Raw, '+/', '-_');

        return rtrim($base64Url, '=');
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->toString();
    }
}
