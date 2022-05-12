<?php

declare(strict_types=1);

namespace Xentral\Components\Mailer\Data;

use JsonSerializable;

final class EmailRecipient implements JsonSerializable
{
    /** @var string $email */
    private $email;

    /** @var string $name */
    private $name;

    /**
     * @param string      $email
     * @param string|null $name
     */
    public function __construct(string $email, string $name = null)
    {
        $this->email = $email;
        $this->name = $name;
    }

    /**
     * @return string
     */
    public function getEmail():string
    {
        return $this->email;
    }

    /**
     * @return string
     */
    public function getName():string
    {
        if ($this->name !== null) {
            return $this->name;
        }

        return '';
    }

    /**
     * @param string $name
     */
    public function setName($name):void
    {
        $this->name = $name;
    }

    /**
     * @return string
     */
    public function __toString():string
    {
        if ($this->name === null) {
            return $this->email;
        }

        return sprintf('%s<%s>', $this->name, $this->email);
    }

    /**
     * @return array
     */
    public function jsonSerialize(): array
    {
        return ['email' => $this->email, 'name' => $this->name];
    }
}
