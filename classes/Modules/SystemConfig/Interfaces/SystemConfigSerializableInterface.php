<?php

declare(strict_types=1);

namespace Xentral\Modules\SystemConfig\Interfaces;

interface SystemConfigSerializableInterface
{

    /**
     * @param array $array
     *
     * @return self
     */
    public static function fromArray(array $array): self;

    /**
     * @return string
     */
    public static function getSystemConfigNamespace(): string;

    /**
     * @return string
     */
    public static function getSystemConfigKey(): string;

    /**
     * @return array
     */
    public function toArray(): array;
}
