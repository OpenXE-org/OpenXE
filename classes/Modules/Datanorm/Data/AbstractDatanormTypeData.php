<?php

namespace Xentral\Modules\Datanorm\Data;

use JsonSerializable;

abstract class AbstractDatanormTypeData implements JsonSerializable
{

    /**
     * @return array
     */
    public abstract function jsonSerialize(): array;

    /**
     * @param string $data
     */
    public abstract function fillByJson(string $data): void;
}
