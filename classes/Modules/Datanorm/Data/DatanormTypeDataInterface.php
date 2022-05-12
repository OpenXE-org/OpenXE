<?php

declare(strict_types=1);

namespace Xentral\Modules\Datanorm\Data;

interface DatanormTypeDataInterface
{

    /**
     * @return array
     */
    public function jsonSerialize();

    /**
     * @param string $data
     */
    public function fillByJson(string $data): void;
}
