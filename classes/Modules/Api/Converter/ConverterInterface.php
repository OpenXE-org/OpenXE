<?php

namespace Xentral\Modules\Api\Converter;

interface ConverterInterface
{
    /**
     * Wandle Array in Converter-Format (XML oder JSON)
     *
     * @param array $array
     *
     * @return string
     */
    public function fromArray($array);

    /**
     * Wandle Converter-Format (XML oder JSON) zu Array
     *
     * @param string $data
     *
     * @return array
     */
    public function toArray($data);
}
