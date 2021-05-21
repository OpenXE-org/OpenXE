<?php

namespace Xentral\Modules\Api\Converter;

use Xentral\Modules\Api\Converter\Exception\ConvertionException;

class JsonConverter implements ConverterInterface
{
    /**
     * Array zu JSON
     *
     * @param array $array
     *
     * @return string
     */
    public function fromArray($array)
    {
        $data = json_encode($array);

        if ($data === false || json_last_error() !== JSON_ERROR_NONE) {
            throw new ConvertionException('JSON could not be encoded.');
        }

        return $data;
    }

    /**
     * JSON zu Array
     *
     * @param string $json
     *
     * @return array
     */
    public function toArray($json)
    {
        $data = json_decode($json, true);

        if ($data === null && json_last_error() !== JSON_ERROR_NONE) {
            throw new ConvertionException('JSON could not be decoded.');
        }

        return $data;
    }
}
