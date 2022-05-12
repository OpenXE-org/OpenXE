<?php

namespace Xentral\Components\Http;

use JsonSerializable;
use Xentral\Components\Http\Exception\InvalidArgumentException;

class JsonResponse extends Response
{
    /**
     * @param array|JsonSerializable $data
     * @param int                    $statusCode
     * @param array                  $headers
     */
    public function __construct($data = [], $statusCode = self::HTTP_OK, array $headers = [])
    {
        if (is_object($data) && !$data instanceof JsonSerializable) {
            throw new InvalidArgumentException(sprintf(
                'Class "%s" can not be serialized. It does not implement JsonSerializable', get_class($data)
            ));
        }

        if (!is_object($data) && !is_array($data)) {
            throw new InvalidArgumentException('Parameter $data has to be an array or JsonSerializable.');
        }

        $content = json_encode($data, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT);
        $headers['Content-Type'] = 'application/json; charset=utf8';

        parent::__construct($content, $statusCode, $headers);
    }
}
