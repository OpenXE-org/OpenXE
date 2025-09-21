<?php
/*
 * SPDX-FileCopyrightText: 2019 Xentral ERP Software GmbH, Fuggerstrasse 11, D-86150 Augsburg
 * SPDX-FileCopyrightText: 2023-2025 Andreas Palm
 *
 * SPDX-License-Identifier: LicenseRef-EGPL-3.1
 */

namespace Xentral\Components\Http;

use JsonSerializable;
use Xentral\Components\Http\Exception\InvalidArgumentException;

class JsonResponse extends Response
{
    /**
     * @param array|JsonSerializable $data
     * @param int $statusCode
     * @param array $headers
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

    public static function NoContent(array $headers = []): JsonResponse
    {
        return new JsonResponse([], Response::HTTP_NO_CONTENT, $headers);
    }

    public static function BadRequest(array|JsonSerializable $data = [], array $headers = []): JsonResponse
    {
        return new JsonResponse($data, Response::HTTP_BAD_REQUEST, $headers);
    }

    public static function NotFound(array|JsonSerializable $data = [], array $headers = []): JsonResponse
    {
        return new JsonResponse($data, Response::HTTP_NOT_FOUND, $headers);
    }

    public static function Forbidden(array|JsonSerializable $data = [], array $headers = []): JsonResponse
    {
        return new JsonResponse($data, Response::HTTP_FORBIDDEN, $headers);
    }
}
