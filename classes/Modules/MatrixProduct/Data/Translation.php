<?php
/*
 * SPDX-FileCopyrightText: 2023 Andreas Palm
 * SPDX-License-Identifier: AGPL-3.0-only
 */

namespace Xentral\Modules\MatrixProduct\Data;

use JsonSerializable;

final class Translation implements JsonSerializable
{
    public function __construct(
        public string $nameFrom,
        public string $languageTo,
        public string $nameTo,
        public ?int $id = null,
        public string $nameExternalFrom = '',
        public string $nameExternalTo = '',
        public string $languageFrom = 'DE'
    )
    {
    }

    public static function fromDbArray(array $data): Translation {
        return new self($data['name_from'], $data['language_to'], $data['name_to'], $data['id'],
            $data['name_external_from'], $data['name_external_to'], $data['language_from']);
    }

    public function jsonSerialize(): array
    {
        return (array) $this;
    }
}