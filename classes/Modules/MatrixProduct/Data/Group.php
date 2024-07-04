<?php
/*
 * SPDX-FileCopyrightText: 2023 Andreas Palm
 * SPDX-License-Identifier: AGPL-3.0-only
 */

namespace Xentral\Modules\MatrixProduct\Data;

use JsonSerializable;

final class Group implements JsonSerializable
{
  public function __construct(
      public string $name,
      public ?int $id = null,
      public bool $active = true,
      public ?string $nameExternal = null,
      public int $projectId = 0,
      public bool $required = false,
      public ?int $articleId = null,
      public int $sort = 0
  )
  {  }

  public static function fromDbArray(array $data) : self {
    return new self($data['name'], $data['id'], $data['aktiv'], $data['name_ext'], $data['projekt'], $data['pflicht'],
        $data['artikel'] ?? null, $data['sort'] ?? 0);
  }

  public function jsonSerialize(): array
  {
    return (array) $this;
  }
}