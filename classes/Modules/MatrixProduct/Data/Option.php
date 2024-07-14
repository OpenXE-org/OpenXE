<?php
/*
 * SPDX-FileCopyrightText: 2023 Andreas Palm
 * SPDX-License-Identifier: AGPL-3.0-only
 */

namespace Xentral\Modules\MatrixProduct\Data;

use JsonSerializable;

final class Option implements JsonSerializable
{
  public function __construct(
      public string $name,
      public readonly int $groupId,
      public ?int $id = null,
      public bool $active = true,
      public string $nameExternal = '',
      public int $sort = 0,
      public string $articleNumber = '',
      public string $articleNumberSuffix = '',
      public readonly ?int $globalOptionId = null,
      public readonly ?int $articleId = null
  )
  {
  }

  public static function fromDbArray(array $data) : Option {
    return new self($data['name'], $data['gruppe'], $data['id'], $data['aktiv'], $data['name_ext'], $data['sort'],
      $data['artikelnummer'], $data['articlenumber_suffix'], $data['matrixprodukt_eigenschaftenoptionen'] ?? null,
      $data['artikel'] ?? null);
  }

  public function jsonSerialize(): array
  {
    return (array) $this;
  }
}