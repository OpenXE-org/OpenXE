<?php

/*
 * SPDX-FileCopyrightText: 2022 Andreas Palm
 *
 * SPDX-License-Identifier: AGPL-3.0-only
 */

namespace Xentral\Carrier\SendCloud\Data;

/**
 * Documents object for a parcel
 */
class Document
{
  public const TYPE_LABEL = 'label';
  public const TYPE_CP71 = 'cp71';
  public const TYPE_CN23 = 'cn23';
  public const TYPE_CN23_DEFAULT = 'cn23-default';
  public const TYPE_COMMERCIAL_INVOICE = 'commercial-invoice';

  public string $Type;
  public string $Size;
  public string $Link;

  public static function fromApiResponse(object $data): Document
  {
    $obj = new Document();
    $obj->Type = $data->type;
    $obj->Size = $data->size;
    $obj->Link = $data->link;
    return $obj;
  }
}