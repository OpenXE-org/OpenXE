<?php
/*
 * SPDX-FileCopyrightText: 2025 Andreas Palm
 * SPDX-License-Identifier: LicenseRef-EGPL-3.1
 */

namespace Xentral\Modules\CrossSelling\Data;

class CrossSellingArticle
{
    public function __construct(
        public CrossSellingType $type,
        public int $mainArticleId,
        public int $connectedArticleId,
        public bool $active = true,
        public bool $bidirectional = false,
        public int $shopId = 0,
        public int $sort = 0,
        public string $remark = '',
        public ?int $id = null,
    ) {}
}