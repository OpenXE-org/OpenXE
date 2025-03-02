<?php

namespace Xentral\Modules\Onlineshop\Data;

class ArticleExportResult {
    public int $articleId = 0;
    public bool $success = false;
    public ?string $message = null;
    public ?string $extArticleId = null;
}