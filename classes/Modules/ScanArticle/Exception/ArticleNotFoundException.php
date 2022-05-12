<?php
namespace Xentral\Modules\ScanArticle\Exception;

use InvalidArgumentException;

class ArticleNotFoundException extends InvalidArgumentException implements ScanArticleExceptionInterface
{
}
