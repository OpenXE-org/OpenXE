<?php
namespace Xentral\Modules\ScanArticle\Exception;

use InvalidArgumentException as SplInvalidArgumentException;

class InvalidArgumentException extends SplInvalidArgumentException implements ScanArticleExceptionInterface
{
}
