<?php

namespace Xentral\Modules\TransferSmartyTemplate\Smarty;

use Smarty_Security;

/**
 * @see https://www.smarty.net/docs/en/advanced.features.tpl#advanced.features.security
 */
final class SmartySecurity extends Smarty_Security
{
    /** @var array $php_functions Allowed PHP functions */
    public $php_functions = ['isset', 'empty', 'count', 'sizeof', 'in_array', 'is_array', 'time'];

    /** @var array $php_modifiers Allowed PHP modifiers */
    public $php_modifiers = ['count', 'nl2br'];

    /** @var int $php_handling Remove native PHP tags */
    public $php_handling = \Smarty::PHP_REMOVE;
}
