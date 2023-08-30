<?php
/*
 * SPDX-FileCopyrightText: 2023 Roland Rusch, easy-smart solution GmbH <roland.rusch@easy-smart.ch>
 * SPDX-License-Identifier: AGPL-3.0-only
 */

declare(strict_types=1);

namespace Xentral\Components\I18n\Formatter;

enum FormatterMode
{
    
    /**
     * Only values representing the class type are allowed for input and output. This means, you can only store a
     * float (or it's localized string representation) to a FloatFormatter).
     */
    case MODE_STRICT;
    
    /**
     * Allow NULL as valid value in addition to the formatter type. If the user enters an empty string, this results
     * in a PHP value of NULL and vice versa.
     */
    case MODE_NULL;
    
    /**
     * Allow an empty string in addition to the formatter type. If the user entern an empty string, this results in
     * an emtpy string as PHP value and vice versa.
     */
    case MODE_EMPTY;
}
