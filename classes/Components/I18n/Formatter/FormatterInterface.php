<?php
/*
 * SPDX-FileCopyrightText: 2023 Roland Rusch, easy-smart solution GmbH <roland.rusch@easy-smart.ch>
 * SPDX-License-Identifier: AGPL-3.0-only
 */

declare(strict_types=1);

namespace Xentral\Components\I18n\Formatter;

/**
 * FormatterInterface defines the mandatory interface each formatter has to provide.
 *
 * @author   Roland Rusch, easy-smart solution GmbH <roland.rusch@easy-smart.ch>
 */
interface FormatterInterface
{
    /**
     * Parse string from user input and store as desired type in object.
     * If parsing fails, an Exception is thrown.
     *
     * @param string $input
     *
     * @return self
     */
    public function parseUserInput(string $input): self;
    
    
    
    /**
     * Set the native PHP value in the formatter.
     * The value must ALWAYS be of the requested type or an Exception is thrown.
     *
     * @param mixed $input
     *
     * @return self
     */
    public function setPhpVal(mixed $input): self;
    
    
    
    /**
     * Get the native PHP value from the formatter.
     * The value must ALWAYS be of the requested type or an Exception is thrown.
     *
     * @return mixed
     */
    public function getPhpVal(): mixed;
    
    
    
    /**
     * Return a string representing the PHP value as a formatted value.
     * Throws an Exception if no value was set before or the value is of the wrong type.
     *
     * @return string
     */
    public function formatForUser(): string;
    
    
    
    /**
     * Return a string that can be used in an SQL query to format the value for presentation to a User.
     * Should return the same string as if it was formatted by FormatterInterface::formatForUser(), but directly from
     * the database.
     * This function does not need a native PHP value, but a table column is needed.
     *
     * @param string $col
     *
     * @return string
     */
    public function formatForUserWithSqlStatement(string $col): string;
    
    
    
    /**
     * Test $input for the correct (strict) type the class supports. Return false otherwise.
     *
     * @param mixed $input
     *
     * @return bool
     */
    public function isStrictValidPhpVal($input): bool;
}