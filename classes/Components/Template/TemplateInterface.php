<?php

namespace Xentral\Components\Template;

use Xentral\Components\Template\Exception\DirectoryNotFoundException;

interface TemplateInterface
{
    /**
     * Gets the value of one assigned template variable.
     *
     * @param string $tplVar
     *
     * @return mixed
     */
    public function getVar($tplVar);

    /**
     * Gets all assigned template variables and values.
     *
     * @return array
     */
    public function getVars();

    /**
     * Assigns a value to a template variable.
     *
     * If variable is already assigned, than the value will be overwritten.
     *
     * @param string $tplVar
     * @param mixed  $value
     *
     * @return void
     */
    public function assign($tplVar, $value);

    /**
     * Assigns multiple template variables.
     *
     * Array keys will be used as template variable and array values as value.
     *
     * @example assignAssoc(['foo' => 'zof', 'bar' => 'baz']); In Tempalte: {$foo} {$bar}
     *
     * @param array $assocTplVar
     *
     * @return void
     */
    public function assignAssoc(array $assocTplVar);

    /**
     * Appends a value to a previously assigned variable.
     *
     * * The previously assigned value will be transformed to an array.
     * * The passed value will be pushed to that array.
     *
     * @param string $tplVar
     * @param mixed  $value
     *
     * @return void
     */
    public function append($tplVar, $value);

    /**
     * Appends a value as string
     *
     * Previously assigned values will be transformed to string. Values will be concatenated.
     *
     * @param string $tplVar
     * @param string $value
     *
     * @return void
     */
    public function appendString($tplVar, $value);

    /**
     * Appends multiple template variables.
     *
     * Array keys will be used as template variable and array values as value.
     *
     * For each row:
     * * The previously assigned value will be transformed to an array.
     * * The passed value will be pushed to that array.
     *
     * @param array $assocTplVar
     *
     * @return void
     */
    public function appendAssoc(array $assocTplVar);

    /**
     * Deletes a template variable
     *
     * @param string $tplVar
     *
     * @return void
     */
    public function clearAssign($tplVar);

    /**
     * Deletes multiple template variables.
     *
     * @param array|string[] $tplVars
     *
     * @return void
     */
    public function clearAssoc(array $tplVars);

    /**
     * Parses the template an returns the output.
     *
     * @param string      $template
     * @param string|null $namespace
     *
     * @return string
     */
    public function fetch($template, $namespace = null);

    /**
     * Parses the template an display the output.
     *
     * @param string      $template
     * @param string|null $namespace
     *
     * @return void
     */
    public function display($template, $namespace = null);

    /**
     * @param string $directory Absolute path to template directory
     *
     * @throws DirectoryNotFoundException If the directory does not exist
     *
     * @return void
     */
    public function addTemplateDir($directory);

    /**
     * @return string|null
     */
    public function getDefaultNamespace();

    /**
     * @param string $namespace
     *
     * @return void
     */
    public function setDefaultNamespace($namespace);
}
