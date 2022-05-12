<?php

namespace Xentral\Modules\TransferSmartyTemplate;

use Xentral\Modules\TransferSmartyTemplate\Exception\InvalidArgumentException;
use Xentral\Modules\TransferSmartyTemplate\Exception\PluginRegistrationFailedException;
use Xentral\Modules\TransferSmartyTemplate\Exception\SegmentNotFoundException;
use Xentral\Modules\TransferSmartyTemplate\Exception\TemplateNotFoundException;
use Xentral\Modules\TransferSmartyTemplate\Smarty\SmartyWrapper;

final class TransferSmartyTemplate
{
    /** @var SmartyWrapper $smarty */
    private $smarty;

    /**
     * @param SmartyWrapper $smarty
     */
    public function __construct(SmartyWrapper $smarty)
    {
        $this->smarty = $smarty;
    }

    /**
     * @param string   $type       plugin type
     * @param string   $name       name of template tag
     * @param callback $callback   PHP callback to register
     *
     * @throws PluginRegistrationFailedException
     *
     * @return void
     */
    public function registerPlugin($type, $name, $callback)
    {
        try {
            $this->smarty->registerPlugin($type, $name, $callback);
        } catch (\SmartyException $exception) {
            throw new PluginRegistrationFailedException($exception->getMessage(), $exception->getCode(), $exception);
        }
    }

    /**
     * @param string $segment
     *
     * @return bool
     */
    public function hasTemplateDir($segment)
    {
        $segment = (string)$segment;
        $templateDirs = $this->getTemplateDirs();

        return isset($templateDirs[$segment]) ? true : false;
    }

    /**
     * @param string $segment
     *
     * @throws SegmentNotFoundException
     *
     * @return string
     */
    public function getTemplateDir($segment)
    {
        $segment = (string)$segment;
        $templateDirs = $this->getTemplateDirs();
        if (!isset($templateDirs[$segment])) {
            throw new SegmentNotFoundException(sprintf('Segment "%s" not found.', $segment));
        }

        return $templateDirs[$segment];
    }

    /**
     * @param string $segment
     * @param string $templateDir
     *
     * @return void
     */
    public function addTemplateDir($segment, $templateDir)
    {
        $this->smarty->addTemplateDir($segment, $templateDir);
    }

    /**
     * @return array
     */
    public function getTemplateDirs()
    {
        return $this->smarty->getTemplateDirs();
    }

    /**
     * Checks if template exists
     *
     * @param string $segment
     * @param string $template
     *
     * @return bool
     */
    public function existsTemplate($segment, $template)
    {
        return $this->smarty->existsTemplate($segment, $template);
    }

    /**
     * Parses the template and returns the output.
     *
     * @param string $segment
     * @param string $template
     *
     * @return string
     */
    public function fetch($segment, $template)
    {
        $templatePath = $this->discoverTemplatePath($segment, $template);

        return $this->smarty->fetch($templatePath);
    }

    /**
     * Gets the value of one assigned template variable.
     *
     * @param string $tplVar
     *
     * @return mixed
     */
    public function getVar($tplVar)
    {
        $this->ensureTemplateVarIsString($tplVar);

        return $this->smarty->getTemplateVars($tplVar);
    }

    /**
     * Gets all assigned template variables and values
     *
     * @return array
     */
    public function getVars()
    {
        return $this->smarty->getTemplateVars(null);
    }

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
    public function assign($tplVar, $value)
    {
        $this->ensureTemplateVarIsString($tplVar);
        $this->smarty->assign((string)$tplVar, $value);
    }

    /**
     * Assigns multiple template variables
     *
     * Array keys will be used as template variable and array values as value.
     *
     * @example assignAssoc(['foo' => 'zof', 'bar' => 'baz']); In Tempalte: {$foo} {$bar}
     *
     * @param array $tplVars
     *
     * @return void
     */
    public function assignAssoc(array $tplVars)
    {
        foreach ($tplVars as $tplVar => $value) {
            $this->assign($tplVar, $value);
        }
    }

    /**
     * Deletes a template variable
     *
     * @param string $tplVar
     */
    public function clearAssign($tplVar)
    {
        $this->ensureTemplateVarIsString($tplVar);
        $this->smarty->clearAssign((string)$tplVar);
    }

    /**
     * Deletes multiple template variables
     *
     * @param array $tplVars
     */
    public function clearAssoc(array $tplVars)
    {
        foreach ($tplVars as $tplVar) {
            $this->clearAssign($tplVar);
        }
    }

    /**
     * @param mixed $tplVar
     *
     * @throws InvalidArgumentException
     *
     * @return void
     */
    private function ensureTemplateVarIsString($tplVar)
    {
        if (!is_string($tplVar)) {
            throw new InvalidArgumentException('Template variable is not a string.');
        }
    }

    /**
     * @example 'default', 'list.tpl' => 'Modules/TransferTempalte/templates/default/list.tpl'
     *
     * @param string $segment
     * @param string $template
     *
     * @throws InvalidArgumentException
     * @throws TemplateNotFoundException
     *
     * @return string
     */
    private function discoverTemplatePath($segment, $template)
    {
        if (empty($segment)) {
            throw new InvalidArgumentException('Required parameter "$segment" is empty.');
        }
        if (empty($template)) {
            throw new InvalidArgumentException('Required parameter "$template" is empty.');
        }

        if ($this->smarty->existsTemplate($segment, $template)) {
            return sprintf('[%s]%s', $segment, $template);
        }

        throw new TemplateNotFoundException(sprintf('Template "%s" not found in segment "%s".', $template, $segment));
    }
}
