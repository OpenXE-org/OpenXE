<?php

namespace Xentral\Components\Template;

use Smarty;
use TemplateParser;
use Xentral\Components\Template\Exception\DirectoryNotFoundException;
use Xentral\Components\Template\Exception\InvalidArgumentException;

final class Template implements TemplateInterface
{
    /** @var SmartyFacade $smartyFacade */
    private $smartyFacade;

    /**
     * @deprecated
     * @var TemplateParser $legacyTemplate
     */
    private $legacyTemplate;

    /** @var string $defaultNamespace */
    private $defaultNamespace;

    /**
     * @param SmartyFacade   $smarty
     * @param TemplateParser $legacyTemplate
     */
    public function __construct(SmartyFacade $smarty, TemplateParser $legacyTemplate)
    {
        $this->smartyFacade = $smarty;
        $this->legacyTemplate = $legacyTemplate;
    }

    /**
     * @inheritdoc
     */
    public function getVar($tplVar)
    {
        $this->ensureTemplateVarIsString($tplVar);

        return $this->smartyFacade->getTemplateVars($tplVar);
    }

    /**
     * @inheritdoc
     */
    public function getVars()
    {
        return $this->smartyFacade->getTemplateVars(null);
    }

    /**
     * @inheritdoc
     */
    public function assign($tplVar, $value)
    {
        $this->ensureTemplateVarIsString($tplVar);
        $this->smartyFacade->assign((string)$tplVar, $value);
    }

    /**
     * @inheritdoc
     */
    public function assignAssoc(array $assocTplVars)
    {
        foreach ($assocTplVars as $tplVar => $value) {
            $this->assign($tplVar, $value);
        }
    }

    /**
     * @inheritdoc
     */
    public function append($tplVar, $value)
    {
        $this->ensureTemplateVarIsString($tplVar);
        $this->smartyFacade->append($tplVar, $value, false);
    }

    /**
     * @inheritdoc
     */
    public function appendString($tplVar, $value)
    {
        $this->ensureTemplateVarIsString($tplVar);
        $assigned = (string)$this->getVar($tplVar);

        $this->smartyFacade->assign($tplVar, $assigned . $value);
    }

    /**
     * @inheritdoc
     */
    public function appendAssoc(array $assocTplVar)
    {
        foreach ($assocTplVar as $tplVar => $value) {
            $this->append((string)$tplVar, $value);
        }
    }

    /**
     * @inheritdoc
     */
    public function clearAssign($tplVar)
    {
        $this->ensureTemplateVarIsString($tplVar);
        $this->smartyFacade->clearAssign((string)$tplVar);
    }

    /**
     * @inheritdoc
     */
    public function clearAssoc(array $tplVars)
    {
        foreach ($tplVars as $tplVar) {
            $this->clearAssign($tplVar);
        }
    }

    /**
     * @inheritdoc
     */
    public function fetch($template, $namespace = null)
    {
        $templatePath = $this->discoverTemplatePath($template, $namespace);

        return $this->smartyFacade->fetch($templatePath);
    }

    /**
     * @inheritdoc
     */
    public function display($template, $namespace = null)
    {
        $html = $this->fetch($template, $namespace);

        $this->legacyTemplate->Set('PAGE', $html);
        $this->legacyTemplate->Parse('PAGE', '');
    }

    /**
     * @inheritdoc
     */
    public function addTemplateDir($directory)
    {
        $realPath = realpath($directory);
        if ($realPath === false || !is_dir($realPath)) {
            throw new DirectoryNotFoundException(sprintf(
                'Directory "%s" does not exist.', $directory
            ));
        }

        $this->smartyFacade->addTemplateDir($directory);
    }

    /**
     * @return string|null
     */
    public function getDefaultNamespace()
    {
        return $this->defaultNamespace;
    }

    /**
     * @inheritdoc
     */
    public function setDefaultNamespace($namespace)
    {
        if (empty($namespace)) {
            throw new InvalidArgumentException('Namespace can not be empty.');
        }

        $this->defaultNamespace = $namespace;
    }

    /**
     * @internal
     *
     * @return Smarty
     */
    public function getSmarty()
    {
        return $this->smartyFacade->getSmarty();
    }

    /**
     * Opens Smarty Debugging Console window
     *
     * @return void
     */
    public function displayDebugWindow()
    {
        $this->smartyFacade->displayDebugConsole();
    }

    /**
     * @example 'list.tpl', 'Modules/Chat' => 'Modules/Chat/templates/list.tpl'
     *
     * @param string      $template
     * @param string|null $namespace
     *
     * @throws InvalidArgumentException
     *
     * @return string
     */
    private function discoverTemplatePath($template, $namespace = null)
    {
        if (empty($template)) {
            throw new InvalidArgumentException('Required parameter "$template" is empty.');
        }

        // System templates don't have namespaces
        if ($namespace === null && $this->smartyFacade->templateExists($template)) {
            return $template;
        }

        return $this->discoverNamespacedTemplatePath($template, $namespace);
    }

    /**
     * @param string      $template
     * @param string|null $namespace
     *
     * @return string
     */
    private function discoverNamespacedTemplatePath($template, $namespace = null)
    {
        if ($namespace === null && $this->defaultNamespace === null) {
            throw new InvalidArgumentException('Default namespace is not set.');
        }
        if ($namespace === null) {
            $namespace = $this->defaultNamespace;
        }

        $template = ltrim($template, '/');
        $namespace = trim($namespace, '/');

        return $namespace . '/templates/' . $template;
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
}
