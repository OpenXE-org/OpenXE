<?php

namespace Xentral\Components\Template;

use Exception;
use Smarty;
use Xentral\Components\Template\Exception\TemplateException;

final class SmartyFacade
{
    /** @var Smarty $smarty */
    private $smarty;

    /**
     * @param Smarty $smarty
     */
    public function __construct(Smarty $smarty)
    {
        $this->smarty = $smarty;
    }

    /**
     * @see Smarty_Internal_Template::fetch()
     *
     * @param string $template
     *
     * @throws TemplateException
     *
     * @return string
     */
    public function fetch($template = null)
    {
        try {
            return $this->smarty->fetch($template);
        } catch (Exception $e) {
            throw new TemplateException($e->getMessage(), $e->getCode(), $e);
        }
    }

    /**
     * @see Smarty_Internal_Template::display()
     *
     * @param string $template
     *
     * @throws TemplateException
     */
    public function display($template = null)
    {
        try {
            $this->smarty->display($template);
        } catch (Exception $e) {
            throw new TemplateException($e->getMessage(), $e->getCode(), $e);
        }
    }

    /**
     * @see Smarty_Internal_Data::assign()
     *
     * @param array|string $tplVar
     * @param mixed        $value
     *
     * @return SmartyFacade
     */
    public function assign($tplVar, $value = null)
    {
        $this->smarty->assign($tplVar, $value, false);

        return $this;
    }

    /**
     * @see Smarty_Internal_Data::append()
     *
     * @param array|string $tplVar
     * @param mixed        $value
     * @param bool         $merge
     *
     * @return $this
     */
    public function append($tplVar, $value = null, $merge = false)
    {
        $this->smarty->append($tplVar, $value, $merge, false);

        return $this;
    }

    /**
     * @param string|array $tplVar
     *
     * @return $this
     */
    public function clearAssign($tplVar)
    {
        $this->smarty->clearAssign($tplVar);

        return $this;
    }

    /**
     * @param string|null $varName
     *
     * @return mixed
     */
    public function getTemplateVars($varName = null)
    {
        return $this->smarty->getTemplateVars($varName);
    }

    /**
     * @param string $templateDir
     * @param null   $key
     * @param bool   $isConfig
     *
     * @return $this
     */
    public function addTemplateDir($templateDir, $key = null, $isConfig = false)
    {
        $this->smarty->addTemplateDir($templateDir, $key, $isConfig);

        return $this;
    }

    /**
     * @param string $template
     *
     * @throws TemplateException
     *
     * @return bool
     */
    public function templateExists($template)
    {
        try {
            return $this->smarty->templateExists($template);
        } catch (Exception $e) {
            throw new TemplateException($e->getMessage(), $e->getCode(), $e);
        }
    }

    /**
     * @return Smarty
     */
    public function getSmarty()
    {
        return $this->smarty;
    }

    /**
     * @throws TemplateException
     *
     * @return void
     */
    public function displayDebugConsole()
    {
        try {
            $this->smarty->_debug->display_debug($this->smarty);
        } catch (Exception $e) {
            throw new TemplateException($e->getMessage(), $e->getCode(), $e);
        }
    }
}
