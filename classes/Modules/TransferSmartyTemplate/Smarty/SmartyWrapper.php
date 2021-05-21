<?php

namespace Xentral\Modules\TransferSmartyTemplate\Smarty;

use Exception;
use Smarty;
use Xentral\Modules\TransferSmartyTemplate\Exception\FilesystemFailureException;
use Xentral\Modules\TransferSmartyTemplate\Exception\InvalidArgumentException;
use Xentral\Modules\TransferSmartyTemplate\Exception\TransferTemplateException;

final class SmartyWrapper
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
    * @param string   $type       plugin type
    * @param string   $name       name of template tag
    * @param callback $callback   PHP callback to register
     *
     * @return \Smarty|\Smarty_Internal_Template
     * @throws \SmartyException
     */
    public function registerPlugin($type, $name, $callback)
    {
        return $this->smarty->registerPlugin($type, $name, $callback);
    }

    /**
     * @see Smarty_Internal_Template::fetch()
     *
     * @param string $template
     *
     * @throws TransferTemplateException
     *
     * @return string
     */
    public function fetch($template = null)
    {
        try {
            return $this->smarty->fetch($template);
        } catch (Exception $exception) {
            throw new TransferTemplateException($exception->getMessage(), $exception->getCode(), $exception);
        }
    }

    /**
     * @see Smarty_Internal_Data::assign()
     *
     * @param array|string $tplVar
     * @param mixed        $value
     *
     * @return void
     */
    public function assign($tplVar, $value = null)
    {
        $this->smarty->assign($tplVar, $value, false);
    }

    /**
     * @see Smarty_Internal_Data::append()
     *
     * @param array|string $tplVar
     * @param mixed        $value
     * @param bool         $merge
     *
     * @return void
     */
    public function append($tplVar, $value = null, $merge = false)
    {
        $this->smarty->append($tplVar, $value, $merge, false);
    }

    /**
     * @param string|array $tplVar
     *
     * @return void
     */
    public function clearAssign($tplVar)
    {
        $this->smarty->clearAssign($tplVar);
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
     * @param string $segment
     * @param string $templateDir
     *
     * @throws InvalidArgumentException
     * @throws FilesystemFailureException
     *
     * @return void
     */
    public function addTemplateDir($segment, $templateDir)
    {
        if (empty($segment)) {
            throw new InvalidArgumentException('Required parameter $segment is empty.');
        }

        if (!is_dir($templateDir)) {
            if (!mkdir($templateDir, 0777, true) && !is_dir($templateDir)) {
                throw new FilesystemFailureException(
                    sprintf('Can not create directory "%s" for segment "%s".', $templateDir, $segment)
                );
            }
        }

        $this->smarty->addTemplateDir($templateDir, $segment, false);
    }

    /**
     * @param string $segment
     *
     * @return array
     */
    public function getTemplateDir($segment)
    {
        return $this->smarty->getTemplateDir($segment, false);
    }

    /**
     * @return array
     */
    public function getTemplateDirs()
    {
        return $this->smarty->getTemplateDir(null, false);
    }

    /**
     * @param string $template
     *
     * @return bool
     */
    public function existsTemplate($segment, $template)
    {
        try {
            $resourceName = sprintf('[%s]%s', $segment, $template);
            return $this->smarty->templateExists($resourceName);
        } catch (Exception $exception) {
        }

        return false;
    }
}
