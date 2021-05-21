<?php

namespace Xentral\Components\Template\SmartyPlugin;

use Smarty_Internal_Template;
use Smarty_Template_Source;
use Xentral\Components\Template\Exception\TemplateException;

final class TranslationPlugin
{
    /**
     * @todo Add TranslationService
     * @var null $translator
     */
    private $translator;

    /** @var array $resourceNamespaces */
    private $resourceNamespaces = [];

    /**
     * @param array                    $params
     * @param Smarty_Internal_Template $template
     *
     * @throws TemplateException If 'key' param is not set
     *
     * @return void
     */
    public function compileNamespaceFunction($params, $template)
    {
        if (empty($params['key'])) {
            $sourceName = $this->getResourceSourceName($template);
            throw new TemplateException(sprintf(
                'Template error: Required parameter "key" is missing in {namespace} function. Source: %s', $sourceName
            ));
        }

        // Store which namespace is used in which template
        $resource = $template->source->resource;
        $this->addResourceNamespace($resource, $params['key']);
    }

    /**
     * @param array                    $params
     * @param mixed                    $content
     * @param Smarty_Internal_Template $template
     * @param bool                     $repeat
     *
     * @throws TemplateException On missing params
     *
     * @return string|null
     */
    public function compileTranslateBlock($params, $content, $template, &$repeat)
    {
        // Only output on closing tag @see https://www.smarty.net/docs/en/plugins.block.functions.tpl
        if ($repeat === true) {
            return null;
        }

        if (empty($params['key'])) {
            throw new TemplateException(sprintf(
                'Template error: Required parameter "key" is missing in {translate} block. Source: %s',
                $template->source->resource
            ));
        }

        if (!empty($params['namespace'])) {
            $namespace = $params['namespace'];
        } else {
            $namespace = $this->determineResourceNamespace($template);
        }

        if (empty($namespace)) {
            $sourceName = $this->getResourceSourceName($template);
            throw new TemplateException(sprintf(
                'Template error: Namespace for translation could not be determined. Source: %s', $sourceName
            ));
        }

        // @todo Use TranslationService
        return (string)$content;
    }

    /**
     * @param Smarty_Internal_Template $template
     *
     * @return string
     */
    private function getResourceSourceName($template)
    {
        $source = $template->source;
        if ($source->type === 'file') {
            return 'File ' . $source->filepath;
        }

        return sprintf('Resource %s:%s', $source->type, $source->name);
    }

    /**
     * @param string $resource
     *
     * @return bool
     */
    private function isResourceNamespaceDefined($resource)
    {
        return isset($this->resourceNamespaces[$resource]);
    }

    /**
     * @param string $resource
     *
     * @return string
     */
    private function getResourceNamespace($resource)
    {
        return $this->resourceNamespaces[$resource];
    }

    /**
     * @param Smarty_Internal_Template $template
     *
     * @return string
     */
    private function determineResourceNamespace($template)
    {
        $resource = $template->source->resource;
        if ($this->isResourceNamespaceDefined($resource)) {
            return $this->getResourceNamespace($resource);
        }

        $namespace = $this->findResourceNamespaceFromParents($template->inheritance->sources);
        $this->addResourceNamespace($resource, $namespace);

        return $namespace;
    }

    /**
     * @param array|Smarty_Template_Source[] $parents
     *
     * @return string|null Namespace
     */
    private function findResourceNamespaceFromParents($parents)
    {
        foreach ($parents as $source) {
            $resource = $source->resource;
            if ($this->isResourceNamespaceDefined($resource)) {
                return $this->resourceNamespaces[$resource];
            }
        }

        return 'default';
    }

    /**
     * @param string $resource
     * @param string $namespace
     *
     * @return void
     */
    private function addResourceNamespace($resource, $namespace)
    {
        if ($this->isResourceNamespaceDefined($resource)) {
            throw new TemplateException(sprintf(
                'Template resource namespace for resource "%s" is already defined', $resource
            ));
        }

        $this->resourceNamespaces[$resource] = $namespace;
    }
}
