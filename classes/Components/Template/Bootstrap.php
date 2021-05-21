<?php

namespace Xentral\Components\Template;

use Config;
use Smarty;
use SmartyException;
use Xentral\Components\Template\SmartyPlugin\EscapePlugin;
use Xentral\Components\Template\SmartyPlugin\TranslationPlugin;
use Xentral\Core\DependencyInjection\ContainerInterface;

final class Bootstrap
{
    /**
     * @return array
     */
    public static function registerServices()
    {
        return [
            'Template'     => 'onInitTemplate',
            'SmartyFacade' => 'onInitSmartyFacade',
        ];
    }

    /**
     * @param ContainerInterface $container
     *
     * @return Template
     */
    public static function onInitTemplate(ContainerInterface $container)
    {
        return new Template($container->get('SmartyFacade'), $container->get('LegacyApplication')->Tpl);
    }

    /**
     * @todo Template-Konfiguration einstellbar machen
     * @todo Code eventuell in Factory auslagern
     *
     * @throws SmartyException
     *
     * @return SmartyFacade
     */
    public static function onInitSmartyFacade()
    {
        $config = new Config();
        $userdataDir = $config->WFuserdata !== null
            ? $config->WFuserdata
            : dirname(dirname(dirname(__DIR__))) . '/userdata';

        $smarty = new Smarty();
        $smarty->setCompileDir(realpath($userdataDir) . '/tmp/templates_c');
        $smarty->setCaching(false);
        $smarty->setDebugging(true);
        $smarty->setEscapeHtml(false);
        $smarty->setCompileCheck(true); // @todo Kann deaktiviert werden auf Produktivsystemen
        $smarty->setDebugTemplate(__DIR__ . '/templates/debug.tpl');
        $smarty->setTemplateDir(__DIR__ . '/templates');
        $smarty->addTemplateDir(dirname(dirname(__DIR__)), 'classes');

        /** @see https://www.smarty.net/docs/en/advanced.features.tpl#advanced.features.security */
        $smarty->enableSecurity(); // @todo Eigene Security-Klasse definieren

        $translationPlugin = new TranslationPlugin();
        $smarty->registerPlugin('function', 'namespace', [$translationPlugin, 'compileNamespaceFunction']);
        $smarty->registerPlugin('block', 'translate', [$translationPlugin, 'compileTranslateBlock']);

        $escapePlugin = new EscapePlugin(); // @todo Escape Json, Javascript, Mail, Unescape
        $smarty->registerPlugin('block', 'escape', [$escapePlugin, 'compileEscapeBlock']);
        $smarty->registerPlugin('block', 'escapeHtml', [$escapePlugin, 'compileEscapeHtmlBlock']);
        $smarty->registerPlugin('modifier', 'escape', [$escapePlugin, 'compileEscapeModifier']);
        $smarty->registerPlugin('modifier', 'escapeEntities', [$escapePlugin, 'compileEscapeEntitiesModifier']);
        $smarty->registerPlugin('modifier', 'escapeQuotes', [$escapePlugin, 'compileEscapeQuotesModifier']);
        $smarty->registerPlugin('modifier', 'escapeHtml', [$escapePlugin, 'compileEscapeHtmlModifier']);
        $smarty->registerPlugin('modifier', 'escapeUrl', [$escapePlugin, 'compileEscapeUrlModifier']);

        //$smartyDebug = new Smarty_Internal_Debug();
        //$smartyDebug->display_debug($smarty, true);

        return new SmartyFacade($smarty);
    }
}
