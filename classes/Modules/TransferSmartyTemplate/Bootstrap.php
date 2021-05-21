<?php

namespace Xentral\Modules\TransferSmartyTemplate;

use ApplicationCore;
use RuntimeException;
use Smarty;
use SmartyException;
use Xentral\Core\DependencyInjection\ContainerInterface;
use Xentral\Modules\TransferSmartyTemplate\Smarty\SmartyWrapper;
use Xentral\Modules\TransferSmartyTemplate\Smarty\SmartyTemplateHelper;
use Xentral\Modules\TransferSmartyTemplate\Smarty\SmartySecurity;

final class Bootstrap
{
    /**
     * @return array
     */
    public static function registerServices()
    {
        return [
            'TransferSmartyTemplate' => 'onInitTransferSmartyTemplate',
        ];
    }

    /**
     * @param ContainerInterface $container
     *
     * @throws SmartyException
     * @throws RuntimeException
     *
     * @return TransferSmartyTemplate
     */
    public static function onInitTransferSmartyTemplate(ContainerInterface $container)
    {
        /** @var ApplicationCore $app */
        $app = $container->get('LegacyApplication');
        $userdataTempDir = $app->erp->GetTMP();
        $customBaseDir = realpath($userdataTempDir) . '/Modules/TransferSmartyTemplate';

        $compileDir = $customBaseDir . '/templates_c';
        $defaultTemplateDir = __DIR__ . '/templates/default';

        $transferTemplate = new TransferSmartyTemplate(self::onInitSmartyFacade($compileDir));
        $transferTemplate->addTemplateDir('default', $defaultTemplateDir);

        return $transferTemplate;
    }

    /**
     * @param string $compileDir
     * @param array  $templateDirs
     *
     * @throws SmartyException
     *
     * @return SmartyWrapper
     */
    public static function onInitSmartyFacade($compileDir, array $templateDirs = [])
    {
        $smarty = new Smarty();
        $smarty->setCaching(false);
        $smarty->setDebugging(false);
        $smarty->setEscapeHtml(false);
        $smarty->setCompileCheck(true);
        $smarty->setTemplateDir($templateDirs);
        $smarty->setCompileDir($compileDir);

        // Security-Settings
        $security = new SmartySecurity($smarty);
        $smarty->enableSecurity($security);

        /**
         * Template-Funktionen registrieren
         */
        $helper = new SmartyTemplateHelper();

        // XML
        $smarty->registerPlugin('block', 'cdata', [$helper, 'compileBlockCdata']);
        $smarty->registerPlugin('block', 'escapeXml', [$helper, 'compileBlockEscapeXml']);
        $smarty->registerPlugin('block', 'error', [$helper, 'compileBlockError']);
        $smarty->registerPlugin('modifier', 'cdata', [$helper, 'compileModifierCdata']);
        $smarty->registerPlugin('modifier', 'escapeXml', [$helper, 'compileModifierEscapeXml']);
        $smarty->registerPlugin('modifier', 'error', [$helper, 'compileModifierError']);

        // CSV
        $smarty->registerPlugin('modifier', 'quoteCsv', [$helper, 'compileModifierQuoteCsv']);

        // HTML+URL
        $smarty->registerPlugin('modifier', 'br2nl', [$helper, 'compileModifierBr2Nl']);
        $smarty->registerPlugin('modifier', 'encodeUrl', [$helper, 'compileModifierEncodeUrl']);
        $smarty->registerPlugin('modifier', 'decodeUrl', [$helper, 'compileModifierDecodeUrl']);
        $smarty->registerPlugin('modifier', 'decodeHtmlEntities', [$helper, 'compileModifierDecodeHtmlEntities']);
        $smarty->registerPlugin('modifier', 'decodeHtmlSpecialChars', [$helper, 'compileModifierDecodeHtmlSpecialChars']);

        // Common
        $smarty->registerPlugin('modifier', 'replaceLineBreaks', [$helper, 'compileModifierReplaceLineBreaks']);
        $smarty->registerPlugin('modifier', 'dump', [$helper, 'compileModifierDumpVariable']);

        return new SmartyWrapper($smarty);
    }
}
