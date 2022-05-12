<?php

namespace Xentral\Modules\Wizard;

use Xentral\Core\DependencyInjection\ContainerInterface;

final class Bootstrap
{
    /**
     * @return array
     */
    public static function registerServices()
    {
        return [
            'WizardService' => 'onInitWizardService',
        ];
    }

    /**
     * @return array
     */
    public static function registerJavascript()
    {
        return [
            'wizard' => [
                './classes/Modules/Wizard/www/js/wizard.js',
            ],
        ];
    }

    /**
     * @return array
     */
    public static function registerStylesheets()
    {
        return [
            'wizard' => [
                './classes/Modules/Wizard/www/css/wizard.css',
            ],
        ];
    }

    /**
     * @param ContainerInterface $container
     *
     * @return WizardService
     */
    public static function onInitWizardService(ContainerInterface $container)
    {
        /** @var \ApplicationCore $app */
        $app = $container->get('LegacyApplication');

        return new WizardService(
            $container->get('Database'),
            $container->get('UserConfigService'),
            $app->erp
        );
    }
}
