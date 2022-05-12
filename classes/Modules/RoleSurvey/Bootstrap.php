<?php
declare(strict_types=1);

namespace Xentral\Modules\RoleSurvey;


use Xentral\Core\DependencyInjection\ContainerInterface;

final class Bootstrap
{
    /**
     * @return array
     */
    public static function registerServices(): array
    {
        return [
            'SurveyGateway'         => 'onInitSurveyGateway',
            'SurveyService'         => 'onInitSurveyService',
        ];
    }

    /**
     * @return array
     */
    public static function registerJavascript(): array
    {
        return [
            'RoleSurvey' => [
                './classes/Modules/RoleSurvey/www/js/RoleSurvey.js',
            ],
        ];
    }

    /**
     * @return array
     */
   public static function registerStylesheets(): array
    {
        return [
            'RoleSurvey' => [
                './classes/Modules/RoleSurvey/www/css/RoleSurvey.css',
            ],
        ];
    }

    /**
     * @param ContainerInterface $container
     *
     * @return SurveyService
     */
    public function onInitSurveyService(ContainerInterface $container): SurveyService
    {
        return new SurveyService($container->get('Database'), $container->get('SurveyGateway'));
    }

    /**
     * @param ContainerInterface $container
     *
     * @return SurveyGateway
     */
    public function onInitSurveyGateway(ContainerInterface $container): SurveyGateway
    {
        return new SurveyGateway($container->get('Database'));
    }
}
