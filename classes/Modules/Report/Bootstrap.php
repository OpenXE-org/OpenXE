<?php

namespace Xentral\Modules\Report;

use ApplicationCore;
use Xentral\Core\DependencyInjection\ContainerInterface;
use Xentral\Modules\Report\Service\ReportColumnFormatter;

final class Bootstrap
{
    /**
     * @return array
     */
    public static function registerServices()
    {
        return [
            'ReportGateway' => 'onInitReportGateway',
            'ReportService' => 'onInitReportService',
            'ReportCsvExportService' => 'onInitReportCsvExportService',
            'ReportPdfExportService' => 'onInitReportPdfExportService',
            'ReportJsonExportService' => 'onInitReportJsonExportService',
            'ReportJsonImportService' => 'onInitReportJsonImportService',
            'ReportLegacyConverterService' => 'onInitReportLegacyConverterService',
            'ReportResolveParameterService' => 'onInitReportResolveParameterService',
            'ReportChartService' => 'onInitReportChartService',
            'ReportColumnFormatter' => 'onInitReportColumnFormatter',
        ];
    }

    /**
     * @param ContainerInterface $container
     *
     * @return ReportGateway
     */
    public static function onInitReportGateway(ContainerInterface $container)
    {
        return new ReportGateway($container->get('Database'));
    }

    /**
     * @param ContainerInterface $container
     *
     * @return ReportService
     */
    public static function onInitReportService(ContainerInterface $container)
    {
        return new ReportService(
            $container->get('Database'),
            $container->get('ReportGateway'),
            $container->get('ReportResolveParameterService')
        );
    }

    /**
     * @param ContainerInterface $container
     *
     * @return ReportCsvExportService
     */
    public static function onInitReportCsvExportService(ContainerInterface $container)
    {
        /** @var ApplicationCore $app */
        $app = $container->get('LegacyApplication');

        return new ReportCsvExportService(
            $container->get('Database'),
            $container->get('ReportGateway'),
            $container->get('ReportService'),
            $app->erp->GetTMP()
        );
    }

    /**
     * @param ContainerInterface $container
     *
     * @return ReportPdfExportService
     */
    public static function onInitReportPdfExportService(ContainerInterface $container)
    {
        /** @var ApplicationCore $app */
        $app = $container->get('LegacyApplication');

        return new ReportPdfExportService(
            $container->get('Database'),
            $container->get('ReportGateway'),
            $container->get('ReportService'),
            $app->erp->GetTMP()
        );
    }

    /**
     * @param ContainerInterface $container
     *
     * @return ReportJsonExportService
     */
    public static function onInitReportJsonExportService(ContainerInterface $container)
    {
        /** @var ApplicationCore $app */
        $app = $container->get('LegacyApplication');

        return new ReportJsonExportService(
            $container->get('Database'),
            $container->get('ReportGateway'),
            $container->get('ReportService'),
            $app->erp->GetTMP()
        );
    }

    /**
     * @param ContainerInterface $container
     *
     * @return ReportJsonImportService
     */
    public static function onInitReportJsonImportService(ContainerInterface $container)
    {
        return new ReportJsonImportService(
            $container->get('ReportGateway'),
            $container->get('ReportService')
        );
    }

    /**
     * @param ContainerInterface $container
     *
     * @return ReportLegacyConverterService
     */
    public static function onInitReportLegacyConverterService(ContainerInterface $container)
    {
        return new ReportLegacyConverterService(
            $container->get('Database'),
            $container->get('ReportService')
        );
    }

    /**
     * @param ContainerInterface $container
     *
     * @return ReportResolveParameterService
     */
    public static function onInitReportResolveParameterService(ContainerInterface $container)
    {
        /** @var ApplicationCore $app */
        $app = $container->get('LegacyApplication');
        $userId = (int)$app->User->GetID();
        $userProjects = $app->User->getUserProjects();
        $userAdmin = $app->User->GetType() === 'admin';

        return new ReportResolveParameterService(
            $userId,
            $userProjects,
            $userAdmin
        );
    }

    /**
     * @param ContainerInterface $container
     *
     * @return ReportChartService
     */
    public function onInitReportChartService(ContainerInterface $container)
    {
        return new ReportChartService(
            $container->get('Database'),
            $container->get('ReportService'),
            $container->get('ReportGateway')
        );
    }

    /**
     * @param ContainerInterface $container
     *
     * @return ReportColumnFormatter
     */
    public function onInitReportColumnFormatter(ContainerInterface $container): ReportColumnFormatter
    {
        return new ReportColumnFormatter();
    }
}
