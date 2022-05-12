<?php

namespace Xentral\Modules\Resubmission;

use Xentral\Core\DependencyInjection\ContainerInterface;
use Xentral\Modules\Resubmission\Service\ResubmissionGateway;
use Xentral\Modules\Resubmission\Service\ResubmissionTaskGateway;
use Xentral\Modules\Resubmission\Service\ResubmissionTaskService;
use Xentral\Modules\Resubmission\Service\ResubmissionTextFieldGateway;
use Xentral\Modules\Resubmission\Service\ResubmissionTextFieldService;
use Xentral\Modules\Resubmission\Service\ResubmissionTaskTemplateService;
use Xentral\Modules\Resubmission\Service\ResubmissionTaskTemplateGateway;

class Bootstrap
{
    /**
     * @return array
     */
    public static function registerServices()
    {
        return [
            'ResubmissionGateway'          => 'onInitResubmissionGateway',
            'ResubmissionTaskService'      => 'onInitResubmissionTaskService',
            'ResubmissionTaskGateway'      => 'onInitResubmissionTaskGateway',
            'ResubmissionTextFieldService' => 'onInitResubmissionTextFieldService',
            'ResubmissionTextFieldGateway' => 'onInitResubmissionTextFieldGateway',
            'ResubmissionTaskTemplateService' => 'onInitResubmissionTaskTemplateService',
            'ResubmissionTaskTemplateGateway' => 'onInitResubmissionTaskTemplateGateway',
        ];
    }

    /**
     * @param ContainerInterface $container
     *
     * @return ResubmissionGateway
     */
    public static function onInitResubmissionGateway(ContainerInterface $container)
    {
        return new ResubmissionGateway($container->get('Database'));
    }

    /**
     * @param ContainerInterface $container
     *
     * @return ResubmissionTaskService
     */
    public static function onInitResubmissionTaskService(ContainerInterface $container)
    {
        return new ResubmissionTaskService(
            $container->get('Database'),
            $container->get('ResubmissionTaskGateway'),
            $container->get('ResubmissionGateway')
        );
    }

    /**
     * @param ContainerInterface $container
     *
     * @return ResubmissionTaskGateway
     */
    public static function onInitResubmissionTaskGateway(ContainerInterface $container)
    {
        return new ResubmissionTaskGateway(
            $container->get('Database'),
            $container->get('ResubmissionGateway')
        );
    }

    /**
     * @param ContainerInterface $container
     *
     * @return ResubmissionTextFieldService
     */
    public static function onInitResubmissionTextFieldService(ContainerInterface $container)
    {
        return new ResubmissionTextFieldService(
            $container->get('Database'),
            $container->get('ResubmissionTextFieldGateway'),
            $container->get('ResubmissionGateway')
        );
    }

    /**
     * @param ContainerInterface $container
     *
     * @return ResubmissionTextFieldGateway
     */
    public static function onInitResubmissionTextFieldGateway(ContainerInterface $container)
    {
        return new ResubmissionTextFieldGateway(
            $container->get('Database'),
            $container->get('ResubmissionGateway')
        );
    }

  /**
   * @param ContainerInterface $container
   *
   * @return ResubmissionTaskTemplateService
   */
  public static function onInitResubmissionTaskTemplateService(ContainerInterface $container)
  {
    return new ResubmissionTaskTemplateService(
      $container->get('Database'),
      $container->get('ResubmissionTaskTemplateGateway'),
      $container->get('ResubmissionGateway')
    );
  }

  /**
   * @param ContainerInterface $container
   *
   * @return ResubmissionTaskTemplateGateway
   */
  public static function onInitResubmissionTaskTemplateGateway(ContainerInterface $container)
  {
    return new ResubmissionTaskTemplateGateway(
      $container->get('Database'),
      $container->get('ResubmissionGateway')
    );
  }


}
