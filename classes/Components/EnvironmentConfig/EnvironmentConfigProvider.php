<?php

namespace Xentral\Components\EnvironmentConfig;

use Config;
use License;

final class EnvironmentConfigProvider
{
    /** @var Config $config */
    private $config;

    /**
     * @param Config  $config
     */
    public function __construct(Config $config)
    {
        $this->config = $config;
    }

    /**
     * @return EnvironmentConfig
     */
    public function createEnvironmentConfig(): EnvironmentConfig
    {
        $environmentConfig = new EnvironmentConfig(
            $this->config->WFdbhost, $this->config->WFdbname, $this->config->WFdbuser,
            $this->config->WFdbpass, $this->config->WFdbport, $this->config->WFuserdata
        );

        return $environmentConfig;
    }
}
