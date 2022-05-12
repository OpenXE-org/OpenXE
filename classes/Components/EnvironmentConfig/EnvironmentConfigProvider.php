<?php

namespace Xentral\Components\EnvironmentConfig;

use Config;
use License;

final class EnvironmentConfigProvider
{
    /** @var License $license */
    private $license;

    /** @var Config $config */
    private $config;

    /**
     * @param License $license
     * @param Config  $config
     */
    public function __construct(License $license, Config $config)
    {
        $this->license = $license;
        $this->config = $config;
    }

    /**
     * @return EnvironmentConfig
     */
    public function createEnvironmentConfig(): EnvironmentConfig
    {
        $environmentConfig = new EnvironmentConfig(
            $this->config->WFdbhost, $this->config->WFdbname, $this->config->WFdbuser,
            $this->config->WFdbpass, $this->config->WFdbport, $this->config->WFuserdata,
            (array)$this->license->getProperties()
        );

        return $environmentConfig;
    }
}
