<?php

namespace Xentral\Modules\Postat\SOAP;

use Xentral\Components\Logger\Logger;

/**
 * Factory for new SoapService instances.
 *
 * A dedicated factory is needed because the configuration for the SOAP API
 * service depends on the context where it is being used, and therefore the
 * configuration cannot be injected directly into it by the DI container.
 */
class SoapServiceFactory
{
    private $logger;

    public function __construct(Logger $logger)
    {
        $this->logger = $logger;
    }

    /**
     * Create a new Post.at SOAP API service from the given config array.
     *
     * @param array $soapConfig
     *
     * @throws PostAtException
     *
     * @return SoapService
     */
    public function fromConfigArray(array $soapConfig): SoapService
    {
        $soapConfig = new Config($soapConfig);

        $client = new PostAtClient($soapConfig, $this->logger);

        return new SoapService($client);
    }
}
