<?php

declare(strict_types=1);

namespace Xentral\Modules\Postat\SOAP\Method;

use Xentral\Modules\Postat\SOAP\MethodInterface;
use Xentral\Modules\Postat\SOAP\ParameterInterface;
use Xentral\Modules\Postat\SOAP\PostAtClient;
use Xentral\Modules\Postat\SOAP\PostAtException;

class ImportShipment implements MethodInterface
{
    /** @var PostAtClient */
    private $postAtClient;

    /** @var array */
    private $shipmentRow;

    /**
     * ImportShipment constructor.
     *
     * @param PostAtClient $postAtClient
     */
    public function __construct(PostAtClient $postAtClient)
    {
        $this->postAtClient = $postAtClient;
    }

    /**
     * Call the SOAP API with the given data.
     *
     * @param ParameterInterface $shipmentRow
     *
     * @throws PostAtException
     *
     * @return array $data
     */
    public function call(ParameterInterface $shipmentRow)
    {
        $this->shipmentRow = $shipmentRow->getData();

        return $this->postAtClient->call($this);
    }

    /**
     * @return array
     */
    public function getBody(): array
    {
        return $this->shipmentRow;
    }
}
