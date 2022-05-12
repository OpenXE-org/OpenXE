<?php

namespace Xentral\Modules\Dhl\Response;

use Xentral\Modules\Dhl\Exception\DhlBaseException;
use Xentral\Modules\Dhl\Exception\InvalidRequestDataException;

/**
 * Class CreateShipmentResponse
 *
 * @package Xentral\Modules\Dhl\Response
 */
class GetVersionResponse extends BaseResponse
{
    /**
     * GetVersionResponse constructor.
     *
     */
    public function __construct()
    {
    }

    /**
     * @param string $responseXml
     *
     * @return GetVersionResponse
     */
    public static function fromResponseXml($responseXml)
    {
        $xmlElement = parent::createXmlElement($responseXml);

        return new GetVersionResponse();
    }
}
