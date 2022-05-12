<?php

namespace Xentral\Modules\Dhl\Response;

use phpDocumentor\Reflection\DocBlock\Tags\Param;
use Xentral\Modules\Dhl\Exception\DhlBaseException;
use Xentral\Modules\Dhl\Exception\InvalidRequestDataException;

/**
 * Class GetManifestResponse
 *
 * @package Xentral\Modules\Dhl\Response
 */
class GetManifestResponse extends BaseResponse
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
     * @return GetManifestResponse
     */
    public static function fromResponseXml($responseXml)
    {
        $xmlElement = parent::createXmlElement($responseXml);

        return new GetManifestResponse();
    }
}
