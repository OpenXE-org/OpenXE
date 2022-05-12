<?php

namespace Xentral\Modules\Dhl\Response;

use Xentral\Modules\Dhl\Exception\DhlBaseException;
use Xentral\Modules\Dhl\Exception\InvalidRequestDataException;

/**
 * Class CreateShipmentResponse
 *
 * @package Xentral\Modules\Dhl\Response
 */
class CreateShipmentResponse extends BaseResponse
{
    /** @var string */
    private $label;

    /** @var string */
    private $shipmentNumer;

    /** @var string */
    private $exportDocument;

    /**
     * CreateShipmentResponse constructor.
     *
     * @param string $label
     * @param string $shipmentNumber
     */
    public function __construct($label, $shipmentNumber)
    {
        $this->label = $label;
        $this->shipmentNumer = $shipmentNumber;
    }

    /**
     * @param string $exportDocument
     */
    public function setExportDocument($exportDocument)
    {
        $this->exportDocument = $exportDocument;
    }

    /**
     * @param string $responseXml
     *
     * @return CreateShipmentResponse
     */
    public static function fromResponseXml($responseXml)
    {
        $xmlElement = parent::createXmlElement($responseXml);

        $label = $xmlElement->xpath(
            '/soap:Envelope/soap:Body/bcs:CreateShipmentOrderResponse/CreationState/LabelData/labelData'
        );
        $shipmentNumber = $xmlElement->xpath(
            '/soap:Envelope/soap:Body/bcs:CreateShipmentOrderResponse/CreationState/shipmentNumber'
        );
        $exportDoc = $xmlElement->xpath(
            '/soap:Envelope/soap:Body/bcs:CreateShipmentOrderResponse/CreationState/LabelData/exportLabelData'
        );

        $response = new CreateShipmentResponse(
            base64_decode((string)$label[0]),
            (string)$shipmentNumber[0]
        );

        if (!empty($exportDoc)) {
            $response->setExportDocument(base64_decode((string)$exportDoc[0]));
        }

        return $response;
    }

    /**
     * @return string
     */
    public function getShipmentNumber()
    {
        return $this->shipmentNumer;
    }

    /**
     * @return bool
     */
    public function containsExportDocuments()
    {
        return !empty($this->exportDocument);
    }

    /**
     * @return string downloaded pdf as string
     */
    public function getExportPaperAsPdf()
    {
        return $this->exportDocument;
    }


    /**
     * @return string downloaded pdf as string
     */
    public function getLabelAsPdf()
    {
        return $this->label;
    }
}
