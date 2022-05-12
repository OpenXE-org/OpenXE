<?php

namespace Xentral\Modules\Dhl\Request;

use Xentral\Modules\Dhl\Content\PackageContent;
use Xentral\Modules\Dhl\Exception\UnknownProductTypeException;

/**
 * Class CreateShipmentRequest
 *
 * @package Xentral\Modules\Dhl\Request
 */
class CreateInterationalShipmentRequest extends CreateNationalShipmentRequest
{
    /** @var string */
    private $productType;
    /** @var string */
    private $productTypeDescription;

    /** @var PackageContent[] */
    private $packageContents;

    public function __construct(
        $shipmentDate,
        $weight,
        $length,
        $width,
        $height,
        $name1,
        $name2,
        $name3,
        $street,
        $streetNo,
        $zip,
        $city,
        $country,
        $email,
        $printOnlyIfCodeable,
        $productType,
        $productTypeDescription,
        $packageContents
    ) {
        parent::__construct(
            $shipmentDate,
            $weight,
            $length,
            $width,
            $height,
            $name1,
            $name2,
            $name3,
            $street,
            $streetNo,
            $zip,
            $city,
            $country,
            $email,
            $printOnlyIfCodeable
        );
        $this->product = 'V53WPAK';
        $this->productType = $productType;
        $this->productTypeDescription = $productTypeDescription;
        $this->packageContents = $packageContents;
    }


    /**
     * @param $username
     * @param $password
     * @param $accountNumber
     * @param $senderName
     * @param $senderStreetName
     * @param $senderStreetNo
     * @param $senderZip
     * @param $senderCity
     * @param $senderCountry
     * @param $senderEmail
     *
     * @return string
     */
    public
    function toXml(
        $username,
        $password,
        $accountNumber,
        $senderName,
        $senderStreetName,
        $senderStreetNo,
        $senderZip,
        $senderCity,
        $senderCountry,
        $senderEmail
    ) {
        if (!in_array($this->productType, ['OTHER', 'PRESENT', 'COMMERCIAL_SAMPLE', 'DOCUMENT', 'RETURN_OF_GOODS'])) {
            throw UnknownProductTypeException::fromValid(
                "'OTHER', 'PRESENT', 'COMMERCIAL_SAMPLE', 'DOCUMENT', 'RETURN_OF_GOODS'"
            );
        }
        $productTypeDescriptionXml = '';
        if ($this->productType == 'OTHER') {
            if (empty($this->productTypeDescription)) {
                throw UnknownProductTypeException::invalidDescription();
            }
            $productTypeDescriptionXml = "<exportTypeDescription>{$this->productTypeDescription}</exportTypeDescription>";
        }

        $contentsRoot = new \SimpleXMLElement('<root></root>');

        foreach ($this->packageContents as $packageContent){
            $contentRoot = $contentsRoot->addChild('ExportDocPosition');
            $contentRoot->addChild('description', $packageContent->getDescription());
            $contentRoot->addChild('countryCodeOrigin', $packageContent->getCountryOfOrigin());
            $contentRoot->addChild('customsTariffNumber', $packageContent->getCustomsTariffNumber());
            $contentRoot->addChild('amount', $packageContent->getAmount());
            $contentRoot->addChild('netWeightInKG', $packageContent->getWeightInKg());
            $contentRoot->addChild('customsValue', $packageContent->getValue());
        }

        $contentsXml = '';
        foreach ($contentsRoot->children() as $child){
            $contentsXml .= $child->asXml();
        }

        $printOnlyIfCodeableActive = $this->printOnlyIfCodeable ? '1' : '0';
        $payload = <<<XML
<soapenv:Envelope xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/" xmlns:cis="http://dhl.de/webservice/cisbase" xmlns:ns="http://dhl.de/webservices/businesscustomershipping/3.0">
   <soapenv:Header>
      <cis:Authentification>
         <cis:user>{$username}</cis:user>
         <cis:signature>{$password}</cis:signature>
      </cis:Authentification>
   </soapenv:Header>
   <soapenv:Body>
      <ns:CreateShipmentOrderRequest>
         <ns:Version>
            <majorRelease>3</majorRelease>
            <minorRelease>0</minorRelease>
            <build>1</build>
         </ns:Version>
         <ShipmentOrder>
            <sequenceNumber>?</sequenceNumber>
            <Shipment>
               <ShipmentDetails>
                  <product>{$this->product}</product>
                  <cis:accountNumber>{$accountNumber}</cis:accountNumber>
                  <customerReference/>
                  <shipmentDate>{$this->shipmentDate}</shipmentDate>
                  <costCentre/>
                  <ShipmentItem>
                     <weightInKG>{$this->weight}</weightInKG>
                     <lengthInCM>{$this->length}</lengthInCM>
                     <widthInCM>{$this->width}</widthInCM>
                     <heightInCM>{$this->height}</heightInCM>
                  </ShipmentItem>
                  <Service>
                  </Service>
                  <Notification>
                     <recipientEmailAddress/>
                  </Notification>
                  </ShipmentDetails>
               <Shipper>
                  <Name>
                     <cis:name1>{$senderName}</cis:name1>
                  </Name>
                  <Address>
                     <cis:streetName>{$senderStreetName}</cis:streetName>
                     <cis:streetNumber>{$senderStreetNo}</cis:streetNumber>
                     <cis:addressAddition/>
                     <cis:dispatchingInformation/>
                     <cis:zip>{$senderZip}</cis:zip>
                     <cis:city>{$senderCity}</cis:city>
                     <cis:province/>
                     <cis:Origin>
                        <cis:countryISOCode>{$senderCountry}</cis:countryISOCode>
                        <cis:state/>
                     </cis:Origin>
                  </Address>
                  <Communication>
                     <cis:phone/>
                     <cis:email>{$senderEmail}</cis:email>
                     <cis:contactPerson/>
                  </Communication>
               </Shipper>
               
               <Receiver>
                  <cis:name1>{$this->name1}</cis:name1>
                  <Address>
                     <cis:name2>{$this->name2}</cis:name2>
                     <cis:name3>{$this->name3}</cis:name3>
                     <cis:streetName>{$this->street}</cis:streetName>
                     <cis:streetNumber>{$this->streetNo}</cis:streetNumber>
                     <cis:addressAddition/>
                     <cis:dispatchingInformation/>
                     <cis:zip>{$this->zip}</cis:zip>
                     <cis:city>{$this->city}</cis:city>
                     <cis:province/>
                     <cis:Origin>
                        <cis:countryISOCode>{$this->country}</cis:countryISOCode>
                        <cis:state/>
                     </cis:Origin>
                  </Address>
                  <Communication>
                     <cis:phone/>
                     <cis:email>{$this->email}</cis:email>
                     <cis:contactPerson/>
                  </Communication>
               </Receiver>
               <ExportDocument>
                  <exportType>{$this->productType}</exportType>
                  {$productTypeDescriptionXml}
                  <placeOfCommital>{$senderCity}</placeOfCommital>
                  <additionalFee>0</additionalFee>
                  {$contentsXml}
               </ExportDocument>
               </Shipment>
            <PrintOnlyIfCodeable active="{$printOnlyIfCodeableActive}"/>
         </ShipmentOrder>
         <labelResponseType>B64</labelResponseType>
      </ns:CreateShipmentOrderRequest>
   </soapenv:Body>
</soapenv:Envelope>
XML;

        return $payload;
    }
}
