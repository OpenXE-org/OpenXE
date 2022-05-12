<?php

namespace Xentral\Modules\Dhl\Request;

/**
 * Class CreateShipmentRequest
 *
 * @package Xentral\Modules\Dhl\Request
 */
class CreateNationalShipmentRequest
{
    /** @var string */
    protected $product;
    /** @var string */
    protected $shipmentDate;
    /** @var float */
    protected $weight;
    /** @var float */
    protected $length;
    /** @var float */
    protected $width;
    /** @var float */
    protected $height;
    /** @var string */
    protected $name1;
    /** @var string */
    protected $name2;
    /** @var string */
    protected $name3;
    /** @var string */
    protected $street;
    /** @var string */
    protected $streetNo;
    /** @var string */
    protected $zip;
    /** @var string */
    protected $city;
    /** @var string */
    protected $country;
    /** @var string */
    protected $email;
    /** @var boolean */
    protected $printOnlyIfCodeable;

    /**
     * CreateShipmentRequest constructor.
     *
     * @param string  $shipmentDate
     * @param float   $weight
     * @param float   $length
     * @param float   $width
     * @param float   $height
     * @param string  $name1
     * @param string  $name2
     * @param string  $name3
     * @param string  $street
     * @param string  $streetNo
     * @param string  $zip
     * @param string  $city
     * @param string  $country
     * @param string  $email
     * @param boolean $printOnlyIfCodeable
     */
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
        $printOnlyIfCodeable
    ) {
        $this->product = 'V01PAK';
        $this->shipmentDate = $shipmentDate;
        $this->weight = $weight;
        $this->length = $length;
        $this->width = $width;
        $this->height = $height;
        $this->name1 = $name1;
        $this->name2 = $name2;
        $this->name3 = $name3;
        $this->street = $street;
        $this->streetNo = $streetNo;
        $this->zip = $zip;
        $this->city = $city;
        $this->country = $country;
        $this->email = $email;
        $this->printOnlyIfCodeable = $printOnlyIfCodeable;
    }

    /**
     * @param string $username
     * @param string $password
     * @param string $accountNumber
     * @param string $senderName
     * @param string $senderStreetName
     * @param string $senderStreetNo
     * @param string $senderZip
     * @param string $senderCity
     * @param string $senderCountry
     * @param string $senderEmail
     *
     * @return string
     */
    public function toXml($username, $password, $accountNumber, $senderName, $senderStreetName, $senderStreetNo, $senderZip, $senderCity, $senderCountry, $senderEmail)
    {
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
