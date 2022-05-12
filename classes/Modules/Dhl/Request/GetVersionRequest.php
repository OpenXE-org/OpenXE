<?php

namespace Xentral\Modules\Dhl\Request;

/**
 * Class CreateShipmentRequest
 *
 * @package Xentral\Modules\Dhl\Request
 */
class GetVersionRequest
{

    private $major;
    private $minor;

    public function __construct(
        $major,
        $minor
    ) {
        $this->major = $major;
        $this->minor = $minor;
    }

    /**
     * @param string $username
     * @param string $password
     *
     * @return string
     */
    public function toXml($username, $password)
    {
        $payload = <<<XML
<soapenv:Envelope 
xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/" 
xmlns:ns="http://dhl.de/webservices/businesscustomershipping/3.0"
xmlns:cis="http://dhl.de/webservice/cisbase">
   <soapenv:Header>
      <cis:Authentification>
         <cis:user>{$username}</cis:user>
         <cis:signature>{$password}</cis:signature>
      </cis:Authentification>
   </soapenv:Header>
   <soapenv:Body>
      <ns:Version>
         <majorRelease>{$this->major}</majorRelease>
         <minorRelease>{$this->minor}</minorRelease>
      </ns:Version>
   </soapenv:Body>
</soapenv:Envelope>
XML;

        return $payload;
    }
}
