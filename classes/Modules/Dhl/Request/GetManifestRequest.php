<?php

namespace Xentral\Modules\Dhl\Request;

/**
 * Class GetManifestRequest
 *
 * @package Xentral\Modules\Dhl\Request
 */
class GetManifestRequest
{

    private $manifestDate;

    public function __construct(
        $manifestDate
    ) {
        $this->manifestDate = $manifestDate;
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
         <majorRelease>3</majorRelease>
         <minorRelease>0</minorRelease>
      </ns:Version>
      <manifestDate>$this->manifestDate</manifestDate>
   </soapenv:Body>
</soapenv:Envelope>
XML;

        return $payload;
    }
}
