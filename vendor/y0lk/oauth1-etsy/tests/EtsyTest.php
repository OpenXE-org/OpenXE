<?php
namespace Y0lk\OAuth1\Client\Test\Server;

use Y0lk\OAuth1\Client\Server\Etsy;
use PHPUnit\Framework\TestCase;

class EtsyTest extends TestCase
{
    public function testImplementsInterfaceMethods()
    {
        $client = new Etsy($this->getMockClientCredentials());
        $this->assertInternalType('string', $client->urlTemporaryCredentials());
        $this->assertInternalType('string', $client->urlAuthorization());
        $this->assertInternalType('string', $client->urlTokenCredentials());
        $this->assertInternalType('string', $client->urlUserDetails());
    }
    
	protected function getMockClientCredentials()
    {
        return array(
            'identifier' => 'myidentifier',
            'secret' => 	'mysecret',
            'scope' => 		'email_r',
            'callback_uri' => 'http://app.dev/'
        );
    }
}