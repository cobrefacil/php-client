<?php

namespace CobreFacil\Resources;

use CobreFacil\BaseTest;
use CobreFacil\CobreFacil;
use CobreFacil\Exceptions\InvalidCredentialsException;
use Throwable;

class AuthenticationTest extends BaseTest
{
    /**
     * @dataProvider dataProvider
     */
    public function testAuthenticateWithValidCredentials($appId, $secret, $httpClient)
    {
        $cobrefacil = new CobreFacil($appId, $secret);
        $cobrefacil->setProduction(false);
        if ($httpClient) {
            $cobrefacil->setHttpClient($httpClient);
        }
        $this->assertNotNull($cobrefacil->getValidToken());
    }

    public function testErrorOnAuthenticateWithInvalidCredentials()
    {
        try {
            $appId = 'invalid';
            $secret = 'invalid';
            $httpClient = $this->createHttpClient();
            (new CobreFacil($appId, $secret))
                ->setProduction(false)
                ->setHttpClient($httpClient)
                ->getValidToken();
        } catch (Throwable $e) {
            $this->assertInstanceOf(InvalidCredentialsException::class, $e);
        }
    }

    public function dataProvider()
    {
        $appId = $_ENV['APP_ID'];
        $secret = $_ENV['SECRET'];
        $httpClient = $this->createHttpClient();
        return [
            'development' => [$appId, $secret, $httpClient],
            'sandbox' => [$appId, $secret, null],
        ];
    }
}
