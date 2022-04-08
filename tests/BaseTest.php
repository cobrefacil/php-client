<?php

namespace CobreFacil;

use Dotenv\Dotenv;
use GuzzleHttp\Client;
use GuzzleHttp\ClientInterface;
use PHPUnit\Framework\TestCase;

abstract class BaseTest extends TestCase
{
    /** @var CobreFacil */
    protected $cobreFacil;

    public function __construct($name = null, array $data = [], $dataName = '')
    {
        parent::__construct($name, $data, $dataName);
        Dotenv::createImmutable(__DIR__ . '/..')->load();
        CobreFacil::$production = false;
    }

    protected function setUp()
    {
        $this->cobreFacil = $this->createCobreFacilClient();
    }

    protected function createCobreFacilClient(): CobreFacil
    {
        CobreFacil::$production = false;
        $appId = $_ENV['APP_ID'];
        $secret = $_ENV['SECRET'];
        $httpClient = $this->createHttpClient();
        return new CobreFacil($appId, $secret, $httpClient);
    }

    protected function createHttpClient(): ClientInterface
    {
        return new Client([
            'base_uri' => $_ENV['BASE_URI'],
            'timeout' => 0,
        ]);
    }
}
