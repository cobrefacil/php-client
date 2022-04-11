<?php

namespace CobreFacil;

use CobreFacil\Exceptions\InvalidParamsException;
use CobreFacil\Exceptions\ResourceNotFoundException;
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

    protected function getLastCardId(): string
    {
        return $this->getLastCard()['id'];
    }

    protected function getLastCard(): array
    {
        return $this->cobreFacil->card()->search()[0];
    }

    protected function getLastCustomerId(): string
    {
        return $this->getLastCustomer()['id'];
    }

    protected function getLastCustomer(): array
    {
        return $this->cobreFacil->customer()->search()[0];
    }

    protected function waitAsyncRequestBeProcessed(): void
    {
        sleep(3);
    }

    protected function assertInvalidParamsException(array $expectedErrors, InvalidParamsException $exception)
    {
        $this->assertEquals('Parâmetros inválidos.', $exception->getMessage());
        $this->assertEquals(400, $exception->getCode());
        $this->assertEquals($expectedErrors, $exception->getErrors());
    }

    protected function assertResourceNotFoundException(ResourceNotFoundException $exception, string $expectedMessage)
    {
        $this->assertEquals($expectedMessage, $exception->getMessage());
    }
}
