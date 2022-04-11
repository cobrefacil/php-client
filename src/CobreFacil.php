<?php

namespace CobreFacil;

use CobreFacil\Resources\Authentication;
use CobreFacil\Resources\Card;
use CobreFacil\Resources\Customer;
use CobreFacil\Resources\Invoice;
use GuzzleHttp\Client;
use GuzzleHttp\ClientInterface;

class CobreFacil
{
    const URI_PRODUCTION = 'https://api.cobrefacil.com.br';
    const URI_SANDBOX = 'https://api.sandbox.cobrefacil.com.br';

    /** @var bool */
    public static $production = true;

    /** @var ClientInterface */
    private $client;

    /** @var string */
    private $token;

    /**
     * @throws Exceptions\ResourceException
     */
    public function __construct(string $appId, string $secret, ClientInterface $client = null)
    {
        if ($client) {
            $this->setClient($client);
        } else {
            $this->setClient(new Client([
                'base_uri' => self::$production ? self::URI_PRODUCTION : self::URI_SANDBOX,
                'timeout' => 0,
            ]));
        }
        $response = $this->authentication()->authenticate($appId, $secret);
        $this->setToken($response['token']);
    }

    public function setClient(ClientInterface $client)
    {
        $this->client = $client;
    }

    public function getToken(): string
    {
        return $this->token;
    }

    public function setToken(string $token): CobreFacil
    {
        $this->token = $token;
        return $this;
    }

    public function authentication(): Authentication
    {
        return new Authentication($this->client);
    }

    public function customer(): Customer
    {
        return new Customer($this->client, $this->token);
    }

    public function card(): Card
    {
        return new Card($this->client, $this->token);
    }

    public function invoice(): Invoice
    {
        return new Invoice($this->client, $this->token);
    }
}
