<?php

namespace CobreFacil;

use CobreFacil\Resources\Authentication;
use CobreFacil\Resources\Card;
use CobreFacil\Resources\Customer;
use CobreFacil\Resources\Invoice;
use Exception;
use GuzzleHttp\Client;
use GuzzleHttp\ClientInterface;

/**
 * @property Authentication $authentication
 * @property Customer $customer
 * @property Card $card
 * @property Invoice $invoice
 */
class CobreFacil
{
    const URI_PRODUCTION = 'https://api.cobrefacil.com.br';
    const URI_SANDBOX = 'https://api.sandbox.cobrefacil.com.br';

    /** @var bool */
    private $production = true;

    /** @var ClientInterface */
    private $httpClient;

    /** @var Credentials */
    private $credentials;

    /** @var Token */
    private $token;

    public function __construct(string $appId, string $secret)
    {
        $this->setCredentials(new Credentials($appId, $secret));
    }

    /**
     * @throws Exception
     */
    public function __get($name)
    {
        if (!method_exists($this, $name)) {
            throw new Exception("Nenhum recurso foi encontrado com o nome \"$name\".");
        }
        return $this->$name();
    }

    public function isProduction(): bool
    {
        return $this->production;
    }

    public function setProduction(bool $production): CobreFacil
    {
        $this->production = $production;
        return $this;
    }

    public function getHttpClient(): Client
    {
        if (empty($this->httpClient)) {
            $this->setHttpClient(new Client([
                'base_uri' => $this->production ? self::URI_PRODUCTION : self::URI_SANDBOX,
                'timeout' => 0,
            ]));
        }
        return $this->httpClient;
    }

    public function setHttpClient(ClientInterface $httpClient): CobreFacil
    {
        $this->httpClient = $httpClient;
        return $this;
    }

    public function getCredentials(): Credentials
    {
        return $this->credentials;
    }

    public function setCredentials(Credentials $credentials): CobreFacil
    {
        $this->credentials = $credentials;
        return $this;
    }

    /**
     * @throws Exceptions\ResourceException
     */
    public function getValidToken(): Token
    {
        if (is_null($this->token) || $this->token->isExpired()) {
            $response = $this->authentication()->authenticate(
                $this->credentials->getAppId(),
                $this->credentials->getSecret()
            );
            $this->token = new Token($response['token'], $response['expiration']);
        }
        return $this->token;
    }

    public function getToken(): Token
    {
        return $this->token;
    }

    public function setToken(Token $token): CobreFacil
    {
        $this->token = $token;
        return $this;
    }

    public function authentication(): Authentication
    {
        return new Authentication($this->getHttpClient());
    }

    /**
     * @throws Exceptions\ResourceException
     */
    public function customer(): Customer
    {
        return new Customer($this->getHttpClient(), $this->getValidToken());
    }

    /**
     * @throws Exceptions\ResourceException
     */
    public function card(): Card
    {
        return new Card($this->getHttpClient(), $this->getValidToken());
    }

    /**
     * @throws Exceptions\ResourceException
     */
    public function invoice(): Invoice
    {
        return new Invoice($this->getHttpClient(), $this->getValidToken());
    }
}
