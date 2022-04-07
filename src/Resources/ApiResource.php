<?php

namespace CobreFacil\Resources;

use CobreFacil\Exceptions\InvalidCredentialsException;
use Exception;
use GuzzleHttp\ClientInterface;
use GuzzleHttp\Exception\ClientException;

abstract class ApiResource
{
    /** @var string */
    protected $apiVersion = 'v1';

    /** @var string */
    protected $endpoint;

    /** @var ClientInterface */
    protected $client;

    /** @var array */
    protected $headers;

    public function __construct(ClientInterface $client, string $token = null)
    {
        $this->client = $client;
        if (!is_null($token)) {
            $this->setHeaders([
                'Authorization' => 'Bearer ' . $token,
            ]);
        }
    }

    public function getUri(): string
    {
        return $this->apiVersion . '/' . $this->endpoint;
    }

    public function getHeaders(): ?array
    {
        return $this->headers;
    }

    public function setHeaders(array $headers)
    {
        $this->headers = $headers;
    }

    /**
     * @throws InvalidCredentialsException
     */
    protected function post(array $params): array
    {
        try {
            $response = $this->client->post($this->getUri(), [
                'headers' => $this->getHeaders(),
                'form_params' => $params,
            ]);
            return $this->parseResponse($response);
        } catch (ClientException $e) {
            throw $this->parseClientException($e);
        }
    }

    /**
     * @return ClientException|InvalidCredentialsException
     */
    private function parseClientException(ClientException $e): Exception
    {
        if ($e->getCode() === 401) {
            return new InvalidCredentialsException();
        }
        return $e;// @codeCoverageIgnore
    }

    private function parseResponse($response): array
    {
        $response = (string)$response->getBody();
        $response = json_decode($response, true);
        return $response['data'];
    }
}
