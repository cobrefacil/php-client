<?php

namespace CobreFacil\Resources;

use CobreFacil\Exceptions\InvalidCredentialsException;
use CobreFacil\Exceptions\InvalidParamsException;
use CobreFacil\Exceptions\ResourceException;
use CobreFacil\Exceptions\ResourceNotFoundException;
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

    /** @var string */
    protected $id;

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
        $uri = $this->apiVersion . '/' . $this->endpoint;
        if ($this->hasId()) {
            $uri .= '/' . $this->getId();
        }
        return $uri;
    }

    public function getHeaders(): ?array
    {
        return $this->headers;
    }

    public function setHeaders(array $headers)
    {
        $this->headers = $headers;
    }

    public function hasId(): bool
    {
        return !empty($this->id);
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function setId(string $id): ApiResource
    {
        $this->id = $id;
        return $this;
    }

    /**
     * @throws ResourceException
     */
    protected function getRequest(?array $queryParams = null): array
    {
        try {
            $response = $this->client->get($this->getUri(), [
                'headers' => $this->getHeaders(),
                'query' => $queryParams,
            ]);
            return $this->parseResponse($response);
        } catch (ClientException $e) {
            throw $this->parseClientException($e);
        }
    }

    /**
     * @throws ResourceException
     */
    protected function postRequest(array $params): array
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
     * @throws ResourceException
     */
    protected function putRequest(array $params): array
    {
        try {
            $response = $this->client->put($this->getUri(), [
                'headers' => $this->getHeaders(),
                'form_params' => $params,
            ]);
            return $this->parseResponse($response);
        } catch (ClientException $e) {
            throw $this->parseClientException($e);
        }
    }

    /**
     * @throws ResourceException
     */
    protected function deleteRequest(): array
    {
        try {
            $response = $this->client->delete($this->getUri(), [
                'headers' => $this->getHeaders(),
            ]);
            return $this->parseResponse($response);
        } catch (ClientException $e) {
            throw $this->parseClientException($e);
        }
    }

    /**
     * @return ClientException|ResourceException
     */
    private function parseClientException(ClientException $e): Exception
    {
        $code = $e->getCode();
        $body = json_decode($e->getResponse()->getBody(), true);
        if (400 === $code) {
            return InvalidParamsException::createByBody($body);
        }
        if (401 === $code) {
            return new InvalidCredentialsException();
        }
        if (404 === $code) {
            return new ResourceNotFoundException($body['message'], $code);
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
