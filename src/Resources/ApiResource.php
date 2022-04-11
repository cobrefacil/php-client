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

    /** @var string */
    protected $lastRequestUri;

    public function __construct(ClientInterface $client, string $token = null)
    {
        $this->client = $client;
        if (!is_null($token)) {
            $this->setHeaders([
                'Authorization' => 'Bearer ' . $token,
            ]);
        }
    }

    public function getUri(string $additionalUri = null): string
    {
        $uri = $this->apiVersion . '/' . $this->endpoint;
        if ($this->hasId()) {
            $uri .= '/' . $this->getId();
        }
        if (!empty($additionalUri)) {
            $uri .= '/' . $additionalUri;
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

    public function getLastRequestUri(): string
    {
        return $this->lastRequestUri;
    }

    /**
     * @throws ResourceException
     */
    protected function get(?array $queryParams = null): array
    {
        $uri = $this->getUri();
        $this->lastRequestUri = $uri;
        try {
            $response = $this->client->get($uri, [
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
    protected function post(array $params = null, string $additionalUri = null): array
    {
        $data['headers'] = $this->getHeaders();
        if (!empty($params)) {
            $data['form_params'] = $params;
        }
        $uri = $this->getUri($additionalUri);
        $this->lastRequestUri = $uri;
        try {
            $response = $this->client->post($uri, $data);
            return $this->parseResponse($response);
        } catch (ClientException $e) {
            throw $this->parseClientException($e);
        }
    }

    /**
     * @throws ResourceException
     */
    protected function put(array $params): array
    {
        $uri = $this->getUri();
        $this->lastRequestUri = $uri;
        try {
            $response = $this->client->put($uri, [
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
    protected function delete(): array
    {
        $uri = $this->getUri();
        $this->lastRequestUri = $uri;
        try {
            $response = $this->client->delete($uri, [
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
        switch ($code) {
            case 400:
                return InvalidParamsException::createByBody($body, $code);
            case 401:
                return new InvalidCredentialsException();
            case 404:
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
