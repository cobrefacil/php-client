<?php

namespace CobreFacil\Resources;

use CobreFacil\Exceptions\ResourceException;

class Customer extends ApiResource
{
    protected $endpoint = 'customers';

    /**
     * @throws ResourceException
     */
    public function list(?array $filter = null): array
    {
        return $this->getRequest($filter);
    }

    /**
     * @throws ResourceException
     */
    public function getById(string $id): array
    {
        return $this->setId($id)->getRequest();
    }

    /**
     * @throws ResourceException
     */
    public function create(array $params): array
    {
        return $this->postRequest($params);
    }

    /**
     * @throws ResourceException
     */
    public function update(string $id, array $params): array
    {
        return $this->setId($id)->putRequest($params);
    }

    /**
     * @throws ResourceException
     */
    public function delete(string $id): array
    {
        return $this->setId($id)->deleteRequest();
    }
}
