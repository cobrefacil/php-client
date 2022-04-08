<?php

namespace CobreFacil\ApiOperations;

use CobreFacil\Exceptions\ResourceException;

trait Create
{
    /**
     * @throws ResourceException
     */
    public function create(array $params): array
    {
        return $this->post($params);
    }
}
