<?php

namespace CobreFacil\ApiOperations;

use CobreFacil\Exceptions\ResourceException;

trait Get
{
    /**
     * @throws ResourceException
     */
    public function getById(string $id): array
    {
        return $this->setId($id)->get();
    }
}
