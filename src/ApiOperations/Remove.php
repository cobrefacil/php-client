<?php

namespace CobreFacil\ApiOperations;

use CobreFacil\Exceptions\ResourceException;

trait Remove
{
    /**
     * @throws ResourceException
     */
    public function remove(string $id): array
    {
        return $this->setId($id)->delete();
    }
}
