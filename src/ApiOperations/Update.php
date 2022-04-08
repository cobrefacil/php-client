<?php

namespace CobreFacil\ApiOperations;

use CobreFacil\Exceptions\ResourceException;

trait Update
{
    /**
     * @throws ResourceException
     */
    public function update(string $id, array $params): array
    {
        return $this->setId($id)->put($params);
    }
}
