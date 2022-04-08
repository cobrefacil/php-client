<?php

namespace CobreFacil\ApiOperations;

use CobreFacil\Exceptions\ResourceException;

trait Search
{
    /**
     * @throws ResourceException
     */
    public function search(?array $filter = null): array
    {
        return $this->get($filter);
    }
}
