<?php

namespace CobreFacil\Resources;

use CobreFacil\ApiOperations\Create;
use CobreFacil\ApiOperations\Get;
use CobreFacil\ApiOperations\Remove;
use CobreFacil\ApiOperations\Search;
use CobreFacil\Exceptions\ResourceException;

class Card extends ApiResource
{
    use Create;
    use Get;
    use Remove;
    use Search;

    protected $endpoint = 'cards';

    /**
     * @throws ResourceException
     */
    public function setDefault(string $id): array
    {
        return $this->setId($id)->post(null, 'default');
    }
}
