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

    const MAGIC_NUMBER_TO_APPROVE_VISA = '4539003370725497';
    const MAGIC_NUMBER_TO_APPROVE_MASTERCARD = '5356066320271893';
    const MAGIC_NUMBER_TO_DENY = '6011457819940087';

    protected $endpoint = 'cards';

    /**
     * @throws ResourceException
     */
    public function setDefault(string $id): array
    {
        return $this->setId($id)->post(null, 'default');
    }
}
