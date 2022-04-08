<?php

namespace CobreFacil\Resources;

use CobreFacil\ApiOperations\Create;
use CobreFacil\ApiOperations\Get;
use CobreFacil\ApiOperations\Remove;
use CobreFacil\ApiOperations\Search;
use CobreFacil\ApiOperations\Update;

class Customer extends ApiResource
{
    use Create;
    use Get;
    use Remove;
    use Search;
    use Update;

    protected $endpoint = 'customers';
}
