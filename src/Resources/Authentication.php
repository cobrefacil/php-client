<?php

namespace CobreFacil\Resources;

use CobreFacil\Exceptions\ResourceException;

class Authentication extends ApiResource
{
    protected $endpoint = 'authenticate';

    /**
     * @throws ResourceException
     */
    public function authenticate(string $appId, string $secret): array
    {
        return $this->post([
            'app_id' => $appId,
            'secret' => $secret,
        ]);
    }
}
