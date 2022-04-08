<?php

namespace CobreFacil\Resources;

use CobreFacil\Exceptions\InvalidCredentialsException;

class Authentication extends ApiResource
{
    /** @var string */
    protected $endpoint = 'authenticate';

    /**
     * @throws InvalidCredentialsException
     */
    public function authenticate(string $appId, string $secret): array
    {
        return $this->postRequest([
            'app_id' => $appId,
            'secret' => $secret,
        ]);
    }
}
