<?php

namespace CobreFacil;

class Credentials
{
    /** @var string */
    private $appId;

    /** @var string */
    private $secret;

    public function __construct(string $appId, string $secret)
    {
        $this->appId = $appId;
        $this->secret = $secret;
    }

    public function getAppId(): string
    {
        return $this->appId;
    }

    public function getSecret(): string
    {
        return $this->secret;
    }
}
