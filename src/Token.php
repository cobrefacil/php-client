<?php

namespace CobreFacil;

class Token
{
    /** @var string */
    private $token;

    /** @var int */
    private $expiration;

    /** @var int */
    private $expiresOn;

    /** @var int */
    private $createdAt;

    public function __construct(string $token, int $expiration, int $createdAt = null)
    {
        $this->token = $token;
        $this->expiration = $expiration;
        $this->createdAt = $createdAt ?? time();
        $this->expiresOn = $this->createdAt + $this->expiration;
    }

    public function __toString(): string
    {
        return $this->token;
    }

    public function getToken(): string
    {
        return $this->token;
    }

    public function getExpiration(): int
    {
        return $this->expiration;
    }

    public function getCreatedAt(): int
    {
        return $this->createdAt;
    }

    public function getExpiresOn(): int
    {
        return $this->expiresOn;
    }

    public function isExpired(): bool
    {
        return $this->expiresOn < time();
    }
}
