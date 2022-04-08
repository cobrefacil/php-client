<?php

namespace CobreFacil\Exceptions;

class InvalidParamsException extends ResourceException
{
    /** @var array */
    private $errors;

    public static function createByBody(array $body): InvalidParamsException
    {
        return (new InvalidParamsException($body['message']))->setErrors($body['errors']);
    }

    public function getErrors(): array
    {
        return $this->errors;
    }

    public function setErrors(array $errors): InvalidParamsException
    {
        $this->errors = $errors;
        return $this;
    }
}
