<?php

namespace CobreFacil\Exceptions;

class InvalidParamsException extends ResourceException
{
    /** @var array */
    private $errors;

    public static function createByBody(array $body, int $code): InvalidParamsException
    {
        $exception = new InvalidParamsException($body['message'], $code);
        if (!empty($body['errors'])) {
            $exception->setErrors($body['errors']);
        }
        return $exception;
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
