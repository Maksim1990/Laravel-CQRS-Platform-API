<?php

namespace App\Exceptions;

use Exception;
use Symfony\Component\HttpFoundation\Response;

class ValidationException extends Exception implements ValidationExceptionInterface
{
    private array $messages;
    private string $statusCode;

    public function __construct(array $messages,string $statusCode = Response::HTTP_BAD_REQUEST)
    {
        $this->messages = $messages;
        $this->statusCode = $statusCode;
    }

    public function getStatusCode():string
    {
        return $this->statusCode;
    }

    public function getErrorMessages(): array
    {
        return $this->messages;
    }
}
