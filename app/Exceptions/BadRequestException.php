<?php

namespace App\Exceptions;

use Symfony\Component\HttpFoundation\Response;

class BadRequestException extends AbstractException
{
    public function __construct($message = null, $code = Response::HTTP_BAD_REQUEST)
    {
        parent::__construct($message, $code);
    }
}
