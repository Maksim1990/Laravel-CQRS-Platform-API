<?php
namespace App\Exceptions;

use Symfony\Component\HttpFoundation\Response;

class ServiceException extends AbstractException
{
    public function __construct($message = null, $code = Response::HTTP_INTERNAL_SERVER_ERROR)
    {
        parent::__construct($message, $code);
    }
}
