<?php

namespace App\Exceptions;

use App\Services\Utils;
use App\Utils\ModelUtil;
use Illuminate\Support\Facades\Response;
use Symfony\Component\Routing\Exception\RouteNotFoundException;
use Throwable;

class ExceptionFormatter
{
    public static function getResponse(Throwable $exception, ?string $overrideStatusCode = null)
    {

        $errorCode = $overrideStatusCode !== null ? $overrideStatusCode : $exception->getCode();
        switch ($errorCode) {
        case 404:
            return self::getFormattedErrorResponse($errorCode, self::getExceptionErrorMessage($exception));
        default:
            return self::getFormattedErrorResponse($errorCode, $exception->getMessage());
        }
    }

    public static function getValidationErrorResponse(ValidationExceptionInterface $exception)
    {
        return Response::json(
            [
                'code' => $exception->getStatusCode(),
                'messages' => $exception->getErrorMessages(),
            ],
            $exception->getStatusCode()
        );
    }

    private static function getFormattedErrorResponse(int $errorCode, string $message)
    {
        return Response::json(
            [
                'code' => $errorCode,
                'message' => $message,
            ],
            $errorCode
        );
    }

    private static function getExceptionErrorMessage(Throwable $exception): string
    {
        if (method_exists($exception, 'getModel')) {
            return sprintf(
                '%s with ID %s is not found',
                ModelUtil::getClassModelNameFromNamespace($exception->getModel()),
                current($exception->getIds())
            );
        }

        return $exception->getMessage();
    }
}
