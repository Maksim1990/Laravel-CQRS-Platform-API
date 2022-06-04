<?php

namespace App\Exceptions;

use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Support\Facades\Response;
use Symfony\Component\HttpFoundation\Response as ResponseCodes;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Exception\RouteNotFoundException;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that are not reported.
     *
     * @var array
     */
    protected $dontReport = [
        //
    ];

    /**
     * A list of the inputs that are never flashed for validation exceptions.
     *
     * @var array
     */
    protected $dontFlash = [
        'password',
        'password_confirmation',
    ];

    /**
     * Register the exception handling callbacks for the application.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    public function render($request, Throwable $exception)
    {
        if ($exception instanceof ValidationExceptionInterface) {
            return ExceptionFormatter::getValidationErrorResponse($exception);
        } elseif ($exception instanceof MethodNotAllowedHttpException) {
            return ExceptionFormatter::getResponse($exception, ResponseCodes::HTTP_METHOD_NOT_ALLOWED);
        }elseif ($exception instanceof ModelNotFoundException) {
            return ExceptionFormatter::getResponse($exception, ResponseCodes::HTTP_NOT_FOUND);
        } elseif ($exception instanceof NotFoundHttpException) {
            return ExceptionFormatter::getResponse(
                new RouteNotFoundException('Route not found'),
                ResponseCodes::HTTP_NOT_FOUND
            );
        }
        elseif ($this->isHttpException($exception)) {
            return ExceptionFormatter::getResponse($exception);
        } else {
            return parent::render($request, $exception);
        }
    }
}
