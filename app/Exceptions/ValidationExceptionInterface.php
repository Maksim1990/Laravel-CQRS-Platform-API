<?php

namespace App\Exceptions;

interface ValidationExceptionInterface
{
    public function getStatusCode(): string;

    public function getErrorMessages(): array;
}
