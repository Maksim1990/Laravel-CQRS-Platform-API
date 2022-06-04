<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

abstract class AbstractResource extends JsonResource
{
    use ApiResourceTrait;

    // http://localhost:7000/api/v1/courses/1?relationships=videos;lessons&relationPerPage=10
    protected function buildDataStructure(array $data, Request $request): array
    {
        return $this->processRequestData($data, $request);
    }
}
