<?php

namespace App\Domain\Section\Requests;

use App\Http\Requests\AbstractBaseRequest;

class CreateSectionRequest extends AbstractBaseRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'name' => 'required|unique:sections',
            'course_id' => 'required|exists:courses,id',
            'description' => 'max:50',
        ];
    }
}
