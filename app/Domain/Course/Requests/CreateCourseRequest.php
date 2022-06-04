<?php

namespace App\Domain\Course\Requests;

use App\Http\Requests\AbstractBaseRequest;

class CreateCourseRequest extends AbstractBaseRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'slug' => 'required|unique:courses',
            'name' => 'required|unique:courses',
            'description' => 'max:50',
        ];
    }
}
