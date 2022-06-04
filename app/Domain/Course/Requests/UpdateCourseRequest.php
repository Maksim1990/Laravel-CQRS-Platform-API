<?php

namespace App\Domain\Course\Requests;

use App\Http\Requests\AbstractBaseRequest;

class UpdateCourseRequest extends AbstractBaseRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'name' => 'unique:courses,name,' . $this->route('course')->id,
            'description' => 'max:50',
        ];
    }
}
