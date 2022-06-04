<?php

namespace App\Domain\Lesson\Requests;

use App\Http\Requests\AbstractBaseRequest;

class CreateLessonRequest extends AbstractBaseRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'name' => 'required|unique:lessons',
            'course_id' => 'required|exists:courses,id',
            'section_id' => 'exists:sections,id',
            'description' => 'max:50',
        ];
    }
}
