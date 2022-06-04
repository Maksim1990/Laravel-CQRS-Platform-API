<?php

namespace App\Domain\Lesson\Requests;

use App\Http\Requests\AbstractBaseRequest;

class UpdateLessonRequest extends AbstractBaseRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'name' => 'unique:lessons,name,'.$this->route('lesson')->id,
            'course_id' => 'exists:courses,id',
            'section_id' => 'exists:sections,id',
            'description' => 'max:50',
        ];
    }
}
