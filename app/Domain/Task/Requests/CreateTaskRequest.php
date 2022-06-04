<?php

namespace App\Domain\Task\Requests;

use App\Http\Requests\AbstractBaseRequest;

class CreateTaskRequest extends AbstractBaseRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'title' => 'required|unique:tasks',
            'lesson_id' => 'required|exists:lessons,id',
            'description' => 'max:50',
        ];
    }
}
