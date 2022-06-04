<?php

namespace App\Domain\Task\Requests;

use App\Http\Requests\AbstractBaseRequest;

class UpdateTaskRequest extends AbstractBaseRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'title' => 'unique:tasks,title,'.$this->route('task')->id,
            'description' => 'max:50',
        ];
    }
}
