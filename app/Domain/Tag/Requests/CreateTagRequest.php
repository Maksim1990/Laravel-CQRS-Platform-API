<?php

namespace App\Domain\Tag\Requests;

use App\Http\Requests\AbstractBaseRequest;

class CreateTagRequest extends AbstractBaseRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'name' => 'required|unique:tags',
            'type' => 'in:TASK,VIDEO,LESSON,COURSE',
            'model_id' => 'integer',
            'description' => 'max:50',
        ];
    }
}
