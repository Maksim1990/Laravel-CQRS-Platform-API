<?php

namespace App\Domain\Tag\Requests;

use App\Http\Requests\AbstractBaseRequest;

class UpdateTagRequest extends AbstractBaseRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'name' => 'unique:tags,name,'.$this->route('tag')->id,
            'type' => 'in:TASK,VIDEO,LESSON,COURSE',
            'model_id' => 'integer',
            'description' => 'max:50',
        ];
    }
}
