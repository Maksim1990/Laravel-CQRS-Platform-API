<?php

namespace App\Domain\Comment\Requests;

use App\Http\Requests\AbstractBaseRequest;

class CreateCommentRequest extends AbstractBaseRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'message' => 'required',
            'type' => 'required|in:TASK,VIDEO,LESSON,COURSE',
            'model_id' => 'required|integer',
        ];
    }
}
