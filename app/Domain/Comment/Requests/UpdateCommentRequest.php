<?php

namespace App\Domain\Comment\Requests;

use App\Http\Requests\AbstractBaseRequest;

class UpdateCommentRequest extends AbstractBaseRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'type' => 'in:TASK,VIDEO,LESSON,COURSE',
        ];
    }
}
