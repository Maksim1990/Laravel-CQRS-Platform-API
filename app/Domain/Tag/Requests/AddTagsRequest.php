<?php

namespace App\Domain\Tag\Requests;

use App\Http\Requests\AbstractBaseRequest;

class AddTagsRequest extends AbstractBaseRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'tags' => 'required',
            'action' => 'in:ATTACH,DETACH',
        ];
    }
}
