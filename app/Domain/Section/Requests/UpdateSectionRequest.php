<?php

namespace App\Domain\Section\Requests;

use App\Http\Requests\AbstractBaseRequest;

class UpdateSectionRequest extends AbstractBaseRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'name' => 'unique:sections,name,'.$this->route('section')->id,
            'description' => 'max:50',
        ];
    }
}
