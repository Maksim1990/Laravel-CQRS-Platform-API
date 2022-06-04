<?php

namespace App\Domain\Video\Requests;

use App\Http\Requests\AbstractBaseRequest;

class UpdateVideoRequest extends AbstractBaseRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'title' => 'unique:videos,title,' . $this->route('video')->id,
            'description' => 'max:50',
            'link' => 'regex:/^(https?:\/\/)?([\da-z\.-]+)\.([a-z\.]{2,6})([\/\w \.-]*)*\/?$/',
        ];
    }
}
