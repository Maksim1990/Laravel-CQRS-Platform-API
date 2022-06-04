<?php

namespace App\Domain\Video\Requests;

use App\Http\Requests\AbstractBaseRequest;

class CreateVideoRequest extends AbstractBaseRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'title' => 'required|unique:videos',
            'description' => 'max:50',
            'link' => 'required|regex:/^(https?:\/\/)?([\da-z\.-]+)\.([a-z\.]{2,6})([\/\w \.-]*)*\/?$/',
            'lesson_id' => 'required|exists:lessons,id',
        ];
    }
}
