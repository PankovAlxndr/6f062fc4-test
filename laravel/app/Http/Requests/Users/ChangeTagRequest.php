<?php

namespace App\Http\Requests\Users;

use Illuminate\Foundation\Http\FormRequest;

class ChangeTagRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'state' => ['required', 'bool'],
        ];
    }
}
