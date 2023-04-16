<?php

namespace App\Http\Requests\Telegram;

use Illuminate\Foundation\Http\FormRequest;

class SigInRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'id' => ['required', 'int'],
            'first_name' => ['required', 'string'],
            'last_name' => ['nullable', 'string'],
            'username' => ['required', 'string'],
            'photo_url' => ['nullable', 'string'],
            'auth_date' => ['required', 'int'],
            'hash' => ['required', 'string'],
        ];
    }
}
