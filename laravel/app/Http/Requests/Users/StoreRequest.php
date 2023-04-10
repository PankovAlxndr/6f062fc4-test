<?php

namespace App\Http\Requests\Users;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'name' => ['required', 'string'],
            'avatar' => ['nullable', 'image', 'max:5120'], // 5120 kB (post_max_size 8 MB)
            'description' => ['nullable', 'string', 'max:255'],
            'telegram_login' => ['required', 'string', Rule::unique(User::class, 'telegram_login')],
            'telegram_id' => ['required', 'integer', Rule::unique(User::class, 'telegram_id')],
        ];
    }
}
