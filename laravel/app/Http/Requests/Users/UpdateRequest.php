<?php

namespace App\Http\Requests\Users;

use App\Models\Group;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'name' => ['required', 'string'],
            'avatar' => ['nullable', 'image', 'max:5120'], // 5120 kB (post_max_size 8 MB)
            'description' => ['nullable', 'string', 'max:255'],
            'telegram_login' => ['required', 'string', Rule::unique('users', 'telegram_login')->ignore($this->id)],
            'telegram_id' => ['required', 'integer', Rule::unique('users', 'telegram_id')->ignore($this->id)],
            'tags' => ['nullable', 'json'],
            'group_id' => ['required', 'int', Rule::exists(Group::class, 'id')],
        ];
    }
}
