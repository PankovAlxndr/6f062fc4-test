<?php

namespace App\Dto\Telegram;

use App\Http\Requests\Telegram\SigInRequest;

class AuthDto
{
    public function __construct(
        public readonly int $id,
        public readonly string $first_name,
        public readonly string $username,
        public readonly int $auth_date,
        public readonly string $hash,
        public readonly ?string $last_name = null,
        public readonly ?string $photo_url = null,
    ) {
    }

    public static function createFromRequest(SigInRequest $request): AuthDto
    {
        return new AuthDto(...$request->safe()->all());
    }
}
