<?php

namespace App\Services;

use App\Dto\Telegram\AuthDto;
use App\Models\Group;
use App\Models\User;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;

class UserService
{
    public function __construct(
        public readonly AuthDto $dto
    ) {
    }

    public function isExistUser(): User|false
    {
        if ($user = User::whereTelegramId($this->dto->id)->first()) {
            return $user;
        }

        return false;
    }

    public function createUser(): User
    {
        return User::create(
            [
                'name' => $this->dto->first_name,
                'last_name' => $this->dto->last_name,
                'avatar' => $this->dto->photo_url,
                'telegram_login' => $this->dto->username,
                'telegram_id' => $this->dto->id,
                'group_id' => Group::GROUP_NEW,
                'email' => fake()->unique()->safeEmail(),
                'email_verified_at' => Carbon::now(),
                'password' => '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', // password
                'remember_token' => Str::random(10),
            ],
        );
    }

    public function authUser(User $user): User
    {
        if (! $user->exists) {
            throw new ModelNotFoundException('Model not found');
        }

        if (! $user->wasRecentlyCreated) {
            $this->updateUserFields($user);
        }

        \Auth::login($user, true);

        return $user;
    }

    private function updateUserFields(User $user): bool
    {
        return $user->update(
            [
                'name' => $this->dto->first_name,
                'last_name' => $this->dto->last_name,
                'avatar' => $this->dto->photo_url,
            ],
        );
    }
}
