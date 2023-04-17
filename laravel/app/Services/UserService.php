<?php

namespace App\Services;

use App\Dto\Telegram\AuthDto;
use App\Models\Group;
use App\Models\User;
use Illuminate\Database\Eloquent\ModelNotFoundException;
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
                'name' => Str::of($this->dto->first_name.' '.$this->dto->last_name)->trim()->value(),
                'avatar' => $this->dto->photo_url,
                'telegram_login' => $this->dto->username,
                'telegram_id' => $this->dto->id,
                'group_id' => Group::GROUP_NEW,
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
                'name' => Str::of($this->dto->first_name.' '.$this->dto->last_name)->trim()->value(),
                'avatar' => $this->dto->photo_url,
            ],
        );
    }
}
