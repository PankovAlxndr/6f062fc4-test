<?php

namespace App\Services;

use App\Dto\Telegram\AuthDto;
use App\Exceptions\ImageUploader\SaveFileException;
use App\Jobs\RemoveAvatarJob;
use App\Models\Group;
use App\Models\User;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class UserService
{
    public function __construct(
        public readonly AuthDto $dto,
        public readonly AvatarUploader $avatarUploader
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
        $user = User::create(
            [
                'name' => Str::of($this->dto->first_name.' '.$this->dto->last_name)->trim()->value(),
                'telegram_login' => $this->dto->username,
                'telegram_id' => $this->dto->id,
                'group_id' => Group::GROUP_NEW,
                'remember_token' => Str::random(10),
            ],
        );
        if ($this->dto->photo_url) {
            try {
                $avatarUrl = $this->avatarUploader->uploadAvatar($this->dto->photo_url, $user);
                $user->update(['avatar' => $avatarUrl]);
            } catch (SaveFileException $e) {
            } catch (GuzzleException $e) {
            }
        }

        return $user;
    }

    public function authUser(User $user): User
    {
        if (! $user->exists) {
            throw new ModelNotFoundException('Model not found');
        }

        if (! $user->wasRecentlyCreated) {
            $this->updateUserFields($user);
        }

        Auth::login($user, true);

        return $user;
    }

    private function updateUserFields(User $user): bool
    {
        $arUpdate = [
            'name' => Str::of($this->dto->first_name.' '.$this->dto->last_name)->trim()->value(),
        ];

        $newFilePath = $this->avatarUploader->generatePath($this->dto->photo_url, $user);
        if ($newFilePath !== $user->getRawOriginal('avatar')) {
            try {
                $newAvatarUrl = $this->avatarUploader->uploadAvatar($this->dto->photo_url, $user);
                $arUpdate['avatar'] = $newAvatarUrl;

                RemoveAvatarJob::dispatch($user->getRawOriginal('avatar')); // todo можно будет переместить в обсервер или вызвать событие
            } catch (SaveFileException $e) {
            } catch (GuzzleException $e) {
            }

        }

        return $user->update($arUpdate);
    }
}
