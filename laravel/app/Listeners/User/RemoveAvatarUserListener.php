<?php

namespace App\Listeners\User;

use App\Events\User\DeleteUserEvent;
use App\Services\AvatarUploader;
use Illuminate\Contracts\Queue\ShouldQueue;

class RemoveAvatarUserListener implements ShouldQueue
{
    public function __construct(
        public AvatarUploader $avatarUploader
    ) {
    }

    public function handle(DeleteUserEvent $event): void
    {
        if ($avatarPath = $event->user->getRawOriginal('avatar')) {
            $this->avatarUploader->removeAvatar($avatarPath);
        }
    }
}
