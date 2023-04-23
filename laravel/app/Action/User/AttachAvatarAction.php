<?php

namespace App\Action\User;

use App\Events\User\UpdateAvatarUserEvent;
use App\Models\User;
use Illuminate\Support\Str;

class AttachAvatarAction
{
    public const DISK = 's3-avatar';

    public function execute(User $user, \Illuminate\Http\UploadedFile $uploadedFile): User
    {
        $extension = $uploadedFile->getClientOriginalExtension();
        $path = $uploadedFile->storeAs("/{$user->id}", Str::uuid()->toString().'.'.$extension, $this::DISK);
        UpdateAvatarUserEvent::dispatch($user);
        $user->update(['avatar' => $path]);
        return $user;
    }
}
