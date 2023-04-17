<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class UserPolicy
{
    use HandlesAuthorization;

    public function delete(User $user, User $model): bool
    {
        return $user->id !== $model->id;
    }

    public function forceDelete(User $user, User $model): bool
    {
        return $user->id !== $model->id;
    }
}
