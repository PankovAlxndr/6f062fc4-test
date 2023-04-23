<?php

namespace App\Events\User;

use App\Models\User;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class DeleteUserEvent
{
    use Dispatchable;
    use SerializesModels;

    public function __construct(public User $user)
    {
    }
}
