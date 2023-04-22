<?php

namespace App\Listeners;

use App\Events\RegisterNewUserEvent;
use App\Jobs\Telegram\RegisterNewUserJob;

class RegisterNewUserListener
{
    public function __construct(
    ) {
    }

    public function handle(RegisterNewUserEvent $event): void
    {
        RegisterNewUserJob::dispatch($event->user);
    }
}
