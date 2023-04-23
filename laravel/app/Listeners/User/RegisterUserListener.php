<?php

namespace App\Listeners\User;

use App\Events\User\RegisterUserEvent;
use App\Models\User;
use App\Services\Telegram\SendMessageService;
use Illuminate\Contracts\Queue\ShouldQueue;

class RegisterUserListener implements ShouldQueue
{
    public function __construct(
        public SendMessageService $sendMessageService
    ) {
    }

    public function handle(RegisterUserEvent $event): void
    {
        // todo если выборка возвратит, допустим, 1000+ клиентов (админов)
        //  то посылать каждому из них сообщение не выйдет, упремся
        //  либо в ограничение телеграм
        //  либо в max_time джобы

        $users = User::admin()->select('telegram_id')->get();
        foreach ($users as $admin) {
            $this->sendMessageService->sendMessage("Add new user {$event->user->name}", $admin);
        }
    }
}
