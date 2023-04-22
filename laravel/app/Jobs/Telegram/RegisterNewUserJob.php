<?php

namespace App\Jobs\Telegram;

use App\Models\User;
use App\Services\Telegram\SendMessageService;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class RegisterNewUserJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(private User $user)
    {
        $this->user = $user->withoutRelations();
    }

    public function handle(SendMessageService $sendMessageService): void
    {

        // todo если выборка возвратит, допустим, 1000+ клиентов (админов)
        //  то посылать каждому из них сообщение не выйдет, упремся
        //  либо в ограничение телеграм
        //  либо в max_time джобы

        $users = User::admin()->select('telegram_id')->get();
        $users->each(function (User $user) use ($sendMessageService) {
            try {
                $sendMessageService->sendMessage("Add new user {$this->user->name}", $user);
            } catch (GuzzleException $e) {
            }
        });
    }
}
