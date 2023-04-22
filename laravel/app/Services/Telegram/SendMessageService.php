<?php

namespace App\Services\Telegram;

use App\Models\User;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\RequestOptions;
use Illuminate\Support\Str;
use Psr\Http\Message\ResponseInterface;

class SendMessageService
{
    private string $baseUrl = 'https://api.telegram.org/bot{token}/sendMessage';

    public function __construct(
        public readonly string $token,
        public readonly Client $client
    ) {
    }

    /**
     * @throws GuzzleException
     */
    public function sendMessage(string $message, User $recipient): ResponseInterface
    {
        return $this->client->request(
            'GET',
            Str::replace('{token}', $this->token, $this->baseUrl),
            [RequestOptions::QUERY => [
                'chat_id' => $recipient->telegram_id,
                'text' => $message,
            ]]
        );
    }
}
