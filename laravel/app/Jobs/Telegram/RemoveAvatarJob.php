<?php

namespace App\Jobs\Telegram;

use App\Services\AvatarUploader;
use GuzzleHttp\Client;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;

class RemoveAvatarJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(
        private string $path
    ) {
    }

    public function handle(): void
    {
        $uploader = new AvatarUploader(Storage::disk('s3-avatar'), new Client());
        $uploader->removeAvatar($this->path);
    }
}
