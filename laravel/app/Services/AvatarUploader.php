<?php

declare(strict_types=1);

namespace App\Services;

use App\Exceptions\ImageUploader\SaveFileException;
use App\Models\User;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Contracts\Filesystem\Filesystem;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Str;

class AvatarUploader
{
    public function __construct(
        private Filesystem $storage,
        private Client $client
    ) {
    }

    /**
     * @throws GuzzleException
     * @throws SaveFileException
     */
    public function uploadAvatar(string $sourcePath, User $user): string
    {
        $storagePath = $this->generatePath($sourcePath, $user);
        $sourceData = $this->client->get($sourcePath)->getBody()->getContents();

        if (! $this->storage->put($storagePath, $sourceData)) {
            throw new SaveFileException('Unable to save data data for path: '.$sourcePath);
        }

        return $storagePath;
    }

    public function removeAvatar(string $path): bool
    {
        return $this->storage->delete($path);
    }

    public static function getFileName(string $path): string
    {
        if ($fileHash = last(explode('/', $path))) {
            return $fileHash;
        }

        return Str::uuid()->toString();
    }

    public function generatePath(string $path, User $user): string
    {
        if (! $user->exists) {
            throw new ModelNotFoundException('Model not found');
        }

        $fileName = self::getFileName($path);

        return "/{$user->id}/{$fileName}";
    }
}
