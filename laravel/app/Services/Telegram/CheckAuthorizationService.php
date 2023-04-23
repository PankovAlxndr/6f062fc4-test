<?php

namespace App\Services\Telegram;

use App\Dto\Telegram\AuthDto;
use App\Exceptions\Telegram\InvalidDataException;
use App\Exceptions\Telegram\OutdatedDataException;

class CheckAuthorizationService
{
    private int $deadlineSec = 86400;

    public function __construct(
        public readonly string $token,
    ) {
    }

    public function check(AuthDto $dto): true
    {
        if ($this->isOutdated($dto->auth_date)) {
            throw new OutdatedDataException('Data is outdated');
        }

        $dataStringCheck = $this->prepareInput($dto);
        $secretKey = hash('sha256', $this->token, true);
        $hash = hash_hmac('sha256', $dataStringCheck, $secretKey);

        if (0 !== strcmp($hash, $dto->hash)) {
            throw new InvalidDataException('Data is NOT from Telegram');
        }

        return true;
    }

    /**
     * Data-check-string is a concatenation of all received fields, sorted in alphabetical order,
     * in the format key=<value> with a line feed character ('\n', 0x0A) used as separator
     */
    private function prepareInput(AuthDto $dto): string
    {
        $inputData = array_filter((array) $dto);
        unset($inputData['hash']);

        $arrCheck = [];
        foreach ($inputData as $key => $value) {
            $arrCheck[] = $key.'='.$value;
        }
        sort($arrCheck);

        return implode("\n", $arrCheck);
    }

    private function isOutdated(int $authDate): bool
    {
        return (time() - $authDate) > $this->deadlineSec;
    }
}
