<?php

use App\Dto\Telegram\AuthDto;
use App\Exceptions\Telegram\InvalidDataException;
use App\Exceptions\Telegram\OutdatedDataException;
use App\Services\Telegram\CheckAuthorizationService;

test('invalid hash', function () {
    $authService = new CheckAuthorizationService('mock_token');
    $dto = new AuthDto(
        id: 100,
        first_name: 'James',
        last_name: 'Bond',
        username: 'james_bond',
        photo_url: 'https://t.me/i/userpic/320/sample.jpg',
        auth_date: time(),
        hash: '81501f7c9b52ae1f955c37313671608f7a175966b3c0f5d0ebdd90b423ce61fb'
    );
    $authService->check($dto);
})->throws(InvalidDataException::class);

test('outdated request', function () {
    $authService = new CheckAuthorizationService('mock_token');
    $dto = new AuthDto(
        id: 100,
        first_name: 'James',
        last_name: 'Bond',
        username: 'james_bond',
        photo_url: 'https://t.me/i/userpic/320/sample.jpg',
        auth_date: time() - 864000,
        hash: '81501f7c9b52ae1f955c37313671608f7a175966b3c0f5d0ebdd90b423ce61fb'
    );
    $authService->check($dto);
})->throws(OutdatedDataException::class);
