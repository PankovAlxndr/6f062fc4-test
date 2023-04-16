<?php

use App\Dto\Telegram\AuthDto;
use App\Models\Group;
use App\Models\User;
use App\Services\UserService;
use Illuminate\Database\Eloquent\ModelNotFoundException;

test('check not exist user ', function () {
    $service = createUserService();

    expect($service->isExistUser())->toBeFalse();
});

test('check exist user', function () {
    $user = User::factory([
        'telegram_login' => 'james_bond',
        'telegram_id' => 100,
        'name' => 'James',
    ])->create();
    $service = createUserService();
    $existUser = $service->isExistUser();

    expect($existUser)->toBeInstanceOf(User::class)
        ->and($existUser->telegram_login)->toBe($user->telegram_login)
        ->and($existUser->name)->toBe($user->name)
        ->and($existUser->telegram_id)->toBe($user->telegram_id);
});

test('create new user', function () {
    $service = createUserService();
    $user = $service->createUser();

    expect($user)->toBeInstanceOf(User::class)
        ->and($user->telegram_login)->toBe($service->dto->username)
        ->and($user->telegram_id)->toBe($service->dto->id)
        ->and($user->avatar)->toBe($service->dto->photo_url)
        ->and($user->name)->toBe($service->dto->first_name)
        ->and($user->group_id)->toBe(Group::GROUP_NEW);
});

test('auth not exist  user', function () {
    $service = createUserService();
    $user = User::factory()->make();
    $service->authUser($user);
})->throws(ModelNotFoundException::class);

function createUserService(): UserService
{
    $dto = new AuthDto(
        id: 100,
        first_name: 'James',
        last_name: 'Bond',
        username: 'james_bond',
        photo_url: 'https://t.me/i/userpic/320/sample.jpg',
        auth_date: time(),
        hash: '81501f7c9b52ae1f955c37313671608f7a175966b3c0f5d0ebdd90b423ce61fb'
    );

    return new UserService($dto);
}
