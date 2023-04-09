<?php

use App\Models\User;
use Illuminate\Http\UploadedFile;
use function Pest\Laravel\delete;
use function Pest\Laravel\get;
use function Pest\Laravel\patch;
use function Pest\Laravel\post;

test('users list screen can be rendered', function () {
    get(route('users.index'))->assertStatus(200);
});

test('create user form screen can be rendered', function () {
    get(route('users.create'))->assertStatus(200);
});

test('specific user screen can be rendered', function () {
    $user = User::factory()->create();
    get(route('users.edit', $user))
        ->assertStatus(200)
        ->assertSee($user->name);
});

test('create new user with avatar', function () {
    Storage::fake('public');
    $image = UploadedFile::fake()->image('avatar.jpg');
    $payload = [
        'id' => 1,
        'avatar' => $image,
        'name' => 'James Bond',
        'telegram_login' => 'james_bond',
        'telegram_id' => 999,
        'description' => 'Lorem Ipsum is simply dummy text of the printing and typesetting industry.',
    ];

    post(route('users.store'), $payload)
        ->assertStatus(302);

    $user = User::find($payload['id']);

    expect($user)
        ->toBeInstanceOf(User::class)
        ->and($user->name)->toBe($payload['name'])
        ->and($user->telegram_login)->toBe($payload['telegram_login'])
        ->and($user->telegram_id)->toBe($payload['telegram_id'])
        ->and($user->description)->toBe($payload['description'])
        ->and($user->avatar)->toBe('avatars/'.$image->hashName())
        ->and($user->getAvatarPath())->toBe(url('/storage/'.$user->avatar));

    Storage::disk('public')->assertExists('avatars/'.$image->hashName());
});

test('create new user without avatar', function () {
    $payload = [
        'id' => 1,
        'name' => 'James Bond',
        'telegram_login' => 'james_bond',
        'telegram_id' => 999,
        'description' => 'Lorem Ipsum is simply dummy text of the printing and typesetting industry.',
    ];

    post(route('users.store'), $payload)
        ->assertStatus(302);

    $user = User::find($payload['id']);

    expect($user)
        ->toBeInstanceOf(User::class)
        ->and($user->name)->toBe($payload['name'])
        ->and($user->telegram_login)->toBe($payload['telegram_login'])
        ->and($user->telegram_id)->toBe($payload['telegram_id'])
        ->and($user->description)->toBe($payload['description'])
        ->and($user->avatar)->toBeNull()
        ->and($user->getAvatarPath())->toBe('https://placehold.jp/150x150.png');

});

test('edit existing user', function () {

    $user = User::factory()->create();

    $payload = [
        'id' => $user->id,
        'name' => 'James Bond',
        'telegram_login' => 'james_bond',
        'telegram_id' => 999,
        'description' => 'Lorem Ipsum is simply dummy text of the printing and typesetting industry.',
    ];

    patch(route('users.update', $user), $payload)
        ->assertStatus(302);

    $user = User::find($user->id);

    expect($user)
        ->toBeInstanceOf(User::class)
        ->and($user->name)->toBe($payload['name'])
        ->and($user->telegram_login)->toBe($payload['telegram_login'])
        ->and($user->telegram_id)->toBe($payload['telegram_id'])
        ->and($user->description)->toBe($payload['description']);
});

test('delete existing user', function () {
    $user = User::factory()->create();

    delete(route('users.destroy', $user))->assertStatus(302);

    $user = User::find($user->id);
    expect($user)->toBeNull();
});
