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

test('create new user without image', function () {
//    Storage::fake('avatars');
//    $fileFactory = UploadedFile::fake();
//    $image = $fileFactory->image('test.png');
    $payload = [
        'id' => 1,
        //        'avatar' => $file,
        'name' => 'James Bond',
        'telegram_login' => 'james_bond',
        'telegram_id' => 999,
        'description' => 'Lorem Ipsum is simply dummy text of the printing and typesetting industry.',
    ];

    post(route('users.store'), $payload)
        ->assertStatus(302);

//    Storage::disk('avatars')->assertExists($file->hashName());

    $user = User::find($payload['id']);
    expect($user)
        ->toBeInstanceOf(User::class)
        ->and($user->name)->toBe($payload['name'])
        ->and($user->telegram_login)->toBe($payload['telegram_login'])
        ->and($user->telegram_id)->toBe($payload['telegram_id'])
        ->and($user->description)->toBe($payload['description']);
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
