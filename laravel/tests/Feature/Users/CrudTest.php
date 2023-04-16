<?php

use App\Models\Group;
use App\Models\Tag;
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
    Group::factory(['id' => Group::GROUP_NEW])->create();
    $payload = [
        'id' => 1,
        'avatar' => $image,
        'name' => 'James Bond',
        'telegram_login' => 'james_bond',
        'telegram_id' => 999,
        'description' => 'Lorem Ipsum is simply dummy text of the printing and typesetting industry.',
        'tags' => '[{"value":"php"},{"value":"go"},{"value":"java"}]',
        'group_id' => Group::GROUP_NEW,
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
        ->and($user->avatar)->toBe(url('/storage/avatars/'.$image->hashName()))
        ->and($user->tags)->toHaveCount(3)
        ->and($user->tags)->toContainOnlyInstancesOf(Tag::class)
        ->and($user->group_id)->toBe(Group::GROUP_NEW);

    Storage::disk('public')->assertExists('avatars/'.$image->hashName());
});

test('create new user without avatar', function () {
    Group::factory(['id' => Group::GROUP_NEW])->create();
    $payload = [
        'id' => 1,
        'name' => 'James Bond',
        'telegram_login' => 'james_bond',
        'telegram_id' => 999,
        'description' => 'Lorem Ipsum is simply dummy text of the printing and typesetting industry.',
        'group_id' => Group::GROUP_NEW,
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
        ->and($user->tags)->toHaveCount(0)
        ->and($user->group_id)->toBe(Group::GROUP_NEW);
});

test('edit existing user', function () {
    $group = Group::factory()->create();
    $user = User::factory()->create();

    $payload = [
        'id' => $user->id,
        'name' => 'James Bond',
        'telegram_login' => 'james_bond',
        'telegram_id' => 999,
        'description' => 'Lorem Ipsum is simply dummy text of the printing and typesetting industry.',
        'group_id' => $group->id,
    ];

    patch(route('users.update', $user), $payload)
        ->assertStatus(302);

    $user = User::find($user->id);

    expect($user)
        ->toBeInstanceOf(User::class)
        ->and($user->name)->toBe($payload['name'])
        ->and($user->telegram_login)->toBe($payload['telegram_login'])
        ->and($user->telegram_id)->toBe($payload['telegram_id'])
        ->and($user->description)->toBe($payload['description'])
        ->and($user->group_id)->toBe($group->id);
});

test('delete existing user', function () {
    $user = User::factory()->create();

    delete(route('users.destroy', $user))->assertRedirectToRoute('users.index');

    $user = User::find($user->id);
    expect($user)->toBeNull();
});

test('logout user', function () {
    $user = User::factory()->create();
    $this->actingAs($user, 'web');
    post(route('users.logout'))->assertRedirectToRoute('users.index');
    $this->assertGuest();
});
