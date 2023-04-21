<?php

use App\Jobs\RemoveAvatarJob;
use App\Models\Group;
use App\Models\Tag;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use function Pest\Laravel\actingAs;
use function Pest\Laravel\get;

test('unauthenticated can not be rendered users screen', function () {
    get(route('users.index'))->assertRedirectToRoute('index');
});

test('unauthenticated can not be rendered create user form screen', function () {
    get(route('users.create'))->assertRedirectToRoute('index');
});

test('unauthenticated can not be rendered specific user screen', function () {
    $user = User::factory()->create();
    get(route('users.edit', $user))->assertRedirectToRoute('index');
});

test('not admin can not be rendered users screen', function () {
    $group = Group::factory(['slug' => 'new'])->create();
    $user = User::factory(['group_id' => $group->id])->create();
    actingAs($user)->get(route('users.index'))->assertRedirectToRoute('index');
});

test('not admin can not be rendered create user form screen', function () {
    $group = Group::factory(['slug' => 'new'])->create();
    $user = User::factory(['group_id' => $group->id])->create();
    actingAs($user)->get(route('users.create'))->assertRedirectToRoute('index');
});

test('not admin can not be rendered specific user screen', function () {
    $group = Group::factory(['slug' => 'new'])->create();
    $user = User::factory(['group_id' => $group->id])->create();
    actingAs($user)->get(route('users.edit', $user))->assertRedirectToRoute('index');
});

test('admin can be rendered users list screen', function () {
    $group = Group::factory(['slug' => 'admin'])->create();
    $user = User::factory(['group_id' => $group->id])->create();
    actingAs($user)->get(route('users.index'))->assertSuccessful();
});

test('admin can be rendered create user form screen', function () {
    $group = Group::factory(['slug' => 'admin'])->create();
    $user = User::factory(['group_id' => $group->id])->create();
    actingAs($user)->get(route('users.create'))->assertSuccessful();
});

test('admin can be rendered specific user screen', function () {
    $group = Group::factory(['slug' => 'admin'])->create();
    $user = User::factory(['group_id' => $group->id])->create();
    actingAs($user)->get(route('users.edit', $user))->assertSuccessful();
});

test('admin create new user with avatar', function () {
    $group = Group::factory(['slug' => 'admin'])->create();
    $user = User::factory(['group_id' => $group->id])->create();
    $image = UploadedFile::fake()->image('avatar.jpg');
    $userGroup = Group::factory(['slug' => 'new'])->create();

    Storage::fake('s3-avatar');

    $payload = [
        'id' => 2,
        'avatar' => $image,
        'name' => 'James Bond',
        'telegram_login' => 'james_bond',
        'telegram_id' => 999,
        'description' => 'Lorem Ipsum is simply dummy text of the printing and typesetting industry.',
        'tags' => '[{"value":"php"},{"value":"go"},{"value":"java"}]',
        'group_id' => $userGroup->id,
    ];

    actingAs($user)->post(route('users.store'), $payload)
        ->assertRedirectToRoute('users.edit', $payload['id']);

    $createdUser = User::find($payload['id']);

    expect($createdUser)
        ->toBeInstanceOf(User::class)
        ->and($createdUser->name)->toBe($payload['name'])
        ->and($createdUser->telegram_login)->toBe($payload['telegram_login'])
        ->and($createdUser->telegram_id)->toBe($payload['telegram_id'])
        ->and($createdUser->description)->toBe($payload['description'])
        ->and($createdUser->avatar)->toBeString()
        ->and($createdUser->tags)->toHaveCount(3)
        ->and($createdUser->tags)->toContainOnlyInstancesOf(Tag::class)
        ->and($createdUser->group_id)->toBe($userGroup->id);
});

test('create new user without avatar', function () {
    $group = Group::factory(['slug' => 'admin'])->create();
    $user = User::factory(['group_id' => $group->id])->create();

    $userGroup = Group::factory(['slug' => 'new'])->create();
    $payload = [
        'id' => 2,
        'name' => 'James Bond',
        'telegram_login' => 'james_bond',
        'telegram_id' => 999,
        'description' => 'Lorem Ipsum is simply dummy text of the printing and typesetting industry.',
        'group_id' => $userGroup->id,
    ];

    actingAs($user)->post(route('users.store'), $payload)
        ->assertRedirectToRoute('users.edit', $payload['id']);

    $createdUser = User::find($payload['id']);

    expect($createdUser)
        ->toBeInstanceOf(User::class)
        ->and($createdUser->name)->toBe($payload['name'])
        ->and($createdUser->telegram_login)->toBe($payload['telegram_login'])
        ->and($createdUser->telegram_id)->toBe($payload['telegram_id'])
        ->and($createdUser->description)->toBe($payload['description'])
        ->and($createdUser->avatar)->toBe('//dummyimage.com/150x150/787878/fff.jpg')
        ->and($createdUser->getRawOriginal('avatar'))->toBeNull()
        ->and($createdUser->tags)->toHaveCount(0)
        ->and($createdUser->group_id)->toBe($userGroup->id);
});

test('edit existing user', function () {
    Queue::fake();
    $group = Group::factory(['slug' => 'admin'])->create();
    $user = User::factory(['group_id' => $group->id])->create();

    $createdGroup = Group::factory()->create();
    $createdUser = User::factory()->create();

    $payload = [
        'id' => $createdUser->id,
        'name' => 'James Bond',
        'telegram_login' => 'james_bond',
        'telegram_id' => 999,
        'description' => 'Lorem Ipsum is simply dummy text of the printing and typesetting industry.',
        'group_id' => $createdGroup->id,
    ];

    actingAs($user)->patch(route('users.update', $createdUser), $payload)
        ->assertRedirectToRoute('users.edit', $createdUser);

    $createdUser = User::find($createdUser->id);

    expect($createdUser)
        ->toBeInstanceOf(User::class)
        ->and($createdUser->name)->toBe($payload['name'])
        ->and($createdUser->telegram_login)->toBe($payload['telegram_login'])
        ->and($createdUser->telegram_id)->toBe($payload['telegram_id'])
        ->and($createdUser->description)->toBe($payload['description'])
        ->and($createdUser->group_id)->toBe($createdGroup->id);
});

test('delete existing user', function () {
    Queue::fake();
    $group = Group::factory(['slug' => 'admin'])->create();
    $user = User::factory(['group_id' => $group->id])->create();
    $createdUser = User::factory()->create();

    actingAs($user)->delete(route('users.destroy', $createdUser))
        ->assertRedirectToRoute('users.index');

    Queue::assertPushed(RemoveAvatarJob::class);

    $createdUser = User::find($createdUser->id);
    expect($createdUser)->toBeNull();
});

test('self delete exception', function () {
    $group = Group::factory(['slug' => 'admin'])->create();
    $user = User::factory(['group_id' => $group->id])->create();

    actingAs($user)->delete(route('users.destroy', $user->id))
        ->assertStatus(403);

    $existingUser = User::find($user->id);
    expect($existingUser)->toBeInstanceOf(User::class);
});

test('logout user', function () {
    $group = Group::factory(['slug' => 'admin'])->create();
    $user = User::factory(['group_id' => $group->id])->create();

    actingAs($user)->post(route('users.logout'))
        ->assertRedirectToRoute('users.index');

    $this->assertGuest();
});
