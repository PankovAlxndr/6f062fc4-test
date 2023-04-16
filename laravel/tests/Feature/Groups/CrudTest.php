<?php

use App\Models\Group;
use App\Models\User;
use function Pest\Laravel\actingAs;
use function Pest\Laravel\get;

test('unauthenticated can not be rendered groups screen', function () {
    get(route('groups.index'))->assertRedirectToRoute('index');
});

test('unauthenticated can not be rendered create group form screen', function () {
    get(route('groups.create'))->assertRedirectToRoute('index');
});

test('unauthenticated can not be rendered specific group screen', function () {
    $group = Group::factory()->create();
    get(route('groups.edit', $group))->assertRedirectToRoute('index');
});

test('not admin can not be rendered groups screen', function () {
    $group = Group::factory(['slug' => 'new'])->create();
    $user = User::factory(['group_id' => $group->id])->create();
    actingAs($user)->get(route('groups.index'))->assertRedirectToRoute('index');
});

test('not admin can not be rendered create group form screen', function () {
    $group = Group::factory(['slug' => 'new'])->create();
    $user = User::factory(['group_id' => $group->id])->create();
    actingAs($user)->get(route('groups.create'))->assertRedirectToRoute('index');
});

test('not admin can not be rendered specific group screen', function () {
    $group = Group::factory(['slug' => 'new'])->create();
    $user = User::factory(['group_id' => $group->id])->create();
    actingAs($user)->get(route('groups.edit', $group))->assertRedirectToRoute('index');
});

test('admin can be rendered groups screen', function () {
    $group = Group::factory(['slug' => 'admin'])->create();
    $user = User::factory(['group_id' => $group->id])->create();
    actingAs($user)->get(route('groups.index'))->assertSuccessful();
});

test('admin can be rendered create group form screen', function () {
    $group = Group::factory(['slug' => 'admin'])->create();
    $user = User::factory(['group_id' => $group->id])->create();
    actingAs($user)->get(route('groups.create'))->assertSuccessful();
});

test('admin can be rendered specific group screen', function () {
    $group = Group::factory(['slug' => 'admin'])->create();
    $user = User::factory(['group_id' => $group->id])->create();
    actingAs($user)->get(route('groups.edit', $group))->assertSuccessful();
});

test('admin create new group', function () {
    $adminGroup = Group::factory(['slug' => 'admin'])->create();
    $user = User::factory(['group_id' => $adminGroup->id])->create();

    $payload = ['id' => 2, 'name' => 'foo bar'];
    actingAs($user)->post(route('groups.store'), $payload)
        ->assertRedirectToRoute('groups.edit', $payload['id']);

    $group = Group::find($payload['id']);

    expect($group)
        ->toBeInstanceOf(Group::class)
        ->and($group->name)->toBe($payload['name']);
});

test('admin create new group with existing name', function () {
    $adminGroup = Group::factory(['slug' => 'admin'])->create();            // 1
    $user = User::factory(['group_id' => $adminGroup->id])->create();
    Group::factory(10)->create();                                      // 1 + 10 = 11
    Group::factory()->create(['name' => 'foobar', 'slug' => 'foobar']);     // 11 + 1 = 12
    actingAs($user)->post(route('groups.store'), ['name' => 'foobar', 'slug' => 'foobar'])
        ->assertStatus(302)
        ->assertSessionHasErrors('name');

    $this->assertCount(12, Group::all());
});

test('admin edit existing group', function () {
    $adminGroup = Group::factory(['slug' => 'admin'])->create();
    $user = User::factory(['group_id' => $adminGroup->id])->create();
    $group = Group::factory()->create();
    $payload = [
        'id' => $group->id,
        'name' => 'foo bar',
    ];
    actingAs($user)->patch(route('groups.update', $group), $payload)
        ->assertRedirectToRoute('groups.edit', $payload['id']);
    $group = Group::find($group->id);

    expect($group)
        ->toBeInstanceOf(Group::class)
        ->and($group->name)->toBe($payload['name']);
});

test('admin delete existing group', function () {
    $adminGroup = Group::factory(['slug' => 'admin'])->create();
    $user = User::factory(['group_id' => $adminGroup->id])->create();
    $group = Group::factory()->create();
    actingAs($user)->delete(route('groups.destroy', $group))
        ->assertRedirectToRoute('groups.index');

    $group = Group::find($group->id);
    expect($group)->toBeNull();
});

test('admin can not delete group with is_not_delete', function () {
    $adminGroup = Group::factory(['slug' => 'admin'])->create();
    $user = User::factory(['group_id' => $adminGroup->id])->create();
    $group = Group::factory(['is_not_delete' => true])->create();
    actingAs($user)->delete(route('groups.destroy', $group))->assertStatus(403);

    $group = Group::find($group->id);
    expect($group)->toBeInstanceOf(Group::class);
});
