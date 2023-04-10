<?php

use App\Models\Group;
use function Pest\Laravel\delete;
use function Pest\Laravel\get;
use function Pest\Laravel\patch;
use function Pest\Laravel\post;

test('groups list screen can be rendered', function () {
    get(route('groups.index'))->assertStatus(200);
});

test('create group form screen can be rendered', function () {
    get(route('groups.create'))->assertStatus(200);
});

test('specific group screen can be rendered', function () {
    $group = Group::factory()->create();
    get(route('groups.edit', $group))
        ->assertStatus(200)
        ->assertSee($group->name);
});

test('create new group', function () {
    $payload = ['id' => 1, 'name' => 'foo bar'];
    post(route('groups.store'), $payload)
        ->assertStatus(302);
    $group = Group::find($payload['id']);

    expect($group)
        ->toBeInstanceOf(Group::class)
        ->and($group->name)->toBe($payload['name']);
});

test('create new group with existing name', function () {
    Group::factory()->create(['name' => 'foobar', 'slug' => 'foobar']);
    post(route('groups.store'), ['name' => 'foobar', 'slug' => 'foobar'])
        ->assertStatus(302)
        ->assertSessionHasErrors('name');

    $this->assertCount(1, Group::all());
});

test('edit existing group', function () {
    $group = Group::factory()->create();
    $payload = [
        'id' => $group->id,
        'name' => 'foo bar',
    ];
    patch(route('groups.update', $group), $payload)
        ->assertStatus(302);
    $group = Group::find($group->id);

    expect($group)
        ->toBeInstanceOf(Group::class)
        ->and($group->name)->toBe($payload['name']);
});

test('delete existing group', function () {
    $group = Group::factory()->create();
    delete(route('groups.destroy', $group))->assertStatus(302);

    $group = Group::find($group->id);
    expect($group)->toBeNull();
});
