<?php

use App\Models\Group;
use App\Models\Tag;
use App\Models\User;
use function Pest\Laravel\actingAs;
use function Pest\Laravel\get;

test('unauthenticated can not be rendered tags screen', function () {
    get(route('tags.index'))->assertRedirectToRoute('index');
});

test('unauthenticated can not be rendered create tag form screen', function () {
    get(route('tags.create'))->assertRedirectToRoute('index');
});

test('unauthenticated can not be rendered specific tag screen', function () {
    $tag = Tag::factory()->create();
    get(route('tags.edit', $tag))->assertRedirectToRoute('index');
});

test('not admin can not be rendered tags screen', function () {
    $group = Group::factory(['slug' => 'new'])->create();
    $user = User::factory(['group_id' => $group->id])->create();
    actingAs($user)->get(route('tags.index'))->assertRedirectToRoute('index');
});

test('not admin can not be rendered create tag form screen', function () {
    $group = Group::factory(['slug' => 'new'])->create();
    $user = User::factory(['group_id' => $group->id])->create();
    actingAs($user)->get(route('tags.create'))->assertRedirectToRoute('index');
});

test('not admin can not be rendered specific tag screen', function () {
    $group = Group::factory(['slug' => 'new'])->create();
    $user = User::factory(['group_id' => $group->id])->create();
    $tag = Tag::factory()->create();
    actingAs($user)->get(route('tags.edit', $tag))->assertRedirectToRoute('index');
});

test('admin can be rendered tags screen', function () {
    $group = Group::factory(['slug' => 'admin'])->create();
    $user = User::factory(['group_id' => $group->id])->create();
    actingAs($user)->get(route('tags.index'))->assertSuccessful();
});

test('admin can be rendered create tag form screen', function () {
    $group = Group::factory(['slug' => 'admin'])->create();
    $user = User::factory(['group_id' => $group->id])->create();
    actingAs($user)->get(route('tags.create'))->assertSuccessful();
});

test('admin can be rendered specific tag screen', function () {
    $group = Group::factory(['slug' => 'admin'])->create();
    $user = User::factory(['group_id' => $group->id])->create();
    $tag = Tag::factory()->create();
    actingAs($user)->actingAs($user)->get(route('tags.edit', $tag))->assertSuccessful();
});

test('admin create new tag', function () {
    $adminGroup = Group::factory(['slug' => 'admin'])->create();
    $user = User::factory(['group_id' => $adminGroup->id])->create();

    $payload = ['id' => 1, 'name' => 'foobar'];
    actingAs($user)->post(route('tags.store'), $payload)
        ->assertRedirectToRoute('tags.edit', $payload['id']);
    $tag = Tag::find($payload['id']);

    expect($tag)
        ->toBeInstanceOf(Tag::class)
        ->and($tag->name)->toBe($payload['name']);
});

test('admin create new tag with existing name', function () {
    $adminGroup = Group::factory(['slug' => 'admin'])->create();
    $user = User::factory(['group_id' => $adminGroup->id])->create();

    Tag::factory()->create(['name' => 'foobar', 'slug' => 'foobar']);
    actingAs($user)->post(route('tags.store'), ['name' => 'foobar', 'slug' => 'foobar'])
        ->assertStatus(302)
        ->assertSessionHasErrors('name');

    $this->assertCount(1, Tag::all());
});

test('admin edit existing tag', function () {
    $adminGroup = Group::factory(['slug' => 'admin'])->create();
    $user = User::factory(['group_id' => $adminGroup->id])->create();

    $tag = Tag::factory()->create();
    $payload = [
        'id' => $tag->id,
        'name' => 'foo bar',
    ];
    actingAs($user)->patch(route('tags.update', $tag), $payload)
        ->assertRedirectToRoute('tags.edit', $payload['id']);
    $tag = Tag::find($tag->id);

    expect($tag)
        ->toBeInstanceOf(Tag::class)
        ->and($tag->name)->toBe($payload['name']);
});

test('admin delete existing tag', function () {
    $adminGroup = Group::factory(['slug' => 'admin'])->create();
    $user = User::factory(['group_id' => $adminGroup->id])->create();

    $tag = Tag::factory()->create();
    actingAs($user)->delete(route('tags.destroy', $tag))->assertRedirectToRoute('tags.index');

    $tag = Tag::find($tag->id);
    expect($tag)->toBeNull();
});
