<?php

use App\Models\Tag;
use function Pest\Laravel\delete;
use function Pest\Laravel\get;
use function Pest\Laravel\patch;
use function Pest\Laravel\post;

test('tags list screen can be rendered', function () {
    get(route('tags.index'))->assertStatus(200);
});

test('create tag form screen can be rendered', function () {
    get(route('tags.create'))->assertStatus(200);
});

test('specific tag screen can be rendered', function () {
    $tag = Tag::factory()->create();
    get(route('tags.edit', $tag))
        ->assertStatus(200)
        ->assertSee($tag->name);
});

test('create new tag', function () {
    $payload = ['id' => 1, 'name' => 'foobar'];
    post(route('tags.store'), $payload)
        ->assertStatus(302);
    $tag = Tag::find($payload['id']);

    expect($tag)
        ->toBeInstanceOf(Tag::class)
        ->and($tag->name)->toBe($payload['name']);
});

test('create new tag with existing name', function () {
    Tag::factory()->create(['name' => 'foobar', 'slug' => 'foobar']);
    post(route('tags.store'), ['name' => 'foobar', 'slug' => 'foobar'])
        ->assertStatus(302)
        ->assertSessionHasErrors('name');

    $this->assertCount(1, Tag::all());
});

test('edit existing tag', function () {
    $tag = Tag::factory()->create();
    $payload = [
        'id' => $tag->id,
        'name' => 'foo bar',
    ];
    patch(route('tags.update', $tag), $payload)
        ->assertStatus(302);
    $tag = Tag::find($tag->id);

    expect($tag)
        ->toBeInstanceOf(Tag::class)
        ->and($tag->name)->toBe($payload['name']);
});

test('delete existing tag', function () {
    $tag = Tag::factory()->create();
    delete(route('tags.destroy', $tag))->assertStatus(302);

    $tag = Tag::find($tag->id);
    expect($tag)->toBeNull();
});
