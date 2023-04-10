<?php

use App\Models\Tag;
use App\Models\User;
use function Pest\Laravel\patch;

test('attach duplicate tag to exist user', function () {
    $user = User::factory()->create();
    $tag = Tag::factory()->create();
    $user->tags()->attach($tag);

    patch(route('users.change-tag', [$user, $tag]), ['state' => true])
        ->assertStatus(201);

    $user->refresh();

    expect($user->tags)->toHaveCount(1)
        ->and($user->tags)->toContainOnlyInstancesOf(Tag::class);
});

test('attach exist tag to exist user', function () {
    $user = User::factory()->create();
    $tag = Tag::factory()->create();

    patch(route('users.change-tag', [$user, $tag]), ['state' => true])
        ->assertStatus(201);

    expect($user->tags)->toHaveCount(1)
        ->and($user->tags)->toContainOnlyInstancesOf(Tag::class);
});

test('attach not exist tag to exist user', function () {
    $user = User::factory()->create();

    patch(route('users.change-tag', [$user, 1]), ['state' => true])
        ->assertStatus(404);
});

test('detach tag', function () {
    $user = User::factory()->create();
    $tag1 = Tag::factory()->create();
    $tag2 = Tag::factory()->create();
    $user->tags()->attach($tag1);
    $user->tags()->attach($tag2);

    patch(route('users.change-tag', [$user, $tag1]), ['state' => false])
        ->assertStatus(201);

    $user->refresh();

    expect($user->tags)->toHaveCount(1)
        ->and($user->tags)->toContainOnlyInstancesOf(Tag::class);
});
