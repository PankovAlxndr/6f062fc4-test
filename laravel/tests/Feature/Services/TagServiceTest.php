<?php

use App\Models\Tag;
use App\Services\TagService;

test('empty tags collection', function () {
    $tagService = new TagService();
    $tagService->persistTags(collect());
})->throws(InvalidArgumentException::class);

test('add new tags', function () {
    $tagService = new TagService();
    $tags = collect(['PhP', 'sQl', 'JS', 'SQL', 'GO', 'sql', 'php', 'sQl']);
    $tagService->persistTags($tags);
    $tags = Tag::all();

    expect($tags)->toHaveCount(4)
        ->and($tags->containsStrict('slug', 'php'))->toBeTrue()
        ->and($tags->containsStrict('slug', 'go'))->toBeTrue()
        ->and($tags->containsStrict('slug', 'sql'))->toBeTrue()
        ->and($tags->containsStrict('slug', 'js'))->toBeTrue();
});

test('add new tags bullshit tag', function () {
    $tagService = new TagService();
    $tags = collect([' hello,   world   ']);
    $tagService->persistTags($tags);
    $tags = Tag::all();

    expect($tags)->toHaveCount(1)
        ->and($tags->containsStrict('slug', 'hello-world'))->toBeTrue()
        ->and($tags->containsStrict('name', 'hello world'))->toBeTrue();
});

test('add existing tags', function () {
    Tag::factory()->createMany([
        ['name' => 'php', 'slug' => 'php'],
        ['name' => 'go', 'slug' => 'go'],
        ['name' => 'js', 'slug' => 'js'],
        ['name' => 'sql', 'slug' => 'sql'],
    ]);
    $tagService = new TagService();
    $tags = collect(['php', 'sql', 'foo', 'go', 'js', 'sql']);
    $tagService->persistTags($tags);
    $tags = Tag::all();

    expect($tags)->toHaveCount(5)
        ->and($tags->containsStrict('slug', 'foo'))->toBeTrue();
});
