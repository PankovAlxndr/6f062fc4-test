<?php

use App\Models\Group;
use App\Models\User;
use function Pest\Laravel\artisan;

test('not enough argument', function () {
    artisan('user:admin');
})->throws(RuntimeException::class);

test('not exist login', function () {
    artisan('user:admin james_bond')
        ->assertSuccessful()
        ->expectsOutput('No query results for telegram_login: james_bond');
});

test('already admin login', function () {
    $group = Group::factory(['id' => 2, 'slug' => 'admin'])->create();
    User::factory([
        'telegram_login' => 'james_bond',
        'group_id' => $group->id,
    ])->create();

    artisan('user:admin james_bond')
        ->assertSuccessful()
        ->expectsOutput('Login: james_bond already admin');
});

test('set group admin login', function () {
    $group = Group::factory(['id' => 1, 'slug' => 'novyi'])->create();
    User::factory([
        'telegram_login' => 'james_bond',
        'group_id' => $group->id,
    ])->create();

    artisan('user:admin james_bond')
        ->assertSuccessful()
        ->expectsOutput('Successful! Login: james_bond set admin group');
});
