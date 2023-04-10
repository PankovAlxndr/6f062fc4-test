<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class GroupSeeder extends Seeder
{
    public function run(): void
    {
        \App\Models\Group::factory()->createMany([
            ['name' => 'Новый', 'slug' => 'novyj'],
            ['name' => 'Админ', 'slug' => 'admin'],
        ]);
    }
}
