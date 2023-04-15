<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class GroupSeeder extends Seeder
{
    public function run(): void
    {
        \App\Models\Group::factory()->createMany([
            ['name' => 'Новый', 'slug' => 'novyj', 'is_not_delete' => true],
            ['name' => 'Админ', 'slug' => 'admin', 'is_not_delete' => true],
        ]);
    }
}
