<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class TagSeeder extends Seeder
{
    public function run(): void
    {
        \App\Models\Tag::factory()->createMany([
            ['name' => 'PHP', 'slug' => 'php'],
            ['name' => 'Go', 'slug' => 'go'],
            ['name' => 'JS', 'slug' => 'js'],
            ['name' => 'SQL', 'slug' => 'sql'],
        ]);
    }
}
