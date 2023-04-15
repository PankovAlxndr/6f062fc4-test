<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class TagSeeder extends Seeder
{
    public function run(): void
    {
        \App\Models\Tag::factory()->createMany([
            ['name' => 'php', 'slug' => 'php'],
            ['name' => 'go', 'slug' => 'go'],
            ['name' => 'js', 'slug' => 'js'],
            ['name' => 'sql', 'slug' => 'sql'],
        ]);
    }
}
