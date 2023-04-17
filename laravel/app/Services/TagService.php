<?php

namespace App\Services;

use App\Models\Tag;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

class TagService
{
    private ?Collection $cleanTags = null;

    public function getCleanTags(): ?Collection
    {
        return $this->cleanTags;
    }

    public function persistTags(Collection $collection): void
    {
        if ($collection->isEmpty()) {
            throw new \InvalidArgumentException('Коллекция тегов пуста');
        }

        $this->cleanTagsCollection($collection)
            ->chunk(1000) // маловероятно, что разом прилетит 1000+ тегов
            ->each(function (Collection $chunk) {
                $safe = $chunk->map(function (string $tag) {
                    return ['name' => $tag, 'slug' => Str::slug($tag)];
                });
                Tag::upsert($safe->toArray(), 'slug');
            });
    }

    private function cleanTagsCollection(Collection $collection): Collection
    {
        $this->cleanTags = $collection
            ->map(fn (string $tag) => Str::replace(',', ' ', $tag))
            ->map(fn (string $tag) => preg_replace('/^\s+|\s+$|\s+(?=\s)/', '', $tag))
            ->map(fn (string $tag) => Str::lower($tag))
            ->unique();

        return $this->cleanTags;
    }
}
