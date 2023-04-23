<?php

namespace App\Action\User;

use App\Models\Tag;
use App\Models\User;
use App\Services\TagService;
use Illuminate\Support\Collection;

class AttachTagAction
{
    public function __construct(
        public readonly TagService $tagService
    ) {
    }

    public function execute(User $user, Collection $tags): User
    {
        if($tags->isEmpty()) {
            return $user;
        }

        $this->tagService->persistTags($tags->pluck('value'));
        if ($cleanTags =  $this->tagService->getCleanTags()) {
            $tagsDb = Tag::whereIn('name', $cleanTags->toArray())->get();
            $user->tags()->attach($tagsDb);
        }

        return $user;
    }
}
