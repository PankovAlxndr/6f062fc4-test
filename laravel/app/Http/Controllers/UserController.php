<?php

namespace App\Http\Controllers;

use App\Http\Requests\Users\StoreRequest;
use App\Http\Requests\Users\UpdateRequest;
use App\Jobs\RemoveAvatarJob;
use App\Models\Group;
use App\Models\Tag;
use App\Models\User;
use App\Services\TagService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class UserController extends Controller
{
    public function index()
    {
        $users = User::select(['id', 'name', 'avatar', 'telegram_login', 'group_id'])
            ->with('group')
            ->latest()
            ->simplePaginate(10);

        return view('pages.users.index', compact('users'));
    }

    public function create()
    {
        $groups = Group::all()->sortBy('name');

        return view('pages.users.create', compact('groups'));
    }

    public function store(StoreRequest $request, TagService $tagService)
    {

        $user = User::create(
            array_merge(
                ['remember_token' => Str::random(10)],
                $request->safe()->only('name', 'description', 'telegram_login', 'telegram_id', 'group_id')
            )
        );

        if ($request->safe()->has('tags') && $tags = $request->safe()->only('tags')['tags']) {
            $tagCollection = collect(json_decode($tags, true));
            $tagService->persistTags($tagCollection->pluck('value'));
            if ($cleanTags = $tagService->getCleanTags()) {
                $tagsDb = Tag::whereIn('name', $cleanTags->toArray())->get();
                $user->tags()->attach($tagsDb);
            }
        }

        if ($request->hasFile('avatar')) {
            $extension = $request->file('avatar')->getClientOriginalExtension();
            $path = $request->file('avatar')
                ->storeAs(
                    "/{$user->id}",
                    Str::uuid()->toString().'.'.$extension,
                    's3-avatar'
                );
            $user->update(['avatar' => $path]);
        }

        return redirect()->route('users.edit', $user);
    }

    public function edit(User $user)
    {
        $user->load('tags');
        $tags = $user->tags->implode('name', ',');
        $groups = Group::all()->sortBy('name');

        return view('pages.users.edit', compact('user', 'tags', 'groups'));
    }

    public function update(UpdateRequest $request, User $user, TagService $tagService)
    {
        $user->updateOrFail($request->safe()->only('name', 'description', 'telegram_login', 'telegram_id', 'group_id'));

        if ($request->safe()->has('tags') && $tags = $request->safe()->only('tags')['tags']) {
            $tagCollection = collect(json_decode($tags, true));
            $tagService->persistTags($tagCollection->pluck('value'));
            if ($cleanTags = $tagService->getCleanTags()) {
                $tagsDb = Tag::whereIn('name', $cleanTags->toArray())->get();
                $user->tags()->sync($tagsDb);
            }
        }

        if ($request->hasFile('avatar')) {
            $extension = $request->file('avatar')->getClientOriginalExtension();
            $path = $request->file('avatar')
                ->storeAs(
                    "/{$user->id}",
                    Str::uuid()->toString().'.'.$extension,
                    's3-avatar'
                );
            RemoveAvatarJob::dispatch($user->getRawOriginal('avatar')); // todo можно будет переместить в обсервер или вызвать событие
            $user->update(['avatar' => $path]);
        }

        return redirect()->route('users.edit', $user);
    }

    public function destroy(User $user)
    {
        $this->authorize('delete', $user);

        if ($user->avatar) {
            RemoveAvatarJob::dispatch($user->getRawOriginal('avatar')); // todo можно будет переместить в обсервер или вызвать событие
        }

        $user->delete();

        return redirect()->route('users.index');
    }

    public function logout(Request $request)
    {
        Auth::logout();

        return redirect()->route('users.index');
    }
}
