<?php

namespace App\Http\Controllers;

use App\Http\Requests\Users\StoreRequest;
use App\Http\Requests\Users\UpdateRequest;
use App\Models\Group;
use App\Models\Tag;
use App\Models\User;
use App\Services\TagService;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
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
        $avatarPath['avatar'] = null;
        if ($request->hasFile('avatar')) {
            $avatarPath['avatar'] = $request->file('avatar')->store('avatars', 'public');
        }

        $user = User::create(
            array_merge(
                [
                    'email' => fake()->unique()->safeEmail(),
                    'email_verified_at' => Carbon::now(),
                    'password' => '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', // password
                    'remember_token' => Str::random(10),
                ],
                $avatarPath,
                $request->safe()->only('name', 'description', 'telegram_login', 'telegram_id', 'group_id')
            )
        );

        if ($tags = $request->safe()->only('tags')) {
            $tagCollection = collect(json_decode($tags['tags'], true));
            $tagService->persistTags($tagCollection->pluck('value'));
            if ($cleanTags = $tagService->getCleanTags()) {
                $tagsDb = Tag::whereIn('name', $cleanTags->toArray())->get();
                $user->tags()->attach($tagsDb);
            }

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
        // todo: rollback case (removing file)
        $avatarPath = [];
        if ($request->hasFile('avatar')) {
            $avatarPath['avatar'] = $request->file('avatar')->store('avatars', 'public');
        }

        $user->updateOrFail(
            array_merge(
                $avatarPath,
                $request->safe()->only('name', 'description', 'telegram_login', 'telegram_id', 'group_id')
            )
        );

        if ($tags = $request->safe()->only('tags')) {
            $tagCollection = collect(json_decode($tags['tags'], true));
            $tagService->persistTags($tagCollection->pluck('value'));
            if ($cleanTags = $tagService->getCleanTags()) {
                $tagsDb = Tag::whereIn('name', $cleanTags->toArray())->get();
                $user->tags()->sync($tagsDb);
            }
        }

        return redirect()->route('users.edit', $user);
    }

    public function destroy(User $user)
    {
        $user->delete();

        return redirect()->route('users.index');
    }

    public function logout(Request $request)
    {
        Auth::logout();

        return redirect()->route('users.index');
    }
}
