<?php

namespace App\Http\Controllers;

use App\Http\Requests\Users\ChangeTagRequest;
use App\Http\Requests\Users\StoreRequest;
use App\Http\Requests\Users\UpdateRequest;
use App\Models\Tag;
use App\Models\User;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;

class UserController extends Controller
{
    public function index()
    {
        $users = User::select(['id', 'name', 'avatar', 'telegram_login'])->latest()->simplePaginate(10);

        return view('pages.users.index', compact('users'));
    }

    public function create()
    {
        $tags = Tag::all();

        return view('pages.users.create', compact('tags'));
    }

    public function store(StoreRequest $request)
    {
        //todo: rollback case (removing file)
        $avatarPath = [];
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
                $request->safe()->only('name', 'description', 'telegram_login', 'telegram_id')
            )
        );
        if ($tags = $request->safe()->only('tag')) {
            $user->tags()->attach($tags['tag']);
        }

        return redirect()->route('users.edit', $user);
    }

    public function edit(User $user)
    {
        $user->load('tags');
        $tags = Tag::all();

        return view('pages.users.edit', compact('user', 'tags'));
    }

    public function update(UpdateRequest $request, User $user)
    {
        //todo: rollback case (removing file)
        $avatarPath = [];
        if ($request->hasFile('avatar')) {
            $avatarPath['avatar'] = $request->file('avatar')->store('avatars', 'public');
        }

        $user->updateOrFail(
            array_merge(
                $avatarPath,
                $request->safe()->only('name', 'description', 'telegram_login', 'telegram_id')
            )
        );

        return redirect()->route('users.edit', $user);
    }

    public function destroy(User $user)
    {
        $user->delete();

        return redirect()->route('users.index');
    }

    public function changeTag(ChangeTagRequest $request, User $user, Tag $tag)
    {
        if ($request->safe(['state'])['state'] === true) {
            if ($user->tags->isEmpty() || $user->tags->find($tag->id)->count() === 0) {
                $user->tags()->attach($tag);
            }
        } elseif ($request->safe(['state'])['state'] === false) {
            $user->tags()->detach($tag);
        }

        return response()->noContent(201);
    }
}
