<?php

namespace App\Http\Controllers;

use App\Action\User\AttachAvatarAction;
use App\Action\User\AttachTagAction;
use App\Events\User\DeleteUserEvent;
use App\Events\User\RegisterUserEvent;
use App\Http\Requests\Users\StoreRequest;
use App\Http\Requests\Users\UpdateRequest;
use App\Models\Group;
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

    public function store(StoreRequest $request)
    {
        $user = User::create(
            array_merge(
                ['remember_token' => Str::random(10)],
                $request->safe()->only('name', 'description', 'telegram_login', 'telegram_id', 'group_id')
            )
        );

        RegisterUserEvent::dispatch($user);

        if ($request->safe()->has('tags')) {
            $action = new AttachTagAction(new TagService());
            $tagCollection = collect(json_decode($request->validated('tags'), true));
            $user = $action->execute($user, $tagCollection);
        }

        if ($request->hasFile('avatar')) {
            $action = new AttachAvatarAction();
            $user = $action->execute($user, $request->file('avatar'));
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

    public function update(UpdateRequest $request, User $user)
    {
        $user->updateOrFail($request->safe()->only('name', 'description', 'telegram_login', 'telegram_id', 'group_id'));

        if ($request->safe()->has('tags')) {
            $action = new AttachTagAction(new TagService());
            $tagCollection = collect(json_decode($request->validated('tags'), true));
            $user = $action->execute($user, $tagCollection);
        }

        if ($request->hasFile('avatar')) {
            $action = new AttachAvatarAction();
            $user = $action->execute($user, $request->file('avatar'));
        }

        return redirect()->route('users.edit', $user);
    }

    public function destroy(User $user)
    {
        $this->authorize('delete', $user);
        $user->delete();
        DeleteUserEvent::dispatch($user);

        return redirect()->route('users.index');
    }

    public function logout(Request $request)
    {
        Auth::logout();

        return redirect()->route('users.index');
    }
}
