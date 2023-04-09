<?php

namespace App\Http\Controllers;

use App\Http\Requests\Users\StoreRequest;
use App\Http\Requests\Users\UpdateRequest;
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
        return view('pages.users.create');
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

        return redirect()->route('users.edit', $user);
    }

    public function edit(User $user)
    {
        return view('pages.users.edit', compact('user'));
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
}
