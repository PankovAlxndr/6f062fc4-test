<?php

namespace App\Http\Controllers;

use App\Http\Requests\Groups\StoreRequest;
use App\Http\Requests\Groups\UpdateRequest;
use App\Models\Group;
use Illuminate\Support\Str;

class GroupController extends Controller
{
    public function index()
    {
        $groups = Group::latest()->simplePaginate(10);

        return view('pages.groups.index', compact('groups'));
    }

    public function create()
    {
        return view('pages.groups.create');
    }

    public function store(StoreRequest $request)
    {
        $slug = Str::slug($request->safe()->only('name')['name']);
        if (Group::whereSlug($slug)->exists()) {
            return back()
                ->withInput()
                ->withErrors(['name' => 'The name has already been taken.']);
        }

        $group = Group::create(array_merge(['slug' => $slug], $request->safe()->only('name')));

        return redirect()->route('groups.edit', $group);
    }

    public function edit(Group $group)
    {
        return view('pages.groups.edit', compact('group'));
    }

    public function update(UpdateRequest $request, Group $group)
    {
        $slug = Str::slug($request->safe()->only('name')['name']);
        if (Group::whereSlug($slug)->whereNot('id', $group->id)->exists()) {
            return back()
                ->withInput()
                ->withErrors(['name' => 'The name has already been taken.']);
        }

        $group->updateOrFail(array_merge(['slug' => $slug], $request->safe()->only('name')));

        return redirect()->route('groups.edit', $group);
    }

    public function destroy(Group $group)
    {
        $this->authorize('delete', $group);

        $group->delete();

        return redirect()->route('groups.index');
    }
}
