<?php

namespace App\Http\Controllers;

use App\Http\Requests\Tags\StoreRequest;
use App\Http\Requests\Tags\UpdateRequest;
use App\Models\Tag;
use Illuminate\Support\Str;

class TagController extends Controller
{
    public function index()
    {
        $tags = Tag::latest()->simplePaginate(10);

        return view('pages.tags.index', compact('tags'));
    }

    public function create()
    {
        return view('pages.tags.create');
    }

    public function store(StoreRequest $request)
    {
        $slug = Str::slug($request->validated('name'));

        if (Tag::whereSlug($slug)->exists()) {
            return back()
                ->withInput()
                ->withErrors(['name' => 'The name has already been taken.']);
        }

        $tag = Tag::create(array_merge(['slug' => $slug], $request->safe()->only('name')));

        return redirect()->route('tags.edit', $tag);
    }

    public function edit(Tag $tag)
    {
        return view('pages.tags.edit', compact('tag'));
    }

    public function update(UpdateRequest $request, Tag $tag)
    {
        $slug = Str::slug($request->validated('name'));

        if (Tag::whereSlug($slug)->whereNot('id', $tag->id)->exists()) {
            return back()
                ->withInput()
                ->withErrors(['name' => 'The name has already been taken.']);
        }

        $tag->updateOrFail(array_merge(['slug' => $slug], $request->safe()->only('name')));

        return redirect()->route('tags.edit', $tag);
    }

    public function destroy(Tag $tag)
    {
        $tag->delete();

        return redirect()->route('tags.index');
    }
}
