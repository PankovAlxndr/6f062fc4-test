@extends('layouts.default')
@section('content')
    <div class="max-w-screen-md mx-auto p-4">

        <div class="flex items-center justify-between mb-3">
            <h2 class="font-semibold text-xl mb-3">User id:{{$user->id}}</h2>
            <a href="{{ url()->previous() }}"
               class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm w-full sm:w-auto px-5 py-2.5 text-center dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800">
                Go back
            </a>
        </div>

        <form action="{{ route('users.update', $user) }}" method="POST" enctype="multipart/form-data"
              class="block p-6 bg-white border border-gray-200 rounded-lg  shadow-lg  dark:bg-gray-800 dark:border-gray-700 ">
            @method('PATCH')
            @csrf
            <input type="hidden" name="id" value="{{$user->id}}">
            <div class="mb-6">
                @if($user->isExistAvatar())
                    <img class="w-48 w-48 mb-3" src="{{$user->getAvatarpath()}}" alt="image description">
                @endif
                <label
                    class="block mb-2 text-sm font-medium  @error('avatar') text-red-700 dark:text-red-500 @else text-gray-900 dark:text-white @enderror"
                    for="user_avatar">Avatar</label>
                <input name="avatar"
                       accept="image/*"
                       class="border text-sm rounded-lg block w-full p-2.5
                       @error('avatar') bg-red-50 border-red-500 text-red-900 placeholder-red-700 focus:ring-red-500 focus:border-red-500 dark:bg-red-100 dark:border-red-400
                       @else bg-gray-50 border-gray-300 text-gray-900  focus:ring-blue-500 focus:border-blue-500  dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                       @enderror
                       aria-describedby="user_avatar_help" id="user_avatar" type="file">
                @error('avatar')<p class="mt-1 text-sm text-red-600 dark:text-red-500">{{$message}}</p>@enderror
            </div>

            <div class="mb-6">
                <label for="name"
                       class="block mb-2 text-sm font-medium @error('name') text-red-700 dark:text-red-500 @else text-gray-900 dark:text-white @enderror">User
                    name</label>
                <input type="text" id="name" name="name"
                       class="border  text-sm rounded-lg block w-full p-2.5
                       @error('name') bg-red-50 border-red-500 text-red-900 placeholder-red-700 focus:ring-red-500 focus:border-red-500 dark:bg-red-100 dark:border-red-400
                       @else bg-gray-50 border-gray-300 text-gray-900  focus:ring-blue-500 focus:border-blue-500  dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                       @enderror
                       placeholder="User name" value="{{ old('name', $user->name) }}">
                @error('name')<p class="mt-1 text-sm text-red-600 dark:text-red-500">{{$message}}</p>@enderror
            </div>

            <div class="mb-6">
                <label for="group_id"
                       class="block mb-2 text-sm font-medium @error('group_id') text-red-700 dark:text-red-500 @else text-gray-900 dark:text-white @enderror">Group</label>
                <select id="group_id" name="group_id"
                        class="border text-sm rounded-lg block w-full p-2.5
                        @error('group_id') bg-red-50 border-red-500 text-red-900 placeholder-red-700 focus:ring-red-500 focus:border-red-500 dark:bg-red-100 dark:border-red-400
                        @else bg-gray-50 border-gray-300 text-gray-900 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                    @enderror>
                    @foreach ($groups as $group)
                        <option value="{{ $group->id }}"
                            @selected( old('group_id', $user->group_id) == $group->id)>
                            {{ $group->name }}
                        </option>
                    @endforeach
                </select>
                @error('group_id')<p class="mt-1 text-sm text-red-600 dark:text-red-500">{{$message}}</p>@enderror
            </div>

            <div class="mb-6">
                <label for="telegram_login"
                       class="block mb-2 text-sm font-medium @error('telegram_login') text-red-700 dark:text-red-500 @else text-gray-900 dark:text-white @enderror">Telegram
                    login</label>
                <input type="text" id="telegram_login" name="telegram_login"
                       class="border text-sm rounded-lg block w-full p-2.5
                       @error('telegram_login') bg-red-50 border-red-500 text-red-900 placeholder-red-700 focus:ring-red-500 focus:border-red-500 dark:bg-red-100 dark:border-red-400
                       @else bg-gray-50 border-gray-300 text-gray-900  focus:ring-blue-500 focus:border-blue-500  dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                       @enderror
                       placeholder="Telegram login" value="{{ old('telegram_login', $user->telegram_login) }}">
                @error('telegram_login')<p class="mt-1 text-sm text-red-600 dark:text-red-500">{{$message}}</p>@enderror
            </div>

            <div class="mb-6">
                <label for="telegram_id"
                       class="block mb-2 text-sm font-medium @error('telegram_id') text-red-700 dark:text-red-500 @else text-gray-900 dark:text-white @enderror">Telegram
                    id</label>
                <input type="text" id="telegram_id" name="telegram_id"
                       class="border text-sm rounded-lg block w-full p-2.5
                       @error('telegram_id') bg-red-50 border-red-500 text-red-900 placeholder-red-700 focus:ring-red-500 focus:border-red-500 dark:bg-red-100 dark:border-red-400
                       @else bg-gray-50 border-gray-300 text-gray-900  focus:ring-blue-500 focus:border-blue-500  dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                       @enderror
                       placeholder="Telegram login" value="{{ old('telegram_id', $user->telegram_id) }}">
                @error('telegram_id')<p class="mt-1 text-sm text-red-600 dark:text-red-500">{{$message}}</p>@enderror
            </div>

            <div class="mb-6">
                <label for="message"
                       class="block mb-2 text-sm font-medium  @error('description') text-red-700 dark:text-red-500 @else text-gray-900 dark:text-white @enderror">User
                    description</label>
                <textarea id="message" rows="4" name="description"
                          class="border text-sm rounded-lg block w-full p-2.5
                          @error('description') bg-red-50 border-red-500 text-red-900 placeholder-red-700 focus:ring-red-500 focus:border-red-500 dark:bg-red-100 dark:border-red-400
                          @else bg-gray-50 border-gray-300 text-gray-900  focus:ring-blue-500 focus:border-blue-500  dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                          @enderror
                          placeholder="Leave a comment...">{{ old('description', $user->description) }}</textarea>
                @error('description')<p class="mt-1 text-sm text-red-600 dark:text-red-500">{{$message}}</p>@enderror
            </div>

            <div class="mb-6">
                <label for=""
                       class="block mb-2 text-sm font-medium @error('tags') text-red-700 dark:text-red-500 @else text-gray-900 dark:text-white @enderror">Tags</label>
                <div class="flex flex-wrap">
                    <input id="tagify" name="tags" value='{{ old('tags', $tags) }}'>
                </div>

                @error('tags')<p class="mt-1 text-sm text-red-600 dark:text-red-500">{{$message}}</p>@enderror
            </div>

            <div class="flex items-center justify-between">
                <button
                    class="js-remove text-white bg-red-700 hover:bg-red-800 focus:ring-4 focus:outline-none focus:ring-red-300 font-medium rounded-lg text-sm w-full sm:w-auto px-5 py-2.5 text-center dark:bg-red-600 dark:hover:bg-red-700 dark:focus:ring-red-800">
                    Delete
                </button>
                <button type="submit"
                        class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm w-full sm:w-auto px-5 py-2.5 text-center dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800">
                    Update
                </button>
            </div>
        </form>



        <form method="post"
              id="remove-from"
              action="{{route('users.destroy', $user)}}" style="display: none">
            @csrf
            @method('DELETE')
        </form>

    </div>

@endsection

@section('scripts')
    <script>
        document.addEventListener("DOMContentLoaded", () => {
            const btn = document.querySelector('.js-remove');
            const form = document.querySelector('#remove-from');
            const tagInput = document.querySelector('#tagify');

            if (btn && form) {
                btn.addEventListener('click', function (event) {
                    event.preventDefault();
                    if (confirm('Are you sure you want to delete the this user?'))
                        form.submit();
                })
            }

            if (tagInput)
                tagify = new Tagify(tagInput);
        });
    </script>
@endsection

