@extends('layouts.default')
@section('content')
    <div class="max-w-screen-md mx-auto p-4">

        <div class="flex items-center justify-between mb-3">
            <h2 class="font-semibold text-xl mb-3">Create new group</h2>
            <a href="{{ url()->previous() }}"
               class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm w-full sm:w-auto px-5 py-2.5 text-center dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800">
                Go back
            </a>
        </div>

        <form action="{{ route('groups.store') }}" method="POST" enctype="multipart/form-data"
              class="block p-6 bg-white border border-gray-200 rounded-lg  shadow-lg  dark:bg-gray-800 dark:border-gray-700 ">
            @csrf

            <div class="mb-6">
                <label for="name"
                       class="block mb-2 text-sm font-medium @error('name') text-red-700 dark:text-red-500 @else text-gray-900 dark:text-white @enderror">Group
                    name</label>
                <input type="text" id="name" name="name"
                       class="border text-sm rounded-lg block w-full p-2.5
                       @error('name') bg-red-50 border-red-500 text-red-900 placeholder-red-700 focus:ring-red-500 focus:border-red-500 dark:bg-red-100 dark:border-red-400
                       @else bg-gray-50 border-gray-300 text-gray-900  focus:ring-blue-500 focus:border-blue-500  dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                       @enderror
                       placeholder="User name" value="{{ old('name') }}">
                @error('name')<p class="mt-1 text-sm text-red-600 dark:text-red-500">{{$message}}</p>@enderror
            </div>

            <div class="flex items-center justify-end">
                <button type="submit"
                        class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm w-full sm:w-auto px-5 py-2.5 text-center dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800">
                    Create
                </button>
            </div>
        </form>
    </div>
@endsection

