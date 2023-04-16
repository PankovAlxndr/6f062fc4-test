@extends('layouts.default')
@section('content')
    <div class="max-w-screen-xl mx-auto p-4">

        <div class="flex justify-end mb-3">
            <a href="{{route('users.create')}}"
               class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm w-full sm:w-auto px-5 py-2.5 text-center dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800">
                Create new user
            </a>
        </div>
        <div class="relative overflow-x-auto shadow-lg rounded-lg ">
            <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
                <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                <tr>
                    <th scope="col" class="px-6 py-3">
                        User
                    </th>
                    <th scope="col" class="px-6 py-3">
                        Group
                    </th>
                    <th scope="col" class="px-6 py-3 text-right">
                        Action
                    </th>
                </tr>
                </thead>
                <tbody>
                @foreach($users as $user)
                    <tr class="bg-white @if(!$loop->last) border-b @endif dark:bg-gray-800 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600">
                        <th scope="row"
                            class="flex items-center px-6 py-4 text-gray-900 whitespace-nowrap dark:text-white">
                            <img class="w-10 h-10 rounded-full" src="{{$user->avatar}}"
                                 alt="{{$user->name}}">
                            <div class="pl-3">
                                <div class="text-base font-semibold">{{$user->name}}</div>
                                <div class="font-normal text-gray-500">{{'@'.$user->telegram_login}}</div>
                            </div>
                        </th>
                        <td class="px-6 py-4">
                            <div class="font-normal text-gray-500">{{$user->group->name}}</div>
                        </td>
                        <td class="px-6 py-4 text-right">
                            <a href="{{route('users.edit', $user)}}"
                               class="font-medium text-blue-600 dark:text-blue-500 hover:underline">Edit
                                user</a>
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
        <div class="mt-3">
            {{ $users->links() }}

        </div>
    </div>
@endsection

