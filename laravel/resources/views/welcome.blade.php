@extends('layouts.default')
@section('content')
    <div class="max-w-screen-xl mx-auto p-4 text-center">
        <h1 class="text-center text-xl mb-3">Test Job for FullStack PHP Developer (Laravel) position.</h1>
        <a target="_blank" href="https://github.com/PankovAlxndr/6f062fc4-test" rel="nofollow"
           class="text-center font-medium text-blue-600 dark:text-blue-500 hover:underline">Read more</a>

        @guest
            <div class="flex justify-center mt-10 mb-5">
                <script async src="https://telegram.org/js/telegram-widget.js?22"
                        data-telegram-login="users_pankovalxndr_bot"
                        data-size="large" data-userpic="false" data-auth-url="/signin-telegram"
                        data-request-access="write"></script>
            </div>
        @endguest
    </div>

@stop
