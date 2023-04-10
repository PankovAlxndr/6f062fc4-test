<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    @include('includes.head')
</head>
<body class="antialiased">
<div class="flex flex-col min-h-screen">
    @include('includes.header')
    <main id="app" class="p-4 flex-grow bg-gray-100">
        @yield('content')
    </main>
    @include('includes.footer')
</div>
@vite('resources/js/app.js')
@yield('scripts')
</body>
</html>
