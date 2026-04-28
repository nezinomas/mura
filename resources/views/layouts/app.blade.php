<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" data-theme="lofi">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'mura.') }}</title>

        @vite(['resources/css/app.css', 'resources/js/app.js'])
        @livewireStyles
    </head>
    <body class="text-base-content antialiased bg-base-200 min-h-screen">
        @include('layouts.navigation')

        @isset($header)
            <header class="bg-base-100 border-b border-base-300">
                <div class="max-w-4xl mx-auto py-4 px-4 sm:px-6 lg:px-8">
                    {{ $header }}
                </div>
            </header>
        @endisset

        <main class="max-w-4xl mx-auto pt-8 px-4 sm:px-6 lg:px-8">
            {{ $slot }}
        </main>

        <footer class="pb-12 pt-12 border-t border-base-300/30 text-center">
            <div class="flex justify-center gap-12 pb-8">
                <a href="{{ route('rules') }}" class="text-sm opacity-40 hover:opacity-100 hover:underline transition-opacity text-base-content">
                    House Rules
                </a>
                <a href="{{ route('correspondence.create') }}" class="text-sm opacity-40 hover:opacity-100 hover:underline transition-opacity text-base-content">
                    Correspondence
                </a>
            </div>
        </footer>

        @livewireScripts
    </body>
</html>