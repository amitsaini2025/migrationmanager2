<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <meta name="current-user-id" content="{{ optional(auth()->user())->id }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <style>
            .broadcast-banner {
                position: fixed;
                bottom: 0;
                left: 0;
                right: 0;
                z-index: 1100;
                display: none;
                flex-direction: column;
                gap: 6px;
                padding: 16px 20px;
                border-radius: 16px 16px 0 0;
                background: linear-gradient(135deg, #0f172a, #2563eb);
                color: #ffffff;
                box-shadow: 0 -12px 25px rgba(15, 23, 42, 0.35);
            }

            .broadcast-banner.is-visible {
                display: flex;
            }

            .broadcast-banner__title {
                font-weight: 600;
                font-size: 16px;
            }

            .broadcast-banner__title:not(.has-title) {
                display: none;
            }

            .broadcast-banner__message {
                font-size: 14px;
                line-height: 1.6;
            }

            .broadcast-banner__meta {
                font-size: 12px;
                opacity: 0.85;
            }

            .broadcast-banner__actions {
                display: flex;
                gap: 8px;
            }

            .broadcast-banner__btn {
                border-radius: 999px;
                border: 0;
                padding: 8px 18px;
                font-size: 13px;
                font-weight: 600;
                cursor: pointer;
                transition: background 0.2s ease;
                background: rgba(255, 255, 255, 0.9);
                color: #0f172a;
            }

            .broadcast-banner__btn:hover {
                background: #ffffff;
            }

            .broadcast-banner__btn--ghost {
                background: transparent;
                border: 1px solid rgba(255, 255, 255, 0.6);
                color: #ffffff;
            }

            .broadcast-banner__btn--ghost:hover {
                background: rgba(255, 255, 255, 0.2);
            }
        </style>

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased">
        <div class="broadcast-banner" data-broadcast-banner>
            <div class="broadcast-banner__title" data-broadcast-title></div>
            <div class="broadcast-banner__message" data-broadcast-message></div>
            <div class="broadcast-banner__meta" data-broadcast-meta></div>
            <div class="broadcast-banner__actions">
                <button type="button" class="broadcast-banner__btn" data-action="mark-read">Mark as read</button>
                <button type="button" class="broadcast-banner__btn broadcast-banner__btn--ghost" data-action="dismiss">Dismiss</button>
            </div>
        </div>
        <div class="min-h-screen bg-gray-100">
            @include('EmailUser.layouts.navigation')

            <!-- Page Heading -->
            @isset($header)
                <header class="bg-white shadow">
                    <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                        {{ $header }}
                    </div>
                </header>
            @endisset

            <!-- Page Content -->
            <main>
                {{ $slot }}
            </main>
        </div>
        <script src="{{ asset('js/broadcasts.js') }}" defer></script>
    </body>
</html>
