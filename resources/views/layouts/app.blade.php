<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Manajemen Cuti') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts (Assuming Vite or similar asset compilation) -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased" style="background-color: #F8F8F8;">
        <div class="min-h-screen">
            <!-- Header (Navigation Bar) -->
            @include('layouts.navigation')

            <!-- Page Heading -->
            @if (isset($header))
                <header class="shadow-sm" style="background-color: #FFFFFF; border-bottom: 1px solid #F0F0F0;">
                    <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                        {{ $header }}
                    </div>
                </header>
            @endif

            <!-- Page Content (BODY CONTENT) -->
            <main>
                {{ $slot }}
            </main>
        </div>

        <!-- FOOTER BARU (Menggunakan warna cokelat gelap #473C33) -->
        <footer style="background-color: #473C33; color: #FFF; padding: 1.5rem 0;">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center text-sm">
                <p>&copy; {{ date('Y') }} Sistem Manajemen Cuti Karyawan.</p>
            </div>
        </footer>
    </body>
</html>