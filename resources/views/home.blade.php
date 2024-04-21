<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Laravel</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,600&display=swap" rel="stylesheet"/>

    <!-- Styles -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="antialiased font-sans">
<div class="bg-gray-50 text-black/50">
    <div class="flex  items-center justify-center pt-10">
        <main class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <livewire:video-games.video-games-list/>
        </main>
    </div>
</div>
</body>
</html>
