<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>{{ config('app.name', 'Nerevian | Gestión Logística') }}</title>

        <link rel="icon" href="/favicon.png" type="image/png">

        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600,700,800" rel="stylesheet" />

        @vite(['resources/js/main.js'])
    </head>
    <body class="font-sans antialiased">
        <div id="app"></div>
    </body>
</html>
