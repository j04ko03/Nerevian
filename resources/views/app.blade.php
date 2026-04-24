<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>{{ config('app.name', 'Nerevian | Gestión Logística') }}</title>
    <link href="https://fonts.googleapis.com/css2?family=Instrument+Sans:ital,wght@0,400..700;1,400..700&display=swap"
        rel="stylesheet">

    <link rel="icon" href="/favicon.png" type="image/png">

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600,700,800" rel="stylesheet" />

    @vite(['resources/js/main.js'])
</head>

<body class="font-sans antialiased">
    <div id="app"></div>

    <link href="https://cdn.jsdelivr.net/npm/@n8n/chat/dist/style.css" rel="stylesheet" />
    <style>
        /* Forzamos que el chat esté por encima de cualquier capa de Vue/Laravel */
        .chat-window {
            z-index: 999999 !important;
        }

        /* Protegemos la caja de texto para que no herede resets de Tailwind */
        .chat-window input,
        .chat-window textarea {
            all: revert !important;
            pointer-events: auto !important;
        }
    </style>
    <script type="module">
        import { createChat } from 'https://cdn.jsdelivr.net/npm/@n8n/chat/chat.bundle.es.js';

        createChat({
            // Obligamos a que use localhost
            webhookUrl: 'http://localhost:5678/webhook/3e2f1022-a06a-40d0-a401-077128195595/chat',
            initialMessages: [
                '¡Hola! Soy el analista de datos de Nerevian.',
                'Puedes preguntarme lo que quieras sobre comercio internacional.'
            ]
        });
    </script>

</body>

</html>