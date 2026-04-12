<?php


return [

    /*
    |--------------------------------------------------------------------------
    | Cross-Origin Resource Sharing (CORS) Configuration = Intercambio de Recursos de Origen Cruzado.
    |--------------------------------------------------------------------------
    */

    // Definimos aqui qué rutas de Laravel se aplican a las reglas del CORS. 
    'paths' => ['api/*', 'sanctum/csrf-cookie'],

    // Todos los métodos = *
    'allowed_methods' => ['*'],

    // Aqui se define quién tiene permiso para consumir las API
    // En el server lo pondré así: FRONTEND_URL=https://eldomainqueelijamos.com. Importante que pongamos dominio sino otro dominio nos podría pillar las API (los datos para ser más exactos)
    'allowed_origins' => [env('FRONTEND_URL' /*Primero checa aquí (.env) y si no está checa a lo siguiente */ , 'http://localhost:5173')],

    // Entiendo que aqui se definen cosas especificas como darle permiso a un subdominio escribiendo *.dominio.com
    'allowed_origins_patterns' => [],

    // Cuando VUE hace peticiones estas llevan cabeceras ocultas como por ejemplo los tokens de auth, el tipo de contenido del JSON, etc. COn * pertmitimos todo.
    'allowed_headers' => ['*'],

    // Al revés de arriba, VUE a veces recibe cabeceras por parte del back (en este caso Laravel), yo lo dejo vacío porque no tengo ni puta idea de si lo necesitamos para este proyecto.
    'exposed_headers' => [],

    // Es el tiempo máximo que se le permite al navegador guardar cache de CORS.
    // En 0 CORS checará esto en cada petición importante.
    // De normal en producción la gente pone 24h (86400) para ahorrar milisengundos de carga.
    'max_age' => 0,

    // Está en true para que todo el sistema de auth que hay entre front y back funcione. Por ejemplo fallarái el envío de tokens de vue a laravel.
    'supports_credentials' => true,

];
