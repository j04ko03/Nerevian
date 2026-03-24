# Roadmap Nerevian: Integración Login y Registro (Vue SPA + Laravel Sanctum)

Este documento resume todas las ediciones, creaciones y configuraciones realizadas en esta sesión para transformar el frontend a una SPA de Vue (desechando Inertia/SSR) y habilitar la autenticación de API mediante Laravel Sanctum, utilizando los modelos y campos en catalán ya existentes en la base de datos SQL Server.

---

## 🛠 Backend (Laravel API + Sanctum)

### 1. Configuración Principal
- **`composer.json`**: Se eliminó la dependencia de Fortify (gestión manual por el usuario).
- **`.env.example`** / **`.env`**: 
  - Se configuró la conexión a SQL Server (`DB_CONNECTION=sqlsrv`).
  - Se agregaron las variables `SANCTUM_STATEFUL_DOMAINS=localhost:5173` y `FRONTEND_URL=http://localhost:5173` para CORS y cookies seguras.
- **`config/cors.php`** (CREADO): Configurado para permitir peticiones desde el `FRONTEND_URL` con envío de credenciales (`supports_credentials => true`).
- **`bootstrap/app.php`** (MODIFICADO): Se agregó el enrutamiento de la API (`api: __DIR__.'/../routes/api.php'`).
- **`routes/web.php`** (MODIFICADO): Se cambió para que cualquier ruta de la web (`/{any}`) devuelva la vista `app.blade.php` (el punto de anclaje de Vue SPA).
- **`routes/api.php`** (CREADO): Definición de rutas específicas de la API: `/login`, `/registration-requests` (públicas), `/logout`, `/me` (protegidas por middleware de Sanctum).
- **`resources/views/app.blade.php`** (MODIFICADO): Se eliminaron las directivas de Inertia SSR y se dejó un simple `<div id="app"></div>` junto con `@vite(['resources/js/main.js'])`.
- **`database/migrations/2026_03_23_220000_create_personal_access_tokens_table.php`** (CREADO): Migración manual de la tabla para los tokens de Sanctum (porque `install:api` falló por el driver de SQL Server).

### 2. Modelos y Base de Datos (Adaptados a BD en Catalán)
- **`app/Models/usuaris.php`** (MODIFICADO):
  - Se cambió para extender `Illuminate\Foundation\Auth\User as Authenticatable`.
  - Se añadió el trait `HasApiTokens` para Sanctum.
  - Campos usados: `correu`, `contrasenya`, `nom`, `cognoms`.
  - Se ocultaron `$hidden = ['contrasenya', 'token']`.
- **`app/Models/peticions_registre.php`** (MODIFICADO):
  - Adaptación de la relación `belongsTo` a `usuaris`.
  - Campos usados al crear: `nom_empresa`, `contacte`, `correu`, `telefon`, `missatge`, `estat` (por defecto '0'), `data_creacio`.

 *(Los modelos originales no fueron renombrados, se mantuvieron tal cual para no romper el ecosistema).*

### 3. Controladores y Recursos API
- **`app/Http/Controllers/Auth/AuthController.php`** (CREADO): 
  - Gestión completa de autenticación: `login` (validando `correu` y `contrasenya` con `Hash::check`), `logout` (revoca el token actual), `me` (retorna datos del usuario).
  - Carga la relación `rols` durante el auth para saber el nivel de acceso.
- **`app/Http/Controllers/Auth/RegistrationRequestController.php`** (CREADO): 
  - Valida el array `nom_empresa`, `contacte`, `correu`, `telefon`, `missatge`. 
  - Registra en BD inyectando `estat => '0'` y `data_creacio => Carbon::now()`.
- **`app/Http/Resources/UserResource.php`** (CREADO): 
  - Formatea la salida JSON ocultando los campos sensibles y extrayendo `nom`, `cognoms`, `correu` y el objeto interno del rol (`rols?->nom`).

---

## 🎨 Frontend (Vue SPA + Vite)

### 1. Configuración e Infraestructura
- **`package.json`**: Se instalaron `axios`, `pinia` y `vue-router`.
- **`vite.config.ts`** (MODIFICADO): 
  - Eliminado soporte de SSR y punto de entrada `app.ts`. 
  - Nuevo punto de entrada definido: `resources/js/main.js`.
- **`resources/js/main.js`** (CREADO): El nuevo entrypoint nativo de Vue que instancia `App.vue` usando Pinia y el Router de Vue, sin pasar por InertiaJs.
- **`resources/js/App.vue`** (CREADO): Raíz de la aplicación conteniendo únicamente `<router-view />`.
- **`resources/js/plugins/axios.js`** (CREADO): 
  - Instancia personalizada. Configurada para interceptar todas las peticiones y añadir el `Authorization: Bearer <token>`.
  - Escucha de respuestas (status `401`) para forzar un log-out / limpieza del localstorage si el token expira.
- **`resources/js/stores/authStore.js`** (CREADO):
  - Usa Pinia para estado global.
  - Acciones asíncronas de `login()` (con payload de `correu`/`contrasenya`), `logout()` y `fetchMe()`.
  - Utiliza `localStorage` para persistencia del usuario/token en cada refresco de pantalla.
- **`resources/js/router/index.js`** (CREADO): 
  - Define las rutas del lado del cliente (`/login`, `/solicitar-registro`, `/solicitud-enviada`, `/dashboard`).
  - Configura *Route Guards* para re-enviar a login si se exige token y a dashboard si ya se está logueado pero intenta ver el login.

### 2. Archivos Visuales (Réplica 1:1 de los Diseños)
- **`public/logo.svg`** (CREADO): 
  - Archivo copiado exitosamente desde `storage/assets/Logo vector.svg` para uso público.
- **`resources/js/layouts/AuthNerevianLayout.vue`** (CREADO): 
  - Contenedor agnóstico, centrado y con fondo crema (`#f7f3ef`). Muestra el logotipo Vectorizado copiado en la cabecera.
- **`resources/js/views/auth/LoginView.vue`** (CREADO): 
  - Formulario de login exacto a la captura. Usa el Layout centrado y campos nativos `correu` / `contrasenya` (v-model referenciando al store Pinia).
- **`resources/js/views/auth/SolicitarRegistro.vue`** (CREADO): 
  - Grid simétrico (2 columnas en vista PC) para el registro, campos con el scope catalan del backend.
- **`resources/js/views/auth/SolicitudEnviada.vue`** (CREADO): 
  - Pantalla indicadora de "Solicitud Confirmada" con el icono check (`scaleIn`).
- **`resources/js/views/DashboardView.vue`** (CREADO): 
  - Pantalla post-login que dibuja un Navbar básico, un botón de cierre de sesión y renderiza dinámicamente el `nom` + `cognoms` + `rols` del usuario provenientes de la caché Pinia.

---

## 🗑 Archivos limpios / Eliminados
- `app/Providers/FortifyServiceProvider.php` (Eliminado por usuario).
- `config/fortify.php` (Eliminado por usuario).
- Borrados modelos fallidos (`Usuari.php`, `PeticionsRegistre.php`, `Rol.php`) generados momentáneamente en inglés y revertido todo el uso hacia los ficheros `usuaris.php`, `peticions_registre.php` y `rols.php` originarios.
