# Happy Pizza — Gestión de carta y pedidos

Aplicación web para mostrar una carta de pizzas, tomar pedidos de clientes autenticados y administrar ingredientes, pizzas y pedidos. Backend en **Laravel 13**, interfaz en **Vue 3** con **Inertia** y **TypeScript**.

## Requisitos previos

- **PHP** 8.3 o superior (extensiones habituales: `pdo`, `mbstring`, `openssl`, `tokenizer`, `xml`, `ctype`, `json`, `fileinfo`)
- **Composer** 2.x
- **Node.js** 22 o superior y **npm**
- **Base de datos**: SQLite (por defecto en desarrollo) o MySQL/PostgreSQL

## Puesta en marcha (desarrollo local)

1. **Clonar el repositorio e instalar dependencias PHP**

   ```bash
   composer install
   ```

2. **Entorno**

   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

   Ajusta en `.env` al menos `APP_URL`, base de datos (`DB_*`) y, si vas a enviar correos reales, las variables de correo (ver comentarios en `.env.example`). Para desarrollo rápido puedes dejar `MAIL_MAILER=log` y `QUEUE_CONNECTION=sync`.

3. **Base de datos**

   ```bash
   php artisan migrate
   ```

   Opcional — datos de demo (admin, cliente y pizzas de ejemplo):

   ```bash
   php artisan db:seed
   ```

   Tras el seed existen, entre otros, `test@example.com` (admin) y `cliente@example.com` (cliente); la contraseña por defecto del factory es **`password`**.

4. **Frontend**

   ```bash
   npm ci
   npm run dev
   ```

5. **Servidor Laravel** (otra terminal)

   ```bash
   php artisan serve
   ```

   Abre la URL que indique el comando (por defecto `http://127.0.0.1:8000`).

6. **Cola de trabajos** (solo si usas `QUEUE_CONNECTION=database` u otro driver asíncrono)

   Sin un worker activo, los correos de confirmación de pedido no se envían en segundo plano:

   ```bash
   php artisan queue:work
   ```

## Comandos útiles

| Comando | Descripción |
|--------|-------------|
| `php artisan test` | Suite de tests (PHPUnit) |
| `npm run build` | Compilación de assets para producción |
| `vendor/bin/pint` | Formateo PHP (Laravel Pint) |

## Arquitectura (resumen)

El proyecto organiza la lógica de negocio en capas claras sobre el monolito Laravel:

- **`app/Http`**: entrada HTTP. **Controladores** delgados (Inertia y API), **Form Requests** para validar entradas y **API Resources** para dar forma a las respuestas JSON.
- **`app/Application`**: casos de uso (**Actions**), **DTOs** y orquestación sin acoplarse a HTTP.
- **`app/Domain/Contracts`**: interfaces de repositorios; el dominio no conoce Eloquent.
- **`app/Infrastructure/Repositories`**: implementaciones concretas con **Eloquent**; enlazadas en `AppServiceProvider`.
- **`app/Models`**: modelos Eloquent (`User`, `Ingredient`, `Pizza`, `Order`) y relaciones.

**Rutas y clientes**

- **Web (`routes/web.php`)**: páginas Inertia (carta pública, área de cliente, panel admin). Autenticación con **Laravel Fortify** (sesión).
- **API (`routes/api.php`)**: REST bajo el prefijo `/api` (configuración por defecto de Laravel). La API usa la misma sesión que el SPA (cookies + **CSRF**), no tokens Bearer, para que el front Vue pueda consumirla de forma segura desde el mismo origen.

**Pedidos y correo**

Al confirmarse un pedido se dispara un evento de dominio; un listener encola **`SendOrderConfirmationEmailJob`**, que envía **`OrderPlacedMail`** (Markdown). El driver de cola por defecto es `database`; en producción suele ejecutarse un proceso `queue:work` aparte.

**Front**

- **`resources/js/pages`**: vistas por ruta (carta, admin, ajustes).
- **Vue 3 + Composition API**, **Tailwind CSS** y componentes al estilo **shadcn-vue**.

Para despliegue en plataformas como Railway, revisa `nixpacks.toml`, `railway.toml` y las variables de entorno descritas en `.env.example` (incluido correo vía **Brevo API** cuando SMTP no está disponible).

## Licencia

MIT.
