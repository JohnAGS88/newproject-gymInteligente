# AGENTS.md — Convenciones de desarrollo (GymInteligente)

Guía operativa para cualquier agente/subagente que trabaje en este repo.
El contexto de dominio está en `CLAUDE.md`. Esto es el "cómo".

## Stack y entorno

- Laravel 12 (v12.72) + PHP 8.5 + MySQL 8.4 + Redis, vía **Laravel Sail (Docker)**.
- **No hay PHP/Composer nativos.** Toda ejecución se hace dentro de Sail.
- Frontend: SvelteKit en `resources/apps/admin` y `resources/apps/member`.

## Cómo ejecutar comandos (obligatorio)

```bash
./vendor/bin/sail artisan <comando>        # artisan
./vendor/bin/sail composer require <pkg>    # composer
./vendor/bin/sail artisan test              # Pest
./vendor/bin/sail exec laravel.test vendor/bin/phpstan analyse  # phpstan
./vendor/bin/sail npm run build             # frontend build
./vendor/bin/sail up -d                     # levantar contenedores (ya están arriba)
```

Nunca uses `php`, `composer`, `mysql` o `npm` del host. Usa `./vendor/bin/sail`.

## Claves primarias: UUID v7 (NO negociable)

- Todas las tablas de dominio expuestas por la API usan **UUID v7** como PK.
- En migraciones: `$table->uuid('id')->primary();` y FKs con `$table->foreignUuid(...)`.
- En modelos: importar y usar el trait `App\Models\Concerns\HasUuidsV7`.
- Rutas API direccionan por `{uuid}`, nunca por id incremental (protección IDOR).
- Los morphs (activity_log, personal_access_tokens, permission) ya están ajustados a
  `nullableUuidMorphs`/`uuidMorphs`/`uuid(...)`. No regresarlos a `morphs`.

## Roles (spatie/laravel-permission)

`superadmin`, `admin`, `receptionist`, `trainer`, `member`.
Crear permisos granulares por recurso (ej. `manage-members`, `view-own-routines`).

## Multi-sucursal

Toda tabla operativa lleva `branch_id` (FK uuid a `branches`). Los modelos
operativos usan un global scope de sucursal según el usuario autenticado
(excepto `superadmin`, que ve todas).

## Invariantes de seguridad (de CLAUDE.md)

1. Cero datos de tarjeta en DB (solo Stripe tokenizado + webhooks firmados).
2. Ningún agente IA ni endpoint salta permisos: toda `Tool` verifica `Auth::user()`.
3. Toda acción financiera/destructiva queda en `activity_log` (IP, usuario, antes/después).
4. Todo endpoint tiene `FormRequest` + `Policy`.
5. Cero fotografías faciales: solo embeddings cifrados (`encrypted:array`).
6. QR dinámico anti-clonación (HMAC/JWT 60s ligado a `device_id`).

## Endpoints

- Prefijo `/api/v1/...`. Autenticación por token Sanctum (abilities por recurso).
- Nombres de recursos en plural (`/members`, `/branches`, `/qr-checkins`).

## Calidad

- `./vendor/bin/sail artisan test` debe pasar siempre.
- phpstan (Larastan) nivel 8 sin errores: `./vendor/bin/sail exec laravel.test vendor/bin/phpstan analyse`.
- Código en español para mensajes de cara al usuario; identificadores/métodos en inglés.

## Paquetes ya instalados

`spatie/laravel-permission` (8.5), `spatie/laravel-activitylog` (5.1),
`laravel/cashier` (16.10) + `stripe-php`, `prism-php/prism` (0.103),
`laravel/sanctum` (4.4), `pestphp/pest` (4.x), `larastan/larastan` (3.15).