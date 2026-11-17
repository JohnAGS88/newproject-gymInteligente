# GymInteligente — Contexto del Proyecto

## Visión

Sistema Integral de Gestión de Gimnasio donde la Inteligencia Artificial no es
un simple chat: es el motor operativo que gestiona la seguridad, la
administración, la cobranza y el entrenamiento, bajo estándares estrictos de
ciberseguridad.

## Arquitectura

- **Backend**: Laravel (API REST). Multi-sucursal (`branch_id` en las tablas operativas).
- **Agentes IA internos**: `prism-php/prism`. Viven en `app/AI/` y operan dentro
  del proceso de Laravel — no son servicios externos.
- **Frontend**: SvelteKit en dos aplicaciones independientes:
  - `resources/apps/admin` — Panel administrativo + kiosco de recepción.
  - `resources/apps/member` — PWA del socio.
- **Permisos**: `spatie/laravel-permission` (roles: `superadmin`, `admin`,
  `receptionist`, `trainer`, `member`).
- **Auditoría**: `spatie/laravel-activitylog` (tabla `activity_log`).
- **Pagos**: Laravel Cashier / Stripe — tokenizado, webhooks firmados.
- **Biometría**: `@vladmandic/face-api` en el kiosco; solo embeddings viajan al backend.

## Estructura de directorios clave

```
app/
├── AI/
│   ├── Agents/        # SecuritySentinelAgent, GymOpsAdminAgent,
│   │                  # SmartCoachAgent, FrontDeskBotAgent
│   ├── Tools/         # Clases Tool tipadas que ejecutan acciones reales
│   └── Guardrails/    # Anti prompt-injection, rate limiting, scopes
├── Http/Requests/     # FormRequest en TODOS los endpoints
└── Policies/          # Policies en TODOS los modelos
resources/apps/
├── admin/             # SvelteKit: dashboard, kiosco QR/FaceID, consola IA
└── member/            # SvelteKit PWA: QR dinámico, rutinas, asistente
```

## Invariantes de seguridad (NO negociables)

1. **Cero datos de tarjeta en la base de datos.** Todo cobro usa sesiones
   tokenizadas externas y webhooks con firma criptográfica verificada.
2. **Ningún agente IA ni endpoint puede saltarse los permisos de rol.**
   Toda `Tool` verifica `Auth::user()` antes de tocar la base de datos.
3. **Toda acción financiera o destructiva queda en `activity_log`** con IP,
   usuario y valores anteriores/nuevos.
4. **Todo endpoint** tiene `FormRequest` con reglas estrictas y `Policy`.
5. **Ninguna fotografía facial en la base de datos.** Solo vectores de 128
   dimensiones cifrados con el cast `encrypted:array` + consentimiento firmado.
6. **QR dinámico anti-clonación**: token HMAC/JWT que rota cada 60 segundos,
   vinculado al `device_id` del socio.

## Base de datos (referencia)

- `users`, `branches`, `membership_plans`, `memberships`, `payments`
- `qr_checkins` (tipos: `qr`, `face_id`)
- `member_biometrics` (`face_descriptor` cifrado, `consent_signed_at`)
- `exercise_gym_gifs` — catálogo `ExerciseGymGifsDB` de ejercicios con GIF
- `activity_log` (spatie), `personal_access_tokens`

## Comandos

```bash
php artisan migrate --seed
./vendor/bin/phpstan analyse      # análisis estático obligatorio
./vendor/bin/pest                 # suite de pruebas (Pest)
```
