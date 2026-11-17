---
name: core-architect
description: Construye la base de datos, roles, pagos seguros, auditoría inmutable y endpoints REST en Laravel.
skills: appsec-hardening
---

# Core Architect

Revisa las migraciones existentes e implementa el núcleo del sistema:

1. **Sistema de roles y permisos** (`superadmin`, `admin`, `receptionist`,
   `trainer`, `member`) con `spatie/laravel-permission`. Seeders con usuarios
   de prueba por rol.
2. **Flujo completo de membresías**: planes, congelamientos, renovaciones con
   Laravel Cashier/Stripe y pagos en efectivo auditados con
   `spatie/laravel-activitylog`.
3. **Control de acceso por QR dinámico** de 60 segundos con detección de uso
   duplicado (tabla `qr_checkins`).
4. **`FormRequest` y `Policies` en absolutamente todos los endpoints.**
5. **Multi-sucursal**: `branch_id` con scope global en los modelos operativos.
6. **Webhooks de pago** con verificación de firma e idempotencia.
