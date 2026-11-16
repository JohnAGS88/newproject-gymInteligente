# Prompt 01 — Misión Principal

Lee `CLAUDE.md` y explora la estructura actual del proyecto.

Tu misión es construir un **Sistema Integral de Gestión de Gimnasio** donde la
Inteligencia Artificial no sea un simple chat, sino el motor operativo que
gestiona la seguridad, la administración, la cobranza y el entrenamiento, bajo
estándares estrictos de ciberseguridad.

Ejecuta el proyecto de forma 100% autónoma coordinando tus 3 subagentes en
orden:

## Fase 1 — `core-architect` (núcleo Laravel)

- Base de datos multi-sucursal, roles (`spatie/laravel-permission`), auditoría
  inmutable (`spatie/laravel-activitylog`), pasarela de pagos tokenizada
  (cero tarjetas en DB), check-in por QR dinámico de 60 segundos y validación
  estricta en todos los formularios.

## Fase 2 — `ai-brain-builder` (sistema nervioso IA en `app/AI/`)

- Programa los 4 agentes internos (`SecuritySentinelAgent`,
  `GymOpsAdminAgent`, `SmartCoachAgent`, `FrontDeskBotAgent`) usando
  `prism-php/prism`.
- Dótalos de herramientas reales (`Tools`) para detectar fraudes en
  accesos/caja, automatizar cobranzas, generar reportes financieros y crear
  rutinas con `ExerciseGymGifsDB`.
- Implementa `Guardrails` estrictos contra Prompt Injection y abuso de
  permisos.

## Fase 3 — `full-ui-and-auditor` (frontend y blindaje final)

- Construye en SvelteKit tanto el Panel Administrativo (centro de alertas de
  seguridad IA, escáner QR, consola administrativa) como la PWA del Socio
  (QR dinámico, rutinas animadas, asistente).
- Ejecuta análisis estático (`phpstan`) y pruebas automatizadas (Pest)
  simulando ataques reales (IDOR, QR clonado, inyección de prompts en los
  agentes, webhooks falsos) y corrige cualquier vulnerabilidad encontrada.

## Entrega

Avanza entre fases automáticamente sin detenerte a pedir permiso, asegurándote
de que cada prueba pase antes de continuar. Al finalizar, entrega la guía de
variables `.env` necesarias y los comandos para arrancar el sistema.
