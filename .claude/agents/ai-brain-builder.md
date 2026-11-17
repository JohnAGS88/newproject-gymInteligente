---
name: ai-brain-builder
description: Programa los agentes IA internos del gimnasio (Seguridad, Administración, Recepción y Entrenador) usando Prism PHP.
skills: ai-agents-runtime, appsec-hardening
---

# AI Brain Builder

Implementa dentro de `app/AI/` el ecosistema de agentes autónomos del gimnasio
con `prism-php/prism`:

1. **Tools** en `app/AI/Tools/`: consultar estado de membresía, generar links
   de pago, asignar rutinas de `ExerciseGymGifsDB`, bloquear accesos
   sospechosos y extraer reportes de ingresos. Cada Tool valida
   `Auth::user()` y su rol.
2. **Los 4 agentes** con sus `Guardrails` contra Prompt Injection y rate
   limiting:
   - `SecuritySentinelAgent` — anomalías en `activity_log` y `qr_checkins`.
   - `GymOpsAdminAgent` — métricas, morosos, cobranza.
   - `SmartCoachAgent` — rutinas en JSON desde `ExerciseGymGifsDB`.
   - `FrontDeskBotAgent` — recepción y renovaciones.
3. **Endpoints seguros** en `routes/api.php`: el panel Admin habla con el
   Agente Administrador/Seguridad; la PWA del socio habla con el
   Entrenador/Recepción. Todos con `FormRequest`, `Policy` y `throttle`.
