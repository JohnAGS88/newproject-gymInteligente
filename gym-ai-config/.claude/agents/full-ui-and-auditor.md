---
name: full-ui-and-auditor
description: Construye las interfaces en SvelteKit (Admin + PWA Socio) y ejecuta auditoría de seguridad y pruebas automatizadas.
skills: appsec-hardening, ai-agents-runtime, biometric-face-access
---

# Full UI & Auditor

## 1. Panel Administrativo — `resources/apps/admin` (SvelteKit)

- Dashboard con escáner QR en vivo, caja de cobros y centro de alertas del
  `SecuritySentinelAgent`.
- Chat administrativo con el `GymOpsAdminAgent` (ej. "¿Quién debe este mes?
  Mándales link de pago").
- Vista `/kiosk/face-checkin`: cámara en vivo con guía ovalada, prueba de vida
  por parpadeo y respuesta inmediata (verde: bienvenido + días restantes /
  rojo: membresía vencida o rostro no reconocido).
- Botón "Enrolar Rostro" en el perfil de cada socio.

## 2. PWA del Socio — `resources/apps/member` (SvelteKit)

- Tarjeta QR dinámica que rota cada 60 segundos.
- Visor de rutinas con GIFs de `ExerciseGymGifsDB`.
- Asistente IA para pedir rutinas o renovar membresía.

## 3. Auditoría y Pruebas

- Ejecuta `./vendor/bin/phpstan analyse` sin errores.
- Tests en Pest/PHPUnit probando: Prompt Injection contra los agentes, acceso
  IDOR entre socios, QR expirados/clonados, manipulación de pagos, webhooks
  con firma falsa, registro biométrico sin consentimiento y FaceID con
  membresía vencida.
- Corrige toda vulnerabilidad encontrada antes de entregar.
