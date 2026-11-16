# Prompt 02 — Módulo de Reconocimiento Facial (FaceID)

Lee la skill `.claude/skills/biometric-face-access/SKILL.md` y agrega el módulo
completo de Reconocimiento Facial con Prueba de Vida al sistema de gimnasio.

## 1. Backend (`core-architect`)

- Crea la migración y modelo `MemberBiometric` con `user_id` (UUID),
  `face_descriptor` (cifrado con `encrypted:array`), `consent_signed_at`
  (cumplimiento legal de datos biométricos) e `is_active`.
- Crea el endpoint `POST /api/v1/members/{uuid}/biometrics` (solo accesible
  por `admin` o `receptionist` tras validar consentimiento del socio).
- Crea el endpoint `POST /api/v1/checkin/face` protegido con `throttle`, que
  compare el vector entrante contra los socios activos de la sucursal,
  registre el acceso en `qr_checkins` (tipo `face_id`) y reporte anomalías
  al `SecuritySentinelAgent`.

## 2. Frontend Kiosco de Recepción (`full-ui-and-auditor`)

- En `resources/apps/admin`, crea la vista `/kiosk/face-checkin` usando
  `@vladmandic/face-api` y los modelos en `/models`.
- Implementa la interfaz tipo FaceID: cámara en vivo con guía ovalada,
  detección automática de parpadeo (prueba de vida) para evitar engaños con
  fotos, y respuesta visual/sonora inmediata:
  - Verde: Bienvenido + días restantes de plan.
  - Rojo: Membresía vencida o rostro no reconocido.
- Agrega el botón "Enrolar Rostro" en el perfil de cada socio dentro del
  panel administrativo.

## 3. Pruebas de Seguridad

- El descriptor facial se guarda cifrado en la base de datos.
- No se permite registrar biometría sin consentimiento explícito.
- Un socio con membresía vencida es rechazado aunque su rostro coincida.
- El umbral de similitud (< 0.45) no acepta rostros distintos.

## Setup previo del kiosco

```bash
cd resources/apps/admin
npm install @vladmandic/face-api
mkdir -p static/models
npx degit vladmandic/face-api/model static/models
cd ../../../
```
