---
name: biometric-face-access
description: Implementa registro facial seguro (embeddings cifrados), prueba de vida (liveness) y control de acceso en recepción.
---

# Reglas de Seguridad Biométrica (FaceID Gimnasio)

## 1. Prohibido guardar fotografías faciales

El frontend (`resources/apps/admin`) procesa el rostro localmente con
`@vladmandic/face-api` y envía únicamente el vector numérico (`Float32Array`
de 128 dimensiones) al backend. Ninguna imagen sale del dispositivo del kiosco.

## 2. Cifrado en Base de Datos

Laravel almacena el vector facial usando el cast `encrypted:array` en el modelo
`MemberBiometric`, nunca en texto plano. Requiere `consent_signed_at` —
cumplimiento legal de datos biométricos.

## 3. Prueba de Vida Obligatoria (Anti-Spoofing)

El kiosco de entrada debe verificar detección de parpadeo (*Eye Aspect Ratio*)
o movimiento facial antes de enviar el descriptor al endpoint
`/api/v1/checkin/face`.

## 4. Doble Validación de Membresía y Fraude

- El endpoint verifica similitud facial (umbral de distancia < 0.45) **y**
  estado de membresía `active`.
- Si el rostro coincide pero la membresía está `expired`, muestra alerta roja
  en pantalla y el `FrontDeskBotAgent` ofrece renovar en ese instante.
- Si falla 3 veces seguidas o detecta foto estática, el `SecuritySentinelAgent`
  bloquea el kiosco 30 segundos y alerta a recepción.

## Setup del kiosco

```bash
cd resources/apps/admin
npm install @vladmandic/face-api
mkdir -p static/models
npx degit vladmandic/face-api/model static/models
cd ../../../
```
