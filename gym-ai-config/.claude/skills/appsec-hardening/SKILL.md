---
name: appsec-hardening
description: Blindaje de seguridad para formularios, tarjetas de crédito, acceso físico y auditoría financiera.
---

# Estándares de Blindaje

## 1. Tarjetas de Crédito

Cero almacenamiento local. Todo cobro usa sesiones tokenizadas externas y
Webhooks firmados criptográficamente. Ningún endpoint, Tool de IA ni log puede
leer o escribir números de tarjeta.

## 2. Formularios Blindados

Uso obligatorio de `FormRequest` con reglas estrictas, sanitización de strings
(`strip_tags`), protección CSRF y validación de UUIDs. Toda escritura pasa por
una `Policy`.

## 3. Auditoría Inmutable de Caja

Cualquier pago en efectivo, descuento manual o cancelación de membresía hecha
por un empleado queda registrada en `activity_log` con IP, usuario y valores
anteriores/nuevos.

## 4. QR Dinámico Anti-Clonación

Token JWT/HMAC que rota cada 60 segundos vinculado al `device_id` del socio.
Un token presentado dos veces o fuera de su ventana dispara alerta al
`SecuritySentinelAgent`.

## 5. Webhooks

Verificación de firma obligatoria (Stripe `Webhook::constructEvent` o
equivalente), tolerancia de timestamp corta e idempotencia por `event_id`.
