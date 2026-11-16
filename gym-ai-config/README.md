# GymInteligente — Configuración de Agentes IA

Paquete de configuración (skills, agents, contextos y prompts) para construir el
Sistema Integral de Gestión de Gimnasio con agentes IA internos.

## Estructura

```
gym-ai-config/
├── README.md                          # Este archivo
├── CLAUDE.md                          # Contexto raíz del proyecto (léelo primero)
├── .claude/
│   ├── agents/                        # Subagentes especializados
│   │   ├── core-architect.md          # Núcleo Laravel: DB, roles, pagos, QR
│   │   ├── ai-brain-builder.md        # Agentes IA con Prism PHP
│   │   └── full-ui-and-auditor.md     # SvelteKit + auditoría de seguridad
│   └── skills/                        # Conocimiento reutilizable (SKILL.md)
│       ├── ai-agents-runtime/
│       ├── appsec-hardening/
│       └── biometric-face-access/
└── prompts/                           # Prompts de orquestación (copiar/pegar)
    ├── 01-mision-principal.md         # Construcción del sistema completo
    └── 02-modulo-faceid.md            # Módulo de reconocimiento facial
```

## Cómo usar

1. Copia `CLAUDE.md` y la carpeta `.claude/` a la raíz del repo
   `newproject-gymInteligente`.
2. Arranca tu agente de código y pega el contenido de
   `prompts/01-mision-principal.md`.
3. Cuando termine la fase principal, pega `prompts/02-modulo-faceid.md`
   para agregar el acceso biométrico con prueba de vida.

## Instalación de modelos faciales (requerido por el kiosco)

```bash
cd resources/apps/admin
npm install @vladmandic/face-api
mkdir -p static/models
npx degit vladmandic/face-api/model static/models
cd ../../../
```
