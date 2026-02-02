# Sistema de Almacén

Sistema web para gestión de inventario, préstamos de equipo y control de horas becario.

## Inicio Rápido

```bash
docker compose up -d
```

Acceder a:
- **Aplicación:** http://localhost:8080
- **phpMyAdmin:** http://localhost:8081

## Requisitos

- Docker 20.10+
- Docker Compose 2.0+
- Puertos disponibles: 8080, 8081, 3307

## Configuración

1. Copiar `.env.example` a `.env`:
```bash
cp .env.example .env
```

2. Configurar variables en `.env` según necesidades

## Comandos Principales

```bash
# Iniciar servicios
docker compose up -d

# Ver logs
docker compose logs -f

# Detener servicios
docker compose down

# Acceder a MySQL
docker compose exec db mysql -u root -prootpass almacen

# Terminal en web
docker compose exec web bash
```

## Estructura

```
├── Dockerfile              # Imagen PHP/Apache
├── docker-compose.yaml     # Configuración de servicios
├── php-custom.ini          # Configuración PHP
├── .env.example            # Plantilla variables de entorno
├── src/                    # Código fuente
│   └── sistema_almacen/
│       ├── php/            # Backend PHP
│       ├── html/           # Plantillas HTML
│       ├── css/            # Estilos
│       ├── js/             # Scripts
│       └── resources/      # Imágenes y fuentes
└── sql/                    # Scripts iniciales de BD
```
