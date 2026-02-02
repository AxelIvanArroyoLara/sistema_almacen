# ⚡ Guía Rápida - Sistema de Almacén en Docker

## 🚀 Inicio Más Rápido

```bash
./start.sh              # Script interactivo
# O
make init               # Con Makefile
# O
docker-compose up -d    # Comando directo
```

## 🌐 URLs

- **App:** http://localhost:8080
- **phpMyAdmin:** http://localhost:8081
- **MySQL:** localhost:3307

## 🔑 Credenciales

```
Usuario: root
Password: rootpass
Database: almacen
```

## 📋 Comandos Esenciales

```bash
# INICIO
docker-compose up -d                    # Iniciar
make up                                 # Iniciar (Make)

# LOGS
docker-compose logs -f                  # Ver todos los logs
docker-compose logs -f web              # Solo web
docker-compose logs -f db               # Solo database

# ESTADO
docker-compose ps                       # Ver contenedores
make ps                                 # Ver contenedores (Make)

# DETENER
docker-compose down                     # Detener
docker-compose down -v                  # Detener y borrar datos

# REINICIAR
docker-compose restart                  # Reiniciar todo
docker-compose restart web              # Solo web

# SHELL
docker-compose exec web bash            # Terminal en web
docker-compose exec db mysql -u root -prootpass almacen  # MySQL

# RECONSTRUIR
docker-compose up -d --build            # Rebuild y start
make rebuild                            # Rebuild completo
```

## 🔧 Debugging

```bash
# Ver extensiones PHP
docker-compose exec web php -m

# Probar conexión DB
make check-connection

# Ver configuración Apache
docker-compose exec web apache2ctl -S

# Verificar salud de MySQL
docker-compose exec db mysqladmin ping -h localhost -u root -prootpass
```

## 💾 Backup

```bash
make backup                             # Crear backup
make restore FILE=backups/archivo.sql   # Restaurar
```

## 🆘 Problemas Comunes

**Puerto en uso:**
```bash
# Cambiar puerto en docker-compose.yaml
ports:
  - "8090:80"  # En lugar de 8080
```

**MySQL no conecta:**
```bash
docker-compose logs db                  # Ver logs
docker-compose restart db               # Reiniciar
```

**Desde cero:**
```bash
docker-compose down -v
docker-compose build --no-cache
docker-compose up -d
```

## 📁 Archivos Importantes

- `docker-compose.yaml` - Configuración de servicios
- `Dockerfile` - Imagen del servidor web
- `Makefile` - Comandos simplificados
- `README-DOCKER.md` - Documentación completa
- `DOCKER-ANALYSIS.md` - Análisis técnico
- `start.sh` - Script interactivo
- `docker-test.sh` - Verificación automática

## ✅ Verificación

```bash
./docker-test.sh        # Test completo
make test              # Test con Make
```

## 🎯 Flujo de Trabajo

```bash
# 1. Primera vez
make init

# 2. Verificar
make test

# 3. Trabajar
# ... editar código ...

# 4. Ver cambios
make logs-web

# 5. Al terminar
make down
```

## 📖 Más Info

- Ver `README-DOCKER.md` para guía completa
- Ver `DOCKER-ANALYSIS.md` para análisis técnico
- Ejecutar `make help` para todos los comandos
