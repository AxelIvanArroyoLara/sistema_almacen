# 🏢 Sistema de Almacén - Departamento de Electrónica

Sistema web para gestión de inventario, préstamos de equipo y control de horas becario.

---

## 🚀 Inicio Rápido

### Linux / macOS
```bash
docker compose up -d
```

### Windows
```powershell
# Opción 1: WSL2 (Recomendado)
wsl
docker compose up -d

# Opción 2: PowerShell
.\start.bat
```

**URLs:**
- Aplicación: http://localhost:8080
- phpMyAdmin: http://localhost:8081

---

## 📋 Requisitos

- Docker 20.10+
- Docker Compose 2.0+
- Puertos disponibles: 8080, 8081, 3307

**Windows específicamente:**
- Docker Desktop con WSL2 habilitado
- Ver [WINDOWS-COMPATIBILITY.md](WINDOWS-COMPATIBILITY.md)

---

## 🎯 Características

- ✅ Gestión de inventario (chips, conexiones, equipos)
- ✅ Sistema de préstamos con historial
- ✅ Control de horas becario
- ✅ Autenticación de usuarios
- ✅ Reportes y estadísticas
- ✅ Interfaz responsive Bootstrap

---

## 🗂️ Estructura del Proyecto

```
sistema_almacen/
├── src/sistema_almacen/     # Código fuente PHP
│   ├── php/
│   │   ├── forms/           # Formularios de usuario
│   │   └── modules/         # Lógica de negocio
│   ├── css/                 # Estilos
│   ├── js/                  # Scripts JavaScript
│   └── resources/           # Imágenes y fuentes
├── sql/                     # Scripts de base de datos
├── Dockerfile              # Imagen PHP/Apache
├── docker-compose.yaml     # Orquestación de servicios
└── test-data.sql          # Datos de prueba
```

---

## 🛠️ Comandos Útiles

### Usando Make (Linux/macOS)
```bash
make init          # Primera vez
make up            # Iniciar
make down          # Detener
make logs          # Ver logs
make test          # Verificar sistema
make backup        # Backup de DB
make help          # Ver todos los comandos
```

### Usando Docker Compose Directo
```bash
docker compose up -d              # Iniciar
docker compose down               # Detener
docker compose logs -f            # Ver logs
docker compose ps                 # Estado
docker compose restart web        # Reiniciar web
```

### Scripts Interactivos
```bash
./start.sh         # Linux/macOS
.\start.bat        # Windows
./docker-test.sh   # Test automático
```

---

## 👤 Usuario de Prueba

**Credenciales:**
- Usuario: `999999`
- Contraseña: `test123`

**Datos incluidos:**
- 10 registros de control becario
- 4 préstamos activos
- 8 registros de historial

**Para agregar más datos:**
```bash
docker compose exec -T db mysql -u root -prootpass almacen < test-data.sql
```

---

## 🔧 Configuración

### Variables de Entorno

Copia `.env.example` a `.env` y ajusta:

```env
# MySQL
MYSQL_ROOT_PASSWORD=rootpass
MYSQL_DATABASE=almacen

# Puertos
WEB_PORT=8080
PHPMYADMIN_PORT=8081
MYSQL_PORT=3307
```

### Conexión a Base de Datos

**Desde la aplicación (interno):**
- Host: `db`
- Puerto: `3306`

**Desde tu máquina (externo):**
- Host: `localhost`
- Puerto: `3307`

**Credenciales:**
- Usuario: `root`
- Contraseña: `rootpass`
- Base de datos: `almacen`

---

## 🐛 Solución de Problemas

Ver documentación detallada:
- **Linux/General:** [TROUBLESHOOTING.md](TROUBLESHOOTING.md)
- **Windows:** [WINDOWS-COMPATIBILITY.md](WINDOWS-COMPATIBILITY.md)
- **Docker:** [DOCKER-ANALYSIS.md](DOCKER-ANALYSIS.md)

### Problemas Comunes

**Puerto en uso:**
```bash
# Cambiar puertos en docker-compose.yaml
ports:
  - "8090:80"  # En lugar de 8080
```

**MySQL no conecta:**
```bash
docker compose logs db
docker compose restart db
```

**Limpiar y reiniciar:**
```bash
docker compose down -v
docker compose build --no-cache
docker compose up -d
```

---

## 📊 Base de Datos

### Tablas Principales

- `usuarios` - Usuarios del sistema
- `control_becario` - Registro de horas
- `prestamos` - Préstamos activos
- `prim14a` - Historial de préstamos
- `chips` - Inventario de componentes
- `conexion` - Cables y conexiones
- `equipo` - Equipos de laboratorio

### Backup y Restore

```bash
# Backup
docker compose exec -T db mysqldump -u root -prootpass almacen > backup.sql

# Restore
docker compose exec -T db mysql -u root -prootpass almacen < backup.sql
```

---

## 🔒 Seguridad

**IMPORTANTE para producción:**
- ✅ Cambiar credenciales de MySQL
- ✅ Usar variables de entorno para secretos
- ✅ No exponer phpMyAdmin
- ✅ Configurar HTTPS
- ✅ Restringir acceso a base de datos
- ✅ Implementar rate limiting

---

## 🧪 Testing

```bash
# Test completo del sistema
./docker-test.sh

# Test manual
curl http://localhost:8080

# Test de conexión DB
docker compose exec web php -r "new PDO('mysql:host=db;dbname=almacen','root','rootpass') && echo 'OK';"
```

---

## 📦 Deployment

### Producción

1. **Configurar variables de entorno:**
   ```bash
   cp .env.example .env
   # Editar .env con credenciales seguras
   ```

2. **Actualizar docker-compose.yaml:**
   - Remover phpMyAdmin
   - Cambiar puertos
   - Agregar SSL/TLS

3. **Iniciar servicios:**
   ```bash
   docker compose -f docker-compose.prod.yaml up -d
   ```

---

## 🤝 Contribuir

1. Fork el proyecto
2. Crea una rama (`git checkout -b feature/nueva-funcionalidad`)
3. Commit cambios (`git commit -am 'Agregar nueva funcionalidad'`)
4. Push a la rama (`git push origin feature/nueva-funcionalidad`)
5. Crea Pull Request

---

## 📄 Licencia

Este proyecto es de uso interno para el Departamento de Electrónica.

---

## 📞 Soporte

Para problemas o preguntas:
1. Revisar documentación en `/docs`
2. Verificar logs: `docker compose logs`
3. Consultar [TROUBLESHOOTING.md](TROUBLESHOOTING.md)

---

## 🔄 Changelog

Ver [CHANGELOG.md](CHANGELOG.md) para historial de cambios.

---

## ⚡ Performance

- **Tiempo de inicio:** ~30-60 segundos (primera vez con DB)
- **Tiempo de inicio:** ~10 segundos (subsecuentes)
- **Usuarios concurrentes:** 50+ (configuración actual)
- **Tamaño base de datos:** ~20MB (con datos de ejemplo)

---

## 🌐 Compatibilidad

| Plataforma | Estado | Notas |
|------------|--------|-------|
| Linux | ✅ Óptimo | Desarrollo y producción |
| macOS | ✅ Óptimo | Via Docker Desktop |
| Windows | ✅ Compatible | Requiere WSL2 |

---

**Hecho con ❤️ para el Departamento de Electrónica**
