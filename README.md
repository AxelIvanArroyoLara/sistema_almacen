# Sistema de Almacén

Sistema web para gestión de inventario, préstamos de equipo y control de horas becario.

**Tecnología:** PHP 8.1 + Apache + MySQL 8.0 en contenedores (Podman/Docker)

---

## Tabla de Contenidos

- [Inicio Rápido](#inicio-rápido)
- [Requisitos](#requisitos)
- [Instalación](#instalación)
- [Comandos Principales](#comandos-principales)
- [Estructura del Proyecto](#estructura-del-proyecto)
- [Desarrollo](#desarrollo)
- [Troubleshooting](#troubleshooting)

---

## Inicio Rápido

### Con Podman (Recomendado)

```bash
# 1. Clonar el repositorio
git clone <repositorio>
cd sistema_almacen

# 2. Configurar variables de entorno
cp .env.example .env
# Editar .env con tus credenciales

# 3. Levantar servicios
podman-compose up -d

# 4. Acceder a la aplicación
# http://localhost:8080
```

### Con Docker

```bash
# Igual que Podman, pero usa 'docker compose' en lugar de 'podman-compose'
docker compose up -d
```

---

## Requisitos

### Para Usuarios / Operadores

- **Podman Desktop** (Windows/macOS) o **Podman** (Linux)
  - Descargar: https://podman-desktop.io
  - O instalar: `dnf install podman` / `apt install podman`
- **podman-compose** (para orquestar múltiples contenedores)
  - Instalar: `pip3 install --user podman-compose`
- Puertos disponibles: **8080, 8081, 3307**

### Para Desarrolladores

Lo anterior + estos requisitos:

- **Git** configurado
- **Editor de código** (VS Code recomendado)
- Conocimiento básico de **PHP, MySQL, Docker/Podman**

---

## Instalación

### En Windows (Podman Desktop)

1. **Descargar e instalar Podman Desktop**
   ```
   https://podman-desktop.io/downloads
   ```

2. **Abrir terminal de la VM de Podman**
   - Click derecho en Podman Desktop → Abrir terminal

3. **Navegar al proyecto**
   ```bash
   cd /mnt/c/Users/[TU_USUARIO]/..../sistema_almacen
   ```

4. **Instalar podman-compose (si no está)**
   ```bash
   pip3 install --user podman-compose
   export PATH=$PATH:~/.local/bin
   echo 'export PATH=$PATH:~/.local/bin' >> ~/.bashrc
   source ~/.bashrc
   ```

5. **Configurar .env**
   ```bash
   cp .env.example .env
   # Editar con tus valores
   ```

6. **Levantar servicios**
   ```bash
   podman-compose up -d
   ```

### En Linux (Podman)

1. **Instalar Podman**
   ```bash
   # Fedora/RHEL
   sudo dnf install podman podman-compose

   # Debian/Ubuntu
   sudo apt install podman podman-compose
   ```

2. **Clonar repositorio**
   ```bash
   git clone <repositorio>
   cd sistema_almacen
   ```

3. **Configurar .env**
   ```bash
   cp .env.example .env
   nano .env  # Editar según necesidades
   ```

4. **Levantar servicios**
   ```bash
   podman-compose up -d
   ```

### En macOS (Podman Desktop)

Similar a Windows:

1. Descargar Podman Desktop desde https://podman-desktop.io
2. Instalar y ejecutar
3. Abrir terminal de la VM
4. Seguir pasos 3-6 de Windows

---

## Comandos Principales

### Iniciar y Detener

```bash
# Levantar servicios en background
podman-compose up -d

# Ver estado de servicios
podman-compose ps

# Detener servicios (conserva datos)
podman-compose stop

# Detener y eliminar contenedores (conserva BD)
podman-compose down

# Eliminar TODO incluyendo BD (CUIDADO!)
podman-compose down -v
```

### Logs y Debugging

```bash
# Ver logs en tiempo real (todos los servicios)
podman-compose logs -f

# Logs de un servicio específico
podman-compose logs -f web
podman-compose logs -f db
podman-compose logs -f phpmyadmin

# Últimas 50 líneas
podman-compose logs --tail 50
```

### Acceso a Contenedores

```bash
# Terminal bash en contenedor web
podman-compose exec web bash

# Conectar a MySQL directamente
podman-compose exec db mysql -u root -prootpass almacen

# Ejecutar comando PHP
podman-compose exec web php -v

# Ver procesos en el contenedor
podman-compose exec web ps aux
```

### Reinicio y Reconstrucción

```bash
# Reiniciar servicios
podman-compose restart

# Reiniciar servicio específico
podman-compose restart web

# Reconstruir imágenes (después de cambios en Dockerfile)
podman-compose up -d --build

# Reconstruir sin cache
podman-compose build --no-cache
podman-compose up -d
```

---

## URLs de Acceso

| Servicio | URL | Credenciales |
|----------|-----|---|
| **Aplicación Web** | http://localhost:8080 | (Según datos en BD) |
| **phpMyAdmin** | http://localhost:8081 | User: `root` / Pass: `rootpass` |
| **MySQL** | localhost:3307 | User: `root` / Pass: `rootpass` / DB: `almacen` |

---

## Estructura del Proyecto

```
sistema_almacen/
├── .dockerignore            # Archivos ignorados en build
├── .env                     # Variables de entorno (local, NO commitear)
├── .env.example             # Plantilla de .env
├── .git/                    # Control de versiones
├── Dockerfile               # Definición de imagen PHP/Apache
├── docker-compose.yaml      # Orquestación de servicios
├── php-custom.ini           # Configuración PHP personalizada
├── README.md                # Este archivo
│
├── sql/
│   └── almacen.sql          # Script inicial de base de datos
│
└── src/sistema_almacen/
    ├── index.html           # Página principal
    │
    ├── php/
    │   ├── forms/           # Formularios del sistema
    │   │   ├── form-consult*.php
    │   │   ├── form-add_user.php
    │   │   ├── form-automatic_control.php
    │   │   └── ...
    │   │
    │   └── modules/         # Backend / Lógica de negocio
    │       ├── conn.php                 # Conexión a BD
    │       ├── bkend-*.php             # Backends de formularios
    │       ├── mod-*.php               # Módulos de operaciones
    │       ├── reader.php              # Lectura de datos
    │       ├── failure.php             # Manejo de errores
    │       └── credentials/            # Autenticación
    │
    ├── css/                 # Estilos
    │   ├── styles.css
    │   ├── styles-index.css
    │   └── styles-success.css
    │
    ├── js/                  # JavaScript frontend
    │   ├── script.js
    │   ├── script-adduser.js
    │   └── script-disable_auto_completion.js
    │
    ├── html/                # Plantillas HTML
    │   ├── plantilla.html
    │   ├── form-*.html
    │   └── ...
    │
    └── resources/           # Recursos estáticos
        ├── images/
        ├── fonts/
        └── bootstrap-3.4.1-dist/
```

---

## Desarrollo

### Para Desarrolladores

#### 1. **Setup Inicial**

```bash
# Clonar y entrar
git clone <repositorio>
cd sistema_almacen

# Crear rama de desarrollo
git checkout -b feature/tu-feature

# Copiar .env
cp .env.example .env

# Levantar servicios
podman-compose up -d

# Verificar que todo funciona
podman-compose exec web bash
php -v  # Debe ser PHP 8.1.x
```

#### 2. **Flujo de Desarrollo**

```bash
# Los cambios en src/ se sincronizan automáticamente
# Edita archivos localmente en tu editor

# Terminal en el contenedor para testing
podman-compose exec web bash

# Ver cambios en tiempo real en http://localhost:8080
```

#### 3. **Acceso a Base de Datos**

```bash
# Opción 1: phpMyAdmin (visual)
# http://localhost:8081

# Opción 2: MySQL CLI
podman-compose exec db mysql -u root -prootpass almacen

# Dentro de MySQL:
show tables;
select * from usuarios limit 5;
```

#### 4. **Debugging**

```bash
# Ver logs de PHP/Apache
podman-compose logs -f web

# Ver logs de MySQL
podman-compose logs -f db

# Combinar todos
podman-compose logs -f
```

#### 5. **Hacer Cambios Seguros**

- Los cambios en `src/` se sincronizan automáticamente
- Para cambios en `Dockerfile` o `php-custom.ini`:
  ```bash
  podman-compose up -d --build
  ```

#### 6. **Git Workflow**

```bash
# Ver cambios
git status

# Agregar cambios
git add src/

# Commit
git commit -m "feat: descripción del cambio"

# Push a tu rama
git push origin feature/tu-feature

# Crear Pull Request en GitHub
```

#### 7. **Testing de BD**

```bash
# Conectar a MySQL
podman-compose exec db mysql -u root -prootpass almacen

# Ejecutar queries
mysql> SELECT table_name FROM information_schema.tables WHERE table_schema='almacen';
mysql> SELECT COUNT(*) FROM usuarios;
mysql> SELECT * FROM prestamos LIMIT 5;
```

---

## Troubleshooting

### Problema: No encuentra docker-compose.yaml

**Solución:** Asegúrate de estar en el directorio correcto:
```bash
pwd
# Debe mostrar: /mnt/c/Users/.../sistema_almacen o /home/.../sistema_almacen
ls docker-compose.yaml  # Debe existir
```

### Problema: Puertos en uso

**Solución:** Los puertos 8080, 8081, 3307 están ocupados

```bash
# En Windows:
netstat -ano | findstr :8080

# En Linux/macOS:
lsof -i :8080

# Liberar puerto o cambiar en docker-compose.yaml:
# Cambiar "8080:80" por "8090:80"
```

### Problema: MySQL tarda en iniciar

**Solución:** Es normal en primera ejecución (30-60s)

```bash
# Esperar a que esté listo:
podman-compose logs -f db
# Buscar: "ready for connections"

# O verificar:
podman-compose ps
# El contenedor db debe estar "Up"
```

### Problema: Conexión a BD fallida en PHP

**Solución:** Verificar `.env` y credenciales

```bash
# Verificar variables de entorno
cat .env

# Comparar con docker-compose.yaml:
# MYSQL_ROOT_PASSWORD debe coincidir con $_ENV en PHP

# Conectar directamente para verificar:
podman-compose exec db mysql -u root -prootpass almacen
```

### Problema: Cambios en PHP no se ven

**Solución:** Los volúmenes sincronizadores pueden necesar refresh

```bash
# Opción 1: Limpiar caché PHP
podman-compose exec web bash
php -r 'opcache_reset();'

# Opción 2: Reiniciar Apache
podman-compose exec web /etc/init.d/apache2 restart

# Opción 3: Reiniciar todo
podman-compose restart web
```

### Problema: Permisos denegados en Linux

**Solución:** Podman rootless requiere permisos especiales

```bash
# Ver si Podman está en rootless mode:
podman info | grep rootless

# Si es necesario ejecutar como root (NO recomendado):
sudo podman-compose up -d

# Mejor: Agregar tu usuario a grupo podman
sudo usermod -aG podman $(whoami)
newgrp podman
podman ps  # Debe funcionar sin sudo
```

---

## Diferencias: Podman vs Docker

| Aspecto | Podman | Docker |
|---------|--------|--------|
| **Instalación** | Más ligero | Requiere Desktop |
| **Rootless** | Por defecto ✅ | Requiere config |
| **Compatibility** | 100% Docker | Standard |
| **Compose** | `podman-compose` | `docker compose` |
| **Imágenes** | Descarga de docker.io | Docker Hub |
| **Performance** | Idéntico | Idéntico |

**Recomendación:** Usa Podman en desarrollo por ser más ligero y seguro.

---

## Soporte

- Para bugs: Abre un issue en GitHub
- Para preguntas: Consulta la documentación oficial
  - Podman: https://docs.podman.io
  - Docker: https://docs.docker.com

---

**Última actualización:** Febrero 2025
