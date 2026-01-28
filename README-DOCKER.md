# Sistema de Almacén - Guía Docker para Linux

## 🚀 Inicio Rápido

### Prerrequisitos
- Docker instalado
- Docker Compose instalado
- Puertos 8080, 8081 y 3307 disponibles

### Comandos Básicos

```bash
# Iniciar todos los servicios
docker-compose up -d

# Ver logs
docker-compose logs -f

# Detener servicios
docker-compose down

# Detener y eliminar volúmenes (CUIDADO: borra la base de datos)
docker-compose down -v

# Reconstruir después de cambios en Dockerfile
docker-compose up -d --build
```

## 📋 Servicios Disponibles

| Servicio | URL | Descripción |
|----------|-----|-------------|
| Aplicación Web | http://localhost:8080 | Sistema de almacén principal |
| phpMyAdmin | http://localhost:8081 | Gestión de base de datos |
| MySQL | localhost:3307 | Base de datos (puerto interno 3306) |

### Credenciales MySQL
- **Host:** db (dentro de Docker) / localhost:3307 (desde el host)
- **Usuario:** root
- **Contraseña:** rootpass
- **Base de datos:** almacen

## 🔧 Solución de Problemas

### El contenedor web no inicia
```bash
# Ver logs detallados
docker-compose logs web

# Verificar que el puerto 8080 no esté en uso
sudo netstat -tulpn | grep 8080
```

### Error de conexión a base de datos
```bash
# Verificar que MySQL esté saludable
docker-compose ps

# Reiniciar solo la base de datos
docker-compose restart db

# Esperar a que MySQL esté listo
docker-compose exec db mysqladmin ping -h localhost -u root -prootpass
```

### Problemas de permisos
```bash
# Ajustar permisos en el directorio de código
sudo chown -R $USER:$USER ./src

# Dentro del contenedor
docker-compose exec web chown -R www-data:www-data /var/www/html
```

### Reconstruir desde cero
```bash
# Detener todo y eliminar volúmenes
docker-compose down -v

# Eliminar imágenes antiguas
docker-compose build --no-cache

# Iniciar de nuevo
docker-compose up -d
```

## 📊 Verificación de Estado

```bash
# Ver estado de todos los contenedores
docker-compose ps

# Verificar logs en tiempo real
docker-compose logs -f web
docker-compose logs -f db

# Entrar al contenedor web
docker-compose exec web bash

# Entrar a MySQL
docker-compose exec db mysql -u root -prootpass almacen
```

## 🐛 Debugging

### Verificar extensiones PHP
```bash
docker-compose exec web php -m | grep -E "pdo|mysqli"
```

### Verificar configuración Apache
```bash
docker-compose exec web apache2ctl -S
```

### Verificar conectividad a MySQL desde el contenedor web
```bash
docker-compose exec web php -r "try { \$pdo = new PDO('mysql:host=db;dbname=almacen', 'root', 'rootpass'); echo 'Conexión exitosa\n'; } catch(Exception \$e) { echo 'Error: ' . \$e->getMessage() . '\n'; }"
```

## 📝 Notas Importantes

1. **Primera ejecución:** La base de datos puede tardar 30-60 segundos en inicializarse con el archivo SQL
2. **Healthchecks:** Los contenedores tienen verificaciones de salud automáticas
3. **Persistencia:** Los datos de MySQL se guardan en un volumen Docker (`db_data`)
4. **Permisos:** El contenedor web ejecuta Apache como `www-data`

## 🔄 Actualizar la Aplicación

```bash
# Sin reconstruir imagen
docker-compose restart web

# Reconstruyendo imagen (si cambió Dockerfile)
docker-compose up -d --build web
```

## 🗑️ Limpieza

```bash
# Limpiar contenedores detenidos
docker container prune

# Limpiar imágenes sin usar
docker image prune

# Limpiar todo (CUIDADO)
docker system prune -a --volumes
```
