# Setup de Podman para Sistema de Almacén

## Instalación en nueva máquina Windows

### 1. Instalar Podman Desktop
1. Descarga desde: https://podman-desktop.io/downloads
2. Instala y abre Podman Desktop
3. Espera a que se cree la VM automáticamente

### 2. Configurar Podman (una sola vez por máquina)

**Opción A - Script automático (recomendado):**

1. Abre la terminal de la VM de Podman (desde Podman Desktop)
2. Navega al proyecto:
   ```bash
   cd /mnt/c/Users/[TU_USUARIO]/[RUTA_AL_PROYECTO]/sistema_almacen
   ```
3. Ejecuta el script de setup:
   ```bash
   bash setup-podman.sh
   ```
4. Carga el PATH:
   ```bash
   source ~/.bashrc
   ```

**Opción B - Manual:**

Si prefieres instalarlo manualmente, ejecuta estos comandos en la VM:

```bash
# Instalar pip3
sudo dnf install -y python3-pip

# Instalar podman-compose
pip3 install --user podman-compose

# Añadir al PATH
export PATH=$PATH:~/.local/bin
echo 'export PATH=$PATH:~/.local/bin' >> ~/.bashrc
```

### 3. Levantar los contenedores

```bash
cd /mnt/c/Users/[TU_USUARIO]/[RUTA_AL_PROYECTO]/sistema_almacen
podman-compose up -d
```

### 4. Verificar que funciona

Desde la VM:
```bash
podman ps
```

Desde Windows: abre el navegador en `http://localhost:8080`

## Comandos útiles

```bash
# Ver contenedores corriendo
podman ps

# Ver logs
podman logs test
podman logs mysql-db

# Detener todo
podman-compose down

# Reiniciar
podman-compose restart

# Ver estado de la VM
podman machine list
```

## Troubleshooting

**Error: podman-compose no encontrado**
```bash
export PATH=$PATH:~/.local/bin
source ~/.bashrc
```

**Puerto ocupado**
```bash
# Detener contenedores anteriores
podman-compose down
# O cambiar el puerto en docker-compose.yaml
```

**No carga desde Windows**
- Verifica que Podman Desktop esté corriendo
- Revisa que los puertos estén mapeados: `podman ps`
- Verifica logs: `podman logs test`

## Diferencias con Docker Desktop

- ✅ Gratis y open source para todos los usos
- ✅ Mismo archivo docker-compose.yaml
- ✅ Misma funcionalidad de contenedores
- ⚠️ Requiere instalación única de podman-compose por máquina
- ⚠️ Comandos usan `podman` en vez de `docker`
