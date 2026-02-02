#!/bin/bash
# Script de setup automático para Podman
# Ejecutar dentro de la VM de Podman Desktop

echo "=== Instalando dependencias para Podman ==="

# Instalar pip3
echo "Instalando python3-pip..."
sudo dnf install -y python3-pip

# Instalar podman-compose
echo "Instalando podman-compose..."
pip3 install --user podman-compose

# Añadir al PATH
echo "Configurando PATH..."
export PATH=$PATH:~/.local/bin

# Hacer permanente el PATH
if ! grep -q "export PATH=\$PATH:~/.local/bin" ~/.bashrc; then
    echo 'export PATH=$PATH:~/.local/bin' >> ~/.bashrc
    echo "PATH añadido a ~/.bashrc"
fi

echo ""
echo "=== Instalación completada ==="
echo ""
echo "Para usar podman-compose, ejecuta:"
echo "  source ~/.bashrc"
echo "  cd /mnt/c/Users/elher/OneDrive/Documentos/Documentos/Coding/Projects/sistema_almacen"
echo "  podman-compose up -d"
echo ""
echo "Verifica con: podman-compose --version"
