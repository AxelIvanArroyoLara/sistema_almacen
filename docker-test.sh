#!/bin/bash

# Script de verificación del sistema Docker
# Sistema de Almacén - Test de Salud

echo "🔍 Verificando Sistema de Almacén en Docker..."
echo "=============================================="
echo ""

# Colores
GREEN='\033[0;32m'
RED='\033[0;31m'
YELLOW='\033[1;33m'
NC='\033[0m' # No Color

# Verificar Docker
echo -n "1. Docker instalado: "
if command -v docker &> /dev/null; then
    echo -e "${GREEN}✓${NC}"
else
    echo -e "${RED}✗${NC} Por favor instala Docker"
    exit 1
fi

# Verificar Docker Compose
echo -n "2. Docker Compose instalado: "
if command -v docker-compose &> /dev/null; then
    echo -e "${GREEN}✓${NC}"
else
    echo -e "${RED}✗${NC} Por favor instala Docker Compose"
    exit 1
fi

# Verificar puertos disponibles
echo "3. Verificando puertos disponibles:"
for port in 8080 8081 3307; do
    echo -n "   Puerto $port: "
    if ! sudo netstat -tulpn 2>/dev/null | grep -q ":$port "; then
        echo -e "${GREEN}✓ Disponible${NC}"
    else
        echo -e "${YELLOW}⚠ En uso${NC}"
    fi
done

echo ""
echo "4. Iniciando contenedores..."
docker compose up -d

echo ""
echo "5. Esperando a que los servicios estén listos..."
sleep 10

echo ""
echo "6. Estado de los contenedores:"
docker compose ps

echo ""
echo "7. Verificando salud de MySQL..."
for i in {1..10}; do
    if docker compose exec -T db mysqladmin ping -h localhost -u root -prootpass &> /dev/null; then
        echo -e "${GREEN}✓ MySQL está funcionando${NC}"
        break
    else
        echo -n "."
        sleep 3
    fi
    if [ $i -eq 10 ]; then
        echo -e "${RED}✗ MySQL no responde${NC}"
    fi
done

echo ""
echo "8. Verificando conexión desde PHP a MySQL..."
CONNECTION_TEST=$(docker compose exec -T web php -r "
try { 
    \$pdo = new PDO('mysql:host=db;dbname=almacen', 'root', 'rootpass'); 
    echo 'OK'; 
} catch(Exception \$e) { 
    echo 'ERROR: ' . \$e->getMessage(); 
}" 2>&1)

if [[ $CONNECTION_TEST == *"OK"* ]]; then
    echo -e "${GREEN}✓ Conexión PHP->MySQL exitosa${NC}"
else
    echo -e "${RED}✗ Error de conexión: $CONNECTION_TEST${NC}"
fi

echo ""
echo "9. Verificando extensiones PHP necesarias:"
docker compose exec -T web php -m | grep -E "pdo|mysqli|PDO" | while read ext; do
    echo -e "   ${GREEN}✓${NC} $ext"
done

echo ""
echo "=============================================="
echo "🎉 Verificación completada!"
echo ""
echo "📋 URLs de acceso:"
echo "   • Aplicación: http://localhost:8080"
echo "   • phpMyAdmin: http://localhost:8081"
echo "   • MySQL: localhost:3307"
echo ""
echo "📖 Ver logs: docker compose logs -f"
echo "🛑 Detener: docker compose down"
echo ""
