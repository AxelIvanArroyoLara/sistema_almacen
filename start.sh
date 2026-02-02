#!/bin/bash
# Script de inicio rápido para Sistema de Almacén

echo "🚀 Sistema de Almacén - Inicio Rápido"
echo "====================================="
echo ""

# Verificar si Docker está corriendo
if ! docker info > /dev/null 2>&1; then
    echo "❌ Docker no está corriendo. Por favor inicia Docker primero."
    exit 1
fi

echo "✅ Docker está corriendo"
echo ""
echo "Opciones:"
echo "  1) Iniciar sistema (primera vez)"
echo "  2) Iniciar sistema (ya configurado)"
echo "  3) Ver logs"
echo "  4) Detener sistema"
echo "  5) Reiniciar sistema"
echo "  6) Verificar estado"
echo "  7) Backup de base de datos"
echo "  8) Limpiar todo y reiniciar"
echo "  0) Salir"
echo ""
read -p "Selecciona una opción [0-8]: " option

case $option in
    1)
        echo "📦 Construyendo e iniciando sistema..."
        docker compose build
        docker compose up -d
        echo ""
        echo "⏳ Esperando a que los servicios estén listos (30 segundos)..."
        sleep 30
        echo ""
        echo "✅ Sistema iniciado!"
        echo "   • Aplicación: http://localhost:8080"
        echo "   • phpMyAdmin: http://localhost:8081"
        ;;
    2)
        echo "🚀 Iniciando sistema..."
        docker compose up -d
        echo "✅ Sistema iniciado!"
        ;;
    3)
        echo "📋 Mostrando logs (Ctrl+C para salir)..."
        docker compose logs -f
        ;;
    4)
        echo "🛑 Deteniendo sistema..."
        docker compose down
        echo "✅ Sistema detenido"
        ;;
    5)
        echo "🔄 Reiniciando sistema..."
        docker compose restart
        echo "✅ Sistema reiniciado"
        ;;
    6)
        echo "📊 Estado de los contenedores:"
        docker compose ps
        ;;
    7)
        echo "💾 Creando backup..."
        mkdir -p backups
        docker compose exec -T db mysqldump -u root -prootpass almacen > backups/almacen_backup_$(date +%Y%m%d_%H%M%S).sql
        echo "✅ Backup creado en backups/"
        ;;
    8)
        read -p "⚠️  ¿Seguro que quieres eliminar todos los datos? [y/N]: " confirm
        if [[ $confirm =~ ^[Yy]$ ]]; then
            echo "🗑️  Limpiando..."
            docker compose down -v
            docker compose build --no-cache
            docker compose up -d
            sleep 30
            echo "✅ Sistema limpio y reiniciado"
        else
            echo "Operación cancelada"
        fi
        ;;
    0)
        echo "👋 ¡Hasta luego!"
        exit 0
        ;;
    *)
        echo "❌ Opción inválida"
        exit 1
        ;;
esac
