.PHONY: help build up down restart logs clean test shell db-shell backup restore

# Colores para output
YELLOW := \033[1;33m
GREEN := \033[0;32m
RED := \033[0;31m
NC := \033[0m # No Color

help: ## Mostrar esta ayuda
	@echo "$(GREEN)Sistema de Almacén - Comandos Docker$(NC)"
	@echo "======================================"
	@grep -E '^[a-zA-Z_-]+:.*?## .*$$' $(MAKEFILE_LIST) | awk 'BEGIN {FS = ":.*?## "}; {printf "$(YELLOW)%-15s$(NC) %s\n", $$1, $$2}'

build: ## Construir las imágenes Docker
	@echo "$(GREEN)Construyendo imágenes...$(NC)"
	docker compose build

up: ## Iniciar todos los servicios
	@echo "$(GREEN)Iniciando servicios...$(NC)"
	docker compose up -d
	@echo "$(GREEN)✓ Servicios iniciados$(NC)"
	@echo "  • Aplicación: http://localhost:8080"
	@echo "  • phpMyAdmin: http://localhost:8081"

down: ## Detener todos los servicios
	@echo "$(RED)Deteniendo servicios...$(NC)"
	docker compose down

restart: ## Reiniciar todos los servicios
	@echo "$(YELLOW)Reiniciando servicios...$(NC)"
	docker compose restart

logs: ## Ver logs de todos los servicios
	docker compose logs -f

logs-web: ## Ver logs del servidor web
	docker compose logs -f web

logs-db: ## Ver logs de MySQL
	docker compose logs -f db

clean: ## Limpiar contenedores y volúmenes (CUIDADO: borra datos)
	@echo "$(RED)⚠️  ADVERTENCIA: Esto eliminará todos los datos de la base de datos$(NC)"
	@read -p "¿Estás seguro? [y/N] " -n 1 -r; \
	echo; \
	if [[ $$REPLY =~ ^[Yy]$$ ]]; then \
		docker compose down -v; \
		echo "$(GREEN)✓ Limpieza completada$(NC)"; \
	else \
		echo "Operación cancelada"; \
	fi

rebuild: ## Reconstruir y reiniciar (útil después de cambios en Dockerfile)
	@echo "$(YELLOW)Reconstruyendo servicios...$(NC)"
	docker compose down
	docker compose build --no-cache
	docker compose up -d
	@echo "$(GREEN)✓ Reconstrucción completada$(NC)"

test: ## Ejecutar pruebas de verificación
	@./docker-test.sh

shell: ## Abrir terminal en el contenedor web
	docker compose exec web bash

db-shell: ## Abrir MySQL shell
	docker compose exec db mysql -u root -prootpass almacen

ps: ## Ver estado de los contenedores
	docker compose ps

backup: ## Hacer backup de la base de datos
	@echo "$(GREEN)Creando backup...$(NC)"
	@mkdir -p backups
	docker compose exec -T db mysqldump -u root -prootpass almacen > backups/almacen_backup_$$(date +%Y%m%d_%H%M%S).sql
	@echo "$(GREEN)✓ Backup creado en backups/$(NC)"

restore: ## Restaurar backup de la base de datos (uso: make restore FILE=backups/archivo.sql)
	@if [ -z "$(FILE)" ]; then \
		echo "$(RED)Error: Especifica el archivo con FILE=ruta/al/backup.sql$(NC)"; \
		exit 1; \
	fi
	@echo "$(YELLOW)Restaurando desde $(FILE)...$(NC)"
	docker compose exec -T db mysql -u root -prootpass almacen < $(FILE)
	@echo "$(GREEN)✓ Restauración completada$(NC)"

check-php: ## Verificar extensiones PHP instaladas
	docker compose exec web php -m

check-connection: ## Verificar conexión PHP->MySQL
	@docker compose exec -T web php -r "try { \$$pdo = new PDO('mysql:host=db;dbname=almacen', 'root', 'rootpass'); echo '✓ Conexión exitosa\n'; } catch(Exception \$$e) { echo '✗ Error: ' . \$$e->getMessage() . '\n'; }"

init: build up ## Inicialización completa (construir e iniciar)
	@echo "$(GREEN)Esperando a que los servicios estén listos...$(NC)"
	@sleep 10
	@make test
