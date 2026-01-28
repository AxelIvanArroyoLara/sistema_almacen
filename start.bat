@echo off
REM Sistema de Almacen - Script de inicio para Windows
REM ====================================================

echo.
echo Sistema de Almacen - Inicio Rapido
echo ====================================
echo.

REM Verificar si Docker esta corriendo
docker info >nul 2>&1
if errorlevel 1 (
    echo [ERROR] Docker no esta corriendo.
    echo Por favor inicia Docker Desktop primero.
    echo.
    pause
    exit /b 1
)

echo [OK] Docker esta corriendo
echo.

REM Mostrar opciones
echo Opciones:
echo   1) Iniciar sistema (primera vez)
echo   2) Iniciar sistema (ya configurado)
echo   3) Ver logs
echo   4) Detener sistema
echo   5) Reiniciar sistema
echo   6) Verificar estado
echo   7) Backup de base de datos
echo   8) Limpiar todo y reiniciar
echo   0) Salir
echo.

set /p option="Selecciona una opcion [0-8]: "

if "%option%"=="1" goto BUILD_START
if "%option%"=="2" goto START
if "%option%"=="3" goto LOGS
if "%option%"=="4" goto STOP
if "%option%"=="5" goto RESTART
if "%option%"=="6" goto STATUS
if "%option%"=="7" goto BACKUP
if "%option%"=="8" goto CLEAN
if "%option%"=="0" goto EXIT
goto INVALID

:BUILD_START
echo.
echo [*] Construyendo e iniciando sistema...
docker compose build
docker compose up -d
echo.
echo [*] Esperando a que los servicios esten listos (30 segundos)...
timeout /t 30 /nobreak >nul
echo.
echo [OK] Sistema iniciado!
echo    - Aplicacion: http://localhost:8080
echo    - phpMyAdmin: http://localhost:8081
goto END

:START
echo.
echo [*] Iniciando sistema...
docker compose up -d
echo [OK] Sistema iniciado!
goto END

:LOGS
echo.
echo [*] Mostrando logs (Ctrl+C para salir)...
docker compose logs -f
goto END

:STOP
echo.
echo [*] Deteniendo sistema...
docker compose down
echo [OK] Sistema detenido
goto END

:RESTART
echo.
echo [*] Reiniciando sistema...
docker compose restart
echo [OK] Sistema reiniciado
goto END

:STATUS
echo.
echo [*] Estado de los contenedores:
docker compose ps
goto END

:BACKUP
echo.
echo [*] Creando backup...
if not exist "backups" mkdir backups
docker compose exec -T db mysqldump -u root -prootpass almacen > backups\almacen_backup_%date:~-4,4%%date:~-10,2%%date:~-7,2%_%time:~0,2%%time:~3,2%%time:~6,2%.sql
echo [OK] Backup creado en backups\
goto END

:CLEAN
echo.
set /p confirm="[ADVERTENCIA] Esto eliminara todos los datos. Estas seguro? [y/N]: "
if /i "%confirm%"=="y" (
    echo [*] Limpiando...
    docker compose down -v
    docker compose build --no-cache
    docker compose up -d
    timeout /t 30 /nobreak >nul
    echo [OK] Sistema limpio y reiniciado
) else (
    echo Operacion cancelada
)
goto END

:EXIT
echo.
echo Hasta luego!
exit /b 0

:INVALID
echo.
echo [ERROR] Opcion invalida
goto END

:END
echo.
pause
