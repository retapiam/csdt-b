@echo off
REM Script de configuración inicial para CSDT Backend en Windows

echo 🔧 Configurando CSDT Backend para desarrollo local...

REM Crear archivo .env si no existe
if not exist ".env" (
    echo 📝 Creando archivo .env...
    if exist ".env.example" (
        copy .env.example .env
    ) else (
        echo ⚠️  Archivo .env.example no encontrado, creando .env básico...
        (
            echo APP_NAME="CSDT Sistema"
            echo APP_ENV=local
            echo APP_KEY=
            echo APP_DEBUG=true
            echo APP_URL=http://localhost:8000
            echo.
            echo LOG_CHANNEL=stack
            echo LOG_DEPRECATIONS_CHANNEL=null
            echo LOG_LEVEL=debug
            echo.
            echo DB_CONNECTION=sqlite
            echo DB_DATABASE=database/database.sqlite
            echo.
            echo BROADCAST_DRIVER=log
            echo CACHE_DRIVER=file
            echo FILESYSTEM_DISK=local
            echo QUEUE_CONNECTION=sync
            echo SESSION_DRIVER=file
            echo SESSION_LIFETIME=120
            echo.
            echo MEMCACHED_HOST=127.0.0.1
            echo.
            echo REDIS_HOST=127.0.0.1
            echo REDIS_PASSWORD=null
            echo REDIS_PORT=6379
            echo.
            echo MAIL_MAILER=log
            echo MAIL_HOST=mailpit
            echo MAIL_PORT=1025
            echo MAIL_USERNAME=null
            echo MAIL_PASSWORD=null
            echo MAIL_ENCRYPTION=null
            echo MAIL_FROM_ADDRESS="hello@example.com"
            echo MAIL_FROM_NAME="${APP_NAME}"
            echo.
            echo AWS_ACCESS_KEY_ID=
            echo AWS_SECRET_ACCESS_KEY=
            echo AWS_DEFAULT_REGION=us-east-1
            echo AWS_BUCKET=
            echo AWS_USE_PATH_STYLE_ENDPOINT=false
            echo.
            echo PUSHER_APP_ID=
            echo PUSHER_APP_KEY=
            echo PUSHER_APP_SECRET=
            echo PUSHER_HOST=
            echo PUSHER_PORT=443
            echo PUSHER_SCHEME=https
            echo PUSHER_APP_CLUSTER=mt1
            echo.
            echo VITE_PUSHER_APP_KEY="${PUSHER_APP_KEY}"
            echo VITE_PUSHER_HOST="${PUSHER_HOST}"
            echo VITE_PUSHER_PORT="${PUSHER_PORT}"
            echo VITE_PUSHER_SCHEME="${PUSHER_SCHEME}"
            echo VITE_PUSHER_APP_CLUSTER="${PUSHER_APP_CLUSTER}"
        ) > .env
    )
    echo ✅ Archivo .env creado
) else (
    echo ⚠️  El archivo .env ya existe
)

REM Verificar que PHP esté instalado
php --version >nul 2>&1
if %errorlevel% neq 0 (
    echo ❌ ERROR: PHP no está instalado
    echo Por favor instala PHP desde https://php.net/
    pause
    exit /b 1
)

REM Verificar que Composer esté instalado
composer --version >nul 2>&1
if %errorlevel% neq 0 (
    echo ❌ ERROR: Composer no está instalado
    echo Por favor instala Composer desde https://getcomposer.org/
    pause
    exit /b 1
)

REM Instalar dependencias
echo 📦 Instalando dependencias de PHP...
composer install

REM Generar clave de aplicación
echo 🔑 Generando clave de aplicación...
php artisan key:generate

REM Crear base de datos SQLite si no existe
echo 🗄️  Configurando base de datos...
if not exist "database" mkdir database
if not exist "database\database.sqlite" type nul > database\database.sqlite

REM Ejecutar migraciones
echo 📊 Ejecutando migraciones...
php artisan migrate --force

REM Crear enlace simbólico para storage
echo 🔗 Creando enlace simbólico para storage...
php artisan storage:link

echo ✅ Configuración inicial del backend completada
echo.
echo Para iniciar el backend:
echo   php artisan serve
echo.
echo El servidor estará disponible en: http://localhost:8000
pause
