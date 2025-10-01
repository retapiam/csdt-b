@echo off
echo ========================================
echo INSTALACION DE IAs ESPECIALIZADAS EN DERECHOS
echo ========================================
echo.

echo [1/8] Ejecutando migraciones...
php artisan migrate --force
if %errorlevel% neq 0 (
    echo ERROR: Fallo en las migraciones
    pause
    exit /b 1
)
echo ✓ Migraciones ejecutadas correctamente
echo.

echo [2/8] Ejecutando seeders...
php artisan db:seed --class=AnalisisIADerechosEspecializadosSeeder --force
if %errorlevel% neq 0 (
    echo ERROR: Fallo en los seeders
    pause
    exit /b 1
)
echo ✓ Seeders ejecutados correctamente
echo.

echo [3/8] Limpiando cache...
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear
echo ✓ Cache limpiado
echo.

echo [4/8] Optimizando autoloader...
composer dump-autoload --optimize
if %errorlevel% neq 0 (
    echo ERROR: Fallo en la optimizacion del autoloader
    pause
    exit /b 1
)
echo ✓ Autoloader optimizado
echo.

echo [5/8] Verificando configuracion de IA...
if not exist "IA_ENV_CONFIG.txt" (
    echo ERROR: Archivo de configuracion de IA no encontrado
    echo Por favor, configure las variables de entorno de IA
    pause
    exit /b 1
)
echo ✓ Configuracion de IA encontrada
echo.

echo [6/8] Verificando servicios de IA...
php artisan tinker --execute="echo 'Verificando servicios de IA...'; try { \$service = new App\Services\IADerechosEspecializados(); echo 'Servicio de IA creado correctamente'; } catch (Exception \$e) { echo 'Error: ' . \$e->getMessage(); exit(1); }"
if %errorlevel% neq 0 (
    echo ERROR: Fallo en la verificacion de servicios de IA
    pause
    exit /b 1
)
echo ✓ Servicios de IA verificados
echo.

echo [7/8] Verificando rutas de API...
php artisan route:list --path=ia-derechos
if %errorlevel% neq 0 (
    echo ERROR: Fallo en la verificacion de rutas
    pause
    exit /b 1
)
echo ✓ Rutas de API verificadas
echo.

echo [8/8] Ejecutando pruebas de conectividad...
php artisan tinker --execute="echo 'Probando conectividad con OpenAI...'; try { \$service = new App\Services\IADerechosEspecializados(); \$result = \$service->analizarDerechosMineros('Prueba de conectividad', 'general'); echo 'Conectividad exitosa'; } catch (Exception \$e) { echo 'Error de conectividad: ' . \$e->getMessage(); }"
echo ✓ Pruebas de conectividad completadas
echo.

echo ========================================
echo INSTALACION COMPLETADA EXITOSAMENTE
echo ========================================
echo.
echo Las siguientes IAs especializadas han sido instaladas:
echo.
echo 1. Derechos Mineros
echo 2. Derechos Catastrales  
echo 3. Desarrollo Territorial
echo 4. Planes de Desarrollo y Gobierno
echo 5. Derechos Internacionales
echo 6. Derechos CAN e INCA
echo 7. Derechos Latinoamericanos
echo 8. Derechos de Propiedad
echo 9. Derechos en Comunidades Étnicas
echo.
echo Endpoints disponibles:
echo - POST /api/ia-derechos/mineros
echo - POST /api/ia-derechos/catastrales
echo - POST /api/ia-derechos/desarrollo-territorial
echo - POST /api/ia-derechos/planes-desarrollo-gobierno
echo - POST /api/ia-derechos/internacionales
echo - POST /api/ia-derechos/can-inca
echo - POST /api/ia-derechos/latinoamericanos
echo - POST /api/ia-derechos/propiedad
echo - POST /api/ia-derechos/comunidades-etnicas
echo.
echo Para obtener especialidades disponibles:
echo - GET /api/ia-derechos/especialidades
echo.
echo Documentacion completa disponible en:
echo - IA_DERECHOS_ESPECIALIZADOS_DOCUMENTACION.md
echo.
echo ========================================
pause
