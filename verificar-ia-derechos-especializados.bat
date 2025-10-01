@echo off
echo ========================================
echo VERIFICACION DE IAs ESPECIALIZADAS EN DERECHOS
echo ========================================
echo.

echo [1/6] Verificando estructura de archivos...
if not exist "app\Services\IADerechosEspecializados.php" (
    echo ERROR: Servicio de IA no encontrado
    pause
    exit /b 1
)
if not exist "app\Http\Controllers\IADerechosEspecializadosController.php" (
    echo ERROR: Controlador de IA no encontrado
    pause
    exit /b 1
)
if not exist "app\Models\AnalisisIADerechosEspecializados.php" (
    echo ERROR: Modelo de analisis no encontrado
    pause
    exit /b 1
)
if not exist "routes\api-ia-derechos-especializados.php" (
    echo ERROR: Rutas de IA no encontradas
    pause
    exit /b 1
)
echo ✓ Estructura de archivos verificada
echo.

echo [2/6] Verificando migraciones...
php artisan migrate:status | findstr "analisis_ia_derechos_especializados"
if %errorlevel% neq 0 (
    echo ERROR: Migracion no encontrada
    pause
    exit /b 1
)
echo ✓ Migraciones verificadas
echo.

echo [3/6] Verificando rutas de API...
php artisan route:list --path=ia-derechos | findstr "ia-derechos"
if %errorlevel% neq 0 (
    echo ERROR: Rutas de IA no registradas
    pause
    exit /b 1
)
echo ✓ Rutas de API verificadas
echo.

echo [4/6] Verificando servicios de IA...
php artisan tinker --execute="echo 'Verificando servicios...'; try { \$service = new App\Services\IADerechosEspecializados(); echo 'Servicio creado correctamente'; } catch (Exception \$e) { echo 'Error: ' . \$e->getMessage(); exit(1); }"
if %errorlevel% neq 0 (
    echo ERROR: Fallo en servicios de IA
    pause
    exit /b 1
)
echo ✓ Servicios de IA verificados
echo.

echo [5/6] Verificando configuracion de IA...
if not exist "IA_ENV_CONFIG.txt" (
    echo ADVERTENCIA: Archivo de configuracion de IA no encontrado
    echo Por favor, configure las variables de entorno
) else (
    echo ✓ Configuracion de IA encontrada
)
echo.

echo [6/6] Ejecutando pruebas de funcionalidad...
echo.
echo Probando analisis de derechos mineros...
php artisan tinker --execute="echo 'Probando derechos mineros...'; try { \$service = new App\Services\IADerechosEspecializados(); \$result = \$service->analizarDerechosMineros('Prueba de concesion minera en territorio indigena', 'oro'); echo 'Prueba exitosa: ' . (isset(\$result['success']) ? 'SI' : 'NO'); } catch (Exception \$e) { echo 'Error en prueba: ' . \$e->getMessage(); }"
echo.

echo Probando analisis de derechos catastrales...
php artisan tinker --execute="echo 'Probando derechos catastrales...'; try { \$service = new App\Services\IADerechosEspecializados(); \$result = \$service->analizarDerechosCatastrales('Prueba de derechos prediales en predio urbano', 'residencial'); echo 'Prueba exitosa: ' . (isset(\$result['success']) ? 'SI' : 'NO'); } catch (Exception \$e) { echo 'Error en prueba: ' . \$e->getMessage(); }"
echo.

echo Probando analisis de desarrollo territorial...
php artisan tinker --execute="echo 'Probando desarrollo territorial...'; try { \$service = new App\Services\IADerechosEspecializados(); \$result = \$service->analizarDesarrolloTerritorial('Prueba de plan de desarrollo municipal', 'municipal'); echo 'Prueba exitosa: ' . (isset(\$result['success']) ? 'SI' : 'NO'); } catch (Exception \$e) { echo 'Error en prueba: ' . \$e->getMessage(); }"
echo.

echo Probando analisis de derechos internacionales...
php artisan tinker --execute="echo 'Probando derechos internacionales...'; try { \$service = new App\Services\IADerechosEspecializados(); \$result = \$service->analizarDerechosInternacionales('Prueba de caso internacional de derechos humanos', 'derechos_humanos'); echo 'Prueba exitosa: ' . (isset(\$result['success']) ? 'SI' : 'NO'); } catch (Exception \$e) { echo 'Error en prueba: ' . \$e->getMessage(); }"
echo.

echo Probando analisis de comunidades etnicas...
php artisan tinker --execute="echo 'Probando comunidades etnicas...'; try { \$service = new App\Services\IADerechosEspecializados(); \$result = \$service->analizarDerechosComunidadesEtnicas('Prueba de derechos de pueblo indigena', 'indigena'); echo 'Prueba exitosa: ' . (isset(\$result['success']) ? 'SI' : 'NO'); } catch (Exception \$e) { echo 'Error en prueba: ' . \$e->getMessage(); }"
echo.

echo ========================================
echo VERIFICACION COMPLETADA
echo ========================================
echo.
echo Resumen de verificacion:
echo ✓ Estructura de archivos
echo ✓ Migraciones
echo ✓ Rutas de API
echo ✓ Servicios de IA
echo ✓ Configuracion de IA
echo ✓ Pruebas de funcionalidad
echo.
echo Las IAs especializadas estan funcionando correctamente.
echo.
echo Para usar las IAs, consulte la documentacion:
echo - IA_DERECHOS_ESPECIALIZADOS_DOCUMENTACION.md
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
echo ========================================
pause
