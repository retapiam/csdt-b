# CSDT Backend - Laravel

Backend API del Sistema CSDT (Consejo Social de Veeduría y Desarrollo Territorial)

---

## 🚀 Inicio Rápido

### Requisitos Previos

- PHP >= 8.2
- Composer 2.x
- MySQL 8.0+ o MariaDB 10.6+
- Redis 6.x+
- Node.js >= 16 (opcional, para Laravel Mix)

### Instalación

```bash
# 1. Clonar repositorio
git clone [REPO_URL]
cd csdt-b-main

# 2. Instalar dependencias
composer install

# 3. Configurar entorno
cp .env.example .env
php artisan key:generate

# 4. Configurar base de datos en .env
# DB_DATABASE=csdt_db
# DB_USERNAME=tu_usuario
# DB_PASSWORD=tu_contraseña

# 5. Ejecutar migraciones
php artisan migrate

# 6. Ejecutar seeders
php artisan db:seed

# 7. Crear link de storage
php artisan storage:link

# 8. Iniciar servidor
php artisan serve
```

Servidor disponible en: `http://localhost:8000`

---

## 📁 Estructura del Proyecto

```
app/
├── Console/Commands/        # Comandos Artisan
├── Http/
│   ├── Controllers/Api/     # Controladores API (13)
│   └── Middleware/          # Middleware personalizado (4)
├── Models/                  # Modelos Eloquent (28)
├── Providers/               # Service Providers
└── Services/                # Servicios (IAService, CircuitBreaker)

config/                      # Archivos de configuración
├── ai.php                   # Config IA
├── app.php
├── auth.php
└── ...

database/
├── migrations/              # Migraciones (35)
├── seeders/                 # Seeders (6)
└── factories/               # Factories para testing

routes/
├── api.php                  # Rutas API principales
├── web.php
└── console.php

storage/
├── app/                     # Archivos de aplicación
├── framework/               # Cache, sesiones, views
└── logs/                    # Logs del sistema
```

---

## 🔌 API Endpoints

### Base URL
```
http://localhost:8000/api
```

### Autenticación

```http
POST   /api/auth/register          # Registro de usuario
POST   /api/auth/login             # Login
POST   /api/auth/logout            # Logout
GET    /api/auth/me                # Usuario autenticado
```

### Recursos Principales

```http
# Usuarios
GET    /api/users
POST   /api/users
GET    /api/users/{id}
PUT    /api/users/{id}
DELETE /api/users/{id}

# Proyectos
GET    /api/proyectos
POST   /api/proyectos
GET    /api/proyectos/{id}
PUT    /api/proyectos/{id}
DELETE /api/proyectos/{id}

# Actividades (MS Project)
GET    /api/actividades
GET    /api/proyectos/{proyecto}/actividades
POST   /api/actividades
PUT    /api/actividades/{id}
DELETE /api/actividades/{id}

# Casos Legales
GET    /api/casos-legales
POST   /api/casos-legales
GET    /api/casos-legales/{id}

# Inteligencia Artificial
POST   /api/ia/analizar-juridico
POST   /api/ia/analizar-etnico
POST   /api/ia/analizar-veeduria
GET    /api/ia/consultas
GET    /api/ia/estadisticas-centro

# Veedurías
GET    /api/veedurias
POST   /api/veedurias
GET    /api/veedurias/{id}/seguimientos

# Derechos Étnicos
GET    /api/etnicos/pueblos-indigenas
GET    /api/etnicos/comunidades-afro
GET    /api/etnicos/consultas-previas

# Permisos
GET    /api/permisos/usuario/{userId}
POST   /api/permisos/otorgar
```

Ver documentación completa de API en `DOCUMENTACION-TECNICA-CSDT.md`

---

## 🤖 Servicios de IA

### Configuración

Configurar las API keys en `.env`:

```env
OPENAI_API_KEY=sk-...
ANTHROPIC_API_KEY=sk-ant-...
GOOGLE_GEMINI_API_KEY=AI...
ELEVENLABS_API_KEY=...
LEXISNEXIS_API_KEY=...
HUGGINGFACE_API_TOKEN=hf_...
```

### Uso

```php
use App\Services\IAService;

// Análisis jurídico
$resultado = IAService::analizarJuridico([
    'hechos' => '...',
    'derechos_vulnerados' => [...],
    'pretensiones' => '...',
]);

// Análisis étnico
$analisis = IAService::analizarEtnico([
    'territorio' => '...',
    'comunidad_id' => 123,
]);
```

---

## 🗄️ Base de Datos

### Modelos Principales

- **User**: Usuarios del sistema
- **Proyecto**: Proyectos
- **Actividad**: Actividades de proyecto (MS Project)
- **Tarea**: Tareas jerárquicas
- **CasoLegal**: Casos legales
- **Veeduria**: Veedurías ciudadanas
- **PuebloIndigena**: Pueblos indígenas
- **ComunidadAfro**: Comunidades afrodescendientes
- **ConsultaPrevia**: Consultas previas
- **AIConsulta**: Consultas a IA
- **Permiso**: Sistema de permisos

### Migraciones

```bash
# Ejecutar migraciones
php artisan migrate

# Rollback última migración
php artisan migrate:rollback

# Reset completo
php artisan migrate:fresh

# Con seeders
php artisan migrate:fresh --seed
```

### Seeders

```bash
# Todos los seeders
php artisan db:seed

# Seeder específico
php artisan db:seed --class=PermisosRolesSeeder
```

---

## 🔐 Autenticación y Permisos

### Laravel Sanctum

Tokens de autenticación API:

```php
// Login
$token = $user->createToken('auth_token')->plainTextToken;

// Middleware
Route::middleware('auth:sanctum')->group(function () {
    // Rutas protegidas
});
```

### Spatie Permission

Sistema de roles y permisos:

```php
// Asignar rol
$user->assignRole('administrador');

// Verificar permiso
if ($user->hasPermissionTo('gestionar_proyectos')) {
    // ...
}

// Middleware
Route::middleware('can:gestionar_usuarios')->group(function () {
    // ...
});
```

**Roles del Sistema:**
- Super Administrador
- Administrador
- Operador
- Veedor
- Ciudadano
- Comunidad Étnica

---

## 🧪 Testing

```bash
# Ejecutar todos los tests
php artisan test

# Test específico
php artisan test --filter=NombreTest

# Con cobertura
php artisan test --coverage
```

---

## 🛠️ Comandos Útiles

### Artisan

```bash
# Limpiar cachés
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear

# Optimizar para producción
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Listar rutas
php artisan route:list

# Crear modelo con todo
php artisan make:model NombreModelo -mcr
# -m (migración) -c (controlador) -r (resource)

# Crear controlador API
php artisan make:controller Api/NombreController --api

# Crear migración
php artisan make:migration create_tabla_table
```

### Composer

```bash
# Instalar dependencias
composer install

# Actualizar dependencias
composer update

# Autoload
composer dump-autoload

# Verificar dependencias
composer check-platform-reqs
```

### Code Style (Laravel Pint)

```bash
# Formatear código
./vendor/bin/pint

# Ver cambios sin aplicar
./vendor/bin/pint --test
```

---

## 📊 Logs y Debug

### Ver logs en tiempo real

```bash
tail -f storage/logs/laravel.log
```

### Log manual

```php
\Log::info('Mensaje informativo');
\Log::error('Error', ['context' => $data]);
\Log::warning('Advertencia');
```

### Query Log (debug)

```php
\DB::enableQueryLog();
// ... código
dd(\DB::getQueryLog());
```

---

## 🚀 Despliegue

### Producción

```bash
# 1. Clonar y configurar
git clone [REPO] /var/www/csdt-backend
cd /var/www/csdt-backend
composer install --optimize-autoloader --no-dev

# 2. Configurar .env
cp .env.example .env
# Editar .env con configuración de producción

# 3. Optimizar
php artisan key:generate
php artisan migrate --force
php artisan db:seed --force
php artisan storage:link
php artisan config:cache
php artisan route:cache
php artisan view:cache

# 4. Permisos
sudo chown -R www-data:www-data storage bootstrap/cache
sudo chmod -R 775 storage bootstrap/cache
```

### Variables de Entorno Críticas

```env
APP_ENV=production
APP_DEBUG=false
APP_URL=https://api.tudominio.com

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_DATABASE=csdt_db
DB_USERNAME=usuario_prod
DB_PASSWORD=contraseña_segura

CACHE_DRIVER=redis
SESSION_DRIVER=redis
QUEUE_CONNECTION=redis

REDIS_HOST=127.0.0.1
REDIS_PASSWORD=null
REDIS_PORT=6379
```

---

## 🔧 Troubleshooting

### Error: "No application encryption key"
```bash
php artisan key:generate
```

### Error: "Class not found"
```bash
composer dump-autoload
```

### Error: Permisos storage
```bash
sudo chown -R www-data:www-data storage
sudo chmod -R 775 storage
```

### Error: CORS
Verificar `app/Http/Middleware/Cors.php`

### Cache problemas
```bash
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear
```

---

## 📚 Documentación

- **Documentación técnica completa**: `DOCUMENTACION-TECNICA-CSDT.md`
- **Resumen ejecutivo**: `RESUMEN-EJECUTIVO-CSDT.md`
- **Laravel Docs**: https://laravel.com/docs
- **Spatie Permission**: https://spatie.be/docs/laravel-permission
- **Laravel Sanctum**: https://laravel.com/docs/sanctum

---

## 📞 Contacto

**Desarrollador**: Esteban Restrepo  
**Email**: esteban.41m@gmail.com  
**GitHub**: @retapiam

---

## 📄 Licencia

Privado - Todos los derechos reservados © 2024-2025 CSDT

---

**Desarrollado con ❤️ para CSDT**

