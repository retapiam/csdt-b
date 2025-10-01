# 🏛️ CSDT Backend - API Laravel

## 📋 Descripción

Backend API para el Consejo Social de Veeduría y Desarrollo Territorial (CSDT), desarrollado con Laravel 11.

## 🚀 Inicio Rápido

### Prerrequisitos
- PHP 8.2+
- Composer
- MySQL 8.0+
- Node.js 18+ (para assets)

### Instalación

```bash
# Instalar dependencias PHP
composer install

# Configurar variables de entorno
cp .env.example .env
php artisan key:generate

# Configurar base de datos en .env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=csdt_database
DB_USERNAME=root
DB_PASSWORD=

# Ejecutar migraciones
php artisan migrate

# Poblar base de datos
php artisan db:seed

# Instalar dependencias de frontend (si es necesario)
npm install
npm run build

# Iniciar servidor
php artisan serve
```

## 🏗️ Arquitectura

### Estructura de la API
```
routes/
├── api.php                    # Rutas principales de la API
├── api-completa.php          # Rutas completas del sistema
├── api-usuarios.php          # Rutas de gestión de usuarios
├── api-administrador-general.php # Rutas de administración
├── auth.php                  # Rutas de autenticación
└── settings.php              # Rutas de configuración
```

### Modelos Principales
- `User` - Usuarios del sistema
- `Veeduria` - Veedurías ciudadanas
- `PQRSFD` - Peticiones, quejas, reclamos
- `Donacion` - Sistema de donaciones
- `AnalisisIA` - Análisis con IA
- `Tarea` - Gestión de tareas

### Servicios
- `IAService` - Servicios de inteligencia artificial
- `PDFGeneratorService` - Generación de documentos PDF
- `ValidationService` - Validaciones personalizadas
- `LoggingService` - Sistema de logging

## 🔧 Endpoints Principales

### Autenticación
- `POST /api/auth/login` - Iniciar sesión
- `POST /api/auth/register` - Registro de usuarios
- `POST /api/auth/logout` - Cerrar sesión

### Veedurías
- `GET /api/veedurias` - Listar veedurías
- `POST /api/veedurias` - Crear veeduría
- `GET /api/veedurias/{id}` - Obtener veeduría específica

### PQRSFD
- `GET /api/pqrsfd` - Listar PQRSFD
- `POST /api/pqrsfd` - Crear PQRSFD
- `PUT /api/pqrsfd/{id}` - Actualizar PQRSFD

## 🔒 Sistema de Roles

- **Cliente**: Acceso básico a funcionalidades
- **Operador**: Gestión de procesos y usuarios
- **Administrador**: Acceso completo al sistema

## 🤖 Inteligencia Artificial

El sistema incluye servicios de IA para:
- Análisis automático de casos
- Generación de recomendaciones
- Procesamiento de documentos
- Análisis de sentimientos

## 📊 Base de Datos

### Migraciones Principales
- Estructura completa normalizada
- Sistema de roles y permisos
- Tablas de veedurías y PQRSFD
- Sistema de archivos y documentos

## 🛠️ Desarrollo

### Comandos Útiles
```bash
# Crear migración
php artisan make:migration nombre_migracion

# Crear modelo
php artisan make:model NombreModelo

# Crear controlador
php artisan make:controller NombreController

# Ejecutar tests
php artisan test

# Limpiar caché
php artisan cache:clear
php artisan config:clear
php artisan route:clear
```

## 📱 Integración Frontend

La API está diseñada para integrarse con el frontend React en `csdt-f/`:
- CORS configurado para `http://localhost:5173`
- Autenticación con Sanctum
- Respuestas en formato JSON estándar

## 🔧 Configuración

### Variables de Entorno Importantes
```env
APP_NAME="CSDT Backend"
APP_ENV=local
APP_DEBUG=true
APP_URL=http://localhost:8000

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=csdt_database

SANCTUM_STATEFUL_DOMAINS=localhost:5173
```

## 📞 Soporte

Para soporte técnico o consultas sobre la API, contactar al equipo de desarrollo.

---

**Framework**: Laravel 11  
**PHP**: 8.2+  
**Base de Datos**: MySQL 8.0+  
**Última actualización**: $(Get-Date -Format "yyyy-MM-dd")