# Usuarios de Prueba - CSDT

Este documento contiene las credenciales de los usuarios de prueba creados para el sistema.

## 🔐 Credenciales de Acceso

Todos los usuarios utilizan la misma contraseña: **`password123`**

### 1. Super Administrador
- **Email:** superadmin@csdt.test
- **Rol:** superadmin
- **Permisos:** Acceso total al sistema
- **Documento:** CC 1234567890
- **Teléfono:** 3001234567

### 2. Administrador
- **Email:** admin@csdt.test
- **Rol:** administrador
- **Permisos:** Gestión completa del sistema
- **Documento:** CC 1234567891
- **Teléfono:** 3001234568

### 3. Operador
- **Email:** operador@csdt.test
- **Rol:** operador
- **Permisos:** Usuario operativo del sistema
- **Documento:** CC 1234567892
- **Teléfono:** 3001234569

### 4. Cliente
- **Email:** cliente@csdt.test
- **Rol:** cliente
- **Permisos:** Usuario básico
- **Documento:** CC 1234567893
- **Teléfono:** 3001234570

### 5. Operador Adicional (María González)
- **Email:** maria@csdt.test
- **Rol:** operador
- **Documento:** CC 9876543210
- **Teléfono:** 3007654321

### 6. Usuario Inactivo
- **Email:** inactivo@csdt.test
- **Rol:** cliente
- **Estado:** inactivo
- **Documento:** CC 1111111111
- **Teléfono:** 3009999999

---

## 📋 Cómo Ejecutar el Seeder

### Opción 1: Ejecutar todos los seeders
```bash
cd csdt-b-main
php artisan db:seed
```

### Opción 2: Ejecutar solo el seeder de usuarios de prueba
```bash
cd csdt-b-main
php artisan db:seed --class=UsuariosPruebaSeeder
```

### Opción 3: Refrescar la base de datos y ejecutar seeders
```bash
cd csdt-b-main
php artisan migrate:fresh --seed
```

⚠️ **Advertencia:** La opción 3 eliminará todos los datos existentes.

---

## 🧪 Pruebas Recomendadas

1. **Login con diferentes roles:** Probar el acceso con cada tipo de usuario
2. **Permisos:** Verificar que cada rol tiene los permisos correctos
3. **Estados:** Probar el usuario inactivo para validar restricciones
4. **API Tokens:** Generar tokens para pruebas de API

---

## 📝 Notas

- Todos los usuarios tienen el email verificado (`email_verified_at`)
- Los documentos son ficticios para pruebas
- La contraseña está hasheada usando bcrypt
- Los teléfonos siguen el formato colombiano (300XXXXXXX)

