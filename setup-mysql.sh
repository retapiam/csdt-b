#!/bin/bash

# ============================================
# Script de Configuración MySQL para CSDT
# ============================================

set -e

echo "🗄️ Configurando MySQL para CSDT..."

# Colores
GREEN='\033[0;32m'
BLUE='\033[0;34m'
YELLOW='\033[1;33m'
RED='\033[0;31m'
NC='\033[0m'

# Variables de configuración
DB_NAME="csdt_db"
DB_USER="csdt_user"
DB_PASSWORD="csdt_secure_password_2024"

echo -e "${BLUE}╔════════════════════════════════════════╗${NC}"
echo -e "${BLUE}║    Configuración MySQL CSDT           ║${NC}"
echo -e "${BLUE}╚════════════════════════════════════════╝${NC}"
echo ""

# Verificar si MySQL está instalado
if ! command -v mysql &> /dev/null; then
    echo -e "${YELLOW}⚠️  MySQL no está instalado. Instalando...${NC}"
    apt-get update
    apt-get install -y mysql-server
    
    # Iniciar MySQL
    systemctl start mysql
    systemctl enable mysql
fi

echo -e "${BLUE}1️⃣ Creando base de datos y usuario...${NC}"

# Crear base de datos y usuario
mysql -u root << EOF
-- Crear base de datos
CREATE DATABASE IF NOT EXISTS ${DB_NAME} CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

-- Crear usuario
CREATE USER IF NOT EXISTS '${DB_USER}'@'localhost' IDENTIFIED BY '${DB_PASSWORD}';

-- Otorgar privilegios
GRANT ALL PRIVILEGES ON ${DB_NAME}.* TO '${DB_USER}'@'localhost';

-- Permitir conexiones remotas (opcional)
CREATE USER IF NOT EXISTS '${DB_USER}'@'%' IDENTIFIED BY '${DB_PASSWORD}';
GRANT ALL PRIVILEGES ON ${DB_NAME}.* TO '${DB_USER}'@'%';

-- Aplicar cambios
FLUSH PRIVILEGES;

-- Mostrar bases de datos
SHOW DATABASES;
EOF

echo -e "${GREEN}✅ Base de datos creada exitosamente${NC}"

# Actualizar archivo .env
echo -e "${BLUE}2️⃣ Actualizando archivo .env...${NC}"

ENV_FILE="/var/www/csdt-backend/.env"

if [ -f "$ENV_FILE" ]; then
    # Actualizar configuración de base de datos
    sed -i "s/DB_CONNECTION=.*/DB_CONNECTION=mysql/" "$ENV_FILE"
    sed -i "s/DB_HOST=.*/DB_HOST=127.0.0.1/" "$ENV_FILE"
    sed -i "s/DB_PORT=.*/DB_PORT=3306/" "$ENV_FILE"
    sed -i "s/DB_DATABASE=.*/DB_DATABASE=${DB_NAME}/" "$ENV_FILE"
    sed -i "s/DB_USERNAME=.*/DB_USERNAME=${DB_USER}/" "$ENV_FILE"
    sed -i "s/DB_PASSWORD=.*/DB_PASSWORD=${DB_PASSWORD}/" "$ENV_FILE"
    
    echo -e "${GREEN}✅ Archivo .env actualizado${NC}"
else
    echo -e "${RED}❌ Archivo .env no encontrado en $ENV_FILE${NC}"
    exit 1
fi

# Ejecutar migraciones
echo -e "${BLUE}3️⃣ Ejecutando migraciones...${NC}"
cd /var/www/csdt-backend
php artisan migrate --force

echo -e "${BLUE}4️⃣ Ejecutando seeders (datos iniciales)...${NC}"
php artisan db:seed --force

echo -e "${BLUE}5️⃣ Optimizando Laravel...${NC}"
php artisan config:clear
php artisan cache:clear
php artisan config:cache

echo ""
echo -e "${GREEN}╔════════════════════════════════════════════════════════════╗${NC}"
echo -e "${GREEN}║                                                            ║${NC}"
echo -e "${GREEN}║         ✅ MySQL CONFIGURADO EXITOSAMENTE                  ║${NC}"
echo -e "${GREEN}║                                                            ║${NC}"
echo -e "${GREEN}╚════════════════════════════════════════════════════════════╝${NC}"
echo ""
echo -e "${BLUE}📊 Detalles de la base de datos:${NC}"
echo "   Base de datos: $DB_NAME"
echo "   Usuario:       $DB_USER"
echo "   Password:      $DB_PASSWORD"
echo "   Host:          localhost"
echo "   Puerto:        3306"
echo ""
echo -e "${YELLOW}⚠️  IMPORTANTE: Guarda estas credenciales de forma segura${NC}"
echo ""

# Verificar conexión
echo -e "${BLUE}🔍 Verificando conexión...${NC}"
cd /var/www/csdt-backend
php artisan tinker --execute="echo 'Conexión exitosa a: ' . config('database.connections.mysql.database') . PHP_EOL;"

echo -e "${GREEN}✅ Todo listo para usar${NC}"

