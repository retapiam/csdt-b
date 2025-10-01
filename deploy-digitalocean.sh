#!/bin/bash

# ============================================
# Script de Despliegue CSDT Backend
# Servidor: DigitalOcean Droplet
# IP: 68.183.174.82
# ============================================

set -e

echo "🚀 Iniciando despliegue del Backend CSDT..."

# Variables de configuración
SERVER_IP="68.183.174.82"
SERVER_USER="root"
APP_NAME="csdt-backend"
REPO_URL="https://github.com/retapiam/csdt-b.git"
APP_DIR="/var/www/$APP_NAME"
DOMAIN="api.csdt.example.com"  # Cambiar por tu dominio

# Colores para output
GREEN='\033[0;32m'
BLUE='\033[0;34m'
RED='\033[0;31m'
NC='\033[0m'

echo -e "${BLUE}📦 Conectando al servidor $SERVER_IP...${NC}"

# Script que se ejecutará en el servidor
ssh $SERVER_USER@$SERVER_IP << 'ENDSSH'

echo "✅ Conectado al servidor"

# 1. Actualizar sistema
echo "📦 Actualizando sistema..."
apt-get update
apt-get upgrade -y

# 2. Instalar dependencias necesarias
echo "📦 Instalando dependencias..."
apt-get install -y \
    software-properties-common \
    curl \
    git \
    unzip \
    nginx \
    mysql-client

# 3. Instalar PHP 8.2 y extensiones
echo "🐘 Instalando PHP 8.2..."
add-apt-repository -y ppa:ondrej/php
apt-get update
apt-get install -y \
    php8.2 \
    php8.2-fpm \
    php8.2-cli \
    php8.2-common \
    php8.2-mysql \
    php8.2-zip \
    php8.2-gd \
    php8.2-mbstring \
    php8.2-curl \
    php8.2-xml \
    php8.2-bcmath \
    php8.2-sqlite3 \
    php8.2-intl

# 4. Instalar Composer
echo "📦 Instalando Composer..."
if [ ! -f /usr/local/bin/composer ]; then
    curl -sS https://getcomposer.org/installer | php
    mv composer.phar /usr/local/bin/composer
    chmod +x /usr/local/bin/composer
fi

# 5. Crear directorio de aplicación
echo "📁 Creando directorio de aplicación..."
mkdir -p /var/www/csdt-backend
cd /var/www/csdt-backend

# 6. Clonar o actualizar repositorio
if [ -d ".git" ]; then
    echo "🔄 Actualizando repositorio existente..."
    git pull origin main
else
    echo "📥 Clonando repositorio..."
    cd /var/www
    rm -rf csdt-backend
    git clone https://github.com/retapiam/csdt-b.git csdt-backend
    cd csdt-backend
fi

# 7. Instalar dependencias de Composer
echo "📦 Instalando dependencias de Composer..."
composer install --optimize-autoloader --no-dev

# 8. Configurar archivo .env
echo "⚙️ Configurando archivo .env..."
if [ ! -f .env ]; then
    cp .env.example .env
    
    # Generar APP_KEY
    php artisan key:generate
fi

# 9. Configurar permisos
echo "🔒 Configurando permisos..."
chown -R www-data:www-data /var/www/csdt-backend
chmod -R 755 /var/www/csdt-backend
chmod -R 775 /var/www/csdt-backend/storage
chmod -R 775 /var/www/csdt-backend/bootstrap/cache

# 10. Ejecutar migraciones
echo "🗄️ Ejecutando migraciones..."
php artisan migrate --force

# 11. Optimizar Laravel
echo "⚡ Optimizando Laravel..."
php artisan config:cache
php artisan route:cache
php artisan view:cache

# 12. Configurar Nginx
echo "🌐 Configurando Nginx..."
cat > /etc/nginx/sites-available/csdt-backend << 'EOF'
server {
    listen 80;
    listen [::]:80;
    server_name 68.183.174.82;
    root /var/www/csdt-backend/public;

    add_header X-Frame-Options "SAMEORIGIN";
    add_header X-Content-Type-Options "nosniff";

    index index.php;

    charset utf-8;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location = /favicon.ico { access_log off; log_not_found off; }
    location = /robots.txt  { access_log off; log_not_found off; }

    error_page 404 /index.php;

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.2-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
    }

    location ~ /\.(?!well-known).* {
        deny all;
    }
}
EOF

# Activar sitio
ln -sf /etc/nginx/sites-available/csdt-backend /etc/nginx/sites-enabled/
rm -f /etc/nginx/sites-enabled/default

# Verificar configuración de Nginx
nginx -t

# Reiniciar servicios
echo "🔄 Reiniciando servicios..."
systemctl restart php8.2-fpm
systemctl restart nginx
systemctl enable php8.2-fpm
systemctl enable nginx

# 13. Configurar firewall
echo "🔥 Configurando firewall..."
ufw --force enable
ufw allow 22
ufw allow 80
ufw allow 443
ufw allow 3306

echo "✅ ¡Despliegue completado exitosamente!"
echo "🌐 Backend disponible en: http://68.183.174.82"
echo "📝 Recuerda configurar el archivo .env con las credenciales de MySQL"

ENDSSH

echo -e "${GREEN}✅ ¡Despliegue completado!${NC}"
echo -e "${BLUE}🌐 Backend disponible en: http://68.183.174.82${NC}"
echo ""
echo -e "${BLUE}📋 Próximos pasos:${NC}"
echo "1. Conectarse al servidor: ssh root@68.183.174.82"
echo "2. Editar .env: nano /var/www/csdt-backend/.env"
echo "3. Configurar credenciales de MySQL"
echo "4. Ejecutar migraciones: php artisan migrate"

