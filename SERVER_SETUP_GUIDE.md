# Ubuntu Server Setup Guide for Laravel

This guide outlines the step-by-step process used to configure a brand new Ubuntu server for hosting a Laravel application. You can use this guide to set up similar servers for other projects.

## 0. SSH Key Setup (Passwordless Login)

Before configuring the server, it is highly recommended to set up SSH keys so you can log in and deploy without typing a password every time.

1. **Generate an SSH key pair** (on your LOCAL Mac/PC):
```bash
ssh-keygen -t rsa -b 4096 -C "your_email@example.com"
```
*(Press Enter to accept the default file location, and optionally add a passphrase).*

2. **Copy the public key to your new server**:
```bash
ssh-copy-id server_user@server_ip
```
*(You will be prompted for the server's password one last time).*

3. **Verify passwordless login**:
```bash
ssh server_user@server_ip
```

## 1. Initial Server Setup & Updates

First, log into your new server via SSH and update the system packages:

```bash
sudo apt update
sudo apt upgrade -y
```

## 2. Install Required Software

Install Nginx, MySQL, and PHP (including PHP-FPM and required extensions for Laravel). 
*Note: Newer Ubuntu versions (like 24.04) come with PHP 8.3 or 8.5 by default.*

```bash
sudo apt install -y nginx mysql-server zip unzip curl
sudo apt install -y php-fpm php-mysql php-mbstring php-xml php-bcmath php-curl php-zip
```

Install **Composer** (PHP Package Manager):
```bash
curl -sS https://getcomposer.org/installer | php
sudo mv composer.phar /usr/local/bin/composer
```

## 3. Database Configuration

Log into MySQL as root to create the database and a dedicated user:

```bash
sudo mysql
```

Run the following SQL commands inside the MySQL prompt (replace placeholders with your actual details):
```sql
CREATE DATABASE your_database_name;
CREATE USER 'your_db_user'@'localhost' IDENTIFIED BY 'YourStrongPassword123!';
GRANT ALL PRIVILEGES ON your_database_name.* TO 'your_db_user'@'localhost';
FLUSH PRIVILEGES;
EXIT;
```

## 4. Prepare the Application Directory

Create the web directory for your app and give your user ownership so you can upload files without sudo:

```bash
sudo mkdir -p /var/www/your_app_name
sudo chown -R $USER:$USER /var/www/your_app_name
```

## 5. Nginx Configuration

Create a new Nginx server block configuration for your Laravel app:

```bash
sudo nano /etc/nginx/sites-available/your_app_name
```

Paste the following configuration (make sure the `fastcgi_pass` PHP version matches what is installed on your server, e.g., `php8.3-fpm.sock` or `php8.5-fpm.sock`):

```nginx
server {
    listen 80;
    server_name your_domain_or_ip;
    root /var/www/your_app_name/public;

    add_header X-Frame-Options "SAMEORIGIN";
    add_header X-Content-Type-Options "nosniff";

    index index.php index.html index.htm;

    charset utf-8;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location = /favicon.ico { access_log off; log_not_found off; }
    location = /robots.txt  { access_log off; log_not_found off; }

    error_page 404 /index.php;

    location ~ \.php$ {
        # IMPORTANT: Ensure this matches your installed PHP version!
        fastcgi_pass unix:/var/run/php/php8.5-fpm.sock; 
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
        fastcgi_hide_header X-Powered-By;
    }

    location ~ /\.(?!well-known).* {
        deny all;
    }
}
```

Enable the site and restart Nginx:
```bash
sudo ln -s /etc/nginx/sites-available/your_app_name /etc/nginx/sites-enabled/
sudo systemctl restart nginx
```

## 6. Deployment & Permissions

After uploading your Laravel code to `/var/www/your_app_name` (via `rsync`, FTP, or Git), you must configure the `.env` file and fix permissions so the web server can read/write to the cache and logs.

```bash
cd /var/www/your_app_name

# Install dependencies and migrate
composer install --optimize-autoloader --no-dev
php artisan migrate --force
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Set ownership to your user and the web server group (www-data)
sudo chown -R $USER:www-data /var/www/your_app_name

# Set standard file and folder permissions
sudo find /var/www/your_app_name -type f -exec chmod 644 {} \;
sudo find /var/www/your_app_name -type d -exec chmod 755 {} \;

# Grant the web server write access to storage and bootstrap/cache
sudo chgrp -R www-data storage bootstrap/cache
sudo chmod -R ug+rwx storage bootstrap/cache
```

## 7. Syncing Local Database to Production (Optional)

If you need to move your local development database to the new server, you can pipe it directly over SSH:

```bash
# Run this on your LOCAL machine
mysqldump -u root local_database_name | ssh server_user@server_ip "mysql -u production_db_user -pProductionPassword production_database_name"
```
