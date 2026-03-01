# Server Configuration Guide for Production Deployment

This guide provides essential server configuration settings required for the CRFIAgent application to function properly, especially for file uploads (KYC documents, payment proofs, etc.).

---

## Table of Contents

1. [Quick Fix for Upload Errors](#quick-fix-for-upload-errors)
2. [Nginx Configuration](#nginx-configuration)
3. [Apache Configuration](#apache-configuration)
4. [PHP Configuration](#php-configuration)
5. [Troubleshooting](#troubleshooting)
6. [Security Considerations](#security-considerations)

---

## Quick Fix for Upload Errors

### Error: "client intended to send too large body"

If you see this error in Nginx logs:
```
client intended to send too large body: XXXXXX bytes
```

**Immediate Fix:**

1. Edit your Nginx site configuration:
```bash
sudo nano /etc/nginx/sites-available/your-site
```

2. Add this inside the `server` block:
```nginx
client_max_body_size 50M;
```

3. Restart Nginx:
```bash
sudo systemctl restart nginx
```

---

## Nginx Configuration

### Complete Configuration for File Uploads

Edit your Nginx configuration file (usually `/etc/nginx/sites-available/your-site` or `/etc/nginx/nginx.conf`):

```nginx
server {
    listen 80;
    server_name your-domain.com;
    root /var/www/your-site/app;
    index index.php index.html;

    # File upload size limit - IMPORTANT for KYC documents
    client_max_body_size 50M;
    
    # Timeout settings for large uploads
    client_body_timeout 300s;
    client_header_timeout 300s;
    
    # Buffer settings
    client_body_buffer_size 128k;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.3-fpm.sock;
        fastcgi_index index.php;
        fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
        include fastcgi_params;
        
        # Upload timeout settings for PHP
        fastcgi_read_timeout 300;
        fastcgi_send_timeout 300;
    }

    # Deny access to sensitive files
    location ~ /\.ht {
        deny all;
    }
    
    location ~ /config\.php$ {
        deny all;
    }
}
```

### Nginx Global Settings

For global settings that apply to all sites, edit `/etc/nginx/nginx.conf`:

```nginx
http {
    # Global upload size limit
    client_max_body_size 50M;
    
    # Other recommended settings
    client_body_timeout 300s;
    client_header_timeout 300s;
    keepalive_timeout 65;
    types_hash_max_size 2048;
    
    # Include other configurations
    include /etc/nginx/mime.types;
    include /etc/nginx/sites-enabled/*;
}
```

### After Configuration Changes

Always test and reload Nginx after changes:

```bash
# Test configuration
sudo nginx -t

# If test passes, reload
sudo systemctl reload nginx

# Or restart
sudo systemctl restart nginx
```

---

## Apache Configuration

### .htaccess Configuration

Create or edit `.htaccess` in your application root:

```apache
# File upload size limit
php_value upload_max_filesize 50M
php_value post_max_size 50M
php_value max_execution_time 300
php_value max_input_time 300

# Memory limit
php_value memory_limit 256M

# Security: Prevent directory listing
Options -Indexes

# Security: Block access to sensitive files
<FilesMatch "^(config\.php|\.env|\.git)">
    Order allow,deny
    Deny from all
</FilesMatch>
```

### Apache VirtualHost Configuration

Edit your Apache site configuration (e.g., `/etc/apache2/sites-available/your-site.conf`):

```apache
<VirtualHost *:80>
    ServerName your-domain.com
    DocumentRoot /var/www/your-site/app
    
    <Directory /var/www/your-site/app>
        Options -Indexes +FollowSymLinks
        AllowOverride All
        Require all granted
    </Directory>

    # PHP settings
    php_value upload_max_filesize 50M
    php_value post_max_size 50M
    php_value max_execution_time 300
    php_value max_input_time 300
    php_value memory_limit 256M

    # Logging
    ErrorLog ${APACHE_LOG_DIR}/your-site-error.log
    CustomLog ${APACHE_LOG_DIR}/your-site-access.log combined
</VirtualHost>
```

### Reload Apache

```bash
# Test configuration
sudo apache2ctl configtest

# Reload
sudo systemctl reload apache2
```

---

## PHP Configuration

### PHP.ini Settings

Edit your PHP configuration file (location varies by installation):
- Ubuntu/Debian: `/etc/php/8.3/fpm/php.ini` or `/etc/php/8.3/apache2/php.ini`
- CentOS/RHEL: `/etc/php.ini`

```ini
; File upload settings
upload_max_filesize = 50M
post_max_size = 50M
max_file_uploads = 20

; Execution time settings
max_execution_time = 300
max_input_time = 300
default_socket_timeout = 300

; Memory settings
memory_limit = 256M

; Error reporting (production)
display_errors = Off
display_startup_errors = Off
error_reporting = E_ALL & ~E_DEPRECATED & ~E_STRICT
log_errors = On
error_log = /var/log/php/error.log

; Session settings
session.gc_maxlifetime = 3600
session.cookie_httponly = 1
session.cookie_secure = 1
session.use_strict_mode = 1
```

### PHP-FPM Configuration (if using PHP-FPM)

Edit PHP-FPM pool configuration (e.g., `/etc/php/8.3/fpm/pool.d/www.conf`):

```ini
[www]
; Process manager settings
pm = dynamic
pm.max_children = 50
pm.start_servers = 5
pm.min_spare_servers = 5
pm.max_spare_servers = 35

; Timeout settings
request_terminate_timeout = 300s
```

### Restart PHP-FPM

```bash
sudo systemctl restart php8.3-fpm
```

---

## Troubleshooting

### Common Upload Issues

#### 1. "client intended to send too large body" (Nginx)

**Error:** Nginx error log shows this message

**Solution:**
- Increase `client_max_body_size` in Nginx configuration
- Default is 1M, increase to at least 50M

```nginx
client_max_body_size 50M;
```

#### 2. "Maximum upload size exceeded" (PHP)

**Error:** PHP error or application error about file size

**Solution:**
- Check PHP `upload_max_filesize` and `post_max_size`
- `post_max_size` should be larger than `upload_max_filesize`

```ini
upload_max_filesize = 50M
post_max_size = 50M
```

#### 3. "Gateway Timeout" or "504 Gateway Timeout"

**Error:** Upload times out before completing

**Solution:**
- Increase timeout settings in both Nginx and PHP
- Set `fastcgi_read_timeout` in Nginx
- Set `max_execution_time` in PHP

**Nginx:**
```nginx
fastcgi_read_timeout 300;
client_body_timeout 300s;
```

**PHP:**
```ini
max_execution_time = 300
max_input_time = 300
```

#### 4. Upload works but files are not saved

**Error:** No error shown but files not uploaded

**Solution:**
- Check directory permissions
- Ensure upload directory is writable

```bash
# Check current permissions
ls -la /var/www/your-site/app/uploads

# Set correct permissions (example)
sudo chown -R www-data:www-data /var/www/your-site/app/uploads
sudo chmod -R 755 /var/www/your-site/app/uploads
```

#### 5. How to verify current settings

Check current PHP settings:

```bash
# Check upload_max_filesize
php -i | grep upload_max_filesize

# Check post_max_size
php -i | grep post_max_size

# Check all upload-related settings
php -i | grep -i upload
```

Create a test PHP file (`phpinfo.php`):
```php
<?php
phpinfo();
?>
```

Then access it via browser and search for:
- `upload_max_filesize`
- `post_max_size`
- `max_execution_time`

**IMPORTANT:** Delete the phpinfo.php file after testing for security!

---

## Security Considerations

### 1. File Upload Validation

The application already includes:
- File type validation (MIME type and extension)
- Minimum file size check (prevents empty uploads)
- Secure file naming and storage

### 2. Directory Permissions

Recommended permissions:
```bash
# Application files
sudo chown -R www-data:www-data /var/www/your-site
sudo find /var/www/your-site -type d -exec chmod 755 {} \;
sudo find /var/www/your-site -type f -exec chmod 644 {} \;

# Upload directories (need write permission)
sudo chmod -R 775 /var/www/your-site/app/uploads
sudo chmod -R 775 /var/www/your-site/app/documents
```

### 3. Disable Directory Listing

**Nginx:** Already disabled by default

**Apache:** Add to .htaccess:
```apache
Options -Indexes
```

### 4. Restrict Access to Sensitive Files

**Nginx:** Add to server block:
```nginx
location ~ /config\.php$ {
    deny all;
}

location ~ /\.env$ {
    deny all;
}

location ~ /\.git {
    deny all;
}
```

**Apache:** Add to .htaccess:
```apache
<FilesMatch "^(config\.php|\.env|\.git.*|\.htaccess)">
    Order allow,deny
    Deny from all
</FilesMatch>
```

### 5. Enable HTTPS

Always use HTTPS in production:

```bash
# Using Certbot (Let's Encrypt)
sudo apt install certbot python3-certbot-nginx
sudo certbot --nginx -d your-domain.com
```

### 6. Firewall Configuration

```bash
# UFW (Ubuntu)
sudo ufw allow 'Nginx Full'
sudo ufw allow OpenSSH
sudo ufw enable

# Or specific ports
sudo ufw allow 80/tcp
sudo ufw allow 443/tcp
```

---

## Quick Reference

### Minimum Required Settings

| Setting | Recommended Value | Location |
|---------|------------------|----------|
| `client_max_body_size` | 50M | Nginx config |
| `upload_max_filesize` | 50M | php.ini |
| `post_max_size` | 50M | php.ini |
| `max_execution_time` | 300 | php.ini |
| `memory_limit` | 256M | php.ini |
| `fastcgi_read_timeout` | 300 | Nginx config |

### Service Management Commands

```bash
# Nginx
sudo systemctl status nginx
sudo systemctl restart nginx
sudo systemctl reload nginx
sudo nginx -t  # Test configuration

# PHP-FPM
sudo systemctl status php8.3-fpm
sudo systemctl restart php8.3-fpm

# Apache
sudo systemctl status apache2
sudo systemctl restart apache2
sudo apache2ctl configtest  # Test configuration

# MySQL
sudo systemctl status mysql
sudo systemctl restart mysql
```

---

## Testing After Configuration

### 1. Test File Upload

1. Log in to admin panel
2. Go to KYC management
3. Try uploading a test document (>1MB)
4. Verify successful upload

### 2. Check Error Logs

```bash
# Nginx error log
sudo tail -f /var/log/nginx/error.log

# PHP error log
sudo tail -f /var/log/php8.3-fpm.log

# Application error log (if configured)
tail -f /var/www/your-site/app/logs/error.log
```

### 3. Monitor During Upload

Open multiple terminals:

**Terminal 1:** Watch Nginx errors
```bash
sudo tail -f /var/log/nginx/error.log
```

**Terminal 2:** Watch PHP errors
```bash
sudo tail -f /var/log/php8.3-fpm.log
```

**Terminal 3:** Watch application logs
```bash
tail -f /var/www/your-site/app/logs/error.log
```

Then perform an upload and watch for any errors.

---

## Production Deployment Checklist

- [ ] Set `client_max_body_size` in Nginx config
- [ ] Set `upload_max_filesize` in php.ini
- [ ] Set `post_max_size` in php.ini
- [ ] Set `max_execution_time` in php.ini
- [ ] Configure proper directory permissions
- [ ] Enable HTTPS with valid SSL certificate
- [ ] Configure firewall rules
- [ ] Set up proper error logging
- [ ] Disable directory listing
- [ ] Restrict access to sensitive files
- [ ] Test file uploads with various sizes
- [ ] Set up database backups
- [ ] Configure email (SMTP) settings
- [ ] Test all critical functionality

---

## Support

For issues specific to this application:
- Check application logs in `/var/www/your-site/app/logs/`
- Review this documentation thoroughly
- Verify all configuration settings are applied

For server issues:
- Check system logs: `sudo journalctl -xe`
- Verify service status: `sudo systemctl status nginx php8.3-fpm mysql`
- Test configurations before restarting services

---

**Last Updated:** March 2026  
**Application Version:** Compatible with CRFIAgent PHP 8.3+
