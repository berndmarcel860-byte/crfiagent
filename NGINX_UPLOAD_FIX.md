# Quick Fix: Nginx "client body too large" Error

## Error You're Seeing

```
client intended to send too large body: XXXXXX bytes
```

This error occurs when uploading files (KYC documents, payment proofs, etc.) through the admin panel.

---

## Quick Fix (5 Minutes)

### 1. Edit Nginx Configuration

```bash
sudo nano /etc/nginx/sites-available/your-site
```

Or for global settings:
```bash
sudo nano /etc/nginx/nginx.conf
```

### 2. Add This Line

Inside the `server {}` block (or `http {}` block for global), add:

```nginx
client_max_body_size 50M;
```

**Example:**
```nginx
server {
    listen 80;
    server_name novalnet-ai.de;
    root /var/www/novalnet-ai.de/app;
    
    # ADD THIS LINE
    client_max_body_size 50M;
    
    # ... rest of your config
}
```

### 3. Test Configuration

```bash
sudo nginx -t
```

If you see "syntax is ok" and "test is successful", proceed to next step.

### 4. Restart Nginx

```bash
sudo systemctl restart nginx
```

### 5. Test Upload

Try uploading a KYC document through the admin panel. It should now work!

---

## Also Update PHP Settings

Edit PHP configuration:

```bash
sudo nano /etc/php/8.3/fpm/php.ini
```

Find and update these lines:

```ini
upload_max_filesize = 50M
post_max_size = 50M
max_execution_time = 300
```

Restart PHP-FPM:

```bash
sudo systemctl restart php8.3-fpm
```

---

## Verify Settings

Check current Nginx setting:
```bash
grep -r "client_max_body_size" /etc/nginx/
```

Check current PHP settings:
```bash
php -i | grep upload_max_filesize
php -i | grep post_max_size
```

---

## Still Having Issues?

See **SERVER_CONFIGURATION.md** for comprehensive troubleshooting and configuration guide.

---

## What Changed?

- **Before:** Nginx limited uploads to ~1 MB (default)
- **After:** Uploads allowed up to 50 MB
- **Impact:** KYC documents, payment proofs, and other files can now be uploaded successfully

---

**Need help?** Check the full documentation in SERVER_CONFIGURATION.md
