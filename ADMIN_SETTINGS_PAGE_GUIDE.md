# Admin Settings Page - Complete Guide

## Overview

The Admin Settings Page allows administrators to configure system-wide settings and SMTP email configuration through a professional web interface.

## Features

### 1. System Settings
Configure company and brand information:
- **Brand Name** - Your company/brand name (displayed in emails, UI)
- **Website URL** - Your main website URL
- **Contact Email** - Primary contact email for support
- **Contact Phone** - Customer support phone number
- **Company Address** - Full company address
- **FCA Reference Number** - Financial Conduct Authority reference

### 2. SMTP Settings
Configure email server for sending emails:
- **SMTP Host** - Mail server hostname (e.g., smtp.gmail.com)
- **Port** - Mail server port (usually 587 for TLS, 465 for SSL)
- **Encryption** - Security protocol (TLS/SSL/None)
- **Username** - SMTP authentication username
- **Password** - SMTP authentication password
- **From Email** - Email address shown as sender
- **From Name** - Name shown as sender

## Access

**URL:** `/admin/admin_settings.php`

**Requirements:**
- Admin must be logged in
- Valid admin session
- Database access

## Usage

### Updating System Settings

1. Navigate to `/admin/admin_settings.php`
2. Ensure "System Settings" tab is active (default)
3. Update the fields as needed
4. Click "Save System Settings" button
5. Success notification will appear

### Updating SMTP Settings

1. Navigate to `/admin/admin_settings.php`
2. Click the "SMTP Settings" tab
3. Update email server configuration
4. Click "Save SMTP Settings" button
5. Optionally, click "Test SMTP Connection" to verify
6. Success notification will appear

## Form Fields

### System Settings

| Field | Required | Type | Description |
|-------|----------|------|-------------|
| Brand Name | Yes | Text | Company/brand name |
| Website URL | Yes | URL | Main website URL (with https://) |
| Contact Email | Yes | Email | Primary contact email |
| Contact Phone | No | Text | Support phone number |
| Company Address | No | Textarea | Full company address |
| FCA Reference | No | Text | Regulatory reference number |

### SMTP Settings

| Field | Required | Type | Description |
|-------|----------|------|-------------|
| SMTP Host | Yes | Text | Mail server hostname |
| Port | Yes | Number | Mail server port (1-65535) |
| Encryption | Yes | Select | TLS, SSL, or None |
| Username | Yes | Text | SMTP authentication username |
| Password | Yes | Password | SMTP authentication password |
| From Email | Yes | Email | Sender email address |
| From Name | Yes | Text | Sender name |

## Validation

### Client-Side
- Required fields checked before submission
- HTML5 validation for email and URL formats
- Port number range validation

### Server-Side
- CSRF token verification
- Admin authentication check
- Required field validation
- Email format validation (filter_var)
- URL format validation (filter_var)
- Port range validation (1-65535)
- Encryption type validation (tls/ssl/none)

## Security Features

### Authentication
- Admin session required
- Redirects to login if not authenticated

### CSRF Protection
- CSRF token in every form
- Token validated on server-side
- Prevents cross-site request forgery

### Input Sanitization
- All inputs trimmed
- HTML special chars escaped
- SQL injection prevention (prepared statements)

### Password Security
- Password field type="password" (masked in UI)
- Password not logged in audit trail
- Stored as-is in database (for SMTP use)

### Audit Logging
- All changes logged to audit_logs table
- Records: admin_id, action, timestamp, IP address
- System settings changes logged
- SMTP settings changes logged (without password)

## Database Schema

### system_settings Table
```sql
CREATE TABLE `system_settings` (
  `id` int NOT NULL,
  `brand_name` varchar(100) DEFAULT NULL,
  `site_url` varchar(255) DEFAULT NULL,
  `contact_email` varchar(255) DEFAULT NULL,
  `contact_phone` varchar(50) DEFAULT NULL,
  `company_address` text,
  `fca_reference_number` varchar(50) DEFAULT NULL,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
);
```

### smtp_settings Table
```sql
CREATE TABLE `smtp_settings` (
  `id` int NOT NULL,
  `host` varchar(255) NOT NULL,
  `port` int NOT NULL DEFAULT '587',
  `encryption` enum('tls','ssl','none') NOT NULL DEFAULT 'tls',
  `username` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `from_email` varchar(255) NOT NULL,
  `from_name` varchar(255) NOT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
);
```

## API Endpoint

### save_settings.php

**URL:** `/admin/admin_ajax/save_settings.php`

**Method:** POST

**Parameters:**

For System Settings:
```
csrf_token: (required) CSRF token
type: "system"
brand_name: (required) Company name
site_url: (required) Website URL
contact_email: (required) Contact email
contact_phone: (optional) Phone number
company_address: (optional) Address
fca_reference_number: (optional) FCA reference
```

For SMTP Settings:
```
csrf_token: (required) CSRF token
type: "smtp"
host: (required) SMTP host
port: (required) SMTP port
encryption: (required) tls/ssl/none
username: (required) SMTP username
password: (required) SMTP password
from_email: (required) From email
from_name: (required) From name
```

**Response:**
```json
{
  "success": true,
  "message": "Settings saved successfully!"
}
```

Or on error:
```json
{
  "success": false,
  "message": "Error description"
}
```

## Common SMTP Configurations

### Gmail
```
Host: smtp.gmail.com
Port: 587
Encryption: TLS
Username: your-email@gmail.com
Password: Your App Password (not regular password)
```

### Outlook/Office 365
```
Host: smtp.office365.com
Port: 587
Encryption: TLS
Username: your-email@outlook.com
Password: Your account password
```

### Hostinger
```
Host: smtp.hostinger.com
Port: 587
Encryption: TLS
Username: your-email@domain.com
Password: Your email password
```

### SendGrid
```
Host: smtp.sendgrid.net
Port: 587
Encryption: TLS
Username: apikey
Password: Your SendGrid API key
```

## Troubleshooting

### Settings Not Saving
1. Check browser console for JavaScript errors
2. Verify admin is logged in
3. Check database connection
4. Review server error logs
5. Verify CSRF token is present

### SMTP Connection Failed
1. Verify host and port are correct
2. Check username and password
3. Ensure firewall allows outbound SMTP
4. Try different encryption (TLS vs SSL)
5. Contact email provider for settings

### Validation Errors
1. Ensure all required fields are filled
2. Check email format is valid
3. Verify URL includes https://
4. Confirm port is between 1-65535
5. Review error message for details

## Best Practices

### System Settings
- Use full HTTPS URL for site_url
- Use professional email for contact_email
- Include country code in contact_phone
- Keep company_address up to date
- Update FCA reference if it changes

### SMTP Settings
- Use TLS encryption when possible
- Keep SMTP password secure
- Use app-specific passwords for Gmail
- Test email sending after changes
- Use noreply@ or no-reply@ for from_email
- Use company name for from_name

### Security
- Change SMTP password regularly
- Limit admin access to settings
- Review audit logs periodically
- Use strong passwords
- Keep session timeout reasonable

## Future Enhancements

Potential improvements:
- Test SMTP connection functionality
- Backup/restore settings
- Settings history/versioning
- Multiple SMTP profiles
- Email template preview
- Settings export/import
- Role-based access control
- Settings validation rules
- Help tooltips
- Settings templates

## Support

For issues or questions:
- Check server error logs
- Review database for data
- Verify admin permissions
- Test with different browsers
- Contact system administrator

## Changelog

### Version 1.0 (Current)
- Initial release
- System settings management
- SMTP settings management
- Two-tab interface
- Form validation
- CSRF protection
- Audit logging
- Professional UI

---

**Admin Settings Page - Making system configuration easy and secure!** ✅⚙️
