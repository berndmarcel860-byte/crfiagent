# Email Template Management System

## Overview
This system provides centralized email template management for FundTracer AI, allowing administrators to create, manage, and send professional HTML emails using database-stored templates.

## Architecture

### Database Structure
The system uses the existing `email_templates` table:
```sql
CREATE TABLE `email_templates` (
  `id` int NOT NULL,
  `template_key` varchar(100) NOT NULL,
  `subject` varchar(255) NOT NULL,
  `content` text NOT NULL,
  `variables` text COMMENT 'JSON array of available variables',
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
)
```

### Files Structure
```
admin/
├── email_templates_default.sql       # Default templates SQL insert
├── email_template_helper.php         # Template helper class
├── admin_email_templates.php         # Template management UI
└── admin_ajax/
    ├── notify_inactive_users.php     # Uses templates
    ├── send_kyc_reminders.php        # Uses templates
    └── add_platform.php              # Can use templates
```

## Installation

### 1. Import Default Templates
Run the SQL file to create default templates:
```bash
mysql -u username -p database_name < admin/email_templates_default.sql
```

Or through phpMyAdmin:
- Open phpMyAdmin
- Select your database
- Go to "Import" tab
- Choose `email_templates_default.sql`
- Click "Go"

### 2. Verify Installation
Check if templates were created:
```sql
SELECT template_key, subject FROM email_templates;
```

## Default Templates

### 1. Inactive User Templates
- `inactive_user_reminder` - General reminder (any timeframe)
- `inactive_user_7_days` - 7-day inactivity reminder
- `inactive_user_30_days` - 30-day critical reminder
- `inactive_user_60_days` - 60-day urgent notice

### 2. System Notifications
- `kyc_reminder` - KYC verification reminder
- `scam_platform_alert` - New scam platform alert
- `case_update_notification` - Case status updates
- `ai_recovery_update` - AI analysis updates

## Usage

### Using the Helper Class

```php
<?php
require_once 'email_template_helper.php';

// Initialize helper
$emailHelper = new EmailTemplateHelper($pdo);

// Send a single email
$variables = [
    'first_name' => 'John',
    'last_name' => 'Doe',
    'days_inactive' => 30,
    'login_url' => 'https://example.com/login'
];

$success = $emailHelper->sendTemplateEmail(
    'user@example.com',
    'inactive_user_reminder',
    $variables
);

// Send bulk emails
$recipients = [
    [
        'email' => 'user1@example.com',
        'variables' => ['first_name' => 'John', ...]
    ],
    [
        'email' => 'user2@example.com',
        'variables' => ['first_name' => 'Jane', ...]
    ]
];

$result = $emailHelper->sendBulkTemplateEmail(
    $recipients,
    'inactive_user_reminder',
    50 // batch size
);

echo "Sent: {$result['sent']}, Failed: {$result['failed']}";
```

### Quick Function

```php
<?php
require_once 'email_template_helper.php';

// Quick send (shorter syntax)
sendTemplateEmail($pdo, 'user@example.com', 'kyc_reminder', [
    'first_name' => 'John',
    'kyc_url' => 'https://example.com/kyc'
]);
```

### Available Helper Methods

| Method | Description |
|--------|-------------|
| `getTemplate($templateKey)` | Retrieve template from database |
| `renderTemplate($templateKey, $variables)` | Render template with variables |
| `sendTemplateEmail($to, $templateKey, $variables)` | Send single email |
| `sendBulkTemplateEmail($recipients, $templateKey, $batchSize)` | Send bulk emails |
| `getAllTemplates()` | Get list of all templates |
| `validateTemplateVariables($templateKey, $variables)` | Validate variables |

## Template Variables

### Variable Syntax
Templates support both syntaxes:
- `{{variable}}` - Preferred
- `{variable}` - Also supported

### Common Variables

#### User Variables
- `{{first_name}}` - User's first name
- `{{last_name}}` - User's last name
- `{{email}}` - User's email address
- `{{user_id}}` - User ID

#### Activity Variables
- `{{days_inactive}}` - Number of days inactive
- `{{last_login}}` - Last login date
- `{{login_url}}` - Login page URL

#### Case Variables
- `{{case_number}}` - Case reference number
- `{{case_status}}` - Current case status
- `{{case_url}}` - Case details URL
- `{{reported_amount}}` - Amount reported
- `{{recovered_amount}}` - Amount recovered

#### Platform Variables
- `{{platform_name}}` - Scam platform name
- `{{platform_url}}` - Platform URL
- `{{platform_type}}` - Platform type
- `{{platform_description}}` - Platform description

#### System Variables
- `{{support_email}}` - Support email address
- `{{kyc_url}}` - KYC verification URL
- `{{dashboard_url}}` - User dashboard URL
- `{{report_url}}` - Report case URL

## Creating Custom Templates

### Via Admin UI
1. Go to **Admin Panel** → **Email Templates**
2. Click **"Add Template"**
3. Fill in:
   - **Template Key**: Unique identifier (e.g., `welcome_email`)
   - **Subject**: Email subject line (can include variables)
   - **Content**: HTML email content
   - **Variables**: JSON array of variables, e.g., `["first_name", "email"]`
4. Click **"Save Template"**

### Via SQL
```sql
INSERT INTO email_templates (template_key, subject, content, variables) 
VALUES (
    'custom_template',
    'Welcome {{first_name}}!',
    '<h2>Hello {{first_name}},</h2><p>Welcome to our platform!</p>',
    '["first_name", "last_name", "email"]'
);
```

### HTML Email Best Practices

1. **Use Inline CSS**: Email clients don't support external stylesheets
   ```html
   <p style="color: #333; font-size: 14px;">Text</p>
   ```

2. **Use Tables for Layout**: Most reliable for email clients
   ```html
   <table width="100%">
       <tr><td>Content</td></tr>
   </table>
   ```

3. **Test Across Clients**: Gmail, Outlook, Apple Mail all render differently

4. **Keep it Simple**: Avoid JavaScript, complex CSS, and external images

5. **Use Alt Text**: For images
   ```html
   <img src="..." alt="Description">
   ```

## Automatic Template Wrapping

All template content is automatically wrapped in a professional HTML email template with:
- Responsive design
- FundTracer AI branding
- Header with gradient background
- Footer with copyright and unsubscribe links
- Mobile-friendly styling

You only need to provide the email body content in your template.

## Email Logging

All sent emails are automatically logged to the `email_logs` table:
```sql
CREATE TABLE email_logs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    recipient VARCHAR(255),
    subject VARCHAR(255),
    template_key VARCHAR(100),
    status ENUM('sent', 'failed', 'delivered', 'opened'),
    sent_at DATETIME,
    user_id INT
);
```

## Monitoring & Analytics

### Check Email Statistics
```sql
-- Emails sent today
SELECT COUNT(*) FROM email_logs 
WHERE DATE(sent_at) = CURDATE();

-- Most used templates
SELECT template_key, COUNT(*) as sent_count 
FROM email_logs 
GROUP BY template_key 
ORDER BY sent_count DESC;

-- Failed emails
SELECT * FROM email_logs 
WHERE status = 'failed' 
ORDER BY sent_at DESC;
```

### Admin Dashboard
- View email logs in **Admin Panel** → **Email Logs**
- Track delivery rates
- Monitor failed emails
- Review sent history

## Troubleshooting

### Template Not Found
```php
// Check if template exists
$template = $emailHelper->getTemplate('template_key');
if (!$template) {
    echo "Template not found!";
}
```

### Missing Variables
```php
// Validate variables before sending
$validation = $emailHelper->validateTemplateVariables('template_key', $variables);
if (!$validation['valid']) {
    echo "Missing variables: " . implode(', ', $validation['missing']);
}
```

### Email Not Sending
1. Check PHP mail configuration
2. Verify SMTP settings
3. Check email logs for errors
4. Test with simple mail() function first

### HTML Rendering Issues
1. Use inline styles only
2. Test in multiple email clients
3. Validate HTML syntax
4. Check for unsupported CSS properties

## Security Considerations

1. **Input Validation**: All variables are escaped before rendering
2. **SQL Injection**: Uses prepared statements
3. **XSS Protection**: HTML is sanitized
4. **Rate Limiting**: Bulk emails have batch delays
5. **Authentication**: Admin-only template management

## Performance

- **Batch Processing**: Bulk emails sent in configurable batches
- **Rate Limiting**: 0.5s delay between batches
- **Database Indexing**: `template_key` indexed for fast lookups
- **Caching**: Consider implementing template caching for high volume

## Future Enhancements

- [ ] Template versioning
- [ ] A/B testing support
- [ ] Email scheduling
- [ ] Attachment support
- [ ] Template preview in browser
- [ ] Variable autocomplete in UI
- [ ] Template cloning
- [ ] Rich text editor integration
- [ ] Email delivery tracking (opens, clicks)
- [ ] Template categories/tags

## Support

For issues or questions:
- Check logs: `/admin/admin_ajax/error.log`
- Review email logs: Admin Panel → Email Logs
- Contact: support@fundtracerai.com

## License

Proprietary - FundTracer AI Platform
