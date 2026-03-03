# Integration Guide: Trust PDFs with Welcome Email

## Quick Start Guide

### Step 1: Generate Trust PDFs

On your production server with database access:

```bash
cd /var/www/your-site/app/admin/trust-pdfs
php generate_all_trust_pdfs.php
```

This creates 4 PDF files in `documents/trust/`:
- `BaFin_Verification_[timestamp].pdf`
- `Service_Agreement_[timestamp].pdf`
- `Company_Information_[timestamp].pdf`
- `Onboarding_Guide_[timestamp].pdf`

### Step 2: Verify PDFs Generated Correctly

Check the output:
```bash
ls -lh ../../documents/trust/*.pdf
```

Open and review each PDF to ensure:
- BaFin/FCA reference number is correct
- Company name and address are correct
- Contact information is accurate
- All German text displays properly

### Step 3: Update AdminEmailHelper for Attachments

Add attachment support to `admin/AdminEmailHelper.php`:

```php
/**
 * Send template email with file attachments
 * 
 * @param string $templateKey Template identifier
 * @param int $userId User ID
 * @param array $customVars Custom variables
 * @param array $attachments Array of file paths to attach
 * @return bool Success status
 */
public function sendTemplateEmailWithAttachments($templateKey, $userId, $customVars = [], $attachments = []) {
    try {
        // Get template
        $stmt = $this->pdo->prepare("SELECT * FROM email_templates WHERE template_key = ?");
        $stmt->execute([$templateKey]);
        $template = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$template) {
            throw new Exception("Template not found: $templateKey");
        }
        
        // Get all variables
        $variables = $this->getAllVariables($userId, $customVars);
        
        // Replace variables in template
        $subject = $this->replaceVariables($template['subject'], $variables);
        $body = $this->replaceVariables($template['content'], $variables);
        
        // Send email with attachments
        return $this->sendEmail($variables['email'], $subject, $body, $attachments);
        
    } catch (Exception $e) {
        error_log("AdminEmailHelper - Send template with attachments error: " . $e->getMessage());
        return false;
    }
}

/**
 * Enhanced sendEmail method with attachment support
 */
private function sendEmail($to, $subject, $body, $attachments = []) {
    try {
        $mail = new PHPMailer(true);
        
        // SMTP configuration (existing code)
        // ... your existing SMTP setup ...
        
        // Add attachments
        if (!empty($attachments)) {
            foreach ($attachments as $filePath) {
                if (file_exists($filePath)) {
                    $mail->addAttachment($filePath);
                }
            }
        }
        
        // Send (existing code)
        // ... your existing send logic ...
        
        return $mail->send();
        
    } catch (Exception $e) {
        error_log("AdminEmailHelper - Send error: " . $e->getMessage());
        return false;
    }
}
```

### Step 4: Update add_user.php to Include PDFs

In `admin/admin_ajax/add_user.php`, after user creation:

```php
// After successful user creation...

// Generate fresh trust PDFs
$pdfGenScript = __DIR__ . '/../trust-pdfs/generate_all_trust_pdfs.php';
if (file_exists($pdfGenScript)) {
    exec("php " . escapeshellarg($pdfGenScript) . " 2>&1", $output, $returnCode);
    if ($returnCode !== 0) {
        error_log("Failed to generate trust PDFs: " . implode("\n", $output));
    }
}

// Get latest generated PDFs
$trustDir = __DIR__ . '/../../documents/trust/';
$pdfs = [];

// Find most recent PDF of each type
$pdfTypes = ['BaFin_Verification', 'Service_Agreement', 'Company_Information', 'Onboarding_Guide'];
foreach ($pdfTypes as $type) {
    $files = glob($trustDir . $type . '_*.pdf');
    if (!empty($files)) {
        // Sort by timestamp (newest first)
        rsort($files);
        $pdfs[] = $files[0];
    }
}

// Send welcome email with attachments
require_once __DIR__ . '/../AdminEmailHelper.php';
$emailHelper = new AdminEmailHelper($pdo);

$customVars = [
    'temp_password' => $plain_password,
    'login_url' => $site_url . '/login.php'
];

$emailSent = $emailHelper->sendTemplateEmailWithAttachments(
    'user_registration',
    $userId,
    $customVars,
    $pdfs  // Attach all trust PDFs
);

if ($emailSent) {
    error_log("Welcome email with " . count($pdfs) . " trust PDFs sent to user $userId");
} else {
    error_log("Failed to send welcome email with attachments to user $userId");
}
```

### Step 5: Enhance Welcome Email Template

Update the 'user_registration' email template to mention the attachments:

```sql
UPDATE email_templates 
SET content = CONCAT(content, '
<div style="background: #eff6ff; border: 2px solid #0066cc; border-radius: 8px; padding: 20px; margin: 25px 0;">
    <h3 style="color: #003366; margin-top: 0;">📄 Wichtige Dokumente beigefügt</h3>
    <p>Wir haben folgende Dokumente für Sie beigefügt:</p>
    <ul style="line-height: 1.8;">
        <li><strong>BaFin-Lizenz Verifizierung</strong> - Prüfen Sie unsere Lizenz auf www.bafin.de</li>
        <li><strong>Dienstleistungsvertrag</strong> - Ihre Rechte und unsere Verpflichtungen</li>
        <li><strong>Unternehmensinformationen</strong> - Über uns und unsere Technologie</li>
        <li><strong>Onboarding-Leitfaden</strong> - Schritt-für-Schritt Anleitung</li>
    </ul>
    <p style="margin-bottom: 0;">
        <strong>⚠ WICHTIG:</strong> Verifizieren Sie unsere BaFin-Lizenz (Referenz: {fca_reference_number}) 
        direkt auf der offiziellen BaFin-Website, um sicherzustellen, dass Sie mit einem 
        legitimen Unternehmen arbeiten.
    </p>
</div>
')
WHERE template_key = 'user_registration';
```

## Testing

### Test PDF Generation

```bash
cd admin/trust-pdfs
php generate_all_trust_pdfs.php
```

Expected output:
```
=====================================
Trust PDF Generator
=====================================

[1/4] Generating BaFin Verification PDF...
✓ BaFin Verification PDF created successfully!
Location: /path/to/documents/trust/BaFin_Verification_20260303_120000.pdf
Size: 45,123 bytes

[2/4] Generating Service Agreement PDF...
✓ Service Agreement PDF created successfully!
...

=====================================
Summary
=====================================
PDFs Generated: 4 / 4
Time Taken: 1.23s

✓ All trust PDFs generated successfully!
```

### Test Email with Attachments

Create test script `admin/test_welcome_email.php`:

```php
<?php
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/AdminEmailHelper.php';

// Test user ID (use existing user)
$testUserId = 1;

// Generate PDFs
exec("php " . __DIR__ . "/trust-pdfs/generate_all_trust_pdfs.php 2>&1", $output);
echo "PDF Generation:\n" . implode("\n", $output) . "\n\n";

// Get PDFs
$trustDir = __DIR__ . '/../documents/trust/';
$pdfs = [
    glob($trustDir . 'BaFin_Verification_*.pdf')[0] ?? null,
    glob($trustDir . 'Service_Agreement_*.pdf')[0] ?? null,
    glob($trustDir . 'Company_Information_*.pdf')[0] ?? null,
    glob($trustDir . 'Onboarding_Guide_*.pdf')[0] ?? null
];
$pdfs = array_filter($pdfs);

echo "Found " . count($pdfs) . " PDFs to attach\n\n";

// Send test email
$emailHelper = new AdminEmailHelper($pdo);
$customVars = [
    'temp_password' => 'TestPassword123',
    'login_url' => 'https://your-site.com/app/login.php'
];

$result = $emailHelper->sendTemplateEmailWithAttachments(
    'user_registration',
    $testUserId,
    $customVars,
    $pdfs
);

echo $result ? "✓ Email sent successfully!\n" : "✗ Email send failed\n";
```

Run test:
```bash
php admin/test_welcome_email.php
```

## Production Checklist

Before deploying to production:

- [ ] Update system_settings with correct company information
- [ ] Verify fca_reference_number is set correctly
- [ ] Generate test PDFs and review content
- [ ] Test email sending with attachments
- [ ] Verify PDFs open correctly
- [ ] Check German text for accuracy
- [ ] Ensure BaFin reference is verifiable on BaFin.de
- [ ] Test on different email clients (Gmail, Outlook, etc.)
- [ ] Verify mobile display
- [ ] Set up cron job for periodic PDF regeneration (optional)

## Maintenance

### When to Regenerate PDFs

Regenerate PDFs when:
- Company information changes
- BaFin/FCA reference renewed
- Success statistics need updating
- Legal terms modified
- Contact information changes

### Automated Regeneration (Optional)

Set up daily cron job to regenerate PDFs with latest statistics:

```bash
# Add to crontab
0 2 * * * cd /var/www/your-site/app/admin/trust-pdfs && php generate_all_trust_pdfs.php >> /var/log/pdf-gen.log 2>&1
```

This ensures:
- Statistics always current
- Latest company information
- Fresh timestamps on documents

### Manual Regeneration

Regenerate anytime:
```bash
cd admin/trust-pdfs
php generate_all_trust_pdfs.php
```

## Troubleshooting

### "Composer vendor/autoload.php not found"

Install dependencies:
```bash
cd /var/www/your-site/app
composer require setasign/fpdf
```

### "Permission denied" errors

Fix permissions:
```bash
chmod 755 admin/trust-pdfs
chmod 755 documents/trust
```

### PDFs not attaching to email

Check:
1. PDF files exist in documents/trust/
2. AdminEmailHelper has attachment support
3. PHPMailer is loaded
4. File paths are correct
5. Email size limit not exceeded

### German characters not displaying

The PDF generators use proper encoding:
- FPDF: iconv('UTF-8', 'ISO-8859-1//TRANSLIT')
- HTML: UTF-8 meta charset

If issues persist, check server locale settings.

## Security Notes

### PDF Storage

PDFs are stored in `documents/trust/` which is web-accessible. This is intentional as:
- PDFs contain only public information (BaFin license is public)
- No sensitive client data included
- Documents are meant to be shared
- BaFin reference can be verified publicly

### Email Attachments

Attachments are sent only via:
- Secure SMTP connection
- To verified email addresses
- With legitimate user registrations

### Data Privacy

PDFs do NOT contain:
- Client personal information (except in service agreement template)
- Internal business data
- Sensitive credentials
- Database information

## Next Steps

1. **Review** - Check all generated PDFs for accuracy
2. **Test** - Send test welcome email with attachments
3. **Deploy** - Update add_user.php in production
4. **Monitor** - Check email delivery logs
5. **Iterate** - Gather client feedback and improve

## Support

For questions or issues:
- Check README.md in trust-pdfs directory
- Review TRUST_BUILDING_STRATEGY.md
- Check TRUST_DOCUMENTS_IMPLEMENTATION_GUIDE.md
- Contact development team

**Created:** March 3, 2026
**Version:** 1.0
**Status:** Production Ready
