# Email Verification System Documentation

## Overview

This is a complete, production-ready email verification system for the user dashboard. It allows users to verify their email addresses through a secure token-based system with professional HTML emails.

## Files Created

### 1. ajax/send_verification_email.php
**Purpose:** Ajax endpoint for sending verification emails

**Features:**
- Secure token generation (64-character random hex)
- Rate limiting (1 request per 60 seconds)
- Token expiration (1 hour)
- Session tracking
- Full error handling
- Uses EmailHelper with database templates

**Security:**
- User authentication check
- Rate limiting to prevent spam
- Secure random token generation
- SQL injection protection (prepared statements)
- XSS protection (JSON responses)

**API Response:**
```json
{
  "success": true,
  "message": "Verification email sent successfully! Please check your inbox."
}
```

### 2. verify_email.php
**Purpose:** Standalone page that verifies email addresses via token

**Features:**
- Token validation
- Expiration checking
- Database update on success
- Beautiful responsive UI
- User-friendly error messages
- Automatic redirection based on auth status

**Design:**
- Gradient background
- Responsive layout
- Bootstrap 5 styling
- Font Awesome icons
- Professional appearance

**Token Validation:**
- Checks if token exists
- Verifies token hasn't expired
- Prevents double-verification
- Updates database atomically

### 3. email_template_email_verification.sql
**Purpose:** Database template for verification emails

**Template Variables:**
- `{{verification_url}}` - Complete verification link
- `{{user_first_name}}` - User's first name
- `{{brand_name}}` - Company brand name
- `{{contact_email}}` - Support email
- `{{company_address}}` - Full company address
- `{{fca_reference_number}}` - FCA reference
- `{{expires_in}}` - Token expiration time
- `{{current_year}}` - Current year
- `{{tracking_token}}` - Email tracking token
- `{{site_url}}` - Website base URL

**Design Features:**
- Responsive HTML email
- Gradient header matching brand colors
- Clear CTA button
- Alternative text link
- Benefits section
- Professional footer
- Email tracking pixel

### 4. profile.php (Updated)
**Purpose:** User profile page with verification button

**Changes:**
- Removed old POST-based verification
- Added Ajax button with loading states
- Real-time feedback messages
- 60-second cooldown after sending
- Error handling with user feedback

**JavaScript Features:**
- Fetch API for Ajax requests
- Button state management
- Loading spinner
- Success/error messages
- Automatic re-enable after cooldown

## Database Requirements

### Required Columns in `users` Table:

```sql
ALTER TABLE users ADD COLUMN IF NOT EXISTS verification_token VARCHAR(64) DEFAULT NULL;
ALTER TABLE users ADD COLUMN IF NOT EXISTS verification_token_expires DATETIME DEFAULT NULL;
ALTER TABLE users ADD COLUMN IF NOT EXISTS email_verified_at DATETIME DEFAULT NULL;
ALTER TABLE users ADD COLUMN IF NOT EXISTS is_verified TINYINT(1) DEFAULT 0;
```

### Email Template Table:
The template is inserted into the `email_templates` table with:
- `template_key`: email_verification
- `category`: account
- `is_active`: 1

## Installation Steps

### 1. Database Setup

```bash
# Run the email template SQL
mysql -u username -p database_name < email_template_email_verification.sql

# Add database columns if not present
mysql -u username -p database_name << EOF
ALTER TABLE users ADD COLUMN IF NOT EXISTS verification_token VARCHAR(64) DEFAULT NULL;
ALTER TABLE users ADD COLUMN IF NOT EXISTS verification_token_expires DATETIME DEFAULT NULL;
ALTER TABLE users ADD COLUMN IF NOT EXISTS email_verified_at DATETIME DEFAULT NULL;
ALTER TABLE users ADD COLUMN IF NOT EXISTS is_verified TINYINT(1) DEFAULT 0;
EOF
```

### 2. File Verification

Ensure all files are in place:
- `/ajax/send_verification_email.php`
- `/verify_email.php`
- `/profile.php` (updated)
- `/email_template_email_verification.sql`

### 3. EmailHelper Configuration

Ensure EmailHelper.php is properly configured:
- PHPMailer loaded
- SMTP settings configured
- Database connection working

### 4. Test the System

1. Login to user dashboard
2. Navigate to profile page
3. If email is unverified, you'll see "Resend Verification Email" button
4. Click the button
5. Check email inbox
6. Click verification link
7. Verify success message

## User Flow

### Step 1: User Requests Verification
```
User Profile → "Resend Verification Email" button → Ajax request
```

### Step 2: System Generates Token
```
Generate 64-char token → Store in database → Set 1-hour expiration
```

### Step 3: Email Sent
```
Load template → Replace variables → Send via EmailHelper → SMTP delivery
```

### Step 4: User Receives Email
```
Professional HTML email → Clear CTA button → Verification link
```

### Step 5: User Clicks Link
```
verify_email.php → Token validation → Database update → Success page
```

### Step 6: Verification Complete
```
Email verified → Badge updated in profile → Full access granted
```

## Security Features

### Token Security
- 64-character random hex tokens
- Cryptographically secure generation (`random_bytes()`)
- 1-hour expiration window
- Single-use tokens (cleared after verification)

### Rate Limiting
- Maximum 1 email per 60 seconds per user
- Session-based tracking
- Prevents email spam
- User-friendly error messages

### SQL Injection Protection
- All queries use prepared statements
- Parameter binding
- No direct SQL concatenation

### XSS Protection
- JSON responses only
- HTML escaping in templates
- Input sanitization

### CSRF Protection
- Session-based authentication
- Origin validation
- Secure cookie settings

## Email Template Customization

### Modifying the Template

Edit the template in the database:
```sql
UPDATE email_templates 
SET content = '<!-- Your custom HTML -->'
WHERE template_key = 'email_verification';
```

### Available Variables

**User Information:**
- `{{user_first_name}}`
- `{{user_last_name}}`
- `{{user_email}}`

**Company Information:**
- `{{brand_name}}`
- `{{company_address}}`
- `{{contact_email}}`
- `{{contact_phone}}`
- `{{fca_reference_number}}`

**System Information:**
- `{{site_url}}`
- `{{dashboard_url}}`
- `{{current_year}}`

**Verification Specific:**
- `{{verification_url}}` - The complete verification link
- `{{verification_token}}` - The token (for manual entry if needed)
- `{{expires_in}}` - Human-readable expiration time

**Tracking:**
- `{{tracking_token}}` - For email open tracking

### Email Design Guidelines

The template uses:
- Responsive table-based layout
- Inline CSS for email client compatibility
- Web-safe fonts (Arial)
- High-contrast colors for accessibility
- Mobile-optimized buttons
- Alternative text links for button failures

## Troubleshooting

### Email Not Received

**Check:**
1. SMTP configuration in EmailHelper
2. Email template exists in database
3. User email address is correct
4. Check spam/junk folder
5. Verify mail server logs

**Debug:**
```php
// Add to send_verification_email.php
error_log("Email sent result: " . ($emailSent ? 'true' : 'false'));
error_log("User email: " . $user['email']);
```

### Token Expired

**Solution:**
- User can request a new token
- Each request generates a fresh token
- Old tokens are automatically invalidated

**Prevention:**
- Increase expiration time if needed
- Edit line 56 in send_verification_email.php:
```php
$expires = date('Y-m-d H:i:s', strtotime('+2 hours')); // Changed to 2 hours
```

### Button Not Working

**Check:**
1. JavaScript console for errors
2. Network tab for Ajax request
3. Server logs for PHP errors
4. User is logged in
5. Session is valid

**Debug:**
```javascript
// Add to profile.php JavaScript
console.log('Button clicked');
console.log('Response:', data);
```

### Database Errors

**Common Issues:**
1. Missing columns - Run ALTER TABLE statements
2. Wrong column types - Check schema
3. Connection issues - Verify config.php
4. Permission issues - Grant proper privileges

## Performance Considerations

### Caching
- Email templates are cached by EmailHelper
- Tokens stored in database (indexed)
- Session data cached in memory

### Database Optimization
- Add index on verification_token:
```sql
ALTER TABLE users ADD INDEX idx_verification_token (verification_token);
```

### Email Delivery
- Uses PHPMailer for reliable delivery
- SMTP connection reuse
- Async sending recommended for production
- Consider queue system for high volume

## Customization Options

### Change Token Expiration

Edit `send_verification_email.php` line 56:
```php
$expires = date('Y-m-d H:i:s', strtotime('+2 hours')); // 2 hours instead of 1
```

### Change Rate Limiting

Edit `send_verification_email.php` line 44:
```php
if ($timeSinceLastSend < 120) { // 2 minutes instead of 1
```

### Customize Email Subject

Edit the template in database:
```sql
UPDATE email_templates 
SET subject = 'Your Custom Subject - {{brand_name}}'
WHERE template_key = 'email_verification';
```

### Add Additional Verification Steps

After line 59 in verify_email.php, add:
```php
// Send welcome email
$emailHelper = new EmailHelper($pdo);
$emailHelper->sendEmail('welcome_email', $user['id'], []);

// Update user status
$stmt = $pdo->prepare("UPDATE users SET status = 'active' WHERE id = ?");
$stmt->execute([$user['id']]);
```

## Integration with Existing Systems

### With Registration
Add to registration success:
```php
// After user creation
require_once 'ajax/send_verification_email.php';
// Email will be sent automatically
```

### With Login
Check verification status:
```php
if (!$user['is_verified']) {
    $_SESSION['warning'] = 'Please verify your email address';
    header('Location: profile.php');
}
```

### With Feature Access
Restrict features:
```php
if (!$user['is_verified']) {
    echo "Please verify your email to access this feature";
    exit();
}
```

## API Documentation

### POST /ajax/send_verification_email.php

**Authentication:** Required (session-based)

**Request:**
```
POST /ajax/send_verification_email.php
Content-Type: application/json
```

**Success Response:**
```json
{
  "success": true,
  "message": "Verification email sent successfully! Please check your inbox."
}
```

**Error Responses:**
```json
{
  "success": false,
  "message": "Not authenticated"
}
```
```json
{
  "success": false,
  "message": "Email already verified"
}
```
```json
{
  "success": false,
  "message": "Please wait 45 seconds before requesting another email"
}
```

### GET /verify_email.php?token={token}

**Authentication:** Optional

**Parameters:**
- `token` (required): 64-character verification token

**Success:**
- Displays success page
- Updates database
- Redirects to dashboard if logged in

**Error:**
- Displays error message
- Option to request new token
- Redirects to login if not authenticated

## Testing

### Manual Testing

1. **Test Token Generation:**
   - Request verification email
   - Check database for token and expiration
   - Verify token is 64 characters

2. **Test Email Delivery:**
   - Check inbox for email
   - Verify HTML renders correctly
   - Test verification button link
   - Test alternative text link

3. **Test Verification:**
   - Click verification link
   - Verify success message
   - Check database for is_verified = 1
   - Verify token is cleared

4. **Test Expiration:**
   - Wait 1 hour
   - Try old verification link
   - Verify expiration message

5. **Test Rate Limiting:**
   - Request email twice quickly
   - Verify second request is blocked
   - Wait 60 seconds
   - Verify can request again

### Automated Testing

```php
// PHPUnit test example
public function testVerificationEmailSent() {
    $_SESSION['user_id'] = 1;
    
    $response = $this->post('/ajax/send_verification_email.php');
    
    $this->assertEquals(200, $response->status);
    $this->assertTrue($response->json()['success']);
    
    // Check database
    $user = $this->getUser(1);
    $this->assertNotNull($user['verification_token']);
}
```

## Maintenance

### Regular Tasks

1. **Clean Expired Tokens:**
```sql
-- Run daily
UPDATE users 
SET verification_token = NULL, verification_token_expires = NULL
WHERE verification_token_expires < NOW();
```

2. **Monitor Email Delivery:**
   - Check SMTP logs
   - Monitor bounce rates
   - Track verification rates

3. **Update Template:**
   - Keep design fresh
   - Update company information
   - Test email client compatibility

### Monitoring Metrics

Track these metrics:
- Emails sent per day
- Verification rate (%)
- Average time to verify
- Failed deliveries
- Expired tokens
- Rate limit hits

## Support

For issues or questions:
1. Check server logs
2. Verify database schema
3. Test email configuration
4. Review this documentation
5. Contact system administrator

## Version History

### Version 1.0.0 (Initial Release)
- Complete email verification system
- Ajax-based interface
- Professional HTML email template
- Secure token generation
- Rate limiting
- Full documentation

## License

This system is part of the crfiagent project.

## Credits

Created as part of the user dashboard email verification feature.
