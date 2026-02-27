# Withdrawal Rejected Email Template - Summary

## Overview
Professional German email template for withdrawal rejection notifications.

## File Details
- **Filename:** `email_template_withdrawal_rejected.sql`
- **Size:** 11.3 KB
- **Language:** German (Deutsch)
- **Template Key:** `withdrawal_rejected`

## Installation

```bash
mysql -u username -p database_name < email_template_withdrawal_rejected.sql
```

## Template Features

### Design
- **Color Scheme:** Red gradient (#dc3545 to #c82333) - rejection theme
- **Header:** ❌ Auszahlungsantrag Abgelehnt
- **Responsive:** Mobile-friendly design
- **Professional:** Matches existing template styling

### Content Sections

1. **Personalized Greeting**
   - "Sehr geehrte/r {first_name} {last_name},"

2. **Rejection Notification**
   - Clear explanation that the withdrawal was rejected
   - Empathetic tone

3. **Rejection Reason Box**
   - Yellow warning box with 📝 icon
   - Displays: {rejection_reason}

4. **Transaction Details**
   - Referenznummer (Reference)
   - Betrag (Amount)
   - Zahlungsmethode (Payment Method)
   - Zahlungsdetails (Payment Details)
   - Transaktions-ID (Transaction ID)
   - Antragsdatum (Request Date)
   - Ablehnungsdatum (Rejection Date)
   - Status Badge: "Abgelehnt"

5. **Amount Refunded Notice**
   - Red alert box
   - Confirms amount returned to account

6. **Support Contact Section**
   - Blue info box with 💬 icon
   - Support email
   - Contact email

7. **Call to Action**
   - Button: "Zu Transaktionen" (To Transactions)
   - Links to transactions.php

8. **Next Steps Guidance**
   - Bullet list with actionable steps
   - Encourages contacting support if unclear

9. **Footer**
   - Company branding
   - Address
   - Links
   - Copyright

## Variables (16 Total)

### User Data (3)
- `first_name` - User's first name
- `last_name` - User's last name
- `user_email` - User's email address

### Transaction Data (6)
- `amount` - Withdrawal amount
- `reference` - Unique reference number
- `payment_method` - Payment method name
- `payment_details` - Payment account details
- `transaction_id` - Transaction identifier
- `transaction_date` - Date of withdrawal request

### Rejection Data (3)
- `rejection_reason` - Reason for rejection (main variable)
- `rejected_by` - Admin who rejected (optional display)
- `rejected_at` - Date/time of rejection

### System Data (4)
- `brand_name` - Company/platform name
- `site_url` - Website URL
- `support_email` - Support contact email
- `contact_email` - Alternative contact email
- `company_address` - Company address
- `current_year` - Current year for copyright

## Usage in Code

### Example: Send Rejection Email

```php
<?php
require_once 'EmailHelper.php';

$emailHelper = new EmailHelper($pdo);

// Custom variables for rejection
$customVars = [
    'amount' => '€500.00',
    'reference' => 'WD-2026-12345',
    'payment_method' => 'Bank Transfer',
    'payment_details' => 'DE89 3704 0044 0532 0130 00',
    'transaction_id' => 'TXN-987654321',
    'transaction_date' => '2026-02-27 10:30:00',
    'rejection_reason' => 'Die angegebenen Zahlungsdetails stimmen nicht mit Ihrem verifizierten Konto überein. Bitte aktualisieren Sie Ihre Zahlungsinformationen und stellen Sie einen neuen Antrag.',
    'rejected_by' => 'Admin',
    'rejected_at' => '2026-02-27 14:45:00'
];

// Send email
$result = $emailHelper->sendEmail('withdrawal_rejected', $userId, $customVars);

if ($result) {
    echo "Rejection email sent successfully";
} else {
    echo "Failed to send rejection email";
}
?>
```

## Email Subject
**German:** "Auszahlungsantrag Abgelehnt - {reference}"
**English Translation:** "Withdrawal Request Rejected - {reference}"

## Design Highlights

### Color Palette
- **Primary (Rejection):** #dc3545 (Red)
- **Secondary:** #c82333 (Dark Red)
- **Warning Box:** #fff3cd (Yellow background)
- **Info Box:** #d1ecf1 (Blue background)
- **Text:** #333333 (Dark Gray)

### Icons Used
- ❌ Header icon (rejection)
- 📝 Rejection reason icon
- 📋 Transaction details icon
- ⚠️ Warning icon (refund notice)
- 💬 Support icon

## Professional Features

1. **Empathetic Tone:** Acknowledges disappointment while being professional
2. **Clear Communication:** Explains rejection clearly
3. **Actionable Guidance:** Provides next steps
4. **Support Access:** Multiple contact methods
5. **Reassurance:** Confirms amount refunded
6. **Responsive Design:** Works on all devices
7. **Brand Consistent:** Matches other templates

## Testing Checklist

- [ ] Template installs without SQL errors
- [ ] All variables render correctly
- [ ] Rejection reason displays properly
- [ ] Support email links work
- [ ] Transaction button links correctly
- [ ] Mobile display is responsive
- [ ] German text is grammatically correct
- [ ] Colors match rejection theme
- [ ] Footer information is complete

## Maintenance Notes

### To Update Template:
1. Edit the SQL file
2. Re-run the SQL script
3. ON DUPLICATE KEY UPDATE ensures safe updates

### Common Customizations:
- Adjust color scheme in CSS
- Modify rejection reason box styling
- Add additional transaction fields
- Update support contact information
- Change button text or links

## Related Templates
- `withdrawal_pending` - For pending withdrawals
- `withdrawal_approved` - For approved withdrawals (to be created)
- `withdrawal_completed` - For completed withdrawals (to be created)

## Support
For questions or issues with this template:
- Contact: Development Team
- Documentation: This file
- Template Location: `/email_template_withdrawal_rejected.sql`

---

**Created:** 2026-02-27
**Version:** 1.0
**Status:** Production Ready ✅
