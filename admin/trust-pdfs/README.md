# Trust PDF Generators

Professional trust-building PDF documents for welcome email attachments.

## Purpose

Generate professional German PDF documents to build client trust and distinguish from scam companies. All PDFs use data from `system_settings` table.

## Files

1. **generate_bafin_verification_pdf.php** - BaFin license verification document
2. **generate_service_agreement_pdf.php** - Service agreement with fee structure
3. **generate_company_info_pdf.php** - Company credentials and statistics
4. **generate_onboarding_guide_pdf.php** - Step-by-step onboarding instructions
5. **generate_all_trust_pdfs.php** - Master script to generate all PDFs

## Usage

### Generate All PDFs (Recommended)
```bash
cd admin/trust-pdfs
php generate_all_trust_pdfs.php
```

### Generate Individual PDFs
```bash
php generate_bafin_verification_pdf.php
php generate_service_agreement_pdf.php
php generate_company_info_pdf.php
php generate_onboarding_guide_pdf.php
```

## Output

PDFs are saved to: `documents/trust/`

Filename format:
- `BaFin_Verification_YYYYMMDD_HHMMSS.pdf`
- `Service_Agreement_YYYYMMDD_HHMMSS.pdf`
- `Company_Information_YYYYMMDD_HHMMSS.pdf`
- `Onboarding_Guide_YYYYMMDD_HHMMSS.pdf`

## Data Source

All PDFs pull data from `system_settings` table:
- `brand_name` - Company name
- `company_address` - Physical address
- `contact_email` - Support email
- `contact_phone` - Phone number
- `fca_reference_number` - BaFin/FCA license reference
- `site_url` - Website URL

**Important:** BaFin reference = FCA reference number (same field)

## Requirements

- Composer packages installed (`vendor/autoload.php`)
- FPDF library (setasign/fpdf)
- Database connection (`config.php`)
- Write permissions on `documents/trust/` directory

## Features

### BaFin Verification PDF
- Prominent license number display
- Verification instructions (how to check on BaFin.de)
- Anti-scam comparison table
- Warning about fraudulent companies
- Contact information

### Service Agreement PDF
- Legal binding contract
- Transparent 3% fee structure
- NO upfront payment emphasized
- GDPR compliance
- Terms and conditions
- Signature section

### Company Information PDF
- Company credentials
- BaFin license details
- Success statistics (from database)
- Services overview
- Why choose us section
- Contact information

### Onboarding Guide PDF
- 5-step process explained
- Step-by-step instructions
- FAQ section
- Timeline expectations
- Support information

## Integration with Welcome Email

To attach these PDFs to welcome emails, update `admin/admin_ajax/add_user.php`:

```php
// After user creation, generate PDFs
exec('php ' . __DIR__ . '/../trust-pdfs/generate_all_trust_pdfs.php');

// Get latest PDFs (sorted by timestamp)
$trustDir = __DIR__ . '/../../documents/trust/';
$pdfs = [
    glob($trustDir . 'BaFin_Verification_*.pdf')[0] ?? null,
    glob($trustDir . 'Service_Agreement_*.pdf')[0] ?? null,
    glob($trustDir . 'Company_Information_*.pdf')[0] ?? null,
    glob($trustDir . 'Onboarding_Guide_*.pdf')[0] ?? null
];

// Send email with attachments (requires AdminEmailHelper extension)
$emailHelper->sendWelcomeEmailWithAttachments($userId, array_filter($pdfs));
```

## Customization

### Updating Content
Edit the respective PHP files to change PDF content. All text is in German.

### Updating Styling
Color scheme defined in each file:
- Primary Blue: RGB(28, 41, 69)
- Success Green: RGB(76, 175, 80)
- Warning Yellow: RGB(255, 193, 7)
- Danger Red: RGB(244, 67, 54)

### Adding New PDFs
1. Create `generate_[name]_pdf.php`
2. Extend FPDF class
3. Follow existing structure
4. Add to `generate_all_trust_pdfs.php`

## Maintenance

### Regular Updates
- Update statistics monthly (pulled automatically from database)
- Update FCA reference if renewed
- Update company address if changed
- Review legal terms annually

### Testing
Test after system_settings changes:
```bash
php generate_all_trust_pdfs.php
```

Check output files in `documents/trust/` directory.

## Security

- PDFs stored in web-accessible directory
- Consider adding access control if needed
- Do not include sensitive internal information
- BaFin license is public information (safe to share)

## Support

For questions or issues with PDF generation, contact development team.

**Created:** March 3, 2026
**Version:** 1.0
