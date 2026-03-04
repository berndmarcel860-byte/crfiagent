# Modular Components Guide for Novalnet AI Frontend

This guide explains how to use the new modular header, navbar, and footer components for the Novalnet AI frontend pages.

## Overview

The frontend has been refactored to use reusable components:
- **header.php** - HTML head with comprehensive SEO meta tags
- **navbar.php** - Responsive navigation bar
- **footer.php** - Professional footer with links

## Benefits

✅ **No code duplication** - Write once, use everywhere
✅ **Consistent SEO** - All pages use same meta tag structure
✅ **Easy maintenance** - Update header/footer in one place
✅ **Better SEO** - Comprehensive meta tags, Open Graph, structured data
✅ **Social sharing** - Optimized for Facebook, Twitter, LinkedIn
✅ **Mobile responsive** - Works on all devices

## Components

### 1. Header Component (`includes/header.php`)

**Purpose:** HTML `<head>` section with SEO optimization

**Includes:**
- Primary meta tags (title, description, keywords)
- Open Graph tags (Facebook/LinkedIn sharing)
- Twitter Card tags
- Canonical URL
- Structured data (JSON-LD) for Google
- Favicon links
- Bootstrap and Font Awesome
- Inline CSS styles

**Customizable Variables:**
```php
$page_title       = 'Your page title';
$page_description = 'Page description for SEO';
$page_keywords    = 'keyword1, keyword2, keyword3';
$page_url         = 'https://novalnet-ai.de/page.php';
$page_image       = 'https://novalnet-ai.de/assets/img/og-image.jpg';
$page_type        = 'website'; // or 'article'
```

### 2. Navbar Component (`includes/navbar.php`)

**Purpose:** Responsive navigation bar

**Features:**
- Fixed-top positioning
- German menu items
- Scroll shadow effect
- Mobile hamburger menu
- "Konto erstellen" CTA button

**Menu Items:**
- Services
- Prozess
- Features
- KI-Rückerstattung
- Über uns
- FAQ
- Kontakt

### 3. Footer Component (`footer.php`)

**Purpose:** Professional footer with company info and links

**Sections:**
- Company info (address, email, FCA reference)
- Quick links (Unternehmen)
- Service links
- Legal links
- Social media
- Copyright and BaFin badge

## Usage

### Basic Usage

```php
<?php
// Set SEO variables
$page_title = 'Your Page Title';
$page_description = 'Your page description';
$page_url = 'https://novalnet-ai.de/your-page.php';

// Include header
include 'includes/header.php';

// Include navbar
include 'includes/navbar.php';
?>

<!-- Your page content -->
<div class="container">
    <h1>KI-gestützte Blockchain Analyse bei Krypto-Betrug</h1>
    <p>Your content here...</p>
</div>

<?php include 'footer.php'; ?>
```

### With Custom SEO

```php
<?php
$page_title = 'Kontakt – Novalnet AI';
$page_description = 'Kontaktieren Sie Novalnet AI für KI-gestützte Krypto-Wiederherstellung. BaFin-lizenziert und transparent.';
$page_keywords = 'Novalnet AI Kontakt, Krypto Support, Blockchain Analyse Kontakt';
$page_url = 'https://novalnet-ai.de/Frontend/kontakt.php';
$page_image = 'https://novalnet-ai.de/assets/img/og-kontakt.jpg';

include 'includes/header.php';
include 'includes/navbar.php';
?>

<section class="section">
    <div class="container">
        <h1>Kontaktieren Sie uns</h1>
        <!-- Contact form here -->
    </div>
</section>

<?php include 'footer.php'; ?>
```

## Migration Guide

### Step 1: Backup Original File
```bash
cp mission.php mission.php.backup
```

### Step 2: Identify Sections

**Current structure:**
```php
<!DOCTYPE html>
<html lang="de">
<head>
    <!-- meta tags -->
    <!-- Bootstrap -->
    <!-- styles -->
</head>
<body>
    <!-- Navbar -->
    
    <!-- Main Content -->
    
    <?php include 'footer.php'; ?>
</body>
</html>
```

### Step 3: Replace with Modular Components

**New structure:**
```php
<?php
$page_title = 'Mission – Novalnet AI';
$page_description = 'Unsere Mission: Sichere Krypto-Wiederherstellung...';
$page_url = 'https://novalnet-ai.de/Frontend/mission.php';

include 'includes/header.php';
include 'includes/navbar.php';
?>

<!-- Keep your main content exactly as is -->
<section class="section">
    <div class="container">
        <h1>Unsere Mission</h1>
        <!-- existing content -->
    </div>
</section>

<?php include 'footer.php'; ?>
```

### Step 4: Test

1. Load page in browser
2. Check that:
   - Page renders correctly
   - Navbar works
   - Footer appears
   - No console errors
   - SEO meta tags present (View Page Source)

## SEO Best Practices

### Page Titles
- Keep under 60 characters
- Include main keyword
- Brand name at end
- Format: "Page Title – Novalnet AI"

**Examples:**
- "Kontakt – Novalnet AI"
- "Über uns – Novalnet AI"
- "KI-Blockchain-Analyse – Novalnet AI"

### Meta Descriptions
- Keep 150-160 characters
- Include main keywords naturally
- Include call-to-action
- Make compelling for clicks

**Examples:**
- "Professionelle Krypto-Wiederherstellung durch KI. BaFin-lizenziert, 87% Erfolgsquote. Jetzt starten!"
- "Kontaktieren Sie Novalnet AI für sichere Blockchain-Analyse. Transparent und rechtskonform."

### Keywords
- 5-10 relevant keywords
- Include variations
- Separate with commas
- Focus on intent

**Examples:**
- "Krypto Betrug, Blockchain Analyse, Scam Recovery, Kryptowährung zurückholen"

### H1 Tags
- One H1 per page
- Include main keyword
- Make descriptive
- Keep under 70 characters

**Main H1:**
```html
<h1>KI-gestützte Blockchain Analyse bei Krypto-Betrug</h1>
```

**Page-specific H1s:**
- `<h1>Kontaktieren Sie Novalnet AI</h1>`
- `<h1>Unsere Mission: Sichere Krypto-Wiederherstellung</h1>`
- `<h1>Über Novalnet AI</h1>`

### Canonical URLs
- Always set $page_url
- Use absolute URLs
- Match actual page URL
- Include https://

## Structured Data

The header includes Organization schema with:
- Company name: Novalnet AI
- Logo URL
- Description
- Physical address (Gutenbergstraße 7, Garching)
- Contact information
- Service area: Germany

This helps Google display rich snippets in search results.

## Open Graph Tags

For social media sharing (Facebook, LinkedIn):
- og:title - Appears as title in share preview
- og:description - Appears as description
- og:image - Preview image (1200x630px recommended)
- og:url - Canonical URL
- og:locale - de_DE for German

## Twitter Cards

For Twitter sharing:
- twitter:card - summary_large_image
- twitter:title - Share title
- twitter:description - Share description
- twitter:image - Preview image

## Customization

### Adding Page-Specific Styles

If a page needs unique styles, add after includes:

```php
<?php
include 'includes/header.php';
include 'includes/navbar.php';
?>

<style>
.custom-section {
    background: #f5f5f5;
    padding: 60px 0;
}
</style>

<!-- Page content -->
```

### Override Default Variables

Set variables BEFORE including header:

```php
<?php
$page_title = 'Custom Title';
$page_description = 'Custom description';
// ... set all variables

include 'includes/header.php';
?>
```

### Add Page-Specific JSON-LD

After header, before navbar:

```php
<?php include 'includes/header.php'; ?>

<script type="application/ld+json">
{
    "@context": "https://schema.org",
    "@type": "FAQPage",
    "mainEntity": [...]
}
</script>

<?php include 'includes/navbar.php'; ?>
```

## Testing

### Visual Testing
1. Load page in browser
2. Check navbar appears correctly
3. Check footer appears correctly
4. Test mobile responsiveness
5. Test all navigation links

### SEO Testing
1. View page source (right-click → View Source)
2. Verify meta tags present
3. Check Open Graph tags
4. Verify structured data
5. Use Google Rich Results Test: https://search.google.com/test/rich-results

### Social Sharing Testing
1. Facebook Sharing Debugger: https://developers.facebook.com/tools/debug/
2. Twitter Card Validator: https://cards-dev.twitter.com/validator
3. LinkedIn Post Inspector: https://www.linkedin.com/post-inspector/

## File Structure

```
Frontend/
├── includes/
│   ├── header.php      (HTML head with SEO)
│   └── navbar.php      (Navigation bar)
├── footer.php          (Footer component)
├── index.php           (Homepage)
├── mission.php         (Mission page)
├── kontakt.php         (Contact page)
├── ueber-uns.php       (About page)
├── ... (other pages)
└── MODULAR_COMPONENTS_GUIDE.md (this file)
```

## Troubleshooting

### Issue: Styles not loading
**Solution:** Check that header.php includes inline styles. External style.css is optional.

### Issue: Navbar not appearing
**Solution:** Ensure `include 'includes/navbar.php';` is after header include.

### Issue: Footer at wrong position
**Solution:** Ensure main content has proper container/sections.

### Issue: SEO variables not working
**Solution:** Set variables BEFORE including header.php.

### Issue: Duplicate navbars
**Solution:** Make sure old navbar HTML is removed from page.

## Next Steps

1. ✅ Components created
2. ✅ Example page created (index_example_modular.php)
3. ⏳ Migrate existing pages
4. ⏳ Test all pages
5. ⏳ Verify SEO tags
6. ⏳ Test social sharing

## Support

For questions or issues, refer to:
- index_example_modular.php (working example)
- This guide
- Original files (backed up as .backup files)
