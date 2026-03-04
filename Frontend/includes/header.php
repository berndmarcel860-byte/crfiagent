<?php
/**
 * Frontend Header Component with SEO Meta Tags
 * Includes comprehensive SEO optimization for Novalnet AI
 */

// Default values (can be overridden by setting variables before including this file)
$page_title = $page_title ?? 'Novalnet AI – KI-gestützte Blockchain Analyse bei Krypto-Betrug';
$page_description = $page_description ?? 'Professionelle Krypto-Wiederherstellung durch KI-gestützte Blockchain-Analyse. BaFin-lizenziert, transparent und rechtskonform. 87% Erfolgsquote bei der Identifizierung betrügerischer Transaktionen.';
$page_keywords = $page_keywords ?? 'Krypto Betrug, Blockchain Analyse, KI Krypto-Wiederherstellung, BaFin lizenziert, Scam Recovery, Kryptowährung zurückholen, Betrugsbekämpfung, Blockchain Forensik';
$page_url = $page_url ?? 'https://novalnet-ai.de/';
$page_image = $page_image ?? 'https://novalnet-ai.de/assets/img/og-image.jpg';
$page_type = $page_type ?? 'website';
?>
<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    
    <!-- Primary Meta Tags -->
    <title><?php echo htmlspecialchars($page_title); ?></title>
    <meta name="title" content="<?php echo htmlspecialchars($page_title); ?>">
    <meta name="description" content="<?php echo htmlspecialchars($page_description); ?>">
    <meta name="keywords" content="<?php echo htmlspecialchars($page_keywords); ?>">
    <meta name="author" content="Novalnet AI">
    <meta name="robots" content="index, follow">
    <meta name="language" content="German">
    <meta name="revisit-after" content="7 days">
    
    <!-- Canonical URL -->
    <link rel="canonical" href="<?php echo htmlspecialchars($page_url); ?>">
    
    <!-- Open Graph / Facebook Meta Tags -->
    <meta property="og:type" content="<?php echo htmlspecialchars($page_type); ?>">
    <meta property="og:url" content="<?php echo htmlspecialchars($page_url); ?>">
    <meta property="og:title" content="<?php echo htmlspecialchars($page_title); ?>">
    <meta property="og:description" content="<?php echo htmlspecialchars($page_description); ?>">
    <meta property="og:image" content="<?php echo htmlspecialchars($page_image); ?>">
    <meta property="og:locale" content="de_DE">
    <meta property="og:site_name" content="Novalnet AI">
    
    <!-- Twitter Card Meta Tags -->
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:url" content="<?php echo htmlspecialchars($page_url); ?>">
    <meta name="twitter:title" content="<?php echo htmlspecialchars($page_title); ?>">
    <meta name="twitter:description" content="<?php echo htmlspecialchars($page_description); ?>">
    <meta name="twitter:image" content="<?php echo htmlspecialchars($page_image); ?>">
    
    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="/assets/img/favicon.ico">
    <link rel="apple-touch-icon" sizes="180x180" href="/assets/img/apple-touch-icon.png">
    
    <!-- Bootstrap & Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Structured Data (JSON-LD) for SEO -->
    <script type="application/ld+json">
    {
        "@context": "https://schema.org",
        "@type": "Organization",
        "name": "Novalnet AI",
        "url": "https://novalnet-ai.de",
        "logo": "https://novalnet-ai.de/assets/img/logo.png",
        "description": "KI-gestützte Blockchain-Analyse und Krypto-Wiederherstellung für Betrugsopfer",
        "address": {
            "@type": "PostalAddress",
            "streetAddress": "Gutenbergstraße 7",
            "addressLocality": "Garching b.München",
            "postalCode": "85748",
            "addressCountry": "DE"
        },
        "contactPoint": {
            "@type": "ContactPoint",
            "telephone": "+49-xxx-xxxxxxx",
            "contactType": "customer service",
            "areaServed": "DE",
            "availableLanguage": ["German", "English"]
        },
        "sameAs": []
    }
    </script>
    
    <!-- Custom Styles -->
    <style>
        :root {
            --primary: #0d6efd;
            --primary-dark: #0b5ed7;
            --dark: #1a1a2e;
            --light: #f8f9fa;
            --gray: #6c757d;
            --success: #28a745;
        }

        html { scroll-behavior: smooth; }
        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background:#fff; color:#212529;
            padding-top:72px; /* fixed nav offset */
        }

        /* === Navbar === */
        .navbar {
            background: #fff;
            box-shadow: 0 2px 8px rgba(0,0,0,0.05);
            transition: all 0.3s ease;
        }
        .navbar.scrolled {
            box-shadow: 0 6px 16px rgba(0,0,0,0.12);
        }
        .navbar-brand img {
            height: 40px;
            width: auto;
            margin-right: 8px;
        }
        .navbar-brand span {
            font-weight: 700;
        }
        .navbar-nav .nav-link {
            font-weight: 600;
            color: var(--dark);
            padding: .6rem .9rem;
            border-radius: 8px;
            transition: all .2s ease;
        }
        .navbar-nav .nav-link:hover {
            color: var(--primary);
        }
        .navbar-nav .nav-link.active {
            background: linear-gradient(135deg, var(--primary), var(--primary-dark));
            color: #fff !important;
            box-shadow: 0 4px 12px rgba(13,110,253,0.25);
        }

        .hero-section {
            padding: 120px 0 80px;
            background: linear-gradient(135deg, #f8f9ff 0%, #eef4ff 100%);
        }
        .section { padding: 80px 0; }

        .feature-card {
            background:#fff; border-radius:12px; padding:30px;
            box-shadow:0 5px 15px rgba(0,0,0,.05);
            transition: transform .3s ease; height:100%;
        }
        .feature-card:hover { transform: translateY(-5px); }

        .logo-grid {
            display:flex; flex-wrap:wrap; justify-content:center; gap:30px; margin:40px 0;
        }
        .logo-item { height:50px; display:flex; align-items:center; opacity:.85; transition:opacity .3s; }
        .logo-item:hover { opacity:1; }

        .step-icon {
            width:60px; height:60px; border-radius:50%;
            background: linear-gradient(135deg, var(--primary), #0b5ed7);
            display:flex; align-items:center; justify-content:center; color:#fff; font-size:24px; margin-bottom:20px;
        }

        .btn-primary {
            background: linear-gradient(135deg, var(--primary), #0b5ed7);
            border:none; padding:12px 30px; font-weight:600; border-radius:8px;
        }
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow:0 5px 15px rgba(13,110,253,.4);
        }
        .btn-outline-primary { padding:12px 30px; border-width:2px; }

        .section-title { font-weight:700; margin-bottom:20px; font-size:2rem; }
        .section-subtitle { color:var(--gray); margin-bottom:40px; font-size:1.05rem; }

        .trusted-by { background:#f8f9fa; padding:60px 0; text-align:center; }

        .feature-list li { margin-bottom:10px; display:flex; align-items:flex-start; }
        .feature-list li i { color:var(--primary); margin-right:10px; margin-top:4px; }

        /* AI section */
        .ai-section img {
            border-radius:12px;
            box-shadow:0 10px 24px rgba(0,0,0,.08);
        }
        .kpi-badge {
            display:inline-flex; align-items:center; gap:10px;
            background:#fff; border:1px solid #e9ecef; border-radius:999px;
            padding:8px 14px; font-weight:600;
        }

        /* Animated progress (87%) */
        .progress-wrap { margin-top:10px; }
        .progress {
            height:12px; border-radius:999px; background:#e9ecef;
        }
        .progress-bar {
            background: linear-gradient(90deg, var(--primary), #37a0ff);
            width:0%; transition: width 1.6s ease-out;
        }
        .progress-label { font-size:.9rem; color:#6c757d; margin-top:8px; }

        /* Smooth anchor offset for fixed navbar */
        .anchor-offset { scroll-margin-top: 90px; }
        
        .team-card {
            background: #fff;
            border-radius: 12px;
            padding: 30px 20px;
            box-shadow: 0 3px 10px rgba(0,0,0,0.05);
            transition: all 0.3s ease;
        }
        .team-card:hover {
            box-shadow: 0 6px 20px rgba(0,0,0,0.12);
            transform: translateY(-4px);
        }
        .team-card img {
            width: 150px;
            height: 150px;
            object-fit: cover;
            border-radius: 50%;
            border: 4px solid #00b5d6;
            margin-bottom: 15px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
            transition: transform 0.3s ease;
        }
        .team-card img:hover {
            transform: scale(1.05);
        }
        .team-card h5 {
            font-weight: 600;
            margin-bottom: 4px;
            color: #1a1a2e;
        }
        .team-card p {
            color: #00b5d6;
            font-size: 0.95rem;
            margin-bottom: 0;
        }

        /* Footer */
        footer {
            background: #f8f9fa;
            color: #6c757d;
            padding: 40px 0;
            font-size: 0.9rem;
        }
        footer a {
            color: #6c757d;
            transition: color 0.2s;
        }
        footer a:hover {
            color: var(--primary);
        }
        
        /* SEO H1 - visually hidden for design but present for SEO */
        .visually-hidden {
            position: absolute;
            width: 1px;
            height: 1px;
            padding: 0;
            margin: -1px;
            overflow: hidden;
            clip: rect(0, 0, 0, 0);
            white-space: nowrap;
            border-width: 0;
        }
    </style>
</head>
<body>
