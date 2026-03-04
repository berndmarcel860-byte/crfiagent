<?php
/**
 * Frontend Homepage - Example using modular components
 * This demonstrates how to use the new header, navbar, and footer components
 */

// Set page-specific SEO variables
$page_title = 'Novalnet AI – KI-gestützte Blockchain Analyse bei Krypto-Betrug';
$page_description = 'Professionelle Krypto-Wiederherstellung durch KI-gestützte Blockchain-Analyse. BaFin-lizenziert, transparent und rechtskonform. 87% Erfolgsquote bei der Identifizierung betrügerischer Transaktionen.';
$page_keywords = 'Krypto Betrug, Blockchain Analyse, KI Krypto-Wiederherstellung, BaFin lizenziert, Scam Recovery, Kryptowährung zurückholen, Betrugsbekämpfung, Blockchain Forensik, NovalNet AI';
$page_url = 'https://novalnet-ai.de/Frontend/index.php';
$page_image = 'https://novalnet-ai.de/assets/img/og-novalnet-ai.jpg';

// Include header with SEO meta tags
include 'includes/header.php';

// Include navbar
include 'includes/navbar.php';
?>

<!-- Hero Section with H1 for SEO -->
<header class="hero-section text-center">
    <div class="container">
        <h1 class="display-4 fw-bold mb-4">
            <span class="d-block">KI-gestützte Blockchain Analyse bei Krypto-Betrug</span>
            <span class="text-primary">für rechtmäßige Eigentümer</span>
        </h1>
        <p class="lead mb-5" style="max-width:700px;margin:0 auto;">
            Novalnet AI identifiziert betrügerisch entstandene Depots und führt Krypto-Guthaben sicher an die rechtmäßigen Eigentümer zurück – transparent, nachvollziehbar und rechtskonform.
        </p>
        <div class="d-flex justify-content-center gap-3 flex-wrap">
            <a href="/app" class="btn btn-primary btn-lg px-5">Zum Kundenportal</a>
            <a href="#refund-ai" class="btn btn-outline-primary btn-lg px-5">Mehr erfahren</a>
        </div>
    </div>
</header>

<!-- Trusted Partners -->
<section class="trusted-by">
    <div class="container">
        <h3 class="mb-4">Vertrauensvolle Partner & Datenquellen</h3>
        <div class="logo-grid">
            <div class="logo-item">
                <img src="https://tradiascrypto.de/assets/binance-logo-120x40-DwqZd-HJ.png" alt="Binance" style="max-height:100%;width:auto;">
            </div>
            <div class="logo-item">
                <img src="https://tradiascrypto.de/assets/coingecko-logo-120x40-D2Np8XW0.png" alt="CoinGecko" style="max-height:100%;width:auto;">
            </div>
        </div>
    </div>
</section>

<!-- Services Section -->
<section id="services" class="section anchor-offset">
    <div class="container">
        <div class="text-center mb-5">
            <h2 class="section-title">Unsere Services</h2>
            <p class="section-subtitle">Professionelle Krypto-Wiederherstellung mit modernster KI-Technologie</p>
        </div>
        <div class="row g-4">
            <div class="col-md-4">
                <div class="feature-card text-center">
                    <div class="step-icon mx-auto">
                        <i class="fas fa-brain"></i>
                    </div>
                    <h3 class="h5 fw-bold mb-3">KI-Analyse</h3>
                    <p class="text-muted">Blockchain-Transaktionen mit 87% Erfolgsquote identifizieren</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="feature-card text-center">
                    <div class="step-icon mx-auto">
                        <i class="fas fa-shield-alt"></i>
                    </div>
                    <h3 class="h5 fw-bold mb-3">Sichere Rückführung</h3>
                    <p class="text-muted">Rechtskonform und transparent nach europäischen Standards</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="feature-card text-center">
                    <div class="step-icon mx-auto">
                        <i class="fas fa-check-circle"></i>
                    </div>
                    <h3 class="h5 fw-bold mb-3">Erfolgsbasiert</h3>
                    <p class="text-muted">Nur 3% Gebühr bei erfolgreicher Wiederherstellung</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Process Section -->
<section id="process" class="section bg-light anchor-offset">
    <div class="container">
        <div class="text-center mb-5">
            <h2 class="section-title">Unser Prozess</h2>
            <p class="section-subtitle">In 4 einfachen Schritten zur Wiederherstellung</p>
        </div>
        <div class="row g-4">
            <div class="col-md-3">
                <div class="text-center">
                    <div class="step-icon mx-auto">1</div>
                    <h4 class="h5 fw-bold mt-3">Fallaufnahme</h4>
                    <p class="text-muted small">Beschreibung Ihres Falls</p>
                </div>
            </div>
            <div class="col-md-3">
                <div class="text-center">
                    <div class="step-icon mx-auto">2</div>
                    <h4 class="h5 fw-bold mt-3">KI-Analyse</h4>
                    <p class="text-muted small">Blockchain-Analyse</p>
                </div>
            </div>
            <div class="col-md-3">
                <div class="text-center">
                    <div class="step-icon mx-auto">3</div>
                    <h4 class="h5 fw-bold mt-3">Verifizierung</h4>
                    <p class="text-muted small">Rechtliche Prüfung</p>
                </div>
            </div>
            <div class="col-md-3">
                <div class="text-center">
                    <div class="step-icon mx-auto">4</div>
                    <h4 class="h5 fw-bold mt-3">Rückführung</h4>
                    <p class="text-muted small">Sichere Auszahlung</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- KI-Rückerstattung Section -->
<section id="refund-ai" class="section anchor-offset">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-6">
                <h2 class="section-title">KI-gestützte Wiederherstellung</h2>
                <p class="mb-4">
                    Unsere proprietäre KI-Technologie analysiert Blockchain-Transaktionen und identifiziert 
                    betrügerische Muster mit einer Erfolgsquote von 87%. Transparent, nachvollziehbar und rechtskonform.
                </p>
                <ul class="feature-list mb-4">
                    <li><i class="fas fa-check"></i> Automatische Blockchain-Analyse</li>
                    <li><i class="fas fa-check"></i> Betrugsmuster-Erkennung</li>
                    <li><i class="fas fa-check"></i> Rechtskonforme Dokumentation</li>
                    <li><i class="fas fa-check"></i> 24/7 Überwachung</li>
                </ul>
                <a href="/app" class="btn btn-primary">Jetzt starten</a>
            </div>
            <div class="col-lg-6">
                <div class="kpi-badge">
                    <i class="fas fa-chart-line text-primary"></i>
                    <span>87% Erfolgsquote</span>
                </div>
                <div class="progress-wrap">
                    <div class="progress">
                        <div class="progress-bar" role="progressbar" id="successProgress"></div>
                    </div>
                    <div class="progress-label text-center">Identifizierungsrate bei betrügerischen Transaktionen</div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- FAQ -->
<section id="faq" class="section bg-light anchor-offset">
    <div class="container">
        <div class="text-center mb-5">
            <h2 class="section-title">Häufige Fragen</h2>
            <p class="section-subtitle">Antworten auf die wichtigsten Fragen zu Novalnet AI</p>
        </div>
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="accordion" id="faqAccordion">
                    <div class="accordion-item mb-3">
                        <h2 class="accordion-header">
                            <button class="accordion-button fw-bold" type="button" data-bs-toggle="collapse" data-bs-target="#faq1">
                                Wie funktioniert der Wiederherstellungsservice?
                            </button>
                        </h2>
                        <div id="faq1" class="accordion-collapse collapse show" data-bs-parent="#faqAccordion">
                            <div class="accordion-body">
                                Unser spezialisierter Service unterstützt Sie bei der Wiederherstellung verlorener Zugänge. 
                                KI-gestützte Analyse, manuelle Verifikation und transparente Dokumentation sorgen für eine rechtssichere Rückführung.
                            </div>
                        </div>
                    </div>
                    <div class="accordion-item mb-3">
                        <h2 class="accordion-header">
                            <button class="accordion-button fw-bold collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq2">
                                Ist die Plattform reguliert?
                            </button>
                        </h2>
                        <div id="faq2" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                            <div class="accordion-body">
                                Wir arbeiten nach europäischen Compliance-Standards und sind BaFin-lizenziert (FCA Ref: 122702).
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- CTA -->
<section class="section bg-primary text-white text-center">
    <div class="container">
        <h2 class="display-5 fw-bold mb-4">Jetzt starten</h2>
        <p class="lead mb-5" style="max-width:700px;margin:0 auto;">
            Erstellen Sie Ihr Konto und verwalten Sie Krypto mit Vertrauen. Deutschlands sichere Plattform für Krypto-Wiederherstellung.
        </p>
        <a href="/app" class="btn btn-light btn-lg fw-bold px-5 py-3">Konto erstellen</a>
    </div>
</section>

<script>
// Animate progress bar on load
window.addEventListener('load', () => {
    setTimeout(() => {
        const bar = document.getElementById('successProgress');
        if(bar) bar.style.width = '87%';
    }, 300);
});
</script>

<?php include 'footer.php'; ?>
