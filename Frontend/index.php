<?php
$page_title = 'Novalnet AI – Sichere Krypto-Rückführung & Wiederherstellung';
$page_description = 'KI-gestützte Blockchain-Analyse zur Identifizierung und Wiederherstellung betrügerisch entwendeter Kryptowährungen. BaFin-lizenziert mit 87% Erfolgsquote.';
$page_keywords = 'Krypto Wiederherstellung, Blockchain Analyse, Betrugsaufklärung, KI Krypto, BaFin lizenziert';
$page_url = 'https://novalnet-ai.de/Frontend/index.php';

include 'includes/header.php';
include 'includes/navbar.php';
?>

<style>
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

    /* ========== ANIMATED VISUAL ENHANCEMENTS ========== */
    
    /* Particle canvas background */
    #particles-canvas {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        z-index: 1;
        pointer-events: none;
    }
    
    /* Floating crypto icons */
    .crypto-float {
        position: absolute;
        font-size: 2.5rem;
        opacity: 0.15;
        animation: float 20s infinite ease-in-out;
        z-index: 2;
        pointer-events: none;
        filter: drop-shadow(0 0 10px currentColor);
    }
    .crypto-float:nth-child(2) { animation: float-slow 25s infinite ease-in-out; animation-delay: -5s; }
    .crypto-float:nth-child(3) { animation: float 18s infinite ease-in-out; animation-delay: -10s; }
    .crypto-float:nth-child(4) { animation: float-slow 22s infinite ease-in-out; animation-delay: -15s; }
    
    /* Floating animations */
    @keyframes float {
        0%, 100% { transform: translate(0, 0) rotate(0deg); }
        25% { transform: translate(20px, -30px) rotate(5deg); }
        50% { transform: translate(-15px, -60px) rotate(-5deg); }
        75% { transform: translate(30px, -40px) rotate(3deg); }
    }
    @keyframes float-slow {
        0%, 100% { transform: translate(0, 0) rotate(0deg); }
        25% { transform: translate(-25px, -35px) rotate(-5deg); }
        50% { transform: translate(20px, -70px) rotate(5deg); }
        75% { transform: translate(-30px, -45px) rotate(-3deg); }
    }
    
    /* Pulse effect for statistics */
    @keyframes pulse-subtle {
        0%, 100% { transform: scale(1); }
        50% { transform: scale(1.02); }
    }
    .stat-pulse {
        animation: pulse-subtle 3s ease-in-out infinite;
    }
    
    /* Shimmer effect */
    @keyframes shimmer {
        0% { transform: translateX(-100%); }
        100% { transform: translateX(100%); }
    }
    .shimmer-wrapper {
        position: relative;
        overflow: hidden;
    }
    .shimmer-wrapper::after {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: linear-gradient(90deg, transparent, rgba(255,255,255,0.3), transparent);
        animation: shimmer 3s infinite;
    }
    
    /* Fade in up animation */
    @keyframes fadeInUp {
        from {
            opacity: 0;
            transform: translateY(30px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }
    .animate-on-scroll {
        opacity: 0;
        animation: fadeInUp 0.8s ease-out forwards;
    }
    
    /* Gradient shift animation */
    @keyframes gradientShift {
        0%, 100% { background-position: 0% 50%; }
        50% { background-position: 100% 50%; }
    }
    .animated-gradient {
        background: linear-gradient(135deg, #0d6efd, #0b5ed7, #37a0ff, #0d6efd);
        background-size: 300% 300%;
        animation: gradientShift 15s ease infinite;
    }
</style>

<!-- Hero Section -->
<header class="hero-section text-center" style="position: relative; overflow: hidden;">
    <!-- Animated Particle Background -->
    <canvas id="particles-canvas"></canvas>
    
    <!-- Floating Crypto Icons -->
    <div class="crypto-float" style="top: 10%; left: 5%; color: #f7931a;">₿</div>
    <div class="crypto-float" style="top: 70%; left: 8%; color: #627eea;">Ξ</div>
    <div class="crypto-float" style="top: 40%; right: 10%; color: #26a17b;">₮</div>
    <div class="crypto-float" style="top: 15%; right: 5%; color: #f3ba2f;">B</div>
    <div class="crypto-float" style="top: 60%; right: 15%; color: #0033ad;">₳</div>
    <div class="crypto-float" style="top: 25%; left: 12%; color: #00ffa3;">◎</div>
    <div class="crypto-float" style="top: 80%; right: 20%; color: #23292f;">✕</div>
    <div class="crypto-float" style="top: 35%; left: 88%; color: #e6007a;">●</div>
    
    <div class="container" style="position: relative; z-index: 10;">
        <h1 class="display-4 fw-bold mb-4">Sichere Krypto-Wiederherstellung<br>
            <span class="text-primary">für rechtmäßige Eigentümer</span>
        </h1>
        <p class="lead mb-5" style="max-width:700px;margin:0 auto;">
            Novalnet AI identifiziert betrügerisch entstandene Depots und führt Krypto-Guthaben sicher an die rechtmäßigen Eigentümer zurück – transparent, nachvollziehbar und rechtskonform.
        </p>
        <div class="d-flex justify-content-center gap-3 flex-wrap">
            <a href="https://novalnet-ai.de/app" class="btn btn-primary btn-lg px-5">Zum Kunden portal</a>
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
            <div class="logo-item">
               <!-- <img src="https://tradiascrypto.de/assets/sepa-logo-120x40-DYeyGrJW.png" alt="SEPA" style="max-height:100%;width:auto;"> -->
            </div>
            <div class="logo-item">
                <img src="https://tradiascrypto.de/assets/bafin-logo-120x40-CM2mkusB.png" alt="BaFin" style="max-height:100%;width:auto;">
            </div>
            <div class="logo-item">
                <img src="https://tradiascrypto.de/assets/ssl-logo-120x40-CVsSBkas.png" alt="SSL" style="max-height:100%;width:auto;">
            </div>
        </div>
    </div>
</section>

<!-- Security Section -->
<section id="process" class="section bg-light anchor-offset">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-6 mb-5 mb-lg-0">
                <h2 class="section-title">Sicherheit in der digitalen Welt</h2>
                <p class="section-subtitle">Schutz vor Betrug und sichere Rückführung von Krypto-Guthaben</p>

                <div class="row g-4">
                    <div class="col-md-6">
                        <div class="feature-card">
                            <h5 class="fw-bold mb-3">Digitale Risiken</h5>
                            <p class="text-muted">In einer digitalen Welt entstehen Risiken durch Identitätsdiebstahl oder betrügerische Vermittler bei Krypto-Konten.</p>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="feature-card">
                            <h5 class="fw-bold mb-3">Aufdeckung & Rückführung</h5>
                            <p class="text-muted">Wir decken solche Fälle auf und führen betroffene Guthaben sicher an die rechtmäßigen Eigentümer zurück.</p>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="feature-card">
                            <h5 class="fw-bold mb-3">Sichere Auszahlung</h5>
                            <p class="text-muted">Nach Verifizierung erfolgt die Umwandlung in Euro und Auszahlung direkt auf das Bankkonto des Eigentümers.</p>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="feature-card text-center d-flex flex-column justify-content-center">
                            <p class="mb-0"><strong>Vertrauen Sie auf Klarheit, Fairness und Sicherheit</strong></p>
                            <p class="text-muted mb-0">Eine Plattform, die sich dem Schutz und der Wiederherstellung verschrieben hat.</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right side callout -->
            <div class="col-lg-6">
                <div class="bg-primary text-white p-5 rounded-3">
                    <h3 class="fw-bold mb-4">Gerechtigkeit im Krypto-Raum schaffen</h3>

                    <div class="d-flex mb-4">
                        <div class="step-icon me-3"><i class="fas fa-search"></i></div>
                        <div>
                            <h5 class="fw-bold text-white">Identifikation & Analyse</h5>
                            <p class="text-white-75">Wir identifizieren betrügerisch entstandene Depots und analysieren sie durch einen geprüften Sicherheitsprozess.</p>
                        </div>
                    </div>

                    <div class="d-flex mb-4">
                        <div class="step-icon me-3"><i class="fas fa-exchange-alt"></i></div>
                        <div>
                            <h5 class="fw-bold text-white">Sichere Rückführung</h5>
                            <p class="text-white-75">Guthaben werden sicher an rechtmäßige Eigentümer zurückgeführt – transparent und nachvollziehbar.</p>
                        </div>
                    </div>

                    <div class="d-flex">
                        <div class="step-icon me-3"><i class="fas fa-euro-sign"></i></div>
                        <div>
                            <h5 class="fw-bold text-white">Fiat-Auszahlung</h5>
                            <p class="text-white-75">Nach Verifizierung Umwandlung in Euro und Auszahlung auf das Bankkonto.</p>
                        </div>
                    </div>

                    <div class="mt-4 p-3 bg-white bg-opacity-20 rounded text-center">
                        <strong class="text-white">Gerechtigkeit & Fairness</strong><br/>
                        <small class="text-white-75">Sicher, nachvollziehbar und rechtskonform – für Vertrauen in der digitalen Finanzwelt.</small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Features -->
<section id="features" class="section anchor-offset">
    <div class="container text-center">
        <h2 class="section-title">Plattform-Features</h2>
        <p class="section-subtitle">Fokussiert, verlässlich und konform</p>
    </div>
</section>

<!-- Services -->
<section id="services" class="section bg-light anchor-offset">
    <div class="container">
        <div class="text-center mb-5">
            <h2 class="section-title">Unsere Services im Detail</h2>
            <p class="section-subtitle">Alles, was Sie für professionelle Krypto-Wiederherstellung benötigen</p>
        </div>

        <div class="row g-4">
            <div class="col-lg-6">
                <div class="feature-card">
                    <h4 class="fw-bold mb-4"><i class="fas fa-exchange-alt text-primary me-2"></i> Ein- und Auszahlungen</h4>
                    <p class="text-muted mb-4">Sichere SEPA-Überweisungen mit deutscher IBAN. Alle Transaktionen werden manuell geprüft und innerhalb von 1–2 Werktagen bearbeitet.</p>
                    <ul class="feature-list">
                        <li><i class="fas fa-check-circle"></i> Deutsche Bankverbindung</li>
                        <li><i class="fas fa-check-circle"></i> Manuelle Prüfung aller Transaktionen</li>
                        <li><i class="fas fa-check-circle"></i> Schnelle Bearbeitung (1–2 Werktage)</li>
                        <li><i class="fas fa-check-circle"></i> Persönlicher Verwendungszweck</li>
                    </ul>
                </div>
            </div>

            <div class="col-lg-6">
                <div class="feature-card">
                    <h4 class="fw-bold mb-4"><i class="fas fa-key text-primary me-2"></i> Wiederherstellungsservice</h4>
                    <p class="text-muted mb-4">Spezialisierter Service zur Wiederherstellung vergessener Mnemonic-Phrasen. 369 erfolgreich gelöste Fälle mit dokumentierter Erfolgsquote.</p>
                    <ul class="feature-list">
                        <li><i class="fas fa-check-circle"></i> 12/24-Wörter Wiederherstellung</li>
                        <li><i class="fas fa-check-circle"></i> Reihenfolge-Rekonstruktion</li>
                        <li><i class="fas fa-check-circle"></i> Wallet-Adresse Recovery</li>
                        <li><i class="fas fa-check-circle"></i> DSGVO-konform & sicher</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- KI-Rückerstattungs-Sektion -->
<section id="refund-ai" class="section bg-light ai-section anchor-offset">
  <div class="container">
    <div class="text-center mb-5">
      <h2 class="section-title">Geldrückerstattung durch Künstliche Intelligenz</h2>
      <p class="section-subtitle">
        Wie unsere Technologie hilft, Ihr verlorenes Kapital zurückzuerlangen
      </p>
    </div>

    <div class="row align-items-center">
      <!-- Linke Spalte: AI Icon -->
      <div class="col-lg-5 mb-4 mb-lg-0 text-center">
        <img
          src="/assets/img/ai-icon.png"
          alt="AI Symbol"
          class="img-fluid"
          style="max-width:320px; height:auto;"
        />
      </div>

      <!-- Rechte Spalte: Text -->
      <div class="col-lg-7">
        <h4 class="fw-bold mb-3">Präzise Analyse & Transaktionsrückverfolgung</h4>
        <p class="text-muted mb-4">
          Unsere selbstlernenden Systeme analysieren Blockchain-Transaktionen,
          Wallet-Verknüpfungen und Zahlungsströme über internationale Netzwerke.
          Dabei wird jedes Muster, jede Spur und jeder Transfer durch neuronale
          Modelle überprüft – um verlorene Mittel mit höchster Genauigkeit zu
          identifizieren.
        </p>

        <h4 class="fw-bold mb-3">BaFin-konforme Rückführungsprozesse</h4>
        <p class="text-muted mb-4">
          Nach erfolgreicher Verifizierung initiiert unser System die rechtlich
          abgesicherte Rückführung der betroffenen Vermögenswerte. Alle Prozesse
          folgen europäischen AML- und BaFin-Standards, um maximale Transparenz
          und Nachvollziehbarkeit zu gewährleisten.
        </p>

        <h4 class="fw-bold mb-3">Echte Erfolge für unsere Klienten</h4>
        <p class="text-muted mb-4">
          Mit einer Erfolgsquote von über <strong>87 %</strong> hat unser
          KI-gestützter Algorithmus bereits tausende Transaktionen analysiert
          und verlorene Vermögenswerte erfolgreich wiederhergestellt. Vertrauen
          Sie auf datenbasierte Präzision – und auf echte Ergebnisse.
        </p>

        <!-- Fortschrittsanzeige -->
        <div class="d-flex align-items-center gap-3 mb-2">
          <i class="fa-solid fa-chart-line text-success fs-4"></i>
          <strong>Erfolgsquote: <span id="kpiValue">0%</span></strong>
        </div>
        <div class="progress" style="height:12px; border-radius:999px;">
          <div
            id="kpiBar"
            class="progress-bar"
            style="width:0%; background:linear-gradient(90deg,#0d6efd,#37a0ff);"
          ></div>
        </div>
        <div class="progress-label mt-2 text-muted small">
          KI-gestützte Rückführungen – datengestützt & effizient
        </div>

        <a
          href="https://novalnet-ai.de/app"
          class="btn btn-primary btn-lg px-5 mt-4"
          >Kostenlose Analyse starten</a
        >
      </div>
    </div>
  </div>
</section>



<!-- ========================================================= -->
<!-- 📊 SECTION: ERFOLGE IN ZAHLEN (AI STATISTICS) -->
<!-- ========================================================= -->
<section id="stats" class="section animated-gradient">
  <div class="container text-center text-white">
    <h2 class="section-title text-white mb-4">Unsere Erfolge in Zahlen</h2>
    <p class="section-subtitle text-white opacity-90 mb-5">
      Vertrauen durch nachweisbare Ergebnisse – KI-gestützte Wiederherstellung mit messbarem Erfolg
    </p>

    <div class="row g-4 mb-5">
      <!-- Clients -->
      <div class="col-md-6 col-lg-3">
        <div class="stat-card stat-pulse shimmer-wrapper">
          <div class="stat-icon">
            <i class="fas fa-users"></i>
          </div>
          <h2 class="display-3 fw-bold mb-2" data-count="727">0</h2>
          <p class="h5 mb-0">Zufriedene Klienten</p>
          <p class="small opacity-75 mt-2">Weltweit vertrauen uns</p>
        </div>
      </div>

      <!-- Success Rate -->
      <div class="col-md-6 col-lg-3">
        <div class="stat-card stat-pulse shimmer-wrapper">
          <div class="stat-icon">
            <i class="fas fa-chart-line"></i>
          </div>
          <h2 class="display-3 fw-bold mb-2"><span data-count="87">0</span>%</h2>
          <p class="h5 mb-0">Erfolgsquote</p>
          <p class="small opacity-75 mt-2">Bei Identifizierung</p>
        </div>
      </div>

      <!-- Amount Recovered -->
      <div class="col-md-6 col-lg-3">
        <div class="stat-card shimmer-wrapper">
          <div class="stat-icon">
            <i class="fas fa-euro-sign"></i>
          </div>
          <h2 class="display-3 fw-bold mb-2">€<span data-count="47">0</span>M</h2>
          <p class="h5 mb-0">Wiederhergestellt</p>
          <p class="small opacity-75 mt-2">Gesamtvolumen</p>
        </div>
      </div>

      <!-- Processing Time -->
      <div class="col-md-6 col-lg-3">
        <div class="stat-card shimmer-wrapper">
          <div class="stat-icon">
            <i class="fas fa-clock"></i>
          </div>
          <h2 class="display-3 fw-bold mb-2"><span data-count="14">0</span></h2>
          <p class="h5 mb-0">Tage Durchschnitt</p>
          <p class="small opacity-75 mt-2">Bearbeitungszeit</p>
        </div>
      </div>
    </div>

    <!-- Trust Badges -->
    <div class="row justify-content-center mt-5">
      <div class="col-auto">
        <div class="d-flex align-items-center gap-4 flex-wrap justify-content-center">
          <div class="badge-item">
            <i class="fas fa-shield-alt fa-2x mb-2"></i>
            <p class="small mb-0">BaFin-Lizenziert</p>
            <p class="small mb-0 opacity-75">FCA Ref: 122702</p>
          </div>
          <div class="badge-item">
            <i class="fas fa-lock fa-2x mb-2"></i>
            <p class="small mb-0">256-Bit SSL</p>
            <p class="small mb-0 opacity-75">Verschlüsselt</p>
          </div>
          <div class="badge-item">
            <i class="fas fa-check-circle fa-2x mb-2"></i>
            <p class="small mb-0">GDPR Konform</p>
            <p class="small mb-0 opacity-75">EU-Standard</p>
          </div>
          <div class="badge-item">
            <i class="fas fa-robot fa-2x mb-2"></i>
            <p class="small mb-0">KI-Technologie</p>
            <p class="small mb-0 opacity-75">Advanced ML</p>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>

<style>
.stat-card {
  background: rgba(255,255,255,0.1);
  border-radius: 16px;
  padding: 40px 20px;
  backdrop-filter: blur(10px);
  border: 1px solid rgba(255,255,255,0.2);
  transition: all 0.3s ease;
}
.stat-card:hover {
  background: rgba(255,255,255,0.15);
  transform: translateY(-5px);
}
.stat-icon {
  width: 70px;
  height: 70px;
  border-radius: 50%;
  background: rgba(255,255,255,0.2);
  display: flex;
  align-items: center;
  justify-content: center;
  margin: 0 auto 20px;
  font-size: 28px;
}
.badge-item {
  background: rgba(255,255,255,0.1);
  padding: 20px 30px;
  border-radius: 12px;
  border: 1px solid rgba(255,255,255,0.2);
}
</style>

<script>
// Animated counter effect
document.addEventListener('DOMContentLoaded', function() {
  const observer = new IntersectionObserver((entries) => {
    entries.forEach(entry => {
      if (entry.isIntersecting) {
        const counters = entry.target.querySelectorAll('[data-count]');
        counters.forEach(counter => {
          const target = parseInt(counter.getAttribute('data-count'));
          const duration = 2000;
          const steps = 60;
          const increment = target / steps;
          let current = 0;
          
          const timer = setInterval(() => {
            current += increment;
            if (current >= target) {
              counter.textContent = target;
              clearInterval(timer);
            } else {
              counter.textContent = Math.floor(current);
            }
          }, duration / steps);
        });
        observer.unobserve(entry.target);
      }
    });
  }, { threshold: 0.3 });

  const statsSection = document.getElementById('statistics');
  if (statsSection) observer.observe(statsSection);
});
</script>


<!-- ========================================================= -->
<!-- 🤖 SECTION: KI-GESTÜTZTE FUNKTIONEN -->
<!-- ========================================================= -->
<section id="ai-features" class="section bg-light">
  <div class="container">
    <div class="text-center mb-5">
      <h2 class="section-title">KI-gestützte Blockchain-Analyse</h2>
      <p class="section-subtitle">
        Modernste Technologie für maximale Erfolgschancen bei der Wiederherstellung Ihrer Krypto-Guthaben
      </p>
    </div>

    <div class="row g-4 mb-5">
      <!-- AI Feature 1: Machine Learning -->
      <div class="col-md-6 col-lg-4">
        <div class="feature-card">
          <div class="feature-icon-lg mb-4">
            <i class="fas fa-brain fa-3x text-primary"></i>
          </div>
          <h4 class="fw-bold mb-3">Deep Learning Algorithmen</h4>
          <p class="text-muted">
            Unsere KI analysiert Millionen von Blockchain-Transaktionen in Echtzeit und erkennt Betrugsmuster mit einer Genauigkeit von 94%.
          </p>
          <ul class="list-unstyled text-start mt-3">
            <li><i class="fas fa-check-circle text-success me-2"></i> Mustererkennung in Transaktionen</li>
            <li><i class="fas fa-check-circle text-success me-2"></i> Verhaltensanalyse von Wallets</li>
            <li><i class="fas fa-check-circle text-success me-2"></i> Betrugserkennung in Echtzeit</li>
          </ul>
        </div>
      </div>

      <!-- AI Feature 2: Blockchain Tracking -->
      <div class="col-md-6 col-lg-4">
        <div class="feature-card">
          <div class="feature-icon-lg mb-4">
            <i class="fas fa-project-diagram fa-3x text-primary"></i>
          </div>
          <h4 class="fw-bold mb-3">Multi-Chain Tracking</h4>
          <p class="text-muted">
            Verfolgen Sie Ihre Kryptowährungen über 15+ Blockchains hinweg. Unsere KI identifiziert verdächtige Transaktionen und verfolgt Geldflüsse.
          </p>
          <ul class="list-unstyled text-start mt-3">
            <li><i class="fas fa-check-circle text-success me-2"></i> Bitcoin, Ethereum, BSC, Polygon</li>
            <li><i class="fas fa-check-circle text-success me-2"></i> Cross-Chain Analyse</li>
            <li><i class="fas fa-check-circle text-success me-2"></i> Mixer & Tumbler Erkennung</li>
          </ul>
        </div>
      </div>

      <!-- AI Feature 3: Fraud Detection -->
      <div class="col-md-6 col-lg-4">
        <div class="feature-card">
          <div class="feature-icon-lg mb-4">
            <i class="fas fa-shield-alt fa-3x text-primary"></i>
          </div>
          <h4 class="fw-bold mb-3">Betrugs-Identifikation</h4>
          <p class="text-muted">
            Mit über 100.000 bekannten Betrugsfällen trainiert, identifiziert unsere KI neue Betrugsmaschen und schützt Ihr Vermögen.
          </p>
          <ul class="list-unstyled text-start mt-3">
            <li><i class="fas fa-check-circle text-success me-2"></i> Phishing-Erkennung</li>
            <li><i class="fas fa-check-circle text-success me-2"></i> Ponzi-Schema Analyse</li>
            <li><i class="fas fa-check-circle text-success me-2"></i> Exit-Scam Früherkennung</li>
          </ul>
        </div>
      </div>

      <!-- AI Feature 4: Risk Assessment -->
      <div class="col-md-6 col-lg-4">
        <div class="feature-card">
          <div class="feature-icon-lg mb-4">
            <i class="fas fa-chart-bar fa-3x text-primary"></i>
          </div>
          <h4 class="fw-bold mb-3">Risiko-Bewertung</h4>
          <p class="text-muted">
            Automatische Bewertung Ihrer Wiederherstellungschancen basierend auf historischen Daten und aktuellen Blockchain-Informationen.
          </p>
          <ul class="list-unstyled text-start mt-3">
            <li><i class="fas fa-check-circle text-success me-2"></i> Erfolgswahrscheinlichkeit</li>
            <li><i class="fas fa-check-circle text-success me-2"></i> Zeitschätzung</li>
            <li><i class="fas fa-check-circle text-success me-2"></i> Kostenprognose</li>
          </ul>
        </div>
      </div>

      <!-- AI Feature 5: Automated Reports -->
      <div class="col-md-6 col-lg-4">
        <div class="feature-card">
          <div class="feature-icon-lg mb-4">
            <i class="fas fa-file-alt fa-3x text-primary"></i>
          </div>
          <h4 class="fw-bold mb-3">Automatische Berichte</h4>
          <p class="text-muted">
            Erhalten Sie detaillierte Analyseberichte automatisch generiert durch unsere KI – transparent und nachvollziehbar für Behörden.
          </p>
          <ul class="list-unstyled text-start mt-3">
            <li><i class="fas fa-check-circle text-success me-2"></i> Transaktionsanalyse</li>
            <li><i class="fas fa-check-circle text-success me-2"></i> Wallet-Verbindungen</li>
            <li><i class="fas fa-check-circle text-success me-2"></i> Beweissicherung</li>
          </ul>
        </div>
      </div>

      <!-- AI Feature 6: Real-Time Monitoring -->
      <div class="col-md-6 col-lg-4">
        <div class="feature-card">
          <div class="feature-icon-lg mb-4">
            <i class="fas fa-eye fa-3x text-primary"></i>
          </div>
          <h4 class="fw-bold mb-3">Echtzeit-Überwachung</h4>
          <p class="text-muted">
            24/7 Blockchain-Monitoring durch KI. Werden verdächtige Bewegungen erkannt, werden Sie sofort informiert.
          </p>
          <ul class="list-unstyled text-start mt-3">
            <li><i class="fas fa-check-circle text-success me-2"></i> Automatische Benachrichtigungen</li>
            <li><i class="fas fa-check-circle text-success me-2"></i> Wallet-Bewegungen tracken</li>
            <li><i class="fas fa-check-circle text-success me-2"></i> Sofortige Alerts</li>
          </ul>
        </div>
      </div>
    </div>

    <!-- AI Technology Highlight -->
    <div class="row justify-content-center">
      <div class="col-lg-10">
        <div class="ai-highlight-box">
          <div class="row align-items-center">
            <div class="col-md-2 text-center mb-3 mb-md-0">
              <i class="fas fa-microchip fa-4x opacity-75"></i>
            </div>
            <div class="col-md-10 text-start">
              <h4 class="fw-bold mb-3">Modernste KI-Technologie im Einsatz</h4>
              <p class="mb-0 opacity-90">
                Unsere proprietären Machine-Learning-Algorithmen wurden mit über 100.000 Betrugsfällen trainiert und können selbst komplexeste Geldflüsse über mehrere Blockchains hinweg verfolgen. Mit einer Identifizierungsrate von 87% und kontinuierlichem Lernen aus neuen Fällen bieten wir die fortschrittlichste Lösung zur Krypto-Wiederherstellung auf dem Markt.
              </p>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>

<style>
.stat-card {
  padding: 40px 20px;
  border-radius: 16px;
  background: rgba(255,255,255,0.1);
  backdrop-filter: blur(10px);
  border: 1px solid rgba(255,255,255,0.2);
  transition: all 0.3s ease;
}
.stat-card:hover {
  background: rgba(255,255,255,0.15);
  transform: translateY(-5px);
}
.stat-icon {
  width: 80px;
  height: 80px;
  border-radius: 50%;
  background: rgba(255,255,255,0.2);
  display: flex;
  align-items: center;
  justify-content: center;
  margin: 0 auto 20px;
  font-size: 32px;
  border: 2px solid rgba(255,255,255,0.3);
}
.badge-item {
  text-align: center;
  padding: 20px;
  background: rgba(255,255,255,0.1);
  border-radius: 12px;
  transition: all 0.3s ease;
  backdrop-filter: blur(10px);
  border: 1px solid rgba(255,255,255,0.2);
}
.badge-item:hover {
  transform: scale(1.05) rotate(2deg);
  background: rgba(255,255,255,0.15);
}
.feature-icon-lg {
  width: 100px;
  height: 100px;
  border-radius: 20px;
  background: linear-gradient(135deg, #e7f3ff 0%, #cfe8ff 100%);
  display: flex;
  align-items: center;
  justify-content: center;
  margin: 0 auto;
}
.ai-highlight-box {
  background: rgba(255,255,255,0.1);
  border-radius: 16px;
  padding: 40px;
  border: 2px solid rgba(255,255,255,0.2);
  backdrop-filter: blur(10px);
}
</style>


<!-- FAQ -->
<section id="faq" class="section anchor-offset">
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
                                Unser spezialisierter Service unterstützt Sie bei der Wiederherstellung verlorener Zugänge (z. B. Seed-Phrase).
                                KI-gestützte Analyse, manuelle Verifikation und transparente Dokumentation sorgen für eine rechtssichere Rückführung.
                            </div>
                        </div>
                    </div>

                    <div class="accordion-item mb-3">
                        <h2 class="accordion-header">
                            <button class="accordion-button fw-bold collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq2">
                                Wie lange dauern Ein- und Auszahlungen?
                            </button>
                        </h2>
                        <div id="faq2" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                            <div class="accordion-body">
                                Alle SEPA-Überweisungen werden manuell geprüft und in der Regel innerhalb von 1–2 Werktagen bearbeitet.
                            </div>
                        </div>
                    </div>

                    <div class="accordion-item mb-3">
                        <h2 class="accordion-header">
                            <button class="accordion-button fw-bold collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq3">
                                Ist die Plattform reguliert?
                            </button>
                        </h2>
                        <div id="faq3" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                            <div class="accordion-body">
                                Wir arbeiten nach europäischen Compliance-Standards (u. a. AML-Richtlinien) und dokumentieren alle Schritte revisionssicher.
                            </div>
                        </div>
                    </div>
                </div><!-- /accordion -->
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
        <a href="https://novalnet-ai.de/app" class="btn btn-light btn-lg fw-bold px-5 py-3">Konto erstellen</a>
    </div>
</section>

<!-- JavaScript for Animations -->
<script>
// ========== Particle Network Animation ==========
(function() {
    const canvas = document.getElementById('particles-canvas');
    if (!canvas) return;
    
    const ctx = canvas.getContext('2d');
    canvas.width = canvas.offsetWidth;
    canvas.height = canvas.offsetHeight;
    
    const particles = [];
    const particleCount = 30;
    const connectionDistance = 120;
    
    class Particle {
        constructor() {
            this.x = Math.random() * canvas.width;
            this.y = Math.random() * canvas.height;
            this.vx = (Math.random() - 0.5) * 0.5;
            this.vy = (Math.random() - 0.5) * 0.5;
            this.radius = 2;
        }
        
        update() {
            this.x += this.vx;
            this.y += this.vy;
            
            if (this.x < 0 || this.x > canvas.width) this.vx *= -1;
            if (this.y < 0 || this.y > canvas.height) this.vy *= -1;
        }
        
        draw() {
            ctx.beginPath();
            ctx.arc(this.x, this.y, this.radius, 0, Math.PI * 2);
            ctx.fillStyle = 'rgba(13, 110, 253, 0.5)';
            ctx.fill();
        }
    }
    
    // Create particles
    for (let i = 0; i < particleCount; i++) {
        particles.push(new Particle());
    }
    
    // Animation loop
    function animate() {
        ctx.clearRect(0, 0, canvas.width, canvas.height);
        
        // Update and draw particles
        particles.forEach(particle => {
            particle.update();
            particle.draw();
        });
        
        // Draw connections
        for (let i = 0; i < particles.length; i++) {
            for (let j = i + 1; j < particles.length; j++) {
                const dx = particles[i].x - particles[j].x;
                const dy = particles[i].y - particles[j].y;
                const distance = Math.sqrt(dx * dx + dy * dy);
                
                if (distance < connectionDistance) {
                    ctx.beginPath();
                    ctx.moveTo(particles[i].x, particles[i].y);
                    ctx.lineTo(particles[j].x, particles[j].y);
                    ctx.strokeStyle = `rgba(13, 110, 253, ${0.15 * (1 - distance / connectionDistance)})`;
                    ctx.lineWidth = 0.5;
                    ctx.stroke();
                }
            }
        }
        
        requestAnimationFrame(animate);
    }
    
    animate();
    
    // Resize handler
    window.addEventListener('resize', () => {
        canvas.width = canvas.offsetWidth;
        canvas.height = canvas.offsetHeight;
    });
})();

// ========== Scroll-Based Animations ==========
(function() {
    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.classList.add('animate-on-scroll');
                observer.unobserve(entry.target);
            }
        });
    }, { threshold: 0.1 });
    
    // Observe sections for animation
    document.querySelectorAll('.section').forEach(section => {
        observer.observe(section);
    });
})();

// ========== Enhanced Statistics Counter ==========
(function() {
    const statsSection = document.querySelector('#stats');
    if (!statsSection) return;
    
    let animated = false;
    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting && !animated) {
                animated = true;
                
                // Animate counters
                const counters = [
                    { el: document.querySelector('[data-count="727"]'), target: 727 },
                    { el: document.querySelector('[data-count="87"]'), target: 87 },
                    { el: document.querySelector('[data-count="47"]'), target: 47 },
                    { el: document.querySelector('[data-count="14"]'), target: 14 }
                ];
                
                counters.forEach(counter => {
                    if (!counter.el) return;
                    let current = 0;
                    const increment = counter.target / 60;
                    const timer = setInterval(() => {
                        current += increment;
                        if (current >= counter.target) {
                            counter.el.textContent = counter.target;
                            clearInterval(timer);
                        } else {
                            counter.el.textContent = Math.floor(current);
                        }
                    }, 33);
                });
                
                observer.unobserve(entry.target);
            }
        });
    }, { threshold: 0.3 });
    
    observer.observe(statsSection);
})();
</script>

<!-- Footer -->
<!-- ========================================================= -->
<!-- 🌐 FOOTER – Novalnet AI -->
<!-- ========================================================= -->
<?php include 'footer.php'; ?>

