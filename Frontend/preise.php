<!DOCTYPE html>
<html lang="de">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Gebühren & Kostenübersicht – KryptoX</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    body {
      font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
      background-color: #fff;
      color: #1a1a2e;
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

.navbar-brand img {
  height: 40px;
  width: auto;
  margin-right: 8px;
}
    .section {
      padding: 100px 0;
    }
    h2 {
      font-weight: 700;
      margin-bottom: 40px;
      color: #0d1b2a;
    }
    .price-box {
      background: #fff;
      border-radius: 16px;
      box-shadow: 0 3px 12px rgba(0,0,0,0.05);
      padding: 30px;
      margin-bottom: 30px;
    }
    .price-box h4 {
      font-weight: 600;
      margin-bottom: 15px;
      color: #0d1b2a;
    }
    .price-box p, .price-box li {
      color: #444;
    }
    ul {
      padding-left: 20px;
    }
    .highlight {
      font-weight: 600;
      color: #0d6efd;
    }
    footer {
      background: #f8f9fa;
      color: #6c757d;
      padding: 40px 0;
      font-size: 0.9rem;
    }
  </style>
</head>

<body>

<!-- Navbar -->
<nav class="navbar navbar-expand-lg navbar-light fixed-top" id="mainNav">
  <div class="container">
    <a class="navbar-brand fw-bold fs-4 d-flex align-items-center" href="index.php">
      <img src="/assets/img/logo.png" alt="KryptoX Logo">
    </a>

    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav"
            aria-controls="navbarNav" aria-expanded="false" aria-label="Menü umschalten">
      <span class="navbar-toggler-icon"></span>
    </button>

    <div class="collapse navbar-collapse" id="navbarNav">
      <ul class="navbar-nav ms-auto">
        <li class="nav-item"><a class="nav-link" href="#services">Services</a></li>
        <li class="nav-item"><a class="nav-link" href="#process">Prozess</a></li>
        <li class="nav-item"><a class="nav-link" href="#features">Features</a></li>
        <li class="nav-item"><a class="nav-link" href="#refund-ai">KI-Rückerstattung</a></li>
        <li class="nav-item"><a class="nav-link" href="ueber-uns.php">Über uns</a></li>
        <li class="nav-item"><a class="nav-link" href="#faq">FAQ</a></li>
      </ul>
      <a href="https://novalnet-ai.de/app" class="btn btn-primary ms-lg-3 mt-2 mt-lg-0">
        <i class="fa-solid fa-user-plus me-2"></i>Konto erstellen
      </a>
    </div>
  </div>
</nav>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script>
  // Add shadow on scroll
  window.addEventListener('scroll', () => {
    const nav = document.getElementById('mainNav');
    if(window.scrollY > 10) nav.classList.add('scrolled');
    else nav.classList.remove('scrolled');
  });
</script>

<!-- Preise Section -->
<section class="section mt-5">
  <div class="container">
    <h2>Gebühren & Kostenübersicht</h2>

    <div class="price-box">
      <p>
        Transparenz und Fairness stehen bei uns an oberster Stelle. Daher möchten wir Ihnen genau aufzeigen, 
        welche Gebühren bei der Rückführung Ihrer Krypto-Assets anfallen können und unter welchen Umständen 
        zusätzliche Kosten entstehen.
      </p>
    </div>

    <!-- 1. Erfolgsabhängige Rückführungsgebühr -->
    <div class="price-box">
      <h4>1. Erfolgsabhängige Rückführungsgebühr</h4>
      <p>
        Nach erfolgreicher Rückführung Ihres Depots und der Auszahlung auf Ihr verifiziertes Bankkonto fällt eine 
        einmalige Gebühr an:
      </p>
      <ul>
        <li>4,8 % des Gesamtdepotwerts (erfolgsabhängig, nur im Erfolgsfall fällig)</li>
      </ul>
      <p>
        Diese Gebühr deckt sämtliche Kosten des Prüf- und Verifizierungsprozesses, einschließlich Blockchain-Analyse, 
        Identitätsabgleich und rechtssicherer Dokumentation.
      </p>
    </div>

    <!-- 2. Mögliche Zusatzkosten -->
    <div class="price-box">
      <h4>2. Mögliche Zusatzkosten (nur in Einzelfällen)</h4>
      <ul>
        <li>Depotführungsgebühr – z. B. bei besonders alten oder unklar verwalteten Depots</li>
        <li>Wechselgebühr (Krypto → Fiat) – abhängig von Marktsituation und Umfang</li>
      </ul>
    </div>

    <!-- Härtefälle -->
    <div class="price-box">
      <h4>Wichtig: Härtefälle mit hohen Depotwerten</h4>
      <p>
        In besonders schweren Betrugsfällen – häufig bei sogenannten Härtefall-Klienten mit Depotwerten im sechs- bis 
        siebenstelligen Bereich – kann es zu erhöhtem Bearbeitungsaufwand und Sonderprüfungen kommen. 
        Die dadurch entstehenden Zusatzgebühren variieren nach Fall und werden transparent vorab kommuniziert.
      </p>
    </div>

    <!-- Keine versteckten Gebühren -->
    <div class="price-box">
      <h4>Keine versteckten Gebühren – volle Transparenz</h4>
      <ul>
        <li>✓ Keine Vorkosten im Standardfall</li>
        <li>✓ Erfolgsabhängige Hauptgebühr nur nach Rückzahlung</li>
        <li>✓ Zusatzkosten nur bei begründetem Mehraufwand</li>
      </ul>
    </div>

    <!-- Fragen -->
    <div class="price-box">
      <h4>Sie haben Fragen?</h4>
      <p>
        Unser Team steht Ihnen gerne zur Verfügung, um Ihre individuelle Situation zu prüfen und etwaige Gebühren im 
        Vorfeld einzuschätzen.
      </p>
      <a href="kontakt.html" class="btn btn-primary rounded-pill px-4 mt-2">Kontakt aufnehmen</a>
    </div>

  </div>
</section>

<!-- ========================================================= -->
<!-- 🌐 FOOTER – TRADEVEST CRYPTO -->
<!-- ========================================================= -->
<?php include 'footer.php'; ?>
