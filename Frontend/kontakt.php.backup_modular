<!DOCTYPE html>
<html lang="de">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Kontakt – KryptoX</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    body {
      font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
      background-color: #f8f9fc;
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
      margin-bottom: 10px;
      color: #0d1b2a;
    }
    .lead {
      color: #6c757d;
      margin-bottom: 40px;
    }
    .contact-box, .form-box {
      background: #fff;
      border-radius: 16px;
      box-shadow: 0 3px 12px rgba(0,0,0,0.05);
      padding: 30px;
      margin-bottom: 30px;
    }
    .contact-icon {
      width: 50px;
      height: 50px;
      background: #e8f0fe;
      border-radius: 50%;
      display: flex;
      align-items: center;
      justify-content: center;
      margin-right: 15px;
    }
    .btn-primary {
      background: #0066ff;
      border: none;
      border-radius: 30px;
      padding: 12px 30px;
      font-weight: 600;
    }
    .btn-primary:hover {
      background: #004bcc;
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


<!-- Kontakt Section -->
<section class="section mt-5">
  <div class="container">
    <h2>Kontakt</h2>
    <p class="lead">Haben Sie Fragen? Wir sind für Sie da!</p>

    <!-- E-Mail Support -->
    <div class="contact-box d-flex align-items-center">
      <div class="contact-icon">
        <img src="https://cdn-icons-png.flaticon.com/512/561/561127.png" width="28" alt="Mail Icon">
      </div>
      <div>
        <h5 class="mb-1">E-Mail Support</h5>
        <p class="mb-0 text-muted">Für allgemeine Anfragen und Support</p>
        <a href="mailto:support@novalnet-ai.de" class="fw-bold text-decoration-none text-primary">support@novalnet-ai.de</a>
        <p class="text-muted small mb-0">Antwortzeit: 1–2 Werktage</p>
      </div>
    </div>

    <!-- Kontaktformular -->
    <div class="form-box">
      <h5 class="mb-3">📩 Kontaktformular</h5>
      <form action="send_contact.php" method="POST">
        <div class="row mb-3">
          <div class="col-md-6">
            <label class="form-label">Vorname *</label>
            <input type="text" name="vorname" class="form-control" placeholder="Ihr Vorname" required>
          </div>
          <div class="col-md-6">
            <label class="form-label">Nachname *</label>
            <input type="text" name="nachname" class="form-control" placeholder="Ihr Nachname" required>
          </div>
        </div>

        <div class="row mb-3">
          <div class="col-md-6">
            <label class="form-label">E-Mail *</label>
            <input type="email" name="email" class="form-control" placeholder="ihre@email.de" required>
          </div>
          <div class="col-md-6">
            <label class="form-label">Telefon</label>
            <input type="text" name="telefon" class="form-control" placeholder="069 12001194">
          </div>
        </div>

        <div class="mb-3">
          <label class="form-label">Nachricht *</label>
          <textarea name="nachricht" class="form-control" rows="5" maxlength="5000" placeholder="Beschreiben Sie Ihr Anliegen..." required></textarea>
          <div class="text-end text-muted small">0/5000 Zeichen</div>
        </div>

        <button type="submit" class="btn btn-primary w-100 mt-2">Nachricht senden</button>
      </form>
    </div>
  </div>
</section>

<!-- ========================================================= -->
<!-- 🌐 FOOTER – TRADEVEST CRYPTO -->
<!-- ========================================================= -->
<?php include 'footer.php'; ?>
