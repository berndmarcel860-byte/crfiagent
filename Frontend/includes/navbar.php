<!-- Navbar -->
<nav class="navbar navbar-expand-lg navbar-light fixed-top" id="mainNav">
    <div class="container">
        <a class="navbar-brand fw-bold fs-4 d-flex align-items-center" href="/Frontend/index.php">
            <img src="/assets/img/logo.png" alt="Novalnet AI Logo">
            <span class="ms-2">Novalnet AI</span>
        </a>

        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav"
                aria-controls="navbarNav" aria-expanded="false" aria-label="Menü umschalten">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto">
                <li class="nav-item"><a class="nav-link" href="/Frontend/index.php#services">Services</a></li>
                <li class="nav-item"><a class="nav-link" href="/Frontend/index.php#process">Prozess</a></li>
                <li class="nav-item"><a class="nav-link" href="/Frontend/index.php#features">Features</a></li>
                <li class="nav-item"><a class="nav-link" href="/Frontend/index.php#refund-ai">KI-Rückerstattung</a></li>
                <li class="nav-item"><a class="nav-link" href="/Frontend/ueber-uns.php">Über uns</a></li>
                <li class="nav-item"><a class="nav-link" href="/Frontend/index.php#faq">FAQ</a></li>
                <li class="nav-item"><a class="nav-link" href="/Frontend/kontakt.php">Kontakt</a></li>
            </ul>
            <a href="/app" class="btn btn-primary ms-lg-3 mt-2 mt-lg-0">
                <i class="fa-solid fa-user-plus me-2"></i>Konto erstellen
            </a>
        </div>
    </div>
</nav>

<script>
// Add shadow on scroll
window.addEventListener('scroll', () => {
    const nav = document.getElementById('mainNav');
    if(window.scrollY > 10) nav.classList.add('scrolled');
    else nav.classList.remove('scrolled');
});
</script>
