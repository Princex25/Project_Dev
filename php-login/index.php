<?php

require_once __DIR__ . '/../shared/config.php';

if (isLoggedIn()) {
    redirectToUserSpace();
}

$errorMessage = $_SESSION['login_error'] ?? null;
unset($_SESSION['login_error']);
?>
<!DOCTYPE html>
<html lang="fr">
  <head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Connexion - Gestion des demandes</title>
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&family=Product+Sans:wght@400;700&display=swap" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" />
    <link rel="stylesheet" href="assets/css/styles.css" />
  </head>
  <body>
    <main class="login-page">
      <div class="login-shell">
        <div class="decor-backdrop"></div>
        <div class="form-backdrop"></div>

        <div class="section-wrapper">
          <section class="promo-section">
            <span class="promo-overlay"></span>
            <span class="promo-images"></span>
            <div class="promo-content">
              <header class="promo-title">
                <h1>Gestion des demandes</h1>
                <p>Fluidifiez vos demandes, optimisez vos ressources.</p>
              </header>
              <ul class="promo-list list-unstyled">
                <li class="promo-list-item">
                  <span class="promo-list-icon" aria-hidden="true"></span>
                  <p class="promo-list-text">Création facile des demandes.</p>
                </li>
                <li class="promo-list-item">
                  <span class="promo-list-icon" aria-hidden="true"></span>
                  <p class="promo-list-text">Suivi en temps réel.</p>
                </li>
                <li class="promo-list-item">
                  <span class="promo-list-icon" aria-hidden="true"></span>
                  <p class="promo-list-text">Validation rapide.</p>
                </li>
              </ul>
            </div>
          </section>
        </div>

        <div class="section-wrapper">
          <section class="form-section">
            <form
              id="loginForm"
              class="form-card needs-validation"
              method="post"
              action="login.php"
              novalidate
            >
              <header class="form-header text-start">
                <h2>Connexion</h2>
                <p>Veuillez renseigner vos informations</p>
              </header>

              <?php if ($errorMessage): ?>
              <div class="alert alert-danger alert-message" role="alert">
                <?php echo htmlspecialchars($errorMessage, ENT_QUOTES, 'UTF-8'); ?>
              </div>
              <?php endif; ?>

              <div class="form-group">
                <label for="email" class="form-label">Adresse e-mail</label>
                <input
                  type="email"
                  class="form-control form-control-glass"
                  id="email"
                  name="email"
                  placeholder="Enter your email"
                  required
                />
                <div class="invalid-feedback"></div>
              </div>

              <div class="form-group password-toggle">
                <label for="password" class="form-label">Mot de passe</label>
                <input
                  type="password"
                  class="form-control form-control-glass"
                  id="password"
                  name="password"
                  placeholder="Entrez votre mot de passe"
                  required
                />
                <button id="togglePassword" type="button" aria-label="Afficher le mot de passe">
                  <i id="togglePasswordIcon" class="bi bi-eye" aria-hidden="true"></i>
                </button>
                <div class="invalid-feedback"></div>
              </div>

              <div class="form-check">
                <input class="form-check-input" type="checkbox" value="1" id="remember" name="remember" />
                <label class="form-check-label" for="remember">Se souvenir de moi</label>
              </div>

              <div class="d-flex justify-content-center">
                <button id="submitButton" type="submit" class="btn btn-light btn-submit">
                  Se connecter
                </button>
              </div>
              
              <div class="mt-4 text-center">
                <small class="text-muted">
                  <strong></strong><br>
                 <br>
                 <br>
                <br>
                  <em></em>
                </small>
              </div>
            </form>
          </section>
        </div>
      </div>
    </main>

    <script
      src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
      integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz"
      crossorigin="anonymous"
    ></script>
    <script src="assets/js/scripts.js"></script>
  </body>
</html>
