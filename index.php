<?php 
session_start();
require_once 'scripts/functions.php';
redirectIfLoggedIn();

$config = require 'config.php';
$title = $config['site_name'] . ' - Logowanie';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/login.css">
    <link rel="icon" type="image/png" href="./assets/img/favicon.png">
    <title><?php echo $title; ?></title>
</head>
<body>
    <div class="login-container d-flex align-items-center justify-content-center vh-100">
        <div class="login-card p-4 shadow rounded-4 bg-white">
            <div class="text-center mb-4">
                <img src="./assets/img/logo.png" alt="Logo" height="60">
                <h5 class="mt-2 fw-bold">Nazwa twojej firmy</h5>
                <p class="text-muted small m-0">Slogan</p>
            </div>
            <form action="scripts/loginHandler.php" method="post">
                <div class="mb-3">
                    <input type="text" class="form-control" name="email" placeholder="Email" required>
                </div>
                <div class="mb-3">
                    <input type="password" class="form-control" name="password" placeholder="Hasło" required>
                </div>

                <!-- reCAPTCHA -->
                <div class="d-flex align-items-center justify-content-center mb-3">
                    <div class="g-recaptcha" data-sitekey="<?php echo $config['recaptcha_site_key']; ?>"></div>
                </div>

                <?php if (isset($_SESSION['error'])): ?>
                    <div class="alert alert-danger p-2 text-center">
                        <?php echo $_SESSION['error']; unset($_SESSION['error']); ?>
                    </div>
                <?php endif; ?>

                <button type="submit" name="submit" class="btn btn-dark w-100 h-100">Zaloguj się</button>
            </form>
        </div>
    </div>
    <script src="https://www.google.com/recaptcha/api.js" async defer></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>