<?php
session_start();
require_once '../scripts/functions.php';

// Sprawdzenie, czy użytkownik jest zalogowany i czy ma uprawnienia administratora
if (!isUserLoggedIn() || $_SESSION['logged_user']['role_id'] < 3) {
    header("Location: ../index.php");
    exit;
}

$configPath = '../config.php';
$config = require $configPath;
updateConfig($config);

$title = $config['site_name'] . ' - Ustawienia';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" type="text/css" href="../assets/css/configSettings.css">
    <link rel="icon" type="image/png" sizes="16x16" href="<?php echo $config['favicon']; ?>">
    <title><?php echo $title; ?></title>
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <div class="col-auto position-fixed">
                <?php require_once '../views/sidebar.php'; ?>
            </div>  
            <div class="col" style="margin-left: 250px; padding: 20px;">
                <h3 class="font-weight-bold">Ustawienia strony</h3>
                <p class="text-sm text-secondary">Zarządzaj ustawieniami aplikacji.</p>

                <?php
                if (isset($_SESSION['errors']) && is_array($_SESSION['errors'])) {
                    echo '<div class="alert alert-danger">';
                    foreach ($_SESSION['errors'] as $error) {
                        echo "<p>$error</p>";
                    }
                    echo '</div>';
                    unset($_SESSION['errors']);
                }
            
                if (isset($_SESSION['success']) && is_array($_SESSION['success'])) {
                    echo '<div class="alert alert-success">';
                    foreach ($_SESSION['success'] as $success) {
                        echo "<p>$success</p>";
                    }
                    echo '</div>';
                    unset($_SESSION['success']);
                }
                ?>

                <div class="card">
                    <div class="card-header">
                        <h6 class="font-weight-bolder">Konfiguracja</h6>
                    </div>
                    <div class="card-body px-4 pb-4">
                        <?php if (isset($_SESSION['success_message'])): ?>
                            <div class="alert alert-success">
                                <?php echo $_SESSION['success_message']; unset($_SESSION['success_message']); ?>
                            </div>
                        <?php endif; ?>
                        <?php if (isset($_SESSION['error_message'])): ?>
                            <div class="alert alert-danger">
                                <?php echo $_SESSION['error_message']; unset($_SESSION['error_message']); ?>
                            </div>
                        <?php endif; ?>
                        <form method="POST">
                            <div class="mb-3">
                                <label for="site_name" class="form-label">Nazwa strony</label>
                                <input type="text" class="form-control" id="site_name" name="site_name" value="<?php echo htmlspecialchars($config['site_name']); ?>" required>
                            </div>
                            <div class="mb-3">
                                <label for="favicon" class="form-label">Ścieżka do favicon</label>
                                <input type="text" class="form-control" id="favicon" name="favicon" value="<?php echo htmlspecialchars($config['favicon']); ?>" required>
                            </div>
                            <div class="mb-3">
                                <label for="logo" class="form-label">Ścieżka do logo</label>
                                <input type="text" class="form-control" id="logo" name="logo" value="<?php echo htmlspecialchars($config['logo']); ?>" required>
                            </div>
                            <div class="mb-3">
                                <label for="recaptcha_site_key" class="form-label">Klucz strony reCAPTCHA</label>
                                <input type="text" class="form-control" id="recaptcha_site_key" name="recaptcha_site_key" value="<?php echo htmlspecialchars($config['recaptcha_site_key']); ?>" required>
                            </div>
                            <div class="mb-3">
                                <label for="recaptcha_secret_key" class="form-label">Tajny klucz reCAPTCHA</label>
                                <input type="text" class="form-control" id="recaptcha_secret_key" name="recaptcha_secret_key" value="<?php echo htmlspecialchars($config['recaptcha_secret_key']); ?>" required>
                            </div>
                            <button type="submit" class="btn btn-primary">Zapisz zmiany</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
