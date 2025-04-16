<?php
session_start();
require_once '../scripts/functions.php';

// Sprawdzenie, czy użytkownik jest zalogowany i czy ma uprawnienia administratora
if (!isUserLoggedIn() || $_SESSION['logged_user']['role_id'] < 3) {
    header("Location: ../index.php");
    exit;
}
require_once '../scripts/db_connect.php';

$config = require '../config.php';
$title = $config['site_name'] . ' - Backup bazy danych';

// Ścieżka do folderu backupów
$backupDir = '../backups/';
if (!is_dir($backupDir)) {
    mkdir($backupDir, 0777, true);
}
// Stwórz kopię zapasową bazy danych
$message = '';
createBackup($pdo, $backupDir, $message);

// Pobranie listy plików backupu
$backupFiles = array_diff(scandir($backupDir), ['.', '..', 'index.php']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
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
                <h3 class="font-weight-bold">Backup bazy danych</h3>
                <p class="text-sm text-secondary">Utwórz i pobierz backup bazy danych.</p>

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

                <?php echo $message; ?>
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header">
                                <h6 class="font-weight-bolder">Utwórz backup</h6>
                            </div>
                            <div class="card-body">
                                <form method="POST">
                                    <button type="submit" name="create_backup" class="btn btn-primary">Utwórz backup</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row mt-4">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header">
                                <h6 class="font-weight-bolder">Dostępne backupy</h6>
                            </div>
                            <div class="card-body px-0 pb-2">
                                <div class="table-responsive">
                                    <table class="table align-items-center mb-0">
                                        <thead>
                                            <tr>
                                                <th class="text-uppercase text-secondary text-sm font-weight-bolder">Nazwa pliku</th>
                                                <th class="text-uppercase text-secondary text-sm font-weight-bolder">Akcje</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($backupFiles as $file): ?>
                                                <tr>
                                                    <td class="text-sm"><?php echo $file; ?></td>
                                                    <td class="text-sm">
                                                        <a href="<?php echo $backupDir . $file; ?>" class="btn btn-success btn-sm" download>Pobierz</a>
                                                        <form method="POST" action="./../scripts/deleteBackup.php" style="display:inline;">
                                                            <input type="hidden" name="backup_file" value="<?php echo $file; ?>">
                                                            <button type="submit" class="btn btn-danger btn-sm">Usuń</button>
                                                        </form>
                                                    </td>
                                                </tr>
                                            <?php endforeach; ?>
                                            <?php if (empty($backupFiles)): ?>
                                                <tr>
                                                    <td colspan="2" class="text-sm text-center">Brak dostępnych backupów.</td>
                                                </tr>
                                            <?php endif; ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>