<?php
session_start();
require_once '../scripts/functions.php';
// Sprawdzenie, czy użytkownik jest zalogowany
if (!isUserLoggedIn()) {
    header("Location: ../index.php");
    exit;
}
require_once '../scripts/db_connect.php';

// Pobranie danych urlopowych
$leavesData = getUserLeaveRequests($pdo);

$config = require '../config.php';
$title = $config['site_name'] . ' - Status urlopów';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" type="text/css" href="../assets/css/leaveStatus.css">
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
                <h3 class="font-weight-bold">Status urlopów</h3>
                <p class="text-sm text-secondary">Sprawdź status swoich wniosków urlopowych.</p>

                <?php
                if (isset($_SESSION['errors'])) {
                    echo '<div class="alert alert-danger">';
                    foreach ($_SESSION['errors'] as $error) {
                        echo "<p>$error</p>";
                    }
                    echo '</div>';
                    unset($_SESSION['errors']);
                }
                if (isset($_SESSION['success'])) {
                    echo '<div class="alert alert-success">';
                    echo $_SESSION['success'];
                    echo '</div>';
                    unset($_SESSION['success']);
                }?>

                <div class="card">
                    <div class="card-header">
                        <h6 class="font-weight-bolder">Twoje wnioski urlopowe</h6>
                    </div>
                    <div class="card-body px-0 pb-2">
                        <div class="table-responsive">
                            <table class="table align-items-center mb-0">
                                <thead>
                                    <tr>
                                        <th>Pracownik</th>
                                        <th>Data rozpoczęcia</th>
                                        <th>Data zakończenia</th>
                                        <th>Status</th>
                                        <th>Uwagi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($leavesData as $leave): ?>
                                        <tr>
                                          <td class="text-sm">
                                            <?php echo $leave['firstname'] . ' ' . $leave['lastname']; ?>
                                          </td>
                                          <td class="text-sm">
                                            <?php echo $leave['start_date']; ?>
                                          </td>
                                          <td class="text-sm">
                                            <?php echo $leave['end_date']; ?>
                                          </td>
                                          <td class="text-sm">
                                            <span class="status-<?php echo $leave['status']; ?>">
                                              <?php echo ucfirst($leave['status']); ?>
                                            </span>
                                          </td>
                                          <td class="text-sm">
                                            <?php echo !empty($leave['notes']) ? $leave['notes'] : '<span class="text-secondary">Brak uwag</span>'; ?>
                                          </td>
                                        </tr>
                                    <?php endforeach; ?>
                                    <?php if (empty($leavesData)): ?>
                                        <tr>
                                            <td colspan="5" class="text-center text-sm text-secondary">
                                                Brak wniosków urlopowych.
                                            </td>
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
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>