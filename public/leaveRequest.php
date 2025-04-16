<?php
session_start();
require_once '../scripts/functions.php';
// Sprawdzenie, czy użytkownik jest zalogowany
if (!isUserLoggedIn()) {
    header("Location: ../index.php");
    exit;
}
require_once '../scripts/db_connect.php';

$config = require '../config.php';
$title = $config['site_name'] . ' - Zgłaszanie urlopów';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" type="text/css" href="../assets/css/leaveRequest.css">
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
                <h3 class="font-weight-bold">Wniosek o urlop</h3>
                <p class="text-sm text-secondary">Złóż wniosek o urlop i zobacz pozostałe dni urlopowe.</p>

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
                        <h6 class="font-weight-bolder">Informacje o urlopie</h6>
                    </div>
                    <div class="card-body">
                    <?php
                        $remainingVacationDays = calculateRemainingVacationDays($pdo,);   
                        if ($remainingVacationDays === 0) {
                          echo '<div class="alert alert-warning">Nie masz już dostępnych dni urlopowych w tym roku.</div>';
                        } else {
                          echo "<p>Pozostałe dni urlopowe: <b>$remainingVacationDays</b></p>";
                        }
                    ?>

                    <form action="./../scripts/addLeave.php" method="post">
                        <div class="row">
                            <div class="col-md-6">
                                <label for="startDate" class="form-label">Data rozpoczęcia:</label>
                                <input type="date" name="startDate" id="startDate" class="form-control" min="<?= date('Y-m-d') ?>" required>
                            </div>
                            <div class="col-md-6">
                                <label for="endDate" class="form-label">Data zakończenia:</label>
                                <input type="date" name="endDate" id="endDate" class="form-control" required>
                            </div>
                        </div>
                        <div class="mt-3">
                            <button type="submit" class="btn">Złóż wniosek</button>
                        </div>
                    </form>
                  </div>
                </div>
            </div>
        </div>
    </div>
    <script>
        document.getElementById("startDate").addEventListener("change", function () {
            document.getElementById("endDate").min = this.value;
        });
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>