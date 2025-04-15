<?php
session_start();
require_once '../scripts/functions.php';
// Sprawdzenie, czy użytkownik jest zalogowany
if (!isUserLoggedIn()) {
    header("Location: ../index.php");
    exit;
}
require_once '../scripts/db_connect.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>
    <title>Panel Główny</title>
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <div class="col-auto position-fixed">
                <?php require_once '../views/sidebar.php'; ?>
            </div>

            <div class="col" style="margin-left: 250px; padding: 20px;">
                <h3>Witaj <?php echo $_SESSION['logged_user']['firstname']; ?> </h3>
                <p class="text-sm text-secondary">Monitoruj statystyki urlopowe i zarządzaj danymi w jednym miejscu.</p>

                <div class="row mt-4">
                    <!-- Pozostałe dni urlopowe -->
                    <div class="col-md-4 mb-4">
                        <div class="card">
                            <div class="card-header p-3">
                                <p class="text-sm mb-0 text-uppercase font-weight-bold">Pozostałe dni urlopowe</p>
                                <h3 class="font-weight-bolder text-center"><?php echo calculateRemainingVacationDays($pdo); ?></h3>
                            </div>
                            <hr class="dark horizontal my-0">
                            <div class="card-footer p-3">
                                <p class="mb-0 text-sm"><span class="text-success text-bold">Pozostało do końca roku</span></p>
                            </div>
                        </div>
                    </div>
                    <!-- Wykorzystane dni -->
                    <div class="col-md-4 mb-4">
                        <div class="card">
                            <div class="card-header p-3">
                                <p class="text-sm mb-0 text-uppercase font-weight-bold">Wykorzystane dni</p>
                                <h3 class="font-weight-bolder text-center"><?php echo getUsedVacationDays($pdo); ?></h3>
                            </div>
                            <hr class="dark horizontal my-0">
                            <div class="card-footer p-3">
                                <p class="mb-0 text-sm"><span class="text-info text-bold">Dni urlopowych w tym roku</span></p>
                            </div>
                        </div>
                    </div>
                    <!-- Oczekujące wnioski -->
                    <div class="col-md-4 mb-4">
                        <div class="card">
                            <div class="card-header p-3">
                                <p class="text-sm mb-0 text-uppercase font-weight-bold">Oczekujące wnioski</p>
                                <h3 class="font-weight-bolder text-center"><?php echo getPendingRequestsCount($pdo); ?></h3>
                            </div>
                            <hr class="dark horizontal my-0">
                            <div class="card-footer p-3">
                                <p class="mb-0 text-sm"><span class="text-warning text-bold">Do zatwierdzenia przez HR</span></p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row mt-4">
                    <!-- Kalendarz -->
                    <div class="col-md-4 mb-4">
                        <a href="calendar.php" class="text-decoration-none">
                            <div class="card">
                                <div class="card-header p-3">
                                    <p class="text-sm mb-0 text-uppercase font-weight-bold">Kalendarz</p>
                                    <h3 class="font-weight-bolder">Przegląd urlopów</h3>
                                </div>
                                <hr class="dark horizontal my-0">
                                <div class="card-footer p-3">
                                    <p class="mb-0 text-sm text-primary">Otwórz kalendarz</p>
                                </div>
                            </div>
                        </a>
                    </div>
                    <!-- Zgłaszanie urlopu -->
                    <div class="col-md-4 mb-4">
                        <a href="leaveRequest.php" class="text-decoration-none">
                            <div class="card">
                                <div class="card-header p-3">
                                    <p class="text-sm mb-0 text-uppercase font-weight-bold">Zgłaszanie urlopu</p>
                                    <h3 class="font-weight-bolder">Nowy wniosek</h3>
                                </div>
                                <hr class="dark horizontal my-0">
                                <div class="card-footer p-3">
                                    <p class="mb-0 text-sm text-primary">Zgłoś urlop</p>
                                </div>
                            </div>
                        </a>
                    </div>
                    <!-- Status urlopów -->
                    <div class="col-md-4 mb-4">
                        <a href="leaveStatus.php" class="text-decoration-none">
                            <div class="card">
                                <div class="card-header p-3">
                                    <p class="text-sm mb-0 text-uppercase font-weight-bold">Status urlopów</p>
                                    <h3 class="font-weight-bolder">Sprawdź status</h3>
                                </div>
                                <hr class="dark horizontal my-0">
                                <div class="card-footer p-3">
                                    <p class="mb-0 text-sm text-primary">Zobacz szczegóły</p>
                                </div>
                            </div>
                        </a>
                    </div>
                </div>
                <?php require_once '../views/footer.php' ?>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
