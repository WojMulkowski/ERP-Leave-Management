<?php
session_start();
require_once '../scripts/functions.php';
// Sprawdzenie, czy użytkownik jest zalogowany
if (!isUserLoggedIn()) {
    header("Location: ../index.php");
    exit;
}
require_once '../scripts/db_connect.php';

// Obsługa wyboru miesiąca i roku
$selectedMonth = isset($_POST['month']) ? $_POST['month'] : date('n');
$selectedYear = isset($_POST['year']) ? $_POST['year'] : date('Y');

// Nazwy miesięcy
$monthNames = getMonthNames();

// Pobranie danych urlopowych
$leavesData = getApprovedLeaves($pdo);

// Pobieranie danych o świętach w Polsce z API
$holidays = fetchPolishHolidays($selectedYear);

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" type="text/css" href="../assets/css/calendar.css">
    <title>Document</title>
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <div class="col-auto position-fixed">
                <?php require_once '../views/sidebar.php'; ?>
            </div>

            <div class="col" style="margin-left: 250px; padding: 20px;">
                <h3 class="font-weight-bold">Kalendarz urlopów</h3>
                <p class="text-sm text-secondary">Wyświetl zatwierdzone dni urlopu dla wybranego miesiąca i roku.</p>

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
                }
                ?>

                <div class="card">
                    <div class="card-body">
                        <form method="POST" class="mb-4">
                            <div class="row">
                                <div class="col-md-6">
                                    <label for="month" class="form-label">Wybierz miesiąc:</label>
                                    <select name="month" id="month" class="form-select" onchange="this.form.submit()">
                                        <?php
                                        foreach ($monthNames as $month => $name) {
                                            $selected = ($month == $selectedMonth) ? 'selected' : '';
                                            echo "<option value='{$month}' {$selected}>{$name}</option>";
                                        }
                                        ?>
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label for="year" class="form-label">Wybierz rok:</label>
                                    <select name="year" id="year" class="form-select" onchange="this.form.submit()">
                                        <?php
                                        for ($year = 2020; $year <= 2030; $year++) {
                                            $selected = ($year == $selectedYear) ? 'selected' : '';
                                            echo "<option value='{$year}' {$selected}>{$year}</option>";
                                        }
                                        ?>
                                    </select>
                                </div>
                            </div>
                        </form>

                        <div class="row">
                            <?php
                            $date = new DateTime();
                            $date->setDate($selectedYear, $selectedMonth, 1);
                            $daysInMonth = $date->format('t');

                            for ($day = 1; $day <= $daysInMonth; $day++) {
                                $currentDate = $date->format('Y-m-d');
                                $isHoliday = array_key_exists($currentDate, $holidays);
                                $holidayName = $isHoliday ? $holidays[$currentDate] : '';
                                $holidayClass = $isHoliday ? 'holiday' : '';

                                echo '<div class="col-md-2">';
                                echo '<div class="day border border-secondary rounded p-2 mb-2 ' . $holidayClass . '">';
                                echo '<div class="fw-bold mb-2">' . $day . '</div>';

                                if ($isHoliday) {
                                    echo '<div class="text-danger fw-bold">' . $holidayName . '</div>';
                                }

                                foreach ($leavesData as $leave) {
                                    if ($date >= $leave['startDate'] && $date <= $leave['endDate']) {
                                        echo '<div class="leave.approved">Urlop</div>';
                                    }
                                }

                                echo '</div>';
                                echo '</div>';

                                $date->modify('+1 day');
                            }
                            ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>