<?php
session_start();
require_once 'functions.php';
if (!isUserLoggedIn()) {
    header('Location: ../index.php');
    exit;
}
require_once 'db_connect.php';

$userId = $_SESSION['logged_user']['id'];

// Pobierz pozostałe dni urlopowe
$remainingVacationDays = calculateRemainingVacationDays($pdo);

// Pobierz święta z API
$holidays = fetchPolishHolidays(date('Y'));

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['startDate']) && isset($_POST['endDate'])) {
    $startDate = $_POST['startDate'];
    $endDate = $_POST['endDate'];
    $status = 'Oczekujący';

    // Walidacja dat
    $currentDate = date('Y-m-d');
    if ($startDate < $currentDate || $startDate > $endDate) {
        $_SESSION['errors'] = ['Błąd: Nieprawidłowe daty urlopu.'];
        header('Location: ../public/leaveRequest.php');
        exit();
    }

    // Oblicz liczbę dni roboczych niebędących świętami
    $start = new DateTime($startDate);
    $end = new DateTime($endDate);
    $numberOfDays = 0;
    while ($start <= $end) {
        $currentDay = $start->format('Y-m-d');
        $dayOfWeek = $start->format('N');

        if ($dayOfWeek < 6 && !in_array($currentDay, $holidays)) {
            $numberOfDays++;
        }

        $start->modify('+1 day');
    }
    if ($numberOfDays > $remainingVacationDays) {
        $_SESSION['errors'] = ['Błąd: Niewystarczająca liczba dni urlopowych.'];
        header('Location: ../public/leaveRequest.php');
        exit();
    }

    // Sprawdź, czy urlop nie koliduje z istniejącymi
    $query = "
        SELECT * FROM leaves 
        WHERE user_id = :user_id AND status != 'Odrzucony'
        AND (
            (start_date <= :startDate1 AND end_date >= :startDate2) OR 
            (start_date >= :startDate3 AND end_date <= :endDate1) OR 
            (start_date <= :endDate2 AND end_date >= :endDate3)
        )
    ";
    $stmt = $pdo->prepare($query);
    $stmt->execute([
        ':user_id' => $userId,
        ':startDate1' => $startDate,
        ':startDate2' => $startDate,
        ':startDate3' => $startDate,
        ':endDate1' => $endDate,
        ':endDate2' => $endDate,
        ':endDate3' => $endDate,
    ]);
    if ($stmt->rowCount() > 0) {
        $_SESSION['errors'] = ['Błąd: Konflikt z istniejącym wnioskiem urlopowym.'];
        header('Location: ../public/leaveRequest.php');
        exit();
    }

    // Dodaj nowy wniosek urlopowy
    $insertQuery = "
        INSERT INTO leaves (user_id, start_date, end_date, status, days_count) 
        VALUES (:user_id, :start_date, :end_date, :status, :days_count)
    ";
    $insertStmt = $pdo->prepare($insertQuery);
    $success = $insertStmt->execute([
        ':user_id' => $userId,
        ':start_date' => $startDate,
        ':end_date' => $endDate,
        ':status' => $status,
        ':days_count' => $numberOfDays
    ]);

    if ($success) {
        $_SESSION['success'] = ['Wniosek urlopowy został dodany pomyślnie.'];
        // sendLeaveNotification($pdo, $user_id, $startDate, $endDate);
    } else {
        $_SESSION['errors'] = ['Błąd: Nie udało się dodać wniosku urlopowego.'];
    }
    header('Location: ../public/leaveRequest.php');
    exit();

} else {
    $_SESSION['errors'] = ['Błąd: Nieprawidłowe dane.'];
    header('Location: ../public/leaveRequest.php');
    exit();
}
?>
