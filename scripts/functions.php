<?php
function sanitizeInput($data) {
    return htmlspecialchars(stripslashes(trim($data)));
}
function isUserLoggedIn(): bool {
    return isset($_SESSION["logged_user"]["id"]);
}
function redirectIfLoggedIn() {
    if (isUserLoggedIn()) {
        header('Location: public/dashboard.php');
        exit();
    }
}
function calculateRemainingVacationDays($pdo) {
    // Pobierz dane użytkownika z sesji
    $userId = $_SESSION['logged_user']['id'];
    $employedFrom = $_SESSION['logged_user']['employed_from'];

    // Obliczanie stażu pracy użytkownika
    $employmentStartDate = new DateTime($employedFrom);
    $currentDate = new DateTime();

    // Oblicz całkowitą liczbę dni urlopowych w zależności od stażu pracy
    $employmentDuration = $employmentStartDate->diff($currentDate);
    $totalVacationDays = $employmentDuration->y >= 10 ? 26 : 20;

    // Pobierz wykorzystane dni urlopowe w bieżącym roku
    $currentYear = $currentDate->format('Y');
    $query = "
        SELECT SUM(days_count) AS used_vacation_days 
        FROM leaves 
        WHERE user_id = :user_id 
          AND (status = 'Zatwierdzony' OR status = 'Oczekujący') 
          AND YEAR(start_date) = :current_year";
    
    // Przygotowanie zapytania i wykonanie go
    $stmt = $pdo->prepare($query);
    $stmt->bindParam(':user_id', $userId, PDO::PARAM_INT);
    $stmt->bindParam(':current_year', $currentYear, PDO::PARAM_INT);
    $stmt->execute();
    
    $usedVacationDays = 0;
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($result && isset($result['used_vacation_days'])) {
        $usedVacationDays = (int)$result['used_vacation_days'];
    }

    // Oblicz pozostałe dni urlopowe
    $remainingVacationDays = max($totalVacationDays - $usedVacationDays, 0);

    return $remainingVacationDays;
}
function getUsedVacationDays($pdo) {
    // Pobranie user_id z sesji
    $userId = $_SESSION['logged_user']['id'];

    // Zapytanie do bazy o wykorzystane dni urlopowe
    $query = "
        SELECT SUM(days_count) AS used_days
        FROM leaves
        WHERE user_id = :user_id AND status = 'Zatwierdzony'
    ";

    // Przygotowanie zapytania
    $stmt = $pdo->prepare($query);
    $stmt->bindParam(':user_id', $userId, PDO::PARAM_INT);
    $stmt->execute();

    // Pobranie wyniku
    $usedDays = 0;
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($result && isset($result['used_days'])) {
        $usedDays = (int)$result['used_days'];
    }

    return $usedDays;
}
function getPendingRequestsCount($pdo) {
    // Pobranie user_id z sesji
    $userId = $_SESSION['logged_user']['id'];

    // Zapytanie do bazy o liczbę oczekujących wniosków
    $query = "
        SELECT COUNT(*) AS pending_requests
        FROM leaves
        WHERE user_id = :user_id AND status = 'Oczekujący'
    ";

    // Przygotowanie zapytania
    $stmt = $pdo->prepare($query);
    $stmt->bindParam(':user_id', $userId, PDO::PARAM_INT);
    $stmt->execute();

    // Pobranie wyniku
    $pendingRequests = 0;
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($result && isset($result['pending_requests'])) {
        $pendingRequests = (int)$result['pending_requests'];
    }

    return $pendingRequests;
}
function getMonthNames(): array {
    return [
        1 => 'Styczeń', 2 => 'Luty', 3 => 'Marzec', 4 => 'Kwiecień',
        5 => 'Maj', 6 => 'Czerwiec', 7 => 'Lipiec', 8 => 'Sierpień',
        9 => 'Wrzesień', 10 => 'Październik', 11 => 'Listopad', 12 => 'Grudzień',
    ];
}
function fetchPolishHolidays(int $selectedYear): array {
    $url = "https://date.nager.at/api/v3/PublicHolidays/{$selectedYear}/PL";
    $response = @file_get_contents($url);

    $holidays = [];
    if ($response !== false) {
        $data = json_decode($response, true);
        foreach ($data as $holiday) {
            $holidays[$holiday['date']] = $holiday['localName'];
        }
    }
    return $holidays;
}
function getApprovedLeaves($pdo) {
    // Pobranie danych urlopowych
    $userId = $_SESSION['logged_user']['id'];
    $query = "SELECT start_date, end_date FROM leaves WHERE user_id = :user_id AND status = 'Zatwierdzony'";
    $stmt = $pdo->prepare($query);
    $stmt->bindParam(':user_id', $userId);
    $stmt->execute();
    $leavesData = array();

    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $leavesData[] = array(
            'startDate' => new DateTime($row['start_date']),
            'endDate' => new DateTime($row['end_date'])
        );
    }
    return $leavesData;
}
function getUserLeaveRequests($pdo) {
    $userId = $_SESSION['logged_user']['id'];
    $query = "SELECT l.id, l.start_date, l.end_date, l.status, l.notes, u.firstname, u.lastname
              FROM leaves l
              INNER JOIN users u ON l.user_id = u.id
              WHERE u.id = :user_id";
              
    $stmt = $pdo->prepare($query);
    $stmt->bindParam(':user_id', $userId);
    $stmt->execute();
    $leavesData = array();
    $leavesData = $stmt->fetchAll(PDO::FETCH_ASSOC);
    return $leavesData;
}
?>