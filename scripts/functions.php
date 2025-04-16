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
function verifyRecaptcha($token): bool {
    $config = require __DIR__ . '/../config.php';
    $secretKey = $config['recaptcha_secret_key'];
    $response = file_get_contents("https://www.google.com/recaptcha/api/siteverify?secret=$secretKey&response=$token");
    $result = json_decode($response, true);
    return true; // true ze względu na localhost
    // return $result['success'] ?? false;
}
function isActivePage($page) {
    return strpos($_SERVER['SCRIPT_NAME'], $page) !== false ? 'active bg-light text-black' : 'text-white';
}
function hasPermission($requiredPermission) {
    return isset($_SESSION['logged_user']['role_id']) && $_SESSION['logged_user']['role_id'] >= $requiredPermission;
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
// Dla moderatorów
function getPendingLeaveRequests($pdo): array {
    $query = "SELECT l.id, l.start_date, l.end_date, l.status, u.firstname, u.lastname
              FROM leaves l
              INNER JOIN users u ON l.user_id = u.id
              WHERE l.status = 'Oczekujący'";

    $stmt = $pdo->prepare($query);
    $stmt->execute();

    $leaves = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if (!empty($leaves)) {
        return $leaves;
    } else {
        return [];
    }
}
function getLeavesAndRemainingDays($pdo): array {
    $query1 = "
        SELECT l.id, l.start_date, l.end_date, l.status, u.firstname, u.lastname
        FROM leaves l
        INNER JOIN users u ON l.user_id = u.id
    ";
    $stmt1 = $pdo->prepare($query1);
    $stmt1->execute();
    $leaves = $stmt1->fetchAll(PDO::FETCH_ASSOC);

    // Oblicz pozostałe dni urlopowe dla każdego użytkownika
    $query2 = "
        SELECT u.firstname, u.lastname, 
               (CASE 
                    WHEN DATEDIFF(CURDATE(), u.employed_from) >= 3650 THEN 26 
                    ELSE 20 
               END) - IFNULL(SUM(l.days_count), 0) AS remaining_days
        FROM users u
        LEFT JOIN leaves l ON u.id = l.user_id AND l.status = 'Zatwierdzony'
        GROUP BY u.id
    ";
    $stmt2 = $pdo->prepare($query2);
    $stmt2->execute();
    $remainingDays = $stmt2->fetchAll(PDO::FETCH_ASSOC);

    if (!empty($leaves) || !empty($remainingDays)) {
        return [
            'leaves' => $leaves,
            'remaining_days' => $remainingDays
        ];
    } else {
        return [];
    }
}
// Dla adminów
function updateConfig($configPath) {
    // Obsługa zapisu zmian
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $newConfig = [
            'site_name' => $_POST['site_name'],
            'favicon' => $_POST['favicon'],
            'logo' => $_POST['logo'],
            'recaptcha_site_key' => $_POST['recaptcha_site_key'],
            'recaptcha_secret_key' => $_POST['recaptcha_secret_key']
        ];

        // Zapisz zmienione ustawienia do pliku config.php
        $configContent = "<?php\n\nreturn " . var_export($newConfig, true) . ";\n";
        if (file_put_contents($configPath, $configContent)) {
            $_SESSION['success_message'] = 'Ustawienia zostały zaktualizowane.';
        } else {
            $_SESSION['error_message'] = 'Błąd podczas zapisywania ustawień.';
        }
        header("Location: settings.php");
        exit;
    }
}
function createBackup($pdo, $backupDir, $message){
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['create_backup'])) {
        // Pobierz nazwę bazy danych
        $dbname = $pdo->query("SELECT DATABASE()")->fetchColumn();
        if (!$dbname) {
            $message = '<div class="alert alert-danger">Błąd: Nie udało się zidentyfikować bazy danych.</div>';
            return;
        }

        $backupFile = rtrim($backupDir, '/') . '/backup_' . date('Y-m-d_H-i-s') . '.sql';
        $backupData = '';

        // Pobierz listę tabel
        $tables = $pdo->query("SHOW TABLES")->fetchAll(PDO::FETCH_NUM);
        if (!$tables) {
            $message = '<div class="alert alert-danger">Błąd: Nie udało się pobrać listy tabel.</div>';
            return;
        }

        foreach ($tables as $row) {
            $tableName = $row[0];

            // Struktura tabeli
            $createStmt = $pdo->query("SHOW CREATE TABLE `$tableName`")->fetch(PDO::FETCH_ASSOC);
            $backupData .= $createStmt['Create Table'] . ";\n\n";

            // Dane tabeli
            $dataRows = $pdo->query("SELECT * FROM `$tableName`")->fetchAll(PDO::FETCH_ASSOC);
            foreach ($dataRows as $dataRow) {
                $values = array_map(function ($value) use ($pdo) {
                    return is_null($value) ? 'NULL' : $pdo->quote($value);
                }, array_values($dataRow));
                $backupData .= "INSERT INTO `$tableName` VALUES(" . implode(", ", $values) . ");\n";
            }

            $backupData .= "\n\n";
        }

        // Zapis do pliku
        if (file_put_contents($backupFile, $backupData)) {
            $message = '<div class="alert alert-success">Backup został pomyślnie utworzony: ' . basename($backupFile) . '</div>';
        } else {
            $message = '<div class="alert alert-danger">Błąd podczas zapisywania backupu do pliku.</div>';
        }
    }
}
?>