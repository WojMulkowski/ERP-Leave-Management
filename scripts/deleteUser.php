<?php
session_start();
require_once 'functions.php';
if (!isUserLoggedIn()) {
    header("Location: ../index.php");
    exit;
}
require_once 'db_connect.php';

if (!isset($_POST['id']) || empty($_POST['id'])) {
    $_SESSION['errors'] = ["Nieprawidłowe żądanie. Brak ID użytkownika."];
    header("Location: ../public/userList.php");
    exit();
}
// Rzutowanie na int
$userId = (int)$_POST['id'];

// Sprawdzenie, czy użytkownik próbuje usunąć samego siebie
if ($_SESSION['logged_user']['id'] === $userId) {
    $_SESSION['errors'] = ["Nie możesz usunąć swojego własnego konta."];
    header("Location: ../public/userList.php");
    exit();
}

$stmt = $pdo->prepare("SELECT role_id FROM users WHERE id = :id");
$stmt->bindParam(':id', $userId, PDO::PARAM_INT);
$stmt->execute();
$userPermissions = $stmt->fetchColumn();

// Sprawdzenie, czy użytkownik próbuje usunąć osobę o wyższych lub równych uprawnieniach
if ($_SESSION['logged_user']['role_id'] <= $userPermissions) {
    $_SESSION['errors'] = ["Nie masz uprawnień do usunięcia tego użytkownika."];
    header("Location: ../public/userList.php");
    exit();
}

// Usunięcie użytkownika z bazy danych
$stmt = $pdo->prepare("DELETE FROM users WHERE id = :id");
$stmt->bindParam(':id', $userId, PDO::PARAM_INT);

if ($stmt->execute()) {
    $_SESSION['success'] = "Użytkownik został pomyślnie usunięty.";
} else {
    $_SESSION['errors'] = ["Nie udało się usunąć użytkownika."];
}
header("Location: ../public/userList.php");
exit();
?>