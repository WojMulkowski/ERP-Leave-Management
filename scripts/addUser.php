<?php
session_start();
require_once 'functions.php';

// Sprawdzenie, czy użytkownik jest zalogowany
if (!isUserLoggedIn()) {
    header("Location: ../index.php");
    exit;
}

require_once 'db_connect.php';

$firstName     = sanitizeInput($_POST['firstName']);
$lastName      = sanitizeInput($_POST['lastName']);
$email         = sanitizeInput($_POST['email']);
$password1     = $_POST['password1'];
$password2     = $_POST['password2'];
$gender        = sanitizeInput($_POST['gender']);
$birthDate     = sanitizeInput($_POST['birthDate']);
$permissions   = $_POST['permissions'];
$employedFrom  = sanitizeInput($_POST['employedFrom']);

$errors = [];

if (empty($firstName) || empty($lastName) || empty($email) || empty($password1) || empty($password2) || empty($gender) || empty($birthDate) || empty($permissions) || empty($employedFrom)) {
    $errors[] = "Wszystkie pola muszą być wypełnione.";
}

// Sprawdzenie, czy email jest unikalny
$stmt = $pdo->prepare("SELECT email FROM users WHERE email = :email");
$stmt->bindParam(':email', $email);
$stmt->execute();

if ($stmt->rowCount() > 0) {
    $errors[] = "Użytkownik o podanym adresie email już istnieje.";
}

// Sprawdzenie poprawności adresu email
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $errors[] = "Podaj poprawny adres email.";
}

// Sprawdzenie zgodności haseł
if ($password1 !== $password2) {
    $errors[] = "Hasła nie są identyczne.";
}

// Walidacja hasła
$pattern = "/^(?=.*\d)(?=.*[a-z])(?=.*[A-Z])(?=.*\W).{8,}$/";
if (!preg_match($pattern, $password1)) {
    $errors[] = "Hasło musi mieć co najmniej 8 znaków i zawierać małą literę, dużą literę, liczbę i znak specjalny.";
}

// Jeżeli są błędy — wróć do formularza
if (!empty($errors)) {
    $_SESSION['errors'] = $errors;
    echo "<script>history.back();</script>";
    exit();
}

$hashedPassword = password_hash($password1, PASSWORD_DEFAULT);

$stmt = $pdo->prepare("
    INSERT INTO users (firstname, lastname, email, password, gender, birth_date, employed_from, role_id)
    VALUES (:firstname, :lastname, :email, :password, :gender, :birth_date, :employed_from, :role_id)
");

$stmt->bindParam(':firstname', $firstName);
$stmt->bindParam(':lastname', $lastName);
$stmt->bindParam(':email', $email);
$stmt->bindParam(':password', $hashedPassword);
$stmt->bindParam(':gender', $gender);
$stmt->bindParam(':birth_date', $birthDate);
$stmt->bindParam(':employed_from', $employedFrom);
$stmt->bindParam(':role_id', $permissions, PDO::PARAM_INT);

if ($stmt->execute()) {
    $_SESSION['success'] = "Użytkownik został dodany do bazy danych.";
} else {
    $_SESSION['errors'] = ["Błąd podczas dodawania użytkownika."];
}

header("Location: ../public/userList.php");
exit();
?>