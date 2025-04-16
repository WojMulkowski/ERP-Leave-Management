<?php
session_start();
require_once 'db_connect.php';
require_once 'functions.php';
require_once 'user.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit'])) {
    $email = sanitizeInput($_POST['email']);
    $password = sanitizeInput($_POST['password']);
    $recaptchaToken = sanitizeInput($_POST['g-recaptcha-response']);

    // Weryfikacja reCAPTCHA
    if (!verifyRecaptcha($recaptchaToken)) {
        $_SESSION['error'] = 'Niepoprawna weryfikacja reCAPTCHA.';
        header("Location: ../index.php");
        exit;
    }

    if (empty($email) || empty($password)) {
        $_SESSION['error'] = "Wprowadź email i hasło";
        header('Location: ../index.php');
        exit();
    }

    try {
        $user = User::findUser($email, $password, $pdo);
        if ($user !== null) {
            User::logInUser($user);
            header('Location: ../public/dashboard.php');
            exit();
        } else {
            $_SESSION['error'] = "Nieprawidłowy email lub hasło";
            header('Location: ../index.php');
            exit();
        }
    } catch (PDOException $e) {
        echo "Błąd: " . $e->getMessage();
    }
} else {
    header('Location: ../index.php');
    exit();
}
?>