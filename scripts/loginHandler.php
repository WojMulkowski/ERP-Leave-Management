<?php
session_start();
require_once 'db_connect.php';
require_once 'functions.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit'])) {
    $email = sanitizeInput($_POST['email']);
    $password = sanitizeInput($_POST['password']);

    if (empty($email) || empty($password)) {
        $_SESSION['error'] = "Wprowadź email i hasło";
        header('Location: ../public/index.php');
        exit();
    }

    try {
        $stmt = $pdo->prepare("SELECT * FROM users WHERE = email = :email");
        $stmt->execute(['email' => $email]);
        $user = $stmt->fetch();

        if ($user && password_verify($password, $user['password'])) {
            $SESSION['user_id'] = $user['id'];
            $SESSION['user_name'] = $user['firstname'];
            $SESSION['user_role'] = $user['role'];
            header('Location: ../public/dashboard.php');
            exit();
        }
    } catch (PDOException $e) {
        echo "Błąd: " . $e->getMessage();
    }
} else {
    header('Location: ../public/index.php');
}
?>