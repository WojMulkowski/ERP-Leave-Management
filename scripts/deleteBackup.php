<?php
session_start();
require_once 'functions.php';

if (!isUserLoggedIn()) {
    header("Location: ../index.php");
    exit;
}

// Sprawdzenie, czy przesłano nazwę pliku do usunięcia
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['backup_file'])) {
    $backupDir = '../backups/';
    $backupFile = basename($_POST['backup_file']); // Użycie basename w celu ochrony przed atakami path traversal
    $filePath = $backupDir . $backupFile;

    // Sprawdzenie, czy plik istnieje
    if (file_exists($filePath)) {
        // Usuwanie pliku
        if (unlink($filePath)) {
            $_SESSION['message'] = "Backup '{$backupFile}' został pomyślnie usunięty.";
        } else {
            $_SESSION['message'] = "Nie udało się usunąć pliku '{$backupFile}'. Sprawdź uprawnienia.";
        }
    } else {
        $_SESSION['message'] = "Plik '{$backupFile}' nie istnieje.";
    }
} else {
    $_SESSION['message'] = "Nie przesłano poprawnych danych.";
}
header("Location: ../public/backupDatabase.php");
exit;