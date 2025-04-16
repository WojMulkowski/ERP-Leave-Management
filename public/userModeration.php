<?php
session_start();
require_once '../scripts/functions.php';
// Sprawdzenie, czy użytkownik jest zalogowany
if (!isUserLoggedIn() || $_SESSION['logged_user']['role_id'] < 2) {
    header("Location: ../index.php");
    exit;
}
require_once '../scripts/db_connect.php';
$config = require '../config.php';
$title = $config['site_name'] . ' - Moderacja użytkowników';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="icon" type="image/png" sizes="16x16" href="<?php echo $config['favicon']; ?>">
    <title><?php echo $title; ?></title>
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

                <?php
                if (isset($_SESSION['errors']) && is_array($_SESSION['errors'])) {
                    echo '<div class="alert alert-danger">';
                    foreach ($_SESSION['errors'] as $error) {
                        echo "<p>$error</p>";
                    }
                    echo '</div>';
                    unset($_SESSION['errors']);
                }
            
                if (isset($_SESSION['success']) && is_array($_SESSION['success'])) {
                    echo '<div class="alert alert-success">';
                    foreach ($_SESSION['success'] as $success) {
                        echo "<p>$success</p>";
                    }
                    echo '</div>';
                    unset($_SESSION['success']);
                }
                ?>

                <div class="card mb-4">
                  <div class="card-body">
                      <form method="post" action="../scripts/addUser.php">
                          <!-- Dane osobowe -->
                          <h5 class="text-secondary mb-3">Dane osobowe</h5>
                          <div class="row mb-3">
                              <div class="col-md-6">
                                  <div class="form-floating mb-3">
                                      <input class="form-control" id="inputFirstName" type="text" name="firstName" placeholder="Wpisz imię" />
                                      <label for="inputFirstName">Imię</label>
                                  </div>
                              </div>
                              <div class="col-md-6">
                                  <div class="form-floating mb-3">
                                      <input class="form-control" id="inputLastName" type="text" name="lastName" placeholder="Wpisz nazwisko" />
                                      <label for="inputLastName">Nazwisko</label>
                                  </div>
                              </div>
                              <div class="col-md-12">
                                  <div class="form-floating mb-3">
                                      <input class="form-control" id="inputEmail" type="email" name="email" placeholder="name@example.com" />
                                      <label for="inputEmail">Adres e-mail</label>
                                  </div>
                              </div>
                          </div>

                          <!-- Ustawienia konta -->
                          <h5 class="text-secondary mb-3">Ustawienia konta</h5>
                          <div id="passwordFields">
                              <div class="row mb-3">
                                  <div class="col-md-6">
                                      <div class="form-floating">
                                          <input class="form-control" id="inputPassword" type="password" name="password1" placeholder="Hasło" />
                                          <label for="inputPassword">Hasło</label>
                                      </div>
                                  </div>
                                  <div class="col-md-6">
                                      <div class="form-floating">
                                          <input class="form-control" id="inputPasswordConfirm" type="password" name="password2" placeholder="Potwierdź hasło" />
                                          <label for="inputPasswordConfirm">Powtórz hasło</label>
                                      </div>
                                  </div>
                              </div>
                              <div class="row mb-3">
                                  <div class="col-md-6">
                                      <div class="form-floating">
                                          <input class="form-control" id="generatedPassword" type="text" readonly />
                                          <label for="generatedPassword">Wygenerowane hasło</label>
                                      </div>
                                  </div>
                                  <div class="col-md-6 d-flex align-items-center">
                                      <button class="btn btn-secondary" type="button" onclick="generatePassword()">Generuj hasło</button>
                                  </div>
                              </div>
                          </div>

                          <!-- Informacje dodatkowe -->
                          <h5 class="text-secondary mb-3">Informacje dodatkowe</h5>
                          <div class="row mb-3">
                              <div class="col-md-6">
                                  <div class="form-floating mb-3">
                                      <select class="form-control" id="inputGender" name="gender">
                                          <option value="male">Mężczyzna</option>
                                          <option value="female">Kobieta</option>
                                      </select>
                                      <label for="inputGender">Płeć</label>
                                  </div>
                              </div>
                              <div class="col-md-6">
                                  <div class="form-floating mb-3">
                                      <input class="form-control" id="inputBirthDate" name="birthDate" type="date" />
                                      <label for="inputBirthDate">Data urodzenia</label>
                                  </div>
                              </div>
                              <div class="col-md-6">
                                  <div class="form-floating mb-3">
                                      <select class="form-control" id="inputPermissions" name="permissions">
                                          <option value="1">Użytkownik</option>
                                          <option value="2">Moderator</option>
                                          <option value="3">Administrator</option>
                                      </select>
                                      <label for="inputPermissions">Uprawnienia</label>
                                  </div>
                              </div>
                              <div class="col-md-6">
                                  <div class="form-floating mb-3">
                                      <input class="form-control" id="inputEmployee" name="employedFrom" type="date" />
                                      <label for="inputEmployee">Data zatrudnienia</label>
                                  </div>
                              </div>
                          </div>

                          <!-- Przycisk -->
                          <div class="mt-4 mb-0">
                              <div class="d-grid">
                                  <button class="btn btn-primary btn-block" type="submit">Dodaj użytkownika</button>
                              </div>
                          </div>
                      </form>
                  </div>
                </div>
            </div>
        </div>
    </div>
    <script>
    function generatePassword() {
        var passwordLength = 8;
        var lowercaseChars = "abcdefghijklmnopqrstuvwxyz";
        var uppercaseChars = "ABCDEFGHIJKLMNOPQRSTUVWXYZ";
        var specialChars = "!@#$%^&*()";
        var numberChars = "0123456789";

        var validChars = lowercaseChars + uppercaseChars + specialChars + numberChars;
        var password = "";

        var hasSpecialChar = false;
        var hasNumber = false;

        // Dodawanie losowych znaków do hasła
        for (var i = 0; i < passwordLength; i++) {
            var randomIndex = Math.floor(Math.random() * validChars.length);
            var randomChar = validChars[randomIndex];

            password += randomChar;

            // Sprawdzenie, czy dodano znak specjalny
            if (specialChars.includes(randomChar)) {
                hasSpecialChar = true;
            }

            // Sprawdzenie, czy dodano cyfrę
            if (numberChars.includes(randomChar)) {
                hasNumber = true;
            }
        }

        // Sprawdzenie, czy hasło zawiera zarówno znak specjalny, jak i cyfrę
        if (!hasSpecialChar || !hasNumber) {
            // Jeśli brakuje znaku specjalnego lub cyfry, generujemy hasło ponownie
            return generatePassword();
        }

        var generatedPasswordInput = document.getElementById("generatedPassword");
        var inputPassword = document.getElementById("inputPassword");
        var inputPasswordConfirm = document.getElementById("inputPasswordConfirm");

        generatedPasswordInput.value = password;
        inputPassword.value = password;
        inputPasswordConfirm.value = password;
        }
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>