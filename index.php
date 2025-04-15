<?php 
session_start();
require_once 'scripts/functions.php';
redirectIfLoggedIn();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <title>Document</title>
</head>
<body>
    <div class="d-flex align-items-center justify-content-center vh-100 bg-light">
        <div class="card shadow p-4" style="width: 350px;">
            <h4 class="text-center">Logowanie</h4>
            <hr>
            <form action="scripts/loginHandler.php" method="post">
                <div class="mb-3">
                    <label for="email" class="form-label">Email</label>
                    <input type="text" class="form-control" name="email" id="email" required>
                </div>

                <div class="mb-3">
                    <label for="password" class="form-label">Hasło</label>
                    <input type="password" class="form-control" name="password" id="password" required>
                </div>

                <?php if (isset($_SESSION['error'])): ?>
                    <div class="alert alert-danger p-2 text-center">
                        <?php echo $_SESSION['error']; unset($_SESSION['error']); ?>
                    </div>
                <?php endif; ?>
                
                <button type="submit" name="submit" class="btn btn-primary w-100">Zaloguj się</button>
            </form>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>