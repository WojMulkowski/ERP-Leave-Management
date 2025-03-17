<? session_start(); ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
    <form action="../scripts/loginHandler.php" method="post">
        <label for="form-label-email">Email</label>
        <input type="text" name="email" id="form-label-email" required><br>
        <label for="form-label-password">Hasło</label>
        <input type="text" name="password" id="form-label-password" required><br>
        <?php if (isset($_SESSION['error'])) echo "<p>".$_SESSION['error']."</p>"; ?>
        <button type="submit" name="submit">Zaloguj się</button>
    </form>
</body>
</html>