<?php
echo <<<sidebar
<div class="d-flex flex-column flex-shrink-0 p-2 bg-dark text-white vh-100 position-fixed"
     style="width: 250px; left: 0; top: 0; bottom: 0; overflow-y: auto;">
    <h3 class="text-center mt-3">ERP System</h3>
    <hr>
    <ul class="nav nav-pills flex-column mb-auto text-center mx-3">
sidebar;
?>
        <!-- Sekcja widoczna dla użytkowników -->
        <li class="nav-item border rounded-3 border-light mb-3">
            <a class="nav-link <?= isActivePage('dashboard.php'); ?>" href="dashboard.php" class="nav-link text-white">
                <i class="fas fa-home me-2"></i> Panel główny
            </a>
        </li>
        <li class="nav-item border rounded-3 border-light mb-3">
            <a class="nav-link <?= isActivePage('calendar.php'); ?>" href="calendar.php" class="nav-link text-white">
                <i class="fas fa-calendar-alt me-2"></i> Kalendarz
            </a>
        </li>
        <li class="nav-item border rounded-3 border-light mb-3">
            <a class="nav-link <?= isActivePage('leaveRequest.php'); ?>" href="leaveRequest.php" class="nav-link text-white">
                <i class="fas fa-file-alt me-2"></i> Zgłaszanie urlopu
            </a>
        </li>
        <li class="nav-item border rounded-3 border-light mb-3">
            <a class="nav-link <?= isActivePage('leaveStatus.php'); ?>" href="leaveStatus.php" class="nav-link text-white">
                <i class="fas fa-info-circle me-2"></i> Status urlopów
            </a>
        </li>

        <!-- Sekcja widoczna dla moderatorów -->
        <?php if (hasPermission(2)): ?>
        <li class="nav-item mt-2 text-center">
          <h6 class="text-uppercase text-xs text-white font-weight-bolder opacity-5">Moderacje</h6><hr class="m-0 p-2">
        </li>
        <li class="nav-item border rounded-3 border-light mb-3">
            <a class="nav-link <?= isActivePage('leaveModeration.php'); ?>" href="leaveModeration.php" class="nav-link text-white">
                <i class="fas fa-info-circle me-2"></i> Moderacja urlopów
            </a>
        </li>
        <li class="nav-item border rounded-3 border-light mb-3">
            <a class="nav-link <?= isActivePage('leaveList.php'); ?>" href="leaveList.php" class="nav-link text-white">
                <i class="fas fa-info-circle me-2"></i> Lista urlopów
            </a>
        </li>
        <li class="nav-item border rounded-3 border-light mb-3">
            <a class="nav-link <?= isActivePage('userList.php'); ?>" href="userList.php" class="nav-link text-white">
                <i class="fas fa-info-circle me-2"></i> Lista użytkowników
            </a>
        </li>
        <li class="nav-item border rounded-3 border-light mb-3">
            <a class="nav-link <?= isActivePage('userModeration.php'); ?>" href="userModeration.php" class="nav-link text-white">
                <i class="fas fa-info-circle me-2"></i> Dodaj użytkownika
            </a>
        </li>
        <?php endif; ?>

        <!-- Sekcja widoczna dla administratorów -->
        <?php if (hasPermission(3)): ?>
        <li class="nav-item mt-2 text-center">
          <h6 class="text-uppercase text-xs text-white font-weight-bolder opacity-5">Administracja</h6><hr class="m-0 p-2">
        </li>
        <li class="nav-item border rounded-3 border-light mb-3">
            <a class="nav-link <?= isActivePage('configSettings.php'); ?>" href="configSettings.php" class="nav-link text-white">
                <i class="fas fa-info-circle me-2"></i> Konfiguracja strony
            </a>
        </li>
        <li class="nav-item border rounded-3 border-light mb-3">
            <a class="nav-link <?= isActivePage('smtpSettings.php'); ?>" href="smtpSettings.php" class="nav-link text-white">
                <i class="fas fa-info-circle me-2"></i> Ustawienia SMTP
            </a>
        </li>
        <li class="nav-item border rounded-3 border-light mb-3">
            <a class="nav-link <?= isActivePage('backupDatabase.php'); ?>" href="backupDatabase.php" class="nav-link text-white">
                <i class="fas fa-info-circle me-2"></i> Backup bazy danych
            </a>
        </li>
        <?php endif; ?>

    </ul>
    <!-- PRZYCISK WYLOGUJ SIĘ -->
    <div class="text-center mx-3 mb-4">
        <a href="../scripts/logout.php" class="btn btn-secondary w-100">
            <i class="fas fa-sign-out-alt me-2"></i> Wyloguj się
        </a>
    </div>
</div>