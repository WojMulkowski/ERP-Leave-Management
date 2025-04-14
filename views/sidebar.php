<?php
echo <<<sidebar
<div class="d-flex flex-column flex-shrink-0 p-2 bg-dark text-white vh-100 position-fixed"
     style="width: 250px; left: 0; top: 0; bottom: 0; overflow-y: auto;">
    <h4 class="text-center mt-3">ERP System</h4>
    <hr>
    <ul class="nav nav-pills flex-column mb-auto text-center mx-2">
        <li class="nav-item border rounded-3 border-light mb-3">
            <a href="dashboard.php" class="nav-link text-white">
                <i class="fas fa-home me-2"></i> Panel główny
            </a>
        </li>
        <li class="nav-item border rounded-3 border-light mb-3">
            <a href="calendar.php" class="nav-link text-white">
                <i class="fas fa-calendar-alt me-2"></i> Kalendarz
            </a>
        </li>
        <li class="nav-item border rounded-3 border-light mb-3">
            <a href="leaveRequest.php" class="nav-link text-white">
                <i class="fas fa-file-alt me-2"></i> Zgłaszanie urlopu
            </a>
        </li>
        <li class="nav-item border rounded-3 border-light mb-3">
            <a href="leaveStatus.php" class="nav-link text-white">
                <i class="fas fa-info-circle me-2"></i> Status urlopów
            </a>
        </li>
    </ul>
    <!-- PRZYCISK WYLOGUJ SIĘ -->
    <div class="text-center mx-2 mb-4">
        <a href="../scripts/logout.php" class="btn btn-secondary w-100">
            <i class="fas fa-sign-out-alt me-2"></i> Wyloguj się
        </a>
    </div>
</div>
sidebar;
?>