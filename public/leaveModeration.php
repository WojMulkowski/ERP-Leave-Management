<?php
session_start();
require_once '../scripts/functions.php';
// Sprawdzenie, czy użytkownik jest zalogowany
if (!isUserLoggedIn() || $_SESSION['logged_user']['role_id'] < 2) {
    header("Location: ../index.php");
    exit;
}
require_once '../scripts/db_connect.php';

// Oczekujące urlopy
$pendingLeaves = getPendingLeaveRequests($pdo);

$config = require '../config.php';
$title = $config['site_name'] . ' - Moderacja urlopów';
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

                <!-- Tabela -->
                <div class="container-fluid px-4">
                	<div class="card mb-4">
                		<div class="card-body">
                			<table class="table table-bordered">
                				<thead>
                					<tr>
                						<th>Pracownik</th>
                						<th>Data rozpoczęcia</th>
                						<th>Data zakończenia</th>
                						<th>Akcje</th>
                					</tr>
                				</thead>
                				<tbody>
                					<?php foreach ($pendingLeaves as $leave): ?>
                						<tr>
                							<th><?php echo $leave['firstname'] . ' ' . $leave['lastname']; ?></th>
                							<th><?php echo date('d-m-Y', strtotime($leave['start_date'])); ?></th>
                							<th><?php echo date('d-m-Y', strtotime($leave['end_date'])); ?></th>
                							<td>
                								<form action="../scripts/leaveAction.php" method="post">
                									<input type="hidden" name="leave_id" value="<?php echo $leave['id']; ?>">
                									<button type="submit" name="action" value="approve" class="btn btn-success">Zatwierdź</button>
                									<button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#rejectModal" onclick="setRejectLeaveId(<?php echo $leave['id']; ?>)">Odrzuć</button>
                								</form>
                							</td>
                						</tr>
                					<?php endforeach; ?>   
                				</tbody>
                			</table>
                		</div>
                	</div>
                </div>
                                    
                <!-- Modal do wprowadzania uwagi -->
                <div class="modal fade" id="rejectModal" tabindex="-1" aria-labelledby="rejectModalLabel" aria-hidden="true">
                	<div class="modal-dialog">
                		<div class="modal-content">
                			<form action="../scripts/leaveAction.php" method="post" id="rejectForm">
                				<div class="modal-header">
                					<h5 class="modal-title" id="rejectModalLabel">Odrzuć wniosek urlopowy</h5>
                					<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                				</div>
                				<div class="modal-body">
                					<input type="hidden" name="leave_id" id="leaveIdInput">
                					<div class="mb-3">
                						<label for="rejectionNote" class="form-label">Podaj uwagi</label>
                						<textarea class="form-control" id="rejectionNote" name="rejection_note" rows="3" required></textarea>
                					</div>
                				</div>
                				<div class="modal-footer">
                					<button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Anuluj</button>
                					<button type="submit" name="action" value="reject" class="btn btn-danger">Odrzuć</button>
                				</div>
                			</form>
                		</div>
                	</div>
                </div>
            </div>
        </div>
    </div>
    <script>
    function setRejectLeaveId(leaveId) {
        document.getElementById('leaveIdInput').value = leaveId;
    }
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>