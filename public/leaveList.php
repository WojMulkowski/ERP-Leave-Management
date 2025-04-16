<?php
session_start();
require_once '../scripts/functions.php';
// Sprawdzenie, czy użytkownik jest zalogowany
if (!isUserLoggedIn() || $_SESSION['logged_user']['role_id'] < 2) {
    header("Location: ../index.php");
    exit;
}
require_once '../scripts/db_connect.php';

$data = getLeavesAndRemainingDays($pdo);

$config = require '../config.php';
$title = $config['site_name'] . ' - Lista urlopów';
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

                <!-- Przyciski drukowania -->
                <div class="container-fluid px-4">
                	<div class="d-flex justify-content-between mb-4">
                		<button class="btn btn-primary" onclick="printTable('leavesTable')">Drukuj listę urlopów</button>
                		<button class="btn btn-secondary" onclick="printTable('remainingDaysTable')">Drukuj zestawienie pozostałych dni urlopowych</button>
                	</div>

                	<!-- Tabela urlopów -->
                	<div class="card mb-4">
                		<div class="card-body">
                			<table id="leavesTable" class="table table-bordered">
                				<thead>
                					<tr>
                						<th>Pracownik</th>
                						<th>Data rozpoczęcia</th>
                						<th>Data zakończenia</th>
                						<th>Status</th>
                					</tr>
                				</thead>
                				<tbody>
                					<?php foreach ($data['leaves'] as $leave): ?>
                						<tr>
                							<th><?php echo $leave['firstname'] . ' ' . $leave['lastname']; ?></th>
                							<th><?php echo date('d-m-Y', strtotime($leave['start_date'])); ?></th>
                							<th><?php echo date('d-m-Y', strtotime($leave['end_date'])); ?></th>
                							<td>
                								<?php
                								$status = $leave['status'];
                								$statusColor = '';
                								switch ($status) {
                									case 'Zatwierdzony':
                										$statusColor = 'green';
                										break;
                									case 'Odrzucony':
                										$statusColor = 'red';
                										break;
                									case 'Oczekujący':
                										$statusColor = 'blue';
                										break;
                								}
                								?>
                								<span style="color: <?php echo $statusColor; ?>"><?php echo $status; ?></span>
                							</td>
                						</tr>
                					<?php endforeach; ?>   
                				</tbody>
                			</table>
                		</div>
                	</div>
                                            
                	<!-- Tabela pozostałych dni urlopowych -->
                	<div class="card mb-4">
                		<div class="card-body">
                			<table id="remainingDaysTable" class="table table-bordered">
                				<thead>
                					<tr>
                						<th>Pracownik</th>
                						<th>Pozostałe dni urlopowe</th>
                					</tr>
                				</thead>
                				<tbody>
                					<?php foreach ($data['remaining_days'] as $row): ?>
                						<tr>
                							<th><?php echo $row['firstname'] . ' ' . $row['lastname']; ?></th>
                							<td><?php echo $row['remaining_days']; ?></td>
                						</tr>
                					<?php endforeach; ?>
                				</tbody>
                			</table>
                		</div>
                	</div>
                </div>
            </div>
        </div>
    </div>
    <!-- Funkcja drukowania -->
    <script>
    function printTable(tableId) {
        const tableContent = document.getElementById(tableId).outerHTML;
        const printWindow = window.open('', '', 'width=800,height=600');
        printWindow.document.write(`
            <html>
                <head>
                    <title>Drukuj tabelę</title>
                    <style>
                        table {
                            border-collapse: collapse;
                            width: 100%;
                        }
                        table, th, td {
                            border: 1px solid black;
                            padding: 8px;
                            text-align: left;
                        }
                        th {
                            background-color: #f2f2f2;
                        }
                    </style>
                </head>
                <body>${tableContent}</body>
            </html>
        `);
        printWindow.document.close();
        printWindow.focus();
        printWindow.print();
        printWindow.close();
    }
    </script>
</body>
</html>