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
$title = $config['site_name'] . ' - Lista użytkowników';
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

                <!-- AddUser button -->
                <div class="card mb-4">
                	<div class="card-body">
                		Dodaj nowego użytkownika:
                		<a class="btn btn-success" href="userModeration.php">Nowy użytkownik</a>
                	</div>
                </div>
                            
                <!-- Table -->
                <div class="card mb-4">
                	<div class="card-header">
                		<i class="fas fa-table me-1"></i>
                		Lista użytkowników
                	</div>
                	<div class="card-body">
                		<table data-ordering="false" id="datatablesSimple" class="table table-striped" style="width:100%">
                			<thead>
                				<tr>
                					<th>Lp.</th>
                					<th>Imie</th>
                					<th>Nazwisko</th>
                					<th>Email</th>
                					<th>Płeć</th>
                					<th>Data urodzenia</th>
                					<th>Poziom uprawnień</th>
                					<th>Data zatrudnienia</th>
                					<th>Edycja</th>
                					<th>Usuwanie</th>
                				</tr>
                			</thead>
                			<tbody>
                				<?php
                				$stmt = $pdo->prepare("SELECT users.id, firstname, lastname, email, gender, birth_date, role_id, employed_from FROM users INNER JOIN roles ON users.role_id = roles.id ORDER BY users.id");
                				$stmt->execute();
                				$result = $stmt->fetchAll(PDO::FETCH_ASSOC);
                				$i = 1;
                				foreach ($result as $val) {
                					$gender = $val["gender"] == "male" ? "Mężczyzna" : "Kobieta";
                					$level = match ($val["role_id"]) {
                						1 => "Użytkownik",
                						2 => "Moderator",
                						default => "Administrator",
                					};
                					echo <<<dane
                					<tr>
                						<td>$i</td>
                						<td>$val[firstname]</td>
                						<td>$val[lastname]</td>
                						<td>$val[email]</td>
                						<td>$gender</td>
                						<td>$val[birth_date]</td>
                						<td>$level</td>
                						<td>$val[employed_from]</td>
                						<td><a class='btn btn-success' href='userModeration.php?id=$val[id]'>Edytuj</a></td>
                						<td><button class='btn btn-danger delete-user-btn' data-user-id='$val[id]'>Usuń</button></td>
                					</tr>
dane;
                					$i++;
                				}
                				?>
                			</tbody>
                		</table>
                	</div>
                </div>
                            
                <!-- Modal do usuwania użytkownika -->
                <div class="modal fade" id="deleteUserModal" tabindex="-1" aria-labelledby="deleteUserModalLabel" aria-hidden="true">
                	<div class="modal-dialog">
                		<div class="modal-content">
                			<div class="modal-header">
                				<h5 class="modal-title" id="deleteUserModalLabel">Potwierdzenie usunięcia</h5>
                				<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Zamknij"></button>
                			</div>
                			<div class="modal-body">
                				Czy na pewno chcesz usunąć tego użytkownika?
                			</div>
                			<div class="modal-footer">
                				<form id="deleteUserForm" method="post" action="../scripts/deleteUser.php">
                					<input type="hidden" name="id" id="deleteUserId">
                					<button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Anuluj</button>
                					<button type="submit" class="btn btn-danger">Usuń</button>
                				</form>
                			</div>
                		</div>
                	</div>
                </div>
                            
                <!-- Obsługa modułu usunięcia -->
                <script>
                	document.querySelectorAll('.delete-user-btn').forEach(button => {
                		button.addEventListener('click', () => {
                			const userId = button.getAttribute('data-user-id');
                			document.getElementById('deleteUserId').value = userId;
                			const deleteModal = new bootstrap.Modal(document.getElementById('deleteUserModal'));
                			deleteModal.show();
                		});
                	});
                </script>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>