<?php

require_once '../../includes/db.php';
require_once '../../includes/auth.php';
require_role('pracownik');

$reservation_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$new_status = $_GET['status'] ?? '';
$employee_id = $_SESSION['user_id'];


$allowed_statuses = ['potwierdzona', 'zrealizowana'];

if (in_array($new_status, $allowed_statuses)) {
   
    $stmt = $pdo->prepare("UPDATE reservations SET status = ? WHERE id = ? AND employee_id = ?");
    $stmt->execute([$new_status, $reservation_id, $employee_id]);
}

header("Location: index.php");
exit;