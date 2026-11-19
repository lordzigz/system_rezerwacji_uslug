<?php

require_once '../includes/db.php';
require_once '../includes/auth.php';
require_login();

$reservation_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$user_id = $_SESSION['user_id'];


$stmt = $pdo->prepare("SELECT id, status FROM reservations WHERE id = ? AND user_id = ?");
$stmt->execute([$reservation_id, $user_id]);
$reservation = $stmt->fetch();

if ($reservation) {
    
    if ($reservation['status'] === 'oczekujaca' || $reservation['status'] === 'potwierdzona') {
        $update = $pdo->prepare("UPDATE reservations SET status = 'anulowana' WHERE id = ?");
        $update->execute([$reservation_id]);
    }
}

// Wracamy do panelu rezerwacji
header("Location: my_reservations.php");
exit;