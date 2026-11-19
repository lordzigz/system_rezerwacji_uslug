<?php

require_once '../includes/db.php';
require_once '../includes/auth.php';
require_login();


if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_POST['service_id'], $_POST['employee_id'], $_POST['reservation_date'], $_POST['start_time'])) {
    header("Location: index.php");
    exit;
}

$user_id = $_SESSION['user_id'];
$service_id = (int)$_POST['service_id'];
$employee_id = (int)$_POST['employee_id'];
$date = $_POST['reservation_date'];
$start_time = $_POST['start_time'];


$stmt = $pdo->prepare("SELECT duration, price FROM services WHERE id = ?");
$stmt->execute([$service_id]);
$service = $stmt->fetch();

if (!$service) {
    die("Błędna usługa.");
}


$duration = (int)$service['duration'];
$end_time = date('H:i', strtotime($start_time) + ($duration * 60));


$stmt = $pdo->prepare("SELECT COUNT(*) FROM reservations 
                       WHERE employee_id = ? 
                       AND reservation_date = ? 
                       AND status != 'anulowana'
                       AND start_time < ? 
                       AND end_time > ?");
$stmt->execute([$employee_id, $date, $end_time, $start_time]);
$conflict = $stmt->fetchColumn();


if ($conflict > 0) {
    die("Błąd: Ten termin został właśnie zarezerwowany przez kogoś innego. Wróć i wybierz inną godzinę. <br><a href='index.php'>Wróć do oferty</a>");
}


$stmt = $pdo->prepare("INSERT INTO reservations (user_id, employee_id, service_id, reservation_date, start_time, end_time, status) 
                       VALUES (?, ?, ?, ?, ?, ?, 'oczekujaca')");

if ($stmt->execute([$user_id, $employee_id, $service_id, $date, $start_time, $end_time])) {
   
    header("Location: my_reservations.php?success=1");
    exit;
} else {
    die("Wystąpił błąd podczas zapisu rezerwacji.");
}