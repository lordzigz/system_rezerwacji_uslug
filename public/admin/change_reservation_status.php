<?php

require_once '../../includes/db.php';
require_once '../../includes/auth.php';

require_role('admin');

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id'], $_POST['status'])) {
    $id = (int)$_POST['id'];
    $status = $_POST['status'];
    
    $allowed = ['oczekujaca', 'potwierdzona', 'zrealizowana', 'anulowana'];
    
    if (in_array($status, $allowed)) {
        $stmt = $pdo->prepare("UPDATE reservations SET status = ? WHERE id = ?");
        $stmt->execute([$status, $id]);
    }
}


$redirect_url = 'reservations.php';
if (!empty($_SERVER['HTTP_REFERER'])) {

    $referer = parse_url($_SERVER['HTTP_REFERER'], PHP_URL_QUERY);
    if ($referer) {
        $redirect_url .= '?' . $referer;
    }
}
header("Location: " . $redirect_url);
exit;