<?php

require_once '../includes/db.php';
require_once '../includes/auth.php';


require_login();
?>

<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <title>Panel - Studio Włosów</title>
</head>
<body>
    <h1>Witaj, <?= htmlspecialchars($_SESSION['name']) ?>!</h1>
    <p>Jesteś zalogowany jako: <strong><?= htmlspecialchars($_SESSION['role']) ?></strong></p>
    
    <p><a href="logout.php">Wyloguj się</a></p>
</body>
</html>