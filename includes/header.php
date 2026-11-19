<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Studio Włosów - System Rezerwacji</title>
    <link rel="stylesheet" href="/system_rezerwacji_uslug/public/css/style.css">
</head>
<body>
    <nav>
        <a href="/system_rezerwacji_uslug/public/index.php" class="brand">Studio Włosów</a>
        <ul>
            <li><a href="/system_rezerwacji_uslug/public/index.php">Strona główna</a></li>
            
                <?php if (isset($_SESSION['user_id'])): ?>
                <li><a href="/system_rezerwacji_uslug/public/dashboard.php">Mój panel</a></li>
                
                <?php if ($_SESSION['role'] === 'klient'): ?>
                    <li><a href="/system_rezerwacji_uslug/public/my_reservations.php">Moje rezerwacje</a></li>
                <?php endif; ?>

                <?php if ($_SESSION['role'] === 'admin'): ?>
                    <li><a href="/system_rezerwacji_uslug/public/admin/index.php">Admin</a></li>
                <?php endif; ?>

                <li><span style="color:#bdc3c7;">Witaj, <?= htmlspecialchars($_SESSION['name']) ?></span></li>
                <li><a href="/system_rezerwacji_uslug/public/logout.php">Wyloguj</a></li>
            <?php else: ?>
                <li><a href="/system_rezerwacji_uslug/public/login.php">Zaloguj</a></li>
                <li><a href="/system_rezerwacji_uslug/public/register.php">Rejestracja</a></li>
            <?php endif; ?>
        </ul>
    </nav>
    <div class="container">