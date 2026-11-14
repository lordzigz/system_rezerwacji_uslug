<?php
// public/index.php
require_once '../includes/db.php';
require_once '../includes/auth.php';
require_once '../includes/header.php';
?>

<div class="card" style="max-width: 800px; text-align: center;">
    <h1>Witaj w Studio Włosów!</h1>
    <p>Profesjonalne strzyżenie, koloryzacja i pielęgnacja włosów.</p>
    <p>Adres: ul. Piłsudskiego 67, Siedlce</p>
    <br>
    <p><a href="login.php" class="btn" style="max-width: 200px;">Zarezerwuj wizytę</a></p>
</div>

<?php require_once '../includes/footer.php'; ?>