<?php
// public/dashboard.php
require_once '../includes/db.php';
require_once '../includes/auth.php';
require_login();

require_once '../includes/header.php';
?>

<div class="card" style="max-width: 600px;">
    <h1>Witaj, <?= htmlspecialchars($_SESSION['name']) ?>!</h1>
    <p style="text-align: center; margin-bottom: 2rem;">
        Jesteś zalogowany jako: <strong><?= htmlspecialchars($_SESSION['role']) ?></strong>
    </p>
    
    <div style="text-align: center;">
        <p>Tu wkrótce pojawią się Twoje rezerwacje i funkcje panelu.</p>
    </div>
</div>

<?php require_once '../includes/footer.php'; ?>