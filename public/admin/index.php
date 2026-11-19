<?php
// public/admin/index.php
require_once '../../includes/db.php';
require_once '../../includes/auth.php';

// Tylko administrator może tu wejść!
require_role('admin');

require_once '../../includes/header.php';

// Pobieramy statystyki do dashboardu
$usersCount = $pdo->query("SELECT COUNT(*) FROM users WHERE role = 'klient'")->fetchColumn();
$employeesCount = $pdo->query("SELECT COUNT(*) FROM users WHERE role = 'pracownik'")->fetchColumn();
$reservationsCount = $pdo->query("SELECT COUNT(*) FROM reservations")->fetchColumn();
$todayReservations = $pdo->query("SELECT COUNT(*) FROM reservations WHERE reservation_date = CURDATE()")->fetchColumn();
?>

<h1 style="margin-bottom: 1.5rem;">Panel Administratora</h1>

<div style="display: flex; gap: 1rem; flex-wrap: wrap; margin-bottom: 2rem;">
    <div class="card" style="flex: 1; min-width: 200px; text-align: center;">
        <h3>Klienci</h3>
        <p style="font-size: 2rem; color: var(--secondary);"><?= $usersCount ?></p>
    </div>
    <div class="card" style="flex: 1; min-width: 200px; text-align: center;">
        <h3>Pracownicy</h3>
        <p style="font-size: 2rem; color: var(--secondary);"><?= $employeesCount ?></p>
    </div>
    <div class="card" style="flex: 1; min-width: 200px; text-align: center;">
        <h3>Wszystkie rezerwacje</h3>
        <p style="font-size: 2rem; color: var(--secondary);"><?= $reservationsCount ?></p>
    </div>
    <div class="card" style="flex: 1; min-width: 200px; text-align: center;">
        <h3>Wizyty na dziś</h3>
        <p style="font-size: 2rem; color: var(--secondary);"><?= $todayReservations ?></p>
    </div>
</div>

<div class="card" style="max-width: 100%;">
    <h2>Zarządzanie systemem</h2>
    <p>Wybierz moduł, którym chcesz zarządzać:</p>
    <ul style="margin-top: 1rem; list-style-type: none; padding: 0;">
        <li style="margin-bottom: 0.5rem;"><a href="services.php" style="color: var(--secondary); font-size: 1.2rem;">✂️ Usługi i kategorie</a></li>
        <li style="margin-bottom: 0.5rem;"><a href="employee_services.php" style="color: var(--secondary); font-size: 1.2rem;">🔗 Przypisz usługi do pracowników</a></li>
        <li style="margin-bottom: 0.5rem;"><a href="employee_availability.php" style="color: var(--secondary); font-size: 1.2rem;">⏰ Godziny pracy pracowników</a></li>
        <li style="margin-bottom: 0.5rem;"><a href="reservations.php" style="color: var(--secondary); font-size: 1.2rem;">📅 Zarządzanie rezerwacjami</a></li>
    </ul>
</div>

<?php require_once '../../includes/footer.php'; ?>