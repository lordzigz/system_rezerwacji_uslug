<?php

require_once '../includes/db.php';
require_once '../includes/auth.php';
require_login();

$user_id = $_SESSION['user_id'];


$sql = "SELECT r.*, u.name AS emp_name, u.surname AS emp_surname, s.name AS srv_name, s.price 
        FROM reservations r
        JOIN users u ON r.employee_id = u.id
        JOIN services s ON r.service_id = s.id
        WHERE r.user_id = ?
        ORDER BY r.reservation_date DESC, r.start_time DESC";
$stmt = $pdo->prepare($sql);
$stmt->execute([$user_id]);
$reservations = $stmt->fetchAll();

require_once '../includes/header.php';
?>

<h1>Moje rezerwacje</h1>

<?php if (isset($_GET['success'])): ?>
    <div class="alert alert-success" style="max-width: 600px; margin: 0 0 1rem 0;">
        Rezerwacja została pomyślnie zapisana! Czekaj na potwierdzenie.
    </div>
<?php endif; ?>

<?php if (empty($reservations)): ?>
    <p>Nie masz jeszcze żadnych rezerwacji. <a href="index.php">Zarezerwuj pierwszą wizytę!</a></p>
<?php else: ?>
    <table>
        <thead>
            <tr>
                <th>Data i godzina</th>
                <th>Usługa</th>
                <th>Pracownik</th>
                <th>Cena</th>
                <th>Status</th>
                <th>Akcje</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($reservations as $res): ?>
            <tr>
                <td>
                    <strong><?= date('d.m.Y', strtotime($res['reservation_date'])) ?></strong><br>
                    <?= substr($res['start_time'], 0, 5) ?> - <?= substr($res['end_time'], 0, 5) ?>
                </td>
                <td><?= htmlspecialchars($res['srv_name']) ?></td>
                <td><?= htmlspecialchars($res['emp_name'] . ' ' . $res['emp_surname']) ?></td>
                <td><?= number_format($res['price'], 2, ',', ' ') ?> zł</td>
                <td>
                    <?php
                        // Kolorowanie statusów
                        $statusColor = 'black';
                        if ($res['status'] === 'oczekujaca') $statusColor = '#f39c12'; // pomarańczowy
                        if ($res['status'] === 'potwierdzona') $statusColor = '#27ae60'; // zielony
                        if ($res['status'] === 'zrealizowana') $statusColor = '#2980b9'; // niebieski
                        if ($res['status'] === 'anulowana') $statusColor = '#e74c3c'; // czerwony
                    ?>
                    <strong style="color: <?= $statusColor ?>;"><?= ucfirst($res['status']) ?></strong>
                </td>
                <td>
                    <?php if ($res['status'] === 'oczekujaca' || $res['status'] === 'potwierdzona'): ?>
                        <a href="cancel_reservation.php?id=<?= $res['id'] ?>" onclick="return confirm('Czy na pewno chcesz anulować tę rezerwację?');" style="color: var(--danger); font-size: 0.9rem;">Anuluj</a>
                    <?php else: ?>
                        -
                    <?php endif; ?>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
<?php endif; ?>

<?php require_once '../includes/footer.php'; ?>