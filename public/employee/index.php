<?php

require_once '../../includes/db.php';
require_once '../../includes/auth.php';

require_role('pracownik');

$employee_id = $_SESSION['user_id'];
$today = date('Y-m-d');


$stmt = $pdo->prepare("SELECT r.*, s.name AS srv_name, u.name AS cl_name, u.surname AS cl_surname, u.phone 
                       FROM reservations r
                       JOIN services s ON r.service_id = s.id
                       JOIN users u ON r.user_id = u.id
                       WHERE r.employee_id = ? AND r.reservation_date = ? AND r.status != 'anulowana'
                       ORDER BY r.start_time");
$stmt->execute([$employee_id, $today]);
$today_reservations = $stmt->fetchAll();


$stmt = $pdo->prepare("SELECT r.*, s.name AS srv_name, u.name AS cl_name, u.surname AS cl_surname, u.phone 
                       FROM reservations r
                       JOIN services s ON r.service_id = s.id
                       JOIN users u ON r.user_id = u.id
                       WHERE r.employee_id = ? AND r.reservation_date > ? AND r.status != 'anulowana'
                       ORDER BY r.reservation_date, r.start_time");
$stmt->execute([$employee_id, $today]);
$future_reservations = $stmt->fetchAll();

require_once '../../includes/header.php';
?>

<h1>Panel Pracownika</h1>
<p>Witaj, <?= htmlspecialchars($_SESSION['name']) ?>!</p>

<div class="card" style="max-width: 100%; margin-bottom: 2rem;">
    <h2>Wizyty na dziś (<?= date('d.m.Y') ?>)</h2>
    <?php if (empty($today_reservations)): ?>
        <p>Brak wizyt na dzisiaj.</p>
    <?php else: ?>
        <table>
            <thead>
                <tr>
                    <th>Godzina</th>
                    <th>Klient</th>
                    <th>Telefon</th>
                    <th>Usługa</th>
                    <th>Status</th>
                    <th>Akcja</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($today_reservations as $res): ?>
                <tr>
                    <td><strong><?= substr($res['start_time'], 0, 5) ?> - <?= substr($res['end_time'], 0, 5) ?></strong></td>
                    <td><?= htmlspecialchars($res['cl_name'] . ' ' . $res['cl_surname']) ?></td>
                    <td><?= htmlspecialchars($res['phone'] ?? '-') ?></td>
                    <td><?= htmlspecialchars($res['srv_name']) ?></td>
                    <td><?= ucfirst($res['status']) ?></td>
                    <td>
                        <?php if ($res['status'] === 'oczekujaca'): ?>
                            <a href="change_status.php?id=<?= $res['id'] ?>&status=potwierdzona" class="btn" style="padding: 0.25rem 0.5rem; font-size: 0.8rem; width: auto; background: #27ae60;">Potwierdź</a>
                        <?php elseif ($res['status'] === 'potwierdzona'): ?>
                            <a href="change_status.php?id=<?= $res['id'] ?>&status=zrealizowana" class="btn" style="padding: 0.25rem 0.5rem; font-size: 0.8rem; width: auto; background: #2980b9;">Zrealizuj</a>
                        <?php else: ?>
                            -
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</div>

<div class="card" style="max-width: 100%;">
    <h2>Przyszłe wizyty</h2>
    <?php if (empty($future_reservations)): ?>
        <p>Brak przyszłych wizyt.</p>
    <?php else: ?>
        <table>
            <thead>
                <tr>
                    <th>Data</th>
                    <th>Godzina</th>
                    <th>Klient</th>
                    <th>Usługa</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($future_reservations as $res): ?>
                <tr>
                    <td><?= date('d.m.Y', strtotime($res['reservation_date'])) ?></td>
                    <td><?= substr($res['start_time'], 0, 5) ?> - <?= substr($res['end_time'], 0, 5) ?></td>
                    <td><?= htmlspecialchars($res['cl_name'] . ' ' . $res['cl_surname']) ?></td>
                    <td><?= htmlspecialchars($res['srv_name']) ?></td>
                    <td><?= ucfirst($res['status']) ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</div>

<?php require_once '../../includes/footer.php'; ?>