<?php

require_once '../../includes/db.php';
require_once '../../includes/auth.php';

require_role('admin');


$employees = $pdo->query("SELECT id, name, surname FROM users WHERE role = 'pracownik' ORDER BY surname")->fetchAll();


$sql = "SELECT r.*, 
               c.name AS client_name, c.surname AS client_surname, 
               e.name AS emp_name, e.surname AS emp_surname, 
               s.name AS service_name 
        FROM reservations r
        JOIN users c ON r.user_id = c.id
        JOIN users e ON r.employee_id = e.id
        JOIN services s ON r.service_id = s.id
        WHERE 1=1";

$params = [];


if (!empty($_GET['date'])) {
    $sql .= " AND r.reservation_date = ?";
    $params[] = $_GET['date'];
}


if (!empty($_GET['status'])) {
    $sql .= " AND r.status = ?";
    $params[] = $_GET['status'];
}


if (!empty($_GET['employee_id'])) {
    $sql .= " AND r.employee_id = ?";
    $params[] = $_GET['employee_id'];
}

$sql .= " ORDER BY r.reservation_date DESC, r.start_time DESC";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$reservations = $stmt->fetchAll();

require_once '../../includes/header.php';
?>

<div style="display: flex; justify-content: space-between; align-items: center;">
    <h1>Wszystkie rezerwacje</h1>
    <a href="index.php" style="color: var(--secondary);">&larr; Powrót do panelu</a>
</div>


<div class="card" style="max-width: 100%; margin-bottom: 1rem; padding: 1rem;">
    <form method="GET" style="display: flex; gap: 1rem; align-items: flex-end; flex-wrap: wrap;">
        <div class="form-group" style="margin-bottom: 0;">
            <label>Data</label>
            <input type="date" name="date" value="<?= htmlspecialchars($_GET['date'] ?? '') ?>" style="padding: 0.5rem;">
        </div>
        <div class="form-group" style="margin-bottom: 0;">
            <label>Status</label>
            <select name="status" style="padding: 0.5rem; border: 1px solid #ccc; border-radius: 4px;">
                <option value="">Wszystkie</option>
                <option value="oczekujaca" <?= (($_GET['status'] ?? '') === 'oczekujaca') ? 'selected' : '' ?>>Oczekująca</option>
                <option value="potwierdzona" <?= (($_GET['status'] ?? '') === 'potwierdzona') ? 'selected' : '' ?>>Potwierdzona</option>
                <option value="zrealizowana" <?= (($_GET['status'] ?? '') === 'zrealizowana') ? 'selected' : '' ?>>Zrealizowana</option>
                <option value="anulowana" <?= (($_GET['status'] ?? '') === 'anulowana') ? 'selected' : '' ?>>Anulowana</option>
            </select>
        </div>
        <div class="form-group" style="margin-bottom: 0;">
            <label>Pracownik</label>
            <select name="employee_id" style="padding: 0.5rem; border: 1px solid #ccc; border-radius: 4px;">
                <option value="">Wszyscy</option>
                <?php foreach ($employees as $emp): ?>
                    <option value="<?= $emp['id'] ?>" <?= (($_GET['employee_id'] ?? '') == $emp['id']) ? 'selected' : '' ?>>
                        <?= htmlspecialchars($emp['name'] . ' ' . $emp['surname']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        <button type="submit" class="btn" style="width: auto; padding: 0.5rem 1rem;">Filtruj</button>
        <a href="reservations.php" class="btn" style="width: auto; padding: 0.5rem 1rem; background: #95a5a6;">Wyczyść</a>
    </form>
</div>

<table>
    <thead>
        <tr>
            <th>ID</th>
            <th>Data i godzina</th>
            <th>Klient</th>
            <th>Pracownik</th>
            <th>Usługa</th>
            <th>Status</th>
            <th>Zmień status</th>
        </tr>
    </thead>
    <tbody>
        <?php if (empty($reservations)): ?>
            <tr><td colspan="7" style="text-align: center;">Brak rezerwacji spełniających kryteria.</td></tr>
        <?php else: ?>
            <?php foreach ($reservations as $res): ?>
            <tr>
                <td><?= $res['id'] ?></td>
                <td>
                    <strong><?= date('d.m.Y', strtotime($res['reservation_date'])) ?></strong><br>
                    <?= substr($res['start_time'], 0, 5) ?> - <?= substr($res['end_time'], 0, 5) ?>
                </td>
                <td><?= htmlspecialchars($res['client_name'] . ' ' . $res['client_surname']) ?></td>
                <td><?= htmlspecialchars($res['emp_name'] . ' ' . $res['emp_surname']) ?></td>
                <td><?= htmlspecialchars($res['service_name']) ?></td>
                <td>
                    <?php
                        $statusColor = 'black';
                        if ($res['status'] === 'oczekujaca') $statusColor = '#f39c12';
                        if ($res['status'] === 'potwierdzona') $statusColor = '#27ae60';
                        if ($res['status'] === 'zrealizowana') $statusColor = '#2980b9';
                        if ($res['status'] === 'anulowana') $statusColor = '#e74c3c';
                    ?>
                    <strong style="color: <?= $statusColor ?>;"><?= ucfirst($res['status']) ?></strong>
                </td>
                <td>
                    
                    <form method="POST" action="change_reservation_status.php" style="display: flex; gap: 0.5rem;">
                        <input type="hidden" name="id" value="<?= $res['id'] ?>">
                        <select name="status" style="padding: 0.25rem; font-size: 0.9rem;">
                            <option value="oczekujaca" <?= $res['status'] === 'oczekujaca' ? 'selected' : '' ?>>Oczekująca</option>
                            <option value="potwierdzona" <?= $res['status'] === 'potwierdzona' ? 'selected' : '' ?>>Potwierdzona</option>
                            <option value="zrealizowana" <?= $res['status'] === 'zrealizowana' ? 'selected' : '' ?>>Zrealizowana</option>
                            <option value="anulowana" <?= $res['status'] === 'anulowana' ? 'selected' : '' ?>>Anulowana</option>
                        </select>
                        <button type="submit" class="btn" style="width: auto; padding: 0.25rem 0.5rem; font-size: 0.8rem;">Zapisz</button>
                    </form>
                </td>
            </tr>
            <?php endforeach; ?>
        <?php endif; ?>
    </tbody>
</table>

<?php require_once '../../includes/footer.php'; ?>