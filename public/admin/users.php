<?php
// public/admin/users.php
require_once '../../includes/db.php';
require_once '../../includes/auth.php';

require_role('admin');

require_once '../../includes/header.php';

// Pobieramy wszystkich użytkowników
$stmt = $pdo->query("SELECT id, name, surname, email, phone, role, active FROM users ORDER BY role, surname");
$users = $stmt->fetchAll();
?>

<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem;">
    <h1>Użytkownicy</h1>
    <a href="add_employee.php" class="btn" style="width: auto;">+ Dodaj pracownika</a>
</div>

<table>
    <thead>
        <tr>
            <th>ID</th>
            <th>Imię i nazwisko</th>
            <th>E-mail</th>
            <th>Telefon</th>
            <th>Rola</th>
            <th>Status</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($users as $user): ?>
        <tr>
            <td><?= $user['id'] ?></td>
            <td><?= htmlspecialchars($user['name'] . ' ' . $user['surname']) ?></td>
            <td><?= htmlspecialchars($user['email']) ?></td>
            <td><?= htmlspecialchars($user['phone'] ?? '-') ?></td>
            <td><strong><?= htmlspecialchars($user['role']) ?></strong></td>
            <td>
                <?= $user['active'] ? '<span style="color: green;">Aktywny</span>' : '<span style="color: red;">Zablokowany</span>' ?>
            </td>
        </tr>
        <?php endforeach; ?>
    </tbody>
</table>

<div style="margin-top: 1rem;">
    <a href="index.php" style="color: var(--secondary);">&larr; Powrót do panelu</a>
</div>

<?php require_once '../../includes/footer.php'; ?>