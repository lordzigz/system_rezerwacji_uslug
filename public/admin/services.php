<?php
// public/admin/services.php
require_once '../../includes/db.php';
require_once '../../includes/auth.php';

require_role('admin');

// Pobieramy usługi wraz z nazwami ich kategorii
$sql = "SELECT s.*, c.name AS category_name 
        FROM services s 
        JOIN service_categories c ON s.category_id = c.id 
        ORDER BY c.name, s.name";
$stmt = $pdo->query($sql);
$services = $stmt->fetchAll();

require_once '../../includes/header.php';
?>

<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem;">
    <h1>Usługi</h1>
    <div>
        <a href="add_category.php" class="btn" style="width: auto; background-color: #7f8c8d;">+ Nowa kategoria</a>
        <a href="add_service.php" class="btn" style="width: auto;">+ Dodaj usługę</a>
    </div>
</div>

<table>
    <thead>
        <tr>
            <th>ID</th>
            <th>Kategoria</th>
            <th>Nazwa usługi</th>
            <th>Czas</th>
            <th>Cena</th>
            <th>Status</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($services as $service): ?>
        <tr>
            <td><?= $service['id'] ?></td>
            <td><?= htmlspecialchars($service['category_name']) ?></td>
            <td><?= htmlspecialchars($service['name']) ?></td>
            <td><?= $service['duration'] ?> min</td>
            <td><?= number_format($service['price'], 2, ',', ' ') ?> zł</td>
            <td>
                <?= $service['active'] ? '<span style="color: green;">Aktywna</span>' : '<span style="color: red;">Ukryta</span>' ?>
            </td>
        </tr>
        <?php endforeach; ?>
    </tbody>
</table>

<div style="margin-top: 1rem;">
    <a href="index.php" style="color: var(--secondary);">&larr; Powrót do panelu</a>
</div>

<?php require_once '../../includes/footer.php'; ?>