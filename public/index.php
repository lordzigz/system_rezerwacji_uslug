<?php
// public/index.php
require_once '../includes/db.php';
require_once '../includes/auth.php';

// Pobieramy kategorie i przypisane do nich aktywne usługi
$stmt = $pdo->query("SELECT c.id as cat_id, c.name as cat_name, c.description as cat_desc, 
                            s.id as srv_id, s.name as srv_name, s.duration, s.price 
                     FROM service_categories c 
                     LEFT JOIN services s ON c.id = s.category_id AND s.active = 1
                     ORDER BY c.id, s.price");
$rows = $stmt->fetchAll();

// Grupujemy wyniki według kategorii w PHP
$categories = [];
foreach ($rows as $row) {
    if (!isset($categories[$row['cat_id']])) {
        $categories[$row['cat_id']] = [
            'name' => $row['cat_name'],
            'description' => $row['cat_desc'],
            'services' => []
        ];
    }
    if ($row['srv_id']) {
        $categories[$row['cat_id']]['services'][] = $row;
    }
}

require_once '../includes/header.php';
?>

<div style="text-align: center; margin-bottom: 3rem;">
    <h1 style="color: var(--primary); font-size: 2.5rem; margin-bottom: 1rem;">Witaj w Studio Włosów!</h1>
    <p style="font-size: 1.2rem; color: #555;">Profesjonalne strzyżenie, koloryzacja i pielęgnacja włosów.</p>
    <p style="color: #777;">ul. Piłsudskiego 67, Siedlce</p>
</div>

<h2 style="margin-bottom: 1.5rem; color: var(--primary);">Nasza oferta</h2>

<!-- Tutaj usunąłem sztywne ustawianie szerokości, żeby tabele ładnie się rozciągały -->
<div style="display: flex; flex-direction: column; gap: 2rem;">
    <?php foreach ($categories as $cat): ?>
        <div class="card" style="max-width: 100%; width: 100%;">
            <h3 style="color: var(--primary); border-bottom: 2px solid var(--secondary); padding-bottom: 0.5rem; margin-bottom: 1rem;">
                <?= htmlspecialchars($cat['name']) ?>
            </h3>
            <p style="color: #666; margin-bottom: 1.5rem;"><?= htmlspecialchars($cat['description']) ?></p>
            
            <?php if (empty($cat['services'])): ?>
                <p><em>Brak dostępnych usług w tej kategorii.</em></p>
            <?php else: ?>
                <table>
                    <thead>
                        <tr>
                            <th>Usługa</th>
                            <th>Czas trwania</th>
                            <th>Cena</th>
                            <th>Akcja</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($cat['services'] as $srv): ?>
                        <tr>
                            <td><?= htmlspecialchars($srv['srv_name']) ?></td>
                            <td><?= $srv['duration'] ?> min</td>
                            <td><?= number_format($srv['price'], 2, ',', ' ') ?> zł</td>
                            <td>
                                <a href="book.php?service_id=<?= $srv['srv_id'] ?>" class="btn" style="width: auto; padding: 0.5rem 1rem; font-size: 0.9rem; display: inline-block;">Zarezerwuj</a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </div>
    <?php endforeach; ?>
</div>

<?php require_once '../includes/footer.php'; ?>