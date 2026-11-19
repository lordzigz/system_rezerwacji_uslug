<?php
// public/admin/employee_services.php
require_once '../../includes/db.php';
require_once '../../includes/auth.php';

require_role('admin');

// Pobieramy pracowników
$stmt = $pdo->query("SELECT id, name, surname FROM users WHERE role = 'pracownik' ORDER BY surname");
$employees = $stmt->fetchAll();

// Pobieramy wszystkie usługi
$stmt = $pdo->query("SELECT id, name FROM services ORDER BY name");
$services = $stmt->fetchAll();

$message = '';

// Zapisujemy zmiany
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['employee_id'])) {
    $employee_id = (int)$_POST['employee_id'];
    $selected_services = $_POST['services'] ?? []; // Tablica wybranych ID usług

    // Usuwamy stare przypisania dla tego pracownika
    $stmt = $pdo->prepare("DELETE FROM employee_services WHERE employee_id = ?");
    $stmt->execute([$employee_id]);

    // Dodajemy nowe przypisania
    if (!empty($selected_services)) {
        $stmt = $pdo->prepare("INSERT INTO employee_services (employee_id, service_id) VALUES (?, ?)");
        foreach ($selected_services as $service_id) {
            $stmt->execute([$employee_id, $service_id]);
        }
    }
    $message = "Usługi zostały zaktualizowane.";
}

require_once '../../includes/header.php';
?>

<div style="margin-bottom: 1rem;">
    <a href="index.php" style="color: var(--secondary);">&larr; Powrót do panelu</a>
</div>

<h1>Przypisz usługi do pracowników</h1>
<p>Wybierz pracownika, a następnie zaznacz usługi, które ma wykonywać.</p>

<?php if ($message): ?>
    <div class="alert alert-success" style="max-width: 600px; margin: 0;"><?= $message ?></div>
<?php endif; ?>

<div style="display: flex; gap: 2rem; flex-wrap: wrap; margin-top: 2rem;">
    <?php foreach ($employees as $emp): ?>
        <?php
        // Pobieramy aktualnie przypisane usługi dla tego pracownika
        $stmt = $pdo->prepare("SELECT service_id FROM employee_services WHERE employee_id = ?");
        $stmt->execute([$emp['id']]);
        $assigned = $stmt->fetchAll(PDO::FETCH_COLUMN);
        ?>
        <div class="card" style="flex: 1; min-width: 300px;">
            <h3><?= htmlspecialchars($emp['name'] . ' ' . $emp['surname']) ?></h3>
            <form method="POST" style="margin-top: 1rem;">
                <input type="hidden" name="employee_id" value="<?= $emp['id'] ?>">
                
                <div style="max-height: 200px; overflow-y: auto; border: 1px solid #eee; padding: 0.5rem;">
                    <?php foreach ($services as $srv): ?>
                        <label style="display: block; margin-bottom: 0.5rem; font-weight: normal;">
                            <input type="checkbox" name="services[]" value="<?= $srv['id'] ?>" 
                                <?= in_array($srv['id'], $assigned) ? 'checked' : '' ?>>
                            <?= htmlspecialchars($srv['name']) ?>
                        </label>
                    <?php endforeach; ?>
                </div>
                
                <button type="submit" class="btn" style="margin-top: 1rem;">Zapisz zmiany</button>
            </form>
        </div>
    <?php endforeach; ?>
</div>

<?php require_once '../../includes/footer.php'; ?>