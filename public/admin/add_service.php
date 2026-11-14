<?php
// public/admin/add_service.php
require_once '../../includes/db.php';
require_once '../../includes/auth.php';

require_role('admin');

// Pobieramy kategorie do listy rozwijanej
$categories = $pdo->query("SELECT id, name FROM service_categories ORDER BY name")->fetchAll();

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $category_id = $_POST['category_id'] ?? '';
    $name = trim($_POST['name'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $duration = (int)($_POST['duration'] ?? 0);
    $price = (float)($_POST['price'] ?? 0);

    if (empty($category_id) || empty($name) || $duration <= 0 || $price <= 0) {
        $error = "Wypełnij poprawnie wszystkie wymagane pola.";
    } else {
        $stmt = $pdo->prepare("INSERT INTO services (category_id, name, description, duration, price) VALUES (?, ?, ?, ?, ?)");
        
        if ($stmt->execute([$category_id, $name, $description, $duration, $price])) {
            $success = "Usługa została dodana.";
        } else {
            $error = "Wystąpił błąd podczas dodawania usługi.";
        }
    }
}

require_once '../../includes/header.php';
?>

<div class="card" style="max-width: 500px;">
    <h2>Dodaj nową usługę</h2>
    
    <?php if ($error): ?>
        <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>
    
    <?php if ($success): ?>
        <div class="alert alert-success"><?= htmlspecialchars($success) ?></div>
        <p style="text-align: center; margin-top: 1rem;"><a href="services.php">Wróć do listy usług</a></p>
    <?php else: ?>
        <form method="POST" action="add_service.php">
            <div class="form-group">
                <label>Kategoria</label>
                <select name="category_id" required style="width: 100%; padding: 0.75rem; border: 1px solid #ccc; border-radius: 4px;">
                    <option value="">-- Wybierz kategorię --</option>
                    <?php foreach ($categories as $cat): ?>
                        <option value="<?= $cat['id'] ?>"><?= htmlspecialchars($cat['name']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group">
                <label>Nazwa usługi</label>
                <input type="text" name="name" required>
            </div>
            <div class="form-group">
                <label>Opis (opcjonalnie)</label>
                <input type="text" name="description">
            </div>
            <div class="form-group">
                <label>Czas trwania (w minutach)</label>
                <input type="number" name="duration" required min="15" step="15">
            </div>
            <div class="form-group">
                <label>Cena (zł)</label>
                <input type="number" name="price" required min="0" step="0.01">
            </div>
            <button type="submit" class="btn">Dodaj usługę</button>
        </form>
        <p style="text-align: center; margin-top: 1rem;"><a href="services.php" style="color: #666;">Anuluj</a></p>
    <?php endif; ?>
</div>

<?php require_once '../../includes/footer.php'; ?>