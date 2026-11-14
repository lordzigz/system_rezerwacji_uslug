<?php
// public/admin/add_category.php
require_once '../../includes/db.php';
require_once '../../includes/auth.php';

require_role('admin');

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $description = trim($_POST['description'] ?? '');

    if (empty($name)) {
        $error = "Nazwa kategorii jest wymagana.";
    } else {
        $stmt = $pdo->prepare("INSERT INTO service_categories (name, description) VALUES (?, ?)");
        
        if ($stmt->execute([$name, $description])) {
            $success = "Kategoria została dodana.";
        } else {
            $error = "Wystąpił błąd podczas dodawania kategorii.";
        }
    }
}

require_once '../../includes/header.php';
?>

<div class="card" style="max-width: 500px;">
    <h2>Dodaj nową kategorię</h2>
    
    <?php if ($error): ?>
        <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>
    
    <?php if ($success): ?>
        <div class="alert alert-success"><?= htmlspecialchars($success) ?></div>
        <p style="text-align: center; margin-top: 1rem;"><a href="services.php">Wróć do listy usług</a></p>
    <?php else: ?>
        <form method="POST" action="add_category.php">
            <div class="form-group">
                <label>Nazwa kategorii</label>
                <input type="text" name="name" required>
            </div>
            <div class="form-group">
                <label>Opis (opcjonalnie)</label>
                <input type="text" name="description">
            </div>
            <button type="submit" class="btn">Dodaj kategorię</button>
        </form>
        <p style="text-align: center; margin-top: 1rem;"><a href="services.php" style="color: #666;">Anuluj</a></p>
    <?php endif; ?>
</div>

<?php require_once '../../includes/footer.php'; ?>