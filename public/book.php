<?php

require_once '../includes/db.php';
require_once '../includes/auth.php';


require_login();

$service_id = isset($_GET['service_id']) ? (int)$_GET['service_id'] : 0;


$stmt = $pdo->prepare("SELECT * FROM services WHERE id = ? AND active = 1");
$stmt->execute([$service_id]);
$service = $stmt->fetch();

if (!$service) {
    die("Nie znaleziono wybranej usługi.");
}


$sql = "SELECT u.id, u.name, u.surname 
        FROM users u 
        JOIN employee_services es ON u.id = es.employee_id 
        WHERE es.service_id = ? AND u.role = 'pracownik' AND u.active = 1";
$stmt = $pdo->prepare($sql);
$stmt->execute([$service_id]);
$employees = $stmt->fetchAll();

require_once '../includes/header.php';
?>

<div class="card" style="max-width: 600px;">
    <h2>Rezerwacja: <?= htmlspecialchars($service['name']) ?></h2>
    <p style="text-align: center; color: #666; margin-bottom: 1.5rem;">
        Czas trwania: <?= $service['duration'] ?> min | Cena: <?= number_format($service['price'], 2, ',', ' ') ?> zł
    </p>

    <?php if (empty($employees)): ?>
        <div class="alert alert-danger">Przepraszamy, obecnie żaden pracownik nie wykonuje tej usługi.</div>
    <?php else: ?>
        <form method="POST" action="book_process.php">

            <input type="hidden" name="service_id" value="<?= $service['id'] ?>">

            <div class="form-group">
                <label>1. Wybierz pracownika:</label>
                <select name="employee_id" required style="width: 100%; padding: 0.75rem; border: 1px solid #ccc; border-radius: 4px;">
                    <option value="">-- Wybierz pracownika --</option>
                    <?php foreach ($employees as $emp): ?>
                        <option value="<?= $emp['id'] ?>"><?= htmlspecialchars($emp['name'] . ' ' . $emp['surname']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-group">
                <label>2. Wybierz datę:</label>
                <input type="date" name="reservation_date" required min="<?= date('Y-m-d') ?>" style="width: 100%; padding: 0.75rem; border: 1px solid #ccc; border-radius: 4px;">
                <small style="color: #666;">Wybór godziny nastąpi w kolejnym kroku.</small>
            </div>

            <button type="submit" class="btn">Przejdź do wyboru godziny</button>
        </form>
    <?php endif; ?>
</div>

<?php require_once '../includes/footer.php'; ?>