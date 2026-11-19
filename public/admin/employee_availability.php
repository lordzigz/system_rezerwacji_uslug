<?php
// public/admin/employee_availability.php
require_once '../../includes/db.php';
require_once '../../includes/auth.php';

require_role('admin');

// Pobieramy pracowników
$stmt = $pdo->query("SELECT id, name, surname FROM users WHERE role = 'pracownik' ORDER BY surname");
$employees = $stmt->fetchAll();

$message = '';

// Zapisujemy zmiany
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['employee_id'])) {
    $employee_id = (int)$_POST['employee_id'];
    
    // Usuwamy starą dostępność
    $stmt = $pdo->prepare("DELETE FROM employee_availability WHERE employee_id = ?");
    $stmt->execute([$employee_id]);

    // Dodajemy nową dla dni, które mają zaznaczony checkbox
    $stmt = $pdo->prepare("INSERT INTO employee_availability (employee_id, day_of_week, start_time, end_time) VALUES (?, ?, ?, ?)");
    
    // Pętla od 1 (poniedziałek) do 6 (sobota)
    for ($day = 1; $day <= 6; $day++) {
        if (isset($_POST['days'][$day])) {
            $start = $_POST['start'][$day] ?? '09:00';
            $end = $_POST['end'][$day] ?? '17:00';
            $stmt->execute([$employee_id, $day, $start, $end]);
        }
    }
    $message = "Dostępność została zaktualizowana.";
}

$daysNames = [1 => 'Poniedziałek', 2 => 'Wtorek', 3 => 'Środa', 4 => 'Czwartek', 5 => 'Piątek', 6 => 'Sobota'];

require_once '../../includes/header.php';
?>

<div style="margin-bottom: 1rem;">
    <a href="index.php" style="color: var(--secondary);">&larr; Powrót do panelu</a>
</div>

<h1>Godziny pracy pracowników</h1>

<?php if ($message): ?>
    <div class="alert alert-success" style="max-width: 600px; margin: 0;"><?= $message ?></div>
<?php endif; ?>

<div style="display: flex; gap: 2rem; flex-wrap: wrap; margin-top: 2rem;">
    <?php foreach ($employees as $emp): ?>
        <?php
        // Pobieramy aktualną dostępność
        $stmt = $pdo->prepare("SELECT day_of_week, start_time, end_time FROM employee_availability WHERE employee_id = ?");
        $stmt->execute([$emp['id']]);
        $availability = [];
        while ($row = $stmt->fetch()) {
            $availability[$row['day_of_week']] = $row;
        }
        ?>
        <div class="card" style="flex: 1; min-width: 400px;">
            <h3><?= htmlspecialchars($emp['name'] . ' ' . $emp['surname']) ?></h3>
            <form method="POST" style="margin-top: 1rem;">
                <input type="hidden" name="employee_id" value="<?= $emp['id'] ?>">
                
                <table style="box-shadow: none; margin-top: 0;">
                    <tr>
                        <th>Dzień</th>
                        <th>Od</th>
                        <th>Do</th>
                    </tr>
                    <?php foreach ($daysNames as $dayNum => $dayName): ?>
                        <?php 
                            $isChecked = isset($availability[$dayNum]);
                            $startVal = $isChecked ? substr($availability[$dayNum]['start_time'], 0, 5) : '09:00';
                            $endVal = $isChecked ? substr($availability[$dayNum]['end_time'], 0, 5) : '17:00';
                        ?>
                        <tr>
                            <td>
                                <label style="font-weight: normal;">
                                    <input type="checkbox" name="days[<?= $dayNum ?>]" value="1" <?= $isChecked ? 'checked' : '' ?>>
                                    <?= $dayName ?>
                                </label>
                            </td>
                            <td><input type="time" name="start[<?= $dayNum ?>]" value="<?= $startVal ?>" style="padding: 0.25rem;"></td>
                            <td><input type="time" name="end[<?= $dayNum ?>]" value="<?= $endVal ?>" style="padding: 0.25rem;"></td>
                        </tr>
                    <?php endforeach; ?>
                </table>
                
                <button type="submit" class="btn" style="margin-top: 1rem;">Zapisz godziny</button>
            </form>
        </div>
    <?php endforeach; ?>
</div>

<?php require_once '../../includes/footer.php'; ?>