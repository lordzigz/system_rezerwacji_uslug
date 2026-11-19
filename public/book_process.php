<?php

require_once '../includes/db.php';
require_once '../includes/auth.php';
require_login();


if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_POST['service_id'], $_POST['employee_id'], $_POST['reservation_date'])) {
    header("Location: index.php");
    exit;
}

$service_id = (int)$_POST['service_id'];
$employee_id = (int)$_POST['employee_id'];
$date = $_POST['reservation_date'];


$service = $pdo->prepare("SELECT * FROM services WHERE id = ?")->execute([$service_id]) ? $pdo->query("SELECT * FROM services WHERE id = $service_id")->fetch() : null;
$employee = $pdo->prepare("SELECT * FROM users WHERE id = ?")->execute([$employee_id]) ? $pdo->query("SELECT * FROM users WHERE id = $employee_id")->fetch() : null;

if (!$service || !$employee) {
    die("Błędne dane usługi lub pracownika.");
}


$day_of_week = date('N', strtotime($date));


$stmt = $pdo->prepare("SELECT start_time, end_time FROM employee_availability WHERE employee_id = ? AND day_of_week = ?");
$stmt->execute([$employee_id, $day_of_week]);
$availability = $stmt->fetch();

require_once '../includes/header.php';
?>

<div class="card" style="max-width: 600px;">
    <h2>Wybierz godzinę</h2>
    <p style="text-align: center; color: #666;">
        Usługa: <strong><?= htmlspecialchars($service['name']) ?></strong><br>
        Pracownik: <strong><?= htmlspecialchars($employee['name'] . ' ' . $employee['surname']) ?></strong><br>
        Data: <strong><?= date('d.m.Y', strtotime($date)) ?></strong>
    </p>

    <?php if (!$availability): ?>
        <div class="alert alert-danger">Pracownik nie pracuje w tym dniu. Wybierz inną datę.</div>
        <p style="text-align: center;"><a href="index.php">Wróć do oferty</a></p>
    <?php else: ?>
        
        <?php
       
        $start = strtotime($availability['start_time']);
        $end = strtotime($availability['end_time']);
        $duration = $service['duration'] * 60; // czas w sekundach
        
       
        $stmt = $pdo->prepare("SELECT start_time, end_time FROM reservations WHERE employee_id = ? AND reservation_date = ? AND status != 'anulowana'");
        $stmt->execute([$employee_id, $date]);
        $existing_reservations = $stmt->fetchAll();

        $available_slots = [];

    
        for ($time = $start; $time + $duration <= $end; $time += 1800) {
            $slot_start = date('H:i', $time);
            $slot_end = date('H:i', $time + $duration);
            
           
            $is_free = true;

            foreach ($existing_reservations as $res) {
                
                if ($slot_start < $res['end_time'] && $slot_end > $res['start_time']) {
                    $is_free = false;
                    break;
                }
            }

            if ($is_free) {
                $available_slots[] = $slot_start;
            }
        }
        ?>

        <?php if (empty($available_slots)): ?>
            <div class="alert alert-danger">Brak wolnych terminów w tym dniu.</div>
            <p style="text-align: center;"><a href="javascript:history.back()">Wróć i wybierz inną datę</a></p>
        <?php else: ?>
            <form method="POST" action="book_save.php">
                <input type="hidden" name="service_id" value="<?= $service_id ?>">
                <input type="hidden" name="employee_id" value="<?= $employee_id ?>">
                <input type="hidden" name="reservation_date" value="<?= $date ?>">

                <div class="form-group">
                    <label>Dostępne godziny:</label>
                    <div style="display: flex; flex-wrap: wrap; gap: 0.5rem; margin-top: 0.5rem;">
                        <?php foreach ($available_slots as $slot): ?>
                            <label style="font-weight: normal; background: #ecf0f1; padding: 0.5rem; border-radius: 4px; cursor: pointer;">
                                <input type="radio" name="start_time" value="<?= $slot ?>" required>
                                <?= $slot ?>
                            </label>
                        <?php endforeach; ?>
                    </div>
                </div>

                <button type="submit" class="btn" style="margin-top: 1rem;">Potwierdź rezerwację</button>
            </form>
        <?php endif; ?>

    <?php endif; ?>
    
    <div style="margin-top: 1rem; text-align: center;">
        <a href="javascript:history.back()" style="color: #666;">&larr; Wróć</a>
    </div>
</div>

<?php require_once '../includes/footer.php'; ?>