<?php
// public/admin/add_employee.php
require_once '../../includes/db.php';
require_once '../../includes/auth.php';

require_role('admin');

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $surname = trim($_POST['surname'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $password = $_POST['password'] ?? '';

    if (empty($name) || empty($surname) || empty($email) || empty($password)) {
        $error = "Wypełnij wszystkie wymagane pola.";
    } else {
        $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->execute([$email]);
        
        if ($stmt->fetch()) {
            $error = "Ten adres e-mail jest już zajęty.";
        } else {
            $password_hash = password_hash($password, PASSWORD_DEFAULT);
            
            // Wymuszamy rolę 'pracownik'
            $stmt = $pdo->prepare("INSERT INTO users (name, surname, email, phone, password, role) VALUES (?, ?, ?, ?, ?, 'pracownik')");
            
            if ($stmt->execute([$name, $surname, $email, $phone, $password_hash])) {
                $success = "Pracownik został dodany pomyślnie.";
            } else {
                $error = "Wystąpił błąd podczas dodawania pracownika.";
            }
        }
    }
}

require_once '../../includes/header.php';
?>

<div class="card">
    <h2>Dodaj nowego pracownika</h2>
    
    <?php if ($error): ?>
        <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>
    
    <?php if ($success): ?>
        <div class="alert alert-success"><?= htmlspecialchars($success) ?></div>
        <p style="text-align: center; margin-top: 1rem;"><a href="users.php">Wróć do listy użytkowników</a></p>
    <?php else: ?>
        <form method="POST" action="add_employee.php">
            <div class="form-group">
                <label>Imię</label>
                <input type="text" name="name" required>
            </div>
            <div class="form-group">
                <label>Nazwisko</label>
                <input type="text" name="surname" required>
            </div>
            <div class="form-group">
                <label>E-mail</label>
                <input type="email" name="email" required>
            </div>
            <div class="form-group">
                <label>Telefon</label>
                <input type="text" name="phone">
            </div>
            <div class="form-group">
                <label>Hasło startowe</label>
                <input type="password" name="password" required>
            </div>
            <button type="submit" class="btn">Dodaj pracownika</button>
        </form>
        <p style="text-align: center; margin-top: 1rem;"><a href="users.php" style="color: #666;">Anuluj</a></p>
    <?php endif; ?>
</div>

<?php require_once '../../includes/footer.php'; ?>