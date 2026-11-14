<?php
// public/login.php
require_once '../includes/db.php';
require_once '../includes/auth.php';

$error = '';

if (isset($_SESSION['user_id'])) {
    header("Location: dashboard.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if (empty($email) || empty($password)) {
        $error = "Podaj e-mail i hasło.";
    } else {
        $stmt = $pdo->prepare("SELECT id, name, surname, password, role, active FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch();

        if ($user && $user['active'] == 1 && password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['name'] = $user['name'];
            $_SESSION['surname'] = $user['surname'];
            $_SESSION['role'] = $user['role'];

            header("Location: dashboard.php");
            exit;
        } else {
            $error = "Nieprawidłowy e-mail lub hasło, albo konto jest nieaktywne.";
        }
    }
}

require_once '../includes/header.php';
?>

<div class="card">
    <h1>Zaloguj się</h1>
    
    <?php if ($error): ?>
        <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>
    
    <form method="POST" action="login.php">
        <div class="form-group">
            <label>E-mail</label>
            <input type="email" name="email" required>
        </div>
        <div class="form-group">
            <label>Hasło</label>
            <input type="password" name="password" required>
        </div>
        <button type="submit" class="btn">Zaloguj się</button>
    </form>
    <p style="text-align: center; margin-top: 1rem;">
        Nie masz konta? <a href="register.php">Zarejestruj się</a>
    </p>
</div>

<?php require_once '../includes/footer.php'; ?>