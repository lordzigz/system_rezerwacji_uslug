<?php
// public/login.php
require_once '../includes/db.php';
require_once '../includes/auth.php';

$error = '';

// Jeśli użytkownik jest już zalogowany, odeślij go do panelu
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
        // Pobieramy użytkownika o podanym e-mailu
        $stmt = $pdo->prepare("SELECT id, name, surname, password, role, active FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch();

        // Sprawdzamy, czy użytkownik istnieje, czy konto jest aktywne i czy hasło pasuje
        if ($user && $user['active'] == 1 && password_verify($password, $user['password'])) {
            // Logowanie udane - zapisujemy dane w sesji
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['name'] = $user['name'];
            $_SESSION['surname'] = $user['surname'];
            $_SESSION['role'] = $user['role'];

            // Przekierowanie do panelu
            header("Location: dashboard.php");
            exit;
        } else {
            $error = "Nieprawidłowy e-mail lub hasło, albo konto jest nieaktywne.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <title>Logowanie - Studio Włosów</title>
</head>
<body>
    <h1>Zaloguj się</h1>
    
    <?php if ($error): ?>
        <p style="color: red;"><?= htmlspecialchars($error) ?></p>
    <?php endif; ?>
    
    <form method="POST" action="login.php">
        <div>
            <label>E-mail: <input type="email" name="email" required></label>
        </div>
        <div>
            <label>Hasło: <input type="password" name="password" required></label>
        </div>
        <button type="submit">Zaloguj się</button>
    </form>
    <p>Nie masz konta? <a href="register.php">Zarejestruj się</a></p>
</body>
</html>