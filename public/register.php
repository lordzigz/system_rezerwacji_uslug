<?php
// public/register.php
require_once '../includes/db.php';
require_once '../includes/auth.php';

$error = '';
$success = '';

// Sprawdzamy, czy formularz został wysłany metodą POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Pobieramy dane i usuwamy zbędne spacje na początku i końcu
    $name = trim($_POST['name'] ?? '');
    $surname = trim($_POST['surname'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $password = $_POST['password'] ?? '';
    $password_confirm = $_POST['password_confirm'] ?? '';

    // Walidacja serwerowa
    if (empty($name) || empty($surname) || empty($email) || empty($password)) {
        $error = "Wypełnij wszystkie wymagane pola.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Podaj poprawny adres e-mail.";
    } elseif ($password !== $password_confirm) {
        $error = "Hasła nie są identyczne.";
    } elseif (strlen($password) < 6) {
        $error = "Hasło musi mieć co najmniej 6 znaków.";
    } else {
        // Sprawdzamy, czy email już istnieje w bazie
        $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->execute([$email]);
        
        if ($stmt->fetch()) {
            $error = "Ten adres e-mail jest już zajęty.";
        } else {
            // Hashujemy hasło 
            $password_hash = password_hash($password, PASSWORD_DEFAULT);

            // Zapisujemy użytkownika do bazy (domyślna rola to klient)
            $stmt = $pdo->prepare("INSERT INTO users (name, surname, email, phone, password, role) VALUES (?, ?, ?, ?, ?, 'klient')");
            
            if ($stmt->execute([$name, $surname, $email, $phone, $password_hash])) {
                $success = "Konto zostało utworzone! Możesz się teraz zalogować.";
            } else {
                $error = "Wystąpił błąd podczas tworzenia konta.";
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <title>Rejestracja - Studio Włosów</title>
</head>
<body>
    <h1>Załóż konto</h1>
    
    <?php if ($error): ?>
        <p style="color: red;"><?= htmlspecialchars($error) ?></p>
    <?php endif; ?>
    
    <?php if ($success): ?>
        <p style="color: green;"><?= htmlspecialchars($success) ?></p>
        <p><a href="login.php">Przejdź do logowania</a></p>
    <?php else: ?>
        <form method="POST" action="register.php">
            <div>
                <label>Imię: <input type="text" name="name" required></label>
            </div>
            <div>
                <label>Nazwisko: <input type="text" name="surname" required></label>
            </div>
            <div>
                <label>E-mail: <input type="email" name="email" required></label>
            </div>
            <div>
                <label>Telefon: <input type="text" name="phone"></label>
            </div>
            <div>
                <label>Hasło: <input type="password" name="password" required></label>
            </div>
            <div>
                <label>Powtórz hasło: <input type="password" name="password_confirm" required></label>
            </div>
            <button type="submit">Zarejestruj się</button>
        </form>
    <?php endif; ?>
</body>
</html>