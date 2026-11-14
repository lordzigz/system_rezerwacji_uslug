<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

/**
 * Sprawdza, czy użytkownik jest zalogowany.
 * Jeśli nie, przekierowuje go na stronę logowania.
 */
function require_login() {
    if (!isset($_SESSION['user_id'])) {
        header("Location: login.php");
        exit;
    }
}

/**
 * Sprawdza, czy użytkownik ma odpowiednią rolę.
 * Jeśli nie, blokuje dostęp (np. klient nie wejdzie do panelu admina).
 */
function require_role($role) {
    require_login();
    if ($_SESSION['role'] !== $role) {
        // Jeśli rola się nie zgadza, wyrzucamy na stronę główną
        header("Location: index.php");
        exit;
    }
}