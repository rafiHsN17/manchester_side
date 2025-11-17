<?php
session_start();

// Database configuration untuk XAMPP
define('DB_HOST', 'localhost');
define('DB_NAME', 'manchester_side');  // ← Nama database
define('DB_USER', 'root');             // ← Username XAMPP
define('DB_PASS', '');                 // ← Password XAMPP (kosong)

try {
    $pdo = new PDO("mysql:host=".DB_HOST.";dbname=".DB_NAME.";charset=utf8", DB_USER, DB_PASS);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    die("Koneksi database gagal: " . $e->getMessage());
}

// Helper functions
function redirect($url) {
    header("Location: $url");
    exit();
}

function isAdminLoggedIn() {
    return isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true;
}
?>