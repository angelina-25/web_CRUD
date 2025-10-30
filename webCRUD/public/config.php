<?php
// config.php - Konfigurasi Database Toko Sport
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'toko_sport');

try {
    $pdo = new PDO(
        "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4",
        DB_USER,
        DB_PASS,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false
        ]
    );
} catch (PDOException $e) {
    die("Koneksi database gagal: " . $e->getMessage());
}

// Fungsi untuk sanitasi input
function sanitize($data) {
    return htmlspecialchars(strip_tags(trim($data)));
}

// Fungsi untuk validasi input
function validate($data, $field) {
    if (empty($data)) {
        return "$field tidak boleh kosong";
    }
    return null;
}
?>