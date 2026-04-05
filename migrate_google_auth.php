<?php
require_once __DIR__ . '/classes/Database.php';

$db = Database::getInstance();
$conn = $db->getConnection();

try {
    echo "Memulai migrasi database...\n";

    // 1. Tambah kolom google_id jika belum ada
    $checkGoogleId = $db->fetchAll("SHOW COLUMNS FROM users LIKE 'google_id'");
    if (empty($checkGoogleId)) {
        $db->query("ALTER TABLE users ADD COLUMN google_id VARCHAR(255) NULL UNIQUE AFTER password");
        echo "- Kolom 'google_id' ditambahkan.\n";
    } else {
        echo "- Kolom 'google_id' sudah ada.\n";
    }

    // 2. Tambah kolom email_verified jika belum ada
    $checkEmailVerified = $db->fetchAll("SHOW COLUMNS FROM users LIKE 'email_verified'");
    if (empty($checkEmailVerified)) {
        $db->query("ALTER TABLE users ADD COLUMN email_verified TINYINT(1) DEFAULT 0 AFTER google_id");
        echo "- Kolom 'email_verified' ditambahkan.\n";
    } else {
        echo "- Kolom 'email_verified' sudah ada.\n";
    }

    echo "Migrasi selesai dengan sukses!\n";
} catch (Exception $e) {
    echo "Terjadi kesalahan saat migrasi: " . $e->getMessage() . "\n";
    exit(1);
}
