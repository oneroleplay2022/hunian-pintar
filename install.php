<?php
/**
 * WargaKu - Database Installer
 * Jalankan di browser: http://localhost/warga/install.php
 */

error_reporting(E_ALL);
ini_set('display_errors', 1);

$host = '127.0.0.1';
$port = '3307';
$user = 'root';
$pass = '';
$dbname = 'wargaku';

echo "<pre style='font-family:monospace;background:#1a1a2e;color:#e0e0e0;padding:20px;border-radius:8px;max-width:800px;margin:40px auto;line-height:1.6;'>";
echo "╔═══════════════════════════════════════╗\n";
echo "║   WargaKu - Database Installer        ║\n";
echo "╚═══════════════════════════════════════╝\n\n";

try {
    // 1. Connect WITHOUT database, with multi-statement support
    $pdo = new PDO("mysql:host=$host;port=$port", $user, $pass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    ]);
    echo "✅ Koneksi MySQL berhasil (port $port).\n";

    // 2. Drop & recreate database for clean install
    $pdo->exec("DROP DATABASE IF EXISTS `$dbname`");
    $pdo->exec("CREATE DATABASE `$dbname` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
    $pdo->exec("USE `$dbname`");
    echo "✅ Database '$dbname' dibuat.\n";

    // 3. Import schema (skip CREATE DATABASE and USE lines since we already did it)
    echo "\n📦 Importing schema.sql...\n";
    $schema = file_get_contents(__DIR__ . '/sql/schema.sql');
    // Remove CREATE DATABASE and USE lines (we already handled them)
    $schema = preg_replace('/^CREATE DATABASE.*$/m', '', $schema);
    $schema = preg_replace('/^USE .*$/m', '', $schema);
    $pdo->exec($schema);
    echo "✅ Schema berhasil diimport!\n";

    // 4. Generate password hashes
    $hashAdmin = password_hash('password', PASSWORD_BCRYPT);
    $hashWarga = password_hash('warga123', PASSWORD_BCRYPT);
    echo "\n🔑 Password hashes generated.\n";
    echo "   admin@wargaku.id = 'password'\n";
    echo "   semua user lain  = 'warga123'\n";

    // 5. Import seed data
    echo "\n🌱 Importing seed.sql...\n";
    $seed = file_get_contents(__DIR__ . '/sql/seed.sql');
    // Remove USE lines
    $seed = preg_replace('/^USE .*$/m', '', $seed);
    // Replace placeholder hashes
    $seed = str_replace('$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', $hashAdmin, $seed);
    $seed = str_replace('$2y$10$LKaJMz7JG5ZKtVL6bSODdu3O5GPjEH1SvFQ0NP.PXjUBDaHVaO6Gy', $hashWarga, $seed);

    // Execute the entire seed SQL as multi-statement
    $pdo->exec($seed);
    echo "✅ Seed data berhasil diimport!\n";

    // 6. Summary
    echo "\n" . str_repeat("─", 44) . "\n";
    echo "📊 Database Summary:\n";
    
    $tables = $pdo->query("SHOW TABLES")->fetchAll(PDO::FETCH_COLUMN);
    echo "   Total Tables: " . count($tables) . "\n\n";
    
    foreach (['users', 'blocks', 'houses', 'residents', 'invoices', 'news', 'vehicles', 'cashflows'] as $table) {
        if (in_array($table, $tables)) {
            $c = $pdo->query("SELECT COUNT(*) FROM $table")->fetchColumn();
            echo "   ✓ $table: $c rows\n";
        }
    }
    
    echo "\n✅ INSTALASI SELESAI!\n\n";
    echo "🔗 Login: <a href='login.php' style='color:#818cf8;'>http://localhost/warga/login.php</a>\n";
    echo "📧 Admin : admin@wargaku.id / password\n";
    echo "📧 Warga : siti@wargaku.id / warga123\n";
    echo "\n⚠️  HAPUS FILE INI setelah instalasi selesai!\n";

} catch (PDOException $e) {
    echo "❌ ERROR: " . $e->getMessage() . "\n\n";
    echo "Pastikan:\n";
    echo "1. XAMPP MySQL sudah distart (port $port)\n";
    echo "2. User root tanpa password\n";
}

echo "</pre>";
