<?php
require_once 'classes/Auth.php';
require_once 'classes/Database.php';
require_once 'classes/Notification.php';

Auth::requireLogin();
$user = Auth::user();

$success = Notification::addSystemNotification(
    $user['tenant_id'] ?? null,
    $user['id'],
    'Halo ' . $user['name'] . '!',
    'Ini adalah notifikasi uji coba yang baru saja dikirim pada ' . date('H:i:s') . '. Lonceng notifikasi Anda sekarang seharusnya menyala.',
    $user['role'] === 'superadmin' ? 'saas_dashboard.php' : 'index.php'
);

if ($success) {
    echo "<div style='font-family:sans-serif; text-align:center; padding:50px;'>";
    echo "<h2 style='color:green;'>✅ Notifikasi Berhasil Dikirim!</h2>";
    echo "<p>Silakan lihat ke bagian **ikon lonceng** di pojok kanan atas layar Anda.</p>";
    echo "<p><a href='index.php'>Kembali ke Dashboard</a></p>";
    echo "</div>";
} else {
    echo "Gagal mengirim notifikasi.";
}
