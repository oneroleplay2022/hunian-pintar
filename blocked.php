<?php
$pageTitle = 'Layanan Dibekukan';
require_once 'classes/Auth.php';

Auth::init();
if (!Auth::check()) {
    header('Location: login.php');
    exit;
}
?>
<?php include 'includes/header.php'; ?>
<style>
.blocked-wrapper {
    display: flex;
    align-items: center;
    justify-content: center;
    height: 100vh;
    background: var(--bg-main);
    color: var(--text-main);
    text-align: center;
    padding: 20px;
    box-sizing: border-box;
}
.blocked-content {
    background: var(--bg-card);
    padding: 40px;
    border-radius: var(--radius-lg);
    box-shadow: var(--shadow-lg);
    max-width: 500px;
    width: 100%;
    border-top: 5px solid var(--danger);
}
</style>
<div class="blocked-wrapper">
    <div class="blocked-content">
        <div style="font-size: 3rem; color: var(--danger); margin-bottom: 20px;">🔒</div>
        <h1 style="font-size: 1.5rem; margin-bottom: 15px;">Layanan Dibekukan Sementara</h1>
        <p style="color: var(--text-muted); margin-bottom: 30px; line-height: 1.6;">
            Mohon maaf, akses operasional aplikasi <b><?= htmlspecialchars($GLOBAL_SETTINGS['app_name']) ?></b> untuk layanan komunitas / perumahan Anda saat ini sedang diberhentikan karena masa aktif berlangganan telah berakhir.
        </p>
        <p style="font-size: 0.9rem; margin-bottom: 30px; padding: 15px; background: rgba(239,68,68,0.1); border-radius: var(--radius-md);">
            Hanya Pengurus Inti (Admin Perumahan) yang dapat mengakses portal penyelesaian masalah administrasi ini. Silakan hubungi RT/RW atau Pengurus Anda.
        </p>
        <a href="logout.php" class="btn btn-secondary" style="width: 100%;">Keluar (Logout Aplikasi)</a>
    </div>
</div>
