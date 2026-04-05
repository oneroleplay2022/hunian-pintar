<?php
/**
 * Header include - HTML head section
 * Digunakan di semua halaman: <?php include 'includes/header.php'; ?>
 */

// Set timezone Indonesia
date_default_timezone_set('Asia/Jakarta');

// Load backend classes
require_once __DIR__ . '/../classes/Auth.php';
require_once __DIR__ . '/../classes/Helpers.php';
require_once __DIR__ . '/../classes/PlanHelper.php';

// Start session & check login
Auth::init();
if (!Auth::check()) {
    header('Location: login.php');
    exit;
}

// Get current user
$currentUser = Auth::user();

// Load Global Settings
$appSettings = ['app_name' => 'WargaKu', 'maintenance_mode' => false, 'currency' => 'IDR'];
$settingsFile = __DIR__ . '/../config/app_settings.json';
if (file_exists($settingsFile)) {
    $loadedSettings = json_decode(file_get_contents($settingsFile), true);
    if (is_array($loadedSettings)) {
        $appSettings = array_merge($appSettings, $loadedSettings);
    }
}
$GLOBAL_SETTINGS = $appSettings;

// Maintenance Mode Check
if (!empty($appSettings['maintenance_mode']) && (!isset($currentUser['role']) || $currentUser['role'] !== 'superadmin')) {
    http_response_code(503);
    die('<!DOCTYPE html>
    <html lang="id">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <style>
            html, body { overflow: hidden; margin: 0; padding: 0; height: 100%; width: 100%; background: #0f172a; }
            .m-bg { width:100%; height:100%; object-fit: cover; position: absolute; top:0; left:0; }
            .desktop-img { display: block; }
            .mobile-img { display: none; }
            @media only screen and (max-width: 768px) {
                .desktop-img { display: none !important; }
                .mobile-img { display: block !important; }
            }
        </style>
    </head>
    <body style="margin:0;">
        <div style="font-family:\'Inter\', sans-serif; position:fixed; top:0; left:0; width:100%; height:100vh; background: #0f172a; overflow:hidden; z-index:99999;">
            <img src="maintenance.png" alt="Maintenance" class="m-bg desktop-img">
            <img src="maintenance_mobile.png" alt="Maintenance" class="m-bg mobile-img">
            <div style="position:absolute; bottom:50px; left:50%; transform:translateX(-50%); z-index:100000;">
                <a href="logout.php" style="padding: 14px 32px; background: rgba(0,0,0,0.6); color: white; text-decoration: none; font-weight: 700; border: 1px solid rgba(255,255,255,0.3); border-radius: 50px; backdrop-filter: blur(8px); transition: all 0.3s; box-shadow: 0 10px 25px rgba(0,0,0,0.3); white-space: nowrap; display: inline-block;">KEMBALI KE LOGIN</a>
            </div>
        </div>
    </body>
    </html>');
}

// SaaS Tenant Isolation Check (Auto-Suspend / Expired)
if ($currentUser && isset($currentUser['tenant_id']) && $currentUser['role'] !== 'superadmin') {
    $db = Database::getInstance();
    $tenant = $db->fetch("SELECT subscription_status, expired_at FROM tenants WHERE id = ?", [$currentUser['tenant_id']]);
    
    if ($tenant) {
        // Lazy auto-suspend if date has passed
        if ($tenant['expired_at'] && strtotime($tenant['expired_at']) < strtotime('today') && in_array($tenant['subscription_status'], ['active', 'trial'])) {
            $db->update('tenants', ['subscription_status' => 'suspended'], 'id = ?', [$currentUser['tenant_id']]);
            $tenant['subscription_status'] = 'suspended';
            
            // Otomatis terbitkan tagihan bulan berikutnya jika belum ada yang pending
            $pending = $db->fetchColumn("SELECT COUNT(*) FROM subscriptions WHERE tenant_id = ? AND payment_status = 'pending'", [$currentUser['tenant_id']]);
            if ($pending == 0) {
                $db->insert('subscriptions', [
                    'tenant_id' => $currentUser['tenant_id'],
                    'amount' => getTenantPlanPrice($currentUser['tenant_id']),
                    'payment_status' => 'pending',
                    'expired_at' => date('Y-m-d', strtotime('+30 days'))
                ]);
            }
        }
        
        // If suspended or expired, block access
        if (in_array($tenant['subscription_status'], ['expired', 'suspended'])) {
            $currentPage = basename($_SERVER['PHP_SELF']);
            
            if ($currentUser['role'] === 'admin') {
                // Admin must go to saas_payment.php
                if ($currentPage !== 'saas_payment.php' && $currentPage !== 'logout.php' && $currentPage !== 'login.php') {
                    header('Location: saas_payment.php');
                    exit;
                }
            } else {
                // Warga/others go to blocked.php
                if ($currentPage !== 'blocked.php' && $currentPage !== 'logout.php' && $currentPage !== 'login.php') {
                    header('Location: blocked.php');
                    exit;
                }
            }
        }
    }
}

// Default page title
$pageTitle = $pageTitle ?? 'Transparansi Warga';
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <script>
    (function() {
      const theme = localStorage.getItem('theme') || 'light';
      document.documentElement.setAttribute('data-theme', theme);
    })();
  </script>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="description" content="Aplikasi Transparansi & Manajemen Warga - Platform SaaS untuk manajemen lingkungan">
  <?php if (!empty($appSettings['favicon_path'])): ?>
  <link rel="icon" type="image/x-icon" href="<?= htmlspecialchars($appSettings['favicon_path']) ?>">
  <?php endif; ?>
  <title><?= htmlspecialchars($pageTitle) ?> - <?= htmlspecialchars($appSettings['app_name']) ?></title>

  <!-- Google Font: Inter -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

  <!-- Lucide Icons -->
  <script src="https://unpkg.com/lucide@latest/dist/umd/lucide.js"></script>

  <!-- Chart.js -->
  <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>

  <!-- App CSS -->
  <link rel="stylesheet" href="css/style.css">
</head>
<body>
 
<?php if (Auth::isImpersonating()): ?>
<div style="background:linear-gradient(90deg, #f59e0b, #d97706); color:white; padding:10px 20px; display:flex; justify-content:space-between; align-items:center; font-weight:600; font-size:0.9rem; z-index:9999; position:sticky; top:0; box-shadow:0 2px 10px rgba(0,0,0,0.1);">
    <div>
        <i data-lucide="user-check" style="width:16px; height:16px; vertical-align:middle; margin-right:8px;"></i>
        Mode Intip: Anda sedang login sebagai <span style="text-decoration:underline;"><?= htmlspecialchars($currentUser['name']) ?></span> (Tenant ID: <?= $currentUser['tenant_id'] ?>)
    </div>
    <a href="logout.php" style="background:rgba(0,0,0,0.2); color:white; padding:5px 15px; border-radius:50px; text-decoration:none; font-size:0.8rem; border:1px solid rgba(255,255,255,0.3); transition:background 0.3s;" onmouseover="this.style.background='rgba(0,0,0,0.4)'" onmouseout="this.style.background='rgba(0,0,0,0.2)'">
        BERHENTI & KEMBALI KE PUSAT
    </a>
</div>
<?php endif; ?>
