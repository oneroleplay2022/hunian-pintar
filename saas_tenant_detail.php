<?php
date_default_timezone_set('Asia/Jakarta');
require_once 'classes/Auth.php';
require_once 'classes/Database.php';

Auth::requireRole('superadmin');
$db = Database::getInstance();

$id = (int)($_GET['id'] ?? 0);
if (!$id) {
    header('Location: saas_tenants.php');
    exit;
}

$tenant = $db->fetch("
    SELECT t.*, p.name as plan_name, p.price as plan_price, p.max_houses as plan_max_houses
    FROM tenants t 
    LEFT JOIN subscription_plans p ON t.plan_id = p.id 
    WHERE t.id = ?
", [$id]);

if (!$tenant) {
    die("Perumahan tidak ditemukan. Mungkin sudah dihapus.");
}

$pageTitle = 'Detail Perumahan - ' . htmlspecialchars($tenant['name']);

// Get admins for this tenant
$admins = $db->fetchAll("SELECT * FROM users WHERE tenant_id = ? AND role='admin'", [$id]);

// Get X-Ray stats
try {
    $stats = [
        'blocks' => $db->fetchColumn("SELECT COUNT(*) FROM blocks WHERE tenant_id=?", [$id]),
        'houses' => $db->fetchColumn("SELECT COUNT(*) FROM houses WHERE tenant_id=?", [$id]),
        'residents' => $db->fetchColumn("SELECT COUNT(*) FROM residents WHERE tenant_id=?", [$id]),
        'users' => $db->fetchColumn("SELECT COUNT(*) FROM users WHERE tenant_id=?", [$id])
    ];
} catch (Exception $e) {
    $stats = ['blocks' => 0, 'houses' => 0, 'residents' => 0, 'users' => count($admins)];
}

// Subscription billing history
$billingHistory = $db->fetchAll("SELECT * FROM subscriptions WHERE tenant_id = ? ORDER BY created_at DESC LIMIT 10", [$id]);

// Remaining days helper
function getRemainingDaysDetail($date) {
    if (!$date) return ['text' => '-', 'class' => ''];
    $now = new DateTime('today');
    $expired = new DateTime($date);
    $diff = $now->diff($expired);
    if ($now > $expired) return ['text' => 'Expired (' . $diff->days . ' hari lalu)', 'class' => 'color:var(--danger);font-weight:700;'];
    if ($diff->days <= 7) return ['text' => $diff->days . ' Hari Lagi', 'class' => 'color:var(--warning);font-weight:700;'];
    return ['text' => $diff->days . ' Hari Lagi', 'class' => 'color:var(--success);font-weight:600;'];
}

$remaining = getRemainingDaysDetail($tenant['expired_at']);
$planPrice = $tenant['plan_price'] ?? 0;
?>
<?php include 'includes/header.php'; ?>

<div class="app-layout">
  <?php include 'includes/sidebar_saas.php'; ?>

  <div class="main-wrapper">
    <?php include 'includes/topbar.php'; ?>

    <main class="main-content">
      <div class="page-header">
        <div>
          <h1>Detail Perumahan</h1>
          <div class="breadcrumb">
            <span class="separator">/</span>
            <a href="saas_tenants.php" style="color:var(--primary);text-decoration:none;">Daftar Klien</a>
            <span class="separator">/</span>
            <span><?= htmlspecialchars($tenant['name']) ?></span>
          </div>
        </div>
        <a href="saas_tenants.php" class="btn btn-secondary btn-sm"><i data-lucide="arrow-left" style="width:16px;height:16px;"></i> Kembali</a>
      </div>

      <!-- Info Dasar + Biaya Langganan -->
      <div style="display:grid;grid-template-columns:1.2fr 0.8fr;gap:20px;margin-bottom:20px;">
        <div class="card">
          <div class="card-header border-bottom">
            <h3 class="card-title">Informasi Dasar Klien</h3>
          </div>
          <div style="padding:20px;overflow-x:auto;">
            <table style="width:100%;text-align:left;border-spacing:0 10px;font-size:0.92rem;">
              <tr>
                <th style="width:180px;color:var(--text-muted);font-weight:600;padding:4px 0;">Nama Perumahan</th>
                <td style="padding:4px 0;"><strong><?= htmlspecialchars($tenant['name']) ?></strong></td>
              </tr>
              <tr>
                <th style="color:var(--text-muted);font-weight:600;vertical-align:top;padding:4px 0;">Alamat Lengkap</th>
                <td style="padding:4px 0;"><?= nl2br(htmlspecialchars($tenant['address'] ?? 'Belum diisi')) ?></td>
              </tr>
              <tr>
                <th style="color:var(--text-muted);font-weight:600;padding:4px 0;">Status Berlangganan</th>
                <td style="padding:4px 0;">
                    <span class="badge badge-<?= $tenant['subscription_status']==='active'?'success':($tenant['subscription_status']==='trial'?'warning':'danger') ?>"><?= strtoupper($tenant['subscription_status']) ?></span>
                </td>
              </tr>
              <tr>
                <th style="color:var(--text-muted);font-weight:600;padding:4px 0;">Kuota Unit Rumah</th>
                <td style="padding:4px 0;"><?= $tenant['total_houses'] ?> unit bangunan</td>
              </tr>
              <tr>
                <th style="color:var(--text-muted);font-weight:600;padding:4px 0;">Tgl Jatuh Tempo</th>
                <td style="padding:4px 0;">
                    <strong style="<?= $remaining['class'] ?>"><?= $tenant['expired_at'] ? date('d F Y', strtotime($tenant['expired_at'])) : '-' ?></strong>
                    <span style="font-size:0.8rem;color:var(--text-muted);margin-left:8px;">(<?= $remaining['text'] ?>)</span>
                </td>
              </tr>
              <tr>
                <th style="color:var(--text-muted);font-weight:600;padding:4px 0;">Tgl Bergabung</th>
                <td style="padding:4px 0;"><?= date('d M Y H:i', strtotime($tenant['created_at'])) ?></td>
              </tr>
            </table>
          </div>
        </div>

        <!-- Card Biaya Langganan -->
        <div class="card">
          <div class="card-header border-bottom">
            <h3 class="card-title">Biaya Langganan Bulanan</h3>
          </div>
          <div style="padding:20px;">
            <div style="text-align:center;padding:15px 0;">
                <div style="font-size:0.8rem;text-transform:uppercase;letter-spacing:1px;color:var(--text-muted);font-weight:600;">Paket Aktif</div>
                <div style="font-size:1.4rem;font-weight:800;color:var(--primary);margin:6px 0;"><?= strtoupper($tenant['plan_name'] ?? 'LITE') ?></div>
                <div style="font-size:2rem;font-weight:800;color:<?= $planPrice > 0 ? 'var(--success)' : 'var(--text-muted)' ?>;margin:10px 0;">
                    <?= $planPrice > 0 ? 'Rp ' . number_format($planPrice, 0, ',', '.') : 'Gratis' ?>
                </div>
                <div style="font-size:0.8rem;color:var(--text-muted);">per bulan</div>
            </div>
            
            <div style="border-top:1px solid var(--border-color);margin-top:15px;padding-top:15px;">
                <div style="display:flex;justify-content:space-between;margin-bottom:8px;">
                    <span style="font-size:0.85rem;color:var(--text-muted);">Maks Rumah (Paket)</span>
                    <strong style="font-size:0.85rem;"><?= $tenant['plan_max_houses'] ? number_format($tenant['plan_max_houses']) . ' unit' : '-' ?></strong>
                </div>
                <div style="display:flex;justify-content:space-between;margin-bottom:8px;">
                    <span style="font-size:0.85rem;color:var(--text-muted);">Rumah Terdaftar</span>
                    <strong style="font-size:0.85rem;"><?= number_format($stats['houses']) ?> unit</strong>
                </div>
                <div style="display:flex;justify-content:space-between;">
                    <span style="font-size:0.85rem;color:var(--text-muted);">Sisa Kuota</span>
                    <?php $maxH = $tenant['plan_max_houses'] ?? $tenant['total_houses']; $sisaK = max(0, $maxH - $stats['houses']); ?>
                    <strong style="font-size:0.85rem;color:<?= $sisaK <= 5 ? 'var(--danger)' : 'var(--success)' ?>;"><?= number_format($sisaK) ?> unit</strong>
                </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Statistik X-Ray -->
      <div class="card" style="margin-bottom:20px;">
        <div class="card-header border-bottom">
          <h3 class="card-title">Statistik Penggunaan (X-Ray)</h3>
        </div>
        <div class="stats-grid" style="padding:20px;grid-gap:15px;grid-template-columns:repeat(4, 1fr);">
          <div style="background:var(--bg-main);padding:15px;border-radius:var(--radius-md);border:1px solid var(--border-color);text-align:center;">
            <div style="font-size:2rem;font-weight:700;color:var(--primary);"><?= $stats['users'] ?></div>
            <div style="font-size:0.85rem;color:var(--text-muted);margin-top:4px;">Total Akun Pengguna</div>
          </div>
          <div style="background:var(--bg-main);padding:15px;border-radius:var(--radius-md);border:1px solid var(--border-color);text-align:center;">
            <div style="font-size:2rem;font-weight:700;color:var(--success);"><?= $stats['residents'] ?></div>
            <div style="font-size:0.85rem;color:var(--text-muted);margin-top:4px;">Warga Teregistrasi</div>
          </div>
          <div style="background:var(--bg-main);padding:15px;border-radius:var(--radius-md);border:1px solid var(--border-color);text-align:center;">
            <div style="font-size:2rem;font-weight:700;color:var(--warning);"><?= $stats['houses'] ?></div>
            <div style="font-size:0.85rem;color:var(--text-muted);margin-top:4px;">Total Rumah/Kavling</div>
          </div>
          <div style="background:var(--bg-main);padding:15px;border-radius:var(--radius-md);border:1px solid var(--border-color);text-align:center;">
            <div style="font-size:2rem;font-weight:700;color:var(--danger);"><?= $stats['blocks'] ?></div>
            <div style="font-size:0.85rem;color:var(--text-muted);margin-top:4px;">Pemetaan Blok</div>
          </div>
        </div>
      </div>

      <!-- Riwayat Tagihan -->
      <div class="card" style="margin-bottom:20px;">
        <div class="card-header border-bottom">
          <h3 class="card-title">Riwayat Tagihan SaaS</h3>
        </div>
        <div class="table-container">
          <table class="table">
            <thead>
              <tr>
                <th>ID Tagihan</th>
                <th>Jumlah</th>
                <th>Status Bayar</th>
                <th>Jatuh Tempo</th>
                <th>Dibuat</th>
              </tr>
            </thead>
            <tbody>
              <?php if (empty($billingHistory)): ?>
              <tr><td colspan="5" style="text-align:center;padding:20px;color:var(--text-muted);">Belum ada tagihan untuk perumahan ini.</td></tr>
              <?php endif; ?>
              <?php foreach ($billingHistory as $b): ?>
              <tr>
                <td><strong>#INV-<?= $b['id'] ?></strong></td>
                <td><strong style="color:var(--success);">Rp <?= number_format($b['amount'], 0, ',', '.') ?></strong></td>
                <td>
                    <?php
                    $bBadge = 'badge-warning';
                    if ($b['payment_status'] === 'paid') $bBadge = 'badge-success';
                    elseif ($b['payment_status'] === 'overdue') $bBadge = 'badge-danger';
                    ?>
                    <span class="badge <?= $bBadge ?>"><?= strtoupper($b['payment_status']) ?></span>
                </td>
                <td><?= $b['expired_at'] ? date('d M Y', strtotime($b['expired_at'])) : '-' ?></td>
                <td><?= date('d M Y', strtotime($b['created_at'])) ?></td>
              </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        </div>
      </div>

      <!-- Daftar Admin/Pengurus -->
      <div class="card">
        <div class="card-header border-bottom">
          <h3 class="card-title">Daftar Akun Pengurus (Admins)</h3>
        </div>
        <div class="table-container">
          <table class="table">
            <thead>
              <tr>
                <th>ID Akun</th>
                <th>Nama Pengurus</th>
                <th>Alamat Email</th>
                <th>Role</th>
                <th>Tanggal Pendaftaran</th>
              </tr>
            </thead>
            <tbody>
              <?php if (empty($admins)): ?>
              <tr><td colspan="5" style="text-align:center;padding:20px;color:var(--text-muted);">Tidak ada pengurus yang terdaftar di sistem.</td></tr>
              <?php endif; ?>
              
              <?php foreach ($admins as $a): ?>
              <tr>
                <td><strong>#USR-<?= $a['id'] ?></strong></td>
                <td><div style="display:flex;align-items:center;gap:10px;">
                  <div style="width:32px;height:32px;border-radius:50%;background:var(--primary);color:white;display:flex;align-items:center;justify-content:center;font-weight:bold;font-size:0.8rem;">
                    <?= strtoupper(substr($a['name'], 0, 1)) ?>
                  </div>
                  <b><?= htmlspecialchars($a['name']) ?></b>
                </div></td>
                <td><?= htmlspecialchars($a['email']) ?></td>
                <td><span class="badge badge-success">Admin Cluster</span></td>
                <td><?= date('d M Y', strtotime($a['created_at'])) ?></td>
              </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        </div>
      </div>

    </main>
  </div>
</div>

<?php include 'includes/footer.php'; ?>
