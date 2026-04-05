<?php
date_default_timezone_set('Asia/Jakarta');
require_once 'classes/Auth.php';
require_once 'classes/Database.php';
require_once 'classes/Notification.php';
require_once 'classes/PlanHelper.php';

// Initialize Database & Auth
$db = Database::getInstance();
Auth::requireRole('superadmin');

// Auto-suspend expired tenants and generate invoices
$expired_tenants = $db->fetchAll("SELECT id FROM tenants WHERE expired_at < CURDATE() AND subscription_status IN ('active', 'trial')");
if (!empty($expired_tenants)) {
    foreach ($expired_tenants as $t) {
        $db->update('tenants', ['subscription_status' => 'suspended'], 'id = ?', [$t['id']]);
        
        $pending = $db->fetchColumn("SELECT COUNT(*) FROM subscriptions WHERE tenant_id = ? AND payment_status = 'pending'", [$t['id']]);
        if ($pending == 0) {
            $db->insert('subscriptions', [
                'tenant_id' => $t['id'],
                'amount' => getTenantPlanPrice($t['id']),
                'payment_status' => 'pending',
                'expired_at' => date('Y-m-d', strtotime('+30 days'))
            ]);
        }
    }
}
 
// Handle "Login As" / Impersonation
if (isset($_GET['impersonate_user'])) {
    $target_uid = (int)$_GET['impersonate_user'];
    if (Auth::impersonate($target_uid)) {
        header('Location: index.php'); // Go to tenant dashboard
        exit;
    }
}
 
// Fetch All Plans
$plans = $db->fetchAll("SELECT * FROM subscription_plans ORDER BY price ASC");

// Handle form submissions has been moved to saas_tenants.php

$query = "SELECT t.*, p.name as plan_name,
          (SELECT id FROM users u WHERE u.tenant_id = t.id AND u.role = 'admin' LIMIT 1) as first_admin_id,
          (SELECT COUNT(*) FROM users u WHERE u.tenant_id = t.id AND u.role = 'admin') as admin_count,
          (SELECT COUNT(*) FROM houses h WHERE h.tenant_id = t.id) as house_count,
          (SELECT COUNT(*) FROM residents r WHERE r.tenant_id = t.id) as resident_count
          FROM tenants t 
          LEFT JOIN subscription_plans p ON t.plan_id = p.id
          ORDER BY t.created_at DESC LIMIT 5";
$tenants = $db->fetchAll($query);
$allTenants = $db->fetchAll("SELECT subscription_status FROM tenants");

// Data for Charts (Monthly Revenue - Last 6 Months)
$revenue_data = $db->fetchAll("SELECT DATE_FORMAT(created_at, '%M') as month, SUM(amount) as total 
                               FROM subscriptions WHERE payment_status = 'paid' 
                               GROUP BY month ORDER BY created_at ASC LIMIT 6");
$growth_data = $db->fetchAll("SELECT DATE_FORMAT(created_at, '%M') as month, COUNT(*) as count 
                              FROM tenants GROUP BY month ORDER BY created_at ASC LIMIT 6");

$totalTenants = count($allTenants);
$activeTenants = count(array_filter($allTenants, fn($t) => $t['subscription_status'] === 'active'));
$trialTenants = count(array_filter($allTenants, fn($t) => $t['subscription_status'] === 'trial'));

// Financial Stats (SaaS Earnings)
$financials = $db->fetch("SELECT 
    SUM(CASE WHEN payment_status = 'paid' THEN amount ELSE 0 END) as total_revenue,
    SUM(CASE WHEN payment_status = 'pending' THEN amount ELSE 0 END) as pending_revenue
    FROM subscriptions");
$totalRevenue = $financials['total_revenue'] ?? 0;
$pendingRevenue = $financials['pending_revenue'] ?? 0;

function getRemainingDays($date) {
    if (!$date) return '-';
    $now = new DateTime();
    $expired = new DateTime($date);
    $diff = $now->diff($expired);
    if ($now > $expired) return '<span style="color:var(--danger);font-weight:700;">Expired</span>';
    return $diff->days . ' Hari';
}

?>
<?php include 'includes/header.php'; ?>

<div class="app-layout">
  <!-- Sidebar Khusus Superadmin -->
  <?php include 'includes/sidebar_saas.php'; ?>

  <div class="main-wrapper">
    <?php include 'includes/topbar.php'; ?>

    <main class="main-content">
      <div class="page-header">
        <div>
          <h1>Manajemen Perumahan (Tenant)</h1>
          <div class="breadcrumb">
            <span class="separator">/</span>
            <span>Dashboard Utama</span>
          </div>
        </div>
        <a href="saas_tenants.php" class="btn btn-primary btn-sm"><i data-lucide="building" style="width:16px;height:16px;"></i> Kelola Klien Perumahan</a>
      </div>



      <!-- Stats Row -->
      <div class="stats-grid" style="grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));">
        <div class="stat-card">
          <div class="stat-icon blue"><i data-lucide="building"></i></div>
          <div class="stat-info">
            <div class="stat-label">Total Perumahan</div>
            <div class="stat-value"><?= number_format($totalTenants) ?></div>
          </div>
        </div>
        <div class="stat-card">
          <div class="stat-icon green"><i data-lucide="dollar-sign"></i></div>
          <div class="stat-info">
            <div class="stat-label">Pendapatan SaaS (Lunas)</div>
            <div class="stat-value" style="font-size:1.1rem;color:var(--success);">Rp <?= number_format($totalRevenue, 0, ',', '.') ?></div>
          </div>
        </div>
        <div class="stat-card">
          <div class="stat-icon yellow"><i data-lucide="credit-card"></i></div>
          <div class="stat-info">
            <div class="stat-label">Piutang (Pending)</div>
            <div class="stat-value" style="font-size:1.1rem;color:var(--warning);">Rp <?= number_format($pendingRevenue, 0, ',', '.') ?></div>
          </div>
        </div>
        <div class="stat-card">
          <div class="stat-icon green"><i data-lucide="check-circle"></i></div>
          <div class="stat-info">
            <div class="stat-label">Berlangganan Aktif</div>
            <div class="stat-value"><?= number_format($activeTenants) ?></div>
          </div>
        </div>
      </div>
 
      <!-- Charts Row -->
      <div class="grid-2" style="display:grid; grid-template-columns: 1.2fr 0.8fr; gap:20px; margin-top:20px;">
          <div class="card">
              <div class="card-header border-bottom"><h3 class="card-title">Tren Pendapatan SaaS (6 Bln Terakhir)</h3></div>
              <div style="padding:20px;"><canvas id="revenueChart" height="150"></canvas></div>
          </div>
          <div class="card">
              <div class="card-header border-bottom"><h3 class="card-title">Pertumbuhan Tenant Baru</h3></div>
              <div style="padding:20px;"><canvas id="growthChart" height="235"></canvas></div>
          </div>
      </div>

      <!-- Data Table -->
      <div class="card" style="margin-top:20px;">
        <div class="card-header border-bottom" style="display:flex; justify-content:space-between; align-items:center;">
          <h3 class="card-title">Klien Perumahan Terbaru</h3>
          <a href="saas_tenants.php" style="font-size:0.85rem; color:var(--primary); text-decoration:none; font-weight:600;">Lihat Semua &rarr;</a>
        </div>
        <div class="table-container">
          <table class="table">
            <thead>
              <tr>
                <th>ID</th>
                <th>Tenant (Perumahan)</th>
                <th>Paket</th>
                <th>Status</th>
                <th>Rumah</th>
                <th>Warga</th>
                <th>Sisa Aktif</th>
                <th>Aksi</th>
              </tr>
            </thead>
            <tbody>
              <?php if (empty($tenants)): ?>
              <tr>
                <td colspan="8" style="text-align:center;padding:20px;color:var(--text-muted);">Belum ada perumahan / tenant yang terdaftar.</td>
              </tr>
              <?php endif; ?>
              
              <?php foreach ($tenants as $t): 
                $badge = 'badge-secondary';
                if ($t['subscription_status'] === 'active') $badge = 'badge-success';
                elseif ($t['subscription_status'] === 'trial') $badge = 'badge-warning';
                elseif ($t['subscription_status'] === 'suspended') $badge = 'badge-danger';
              ?>
              <tr>
                <td><strong>#<?= $t['id'] ?></strong></td>
                <td>
                    <div style="font-weight:700;color:var(--text-main);"><?= htmlspecialchars($t['name']) ?></div>
                    <small style="color:var(--text-muted);display:block;max-width:180px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;"><?= htmlspecialchars($t['address'] ?? '-') ?></small>
                </td>
                <td><span class="badge badge-info" style="font-size:0.65rem; background:rgba(59,130,246,0.1); color:#3b82f6; border:1px solid rgba(59,130,246,0.2);"><?= strtoupper($t['plan_name'] ?? 'LITE') ?></span></td>
                <td><span class="badge <?= $badge ?>" style="font-size:0.7rem;"><?= strtoupper($t['subscription_status']) ?></span></td>
                <td style="text-align:center;"><b><?= $t['house_count'] ?></b>/<?= $t['total_houses'] ?></td>
                <td style="text-align:center;"><b><?= $t['resident_count'] ?></b></td>
                <td>
                    <div style="font-size:0.85rem;font-weight:600;"><?= getRemainingDays($t['expired_at']) ?></div>
                    <small style="font-size:0.7rem;color:var(--text-muted);"><?= $t['expired_at'] ? date('d/m/y', strtotime($t['expired_at'])) : '-' ?></small>
                </td>
                <td>
                    <div style="display:flex;gap:4px;">
                      <a href="?impersonate_user=<?= $t['first_admin_id'] ?>" class="btn btn-icon btn-sm btn-secondary" title="Login As (Intip Dashboard)" style="color:var(--primary);"><i data-lucide="user-check" style="width:14px;height:14px;"></i></a>
                      <a href="saas_tenant_detail.php?id=<?= $t['id'] ?>" class="btn btn-icon btn-sm btn-secondary" title="Statistik & Detail Warga"><i data-lucide="bar-chart-2" style="width:14px;height:14px;"></i></a>
                    </div>
                </td>
              </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        </div>
      </div>
    </main>
  </div>
<!-- Modals moved to saas_tenants.php -->

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
// Chart Revenue
const ctxRev = document.getElementById('revenueChart').getContext('2d');
new Chart(ctxRev, {
    type: 'line',
    data: {
        labels: <?= json_encode(array_column($revenue_data, 'month')) ?>,
        datasets: [{
            label: 'Total Revenue (Rp)',
            data: <?= json_encode(array_column($revenue_data, 'total')) ?>,
            borderColor: '#10b981', background: 'rgba(16,185,129,0.1)', fill: true, tension: 0.4
        }]
    },
    options: { plugins: { legend: { display: false } }, scales: { y: { beginAtZero: true } } }
});

// Chart Growth
const ctxGrowth = document.getElementById('growthChart').getContext('2d');
new Chart(ctxGrowth, {
    type: 'bar',
    data: {
        labels: <?= json_encode(array_column($growth_data, 'month')) ?>,
        datasets: [{
            label: 'Tenant Baru',
            data: <?= json_encode(array_column($growth_data, 'count')) ?>,
            backgroundColor: '#3b82f6', borderRadius: 5
        }]
    },
    options: { plugins: { legend: { display: false } } }
});
</script>

<?php include 'includes/footer.php'; ?>
