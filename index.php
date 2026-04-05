<?php 
$pageTitle = 'Dashboard';
include 'includes/header.php'; 

// Redirection for Superadmin: They should not be on the Tenant Dashboard
if ($currentUser['role'] === 'superadmin') {
    echo "<script>window.location.href='saas_dashboard.php';</script>";
    exit;
}

// Database Connection
$db = Database::getInstance();
$tenantId = $currentUser['tenant_id'];

// 1. Get Tenant Details
$tenant = $db->fetch("SELECT * FROM tenants WHERE id = ?", [$tenantId]);

// 2. Fetch Aggregated Stats
$totalResidents = $db->fetchColumn("SELECT COUNT(*) FROM residents WHERE tenant_id = ?", [$tenantId]);
$totalHouses = $db->fetchColumn("SELECT COUNT(*) FROM houses WHERE tenant_id = ?", [$tenantId]);

// Kas Terkumpul Bulan Ini (Pemasukan dari Cashflow + Payments Lunas)
$month = date('m');
$year = date('Y');
$collectedIuran = $db->fetchColumn("SELECT SUM(amount) FROM cashflows WHERE tenant_id = ? AND type = 'masuk' AND MONTH(transaction_date) = ? AND YEAR(transaction_date) = ?", [$tenantId, $month, $year]) ?? 0;
// Tambahkan dari payments lunas bulan ini
$collectedPayments = $db->fetchColumn("SELECT SUM(amount) FROM payments WHERE tenant_id = ? AND MONTH(paid_at) = ? AND YEAR(paid_at) = ?", [$tenantId, $month, $year]) ?? 0;
$totalKasBulanIni = $collectedIuran + $collectedPayments;

$pendingLetters = $db->fetchColumn("SELECT COUNT(*) FROM service_requests WHERE tenant_id = ? AND status = 'diajukan'", [$tenantId]);

// 3. Iuran Status (Current Month)
$totalInvoiceCount = $db->fetchColumn("SELECT COUNT(*) FROM invoices WHERE tenant_id = ? AND MONTH(created_at) = ? AND YEAR(created_at) = ?", [$tenantId, $month, $year]) ?: 0;
$paidInvoices = $db->fetchColumn("SELECT COUNT(*) FROM invoices WHERE tenant_id = ? AND status = 'lunas' AND MONTH(created_at) = ? AND YEAR(created_at) = ?", [$tenantId, $month, $year]) ?: 0;
$unpaidInvoices = $db->fetchColumn("SELECT COUNT(*) FROM invoices WHERE tenant_id = ? AND status = 'tagihan' AND MONTH(created_at) = ? AND YEAR(created_at) = ?", [$tenantId, $month, $year]) ?: 0;
$lateInvoices = $db->fetchColumn("SELECT COUNT(*) FROM invoices WHERE tenant_id = ? AND status = 'menunggak' AND MONTH(created_at) = ? AND YEAR(created_at) = ?", [$tenantId, $month, $year]) ?: 0;

$iuranProgress = $totalInvoiceCount > 0 ? round(($paidInvoices / $totalInvoiceCount) * 100) : 0;

// 4. Pembayaran Terakhir
$recentPayments = $db->fetchAll("
    SELECT p.*, r.full_name as resident_name, h.house_number, b.block_name, it.name as invoice_title
    FROM payments p
    JOIN invoices i ON p.invoice_id = i.id
    JOIN invoice_types it ON i.invoice_type_id = it.id
    JOIN houses h ON i.house_id = h.id
    JOIN blocks b ON h.block_id = b.id
    LEFT JOIN residents r ON i.house_id = r.house_id AND r.family_status = 'kepala_keluarga' AND r.deleted_at IS NULL
    WHERE p.tenant_id = ?
    ORDER BY p.paid_at DESC LIMIT 5
", [$tenantId]);

// 5. Berita Terakhir
$recentNews = $db->fetchAll("SELECT * FROM news WHERE tenant_id = ? ORDER BY created_at DESC LIMIT 3", [$tenantId]);

// 6. Cashflow Chart Data (Last 12 Months)
$chartLabels = [];
$chartIncome = [];
$chartExpense = [];

for ($i = 11; $i >= 0; $i--) {
    $targetDate = date('Y-m-01', strtotime("-$i months"));
    $m = date('m', strtotime($targetDate));
    $y = date('Y', strtotime($targetDate));
    $chartLabels[] = date('M', strtotime($targetDate));
    
    // Income from cashflow + payments
    $inc1 = $db->fetchColumn("SELECT SUM(amount) FROM cashflows WHERE tenant_id = ? AND type = 'masuk' AND MONTH(transaction_date) = ? AND YEAR(transaction_date) = ?", [$tenantId, $m, $y]) ?? 0;
    $inc2 = $db->fetchColumn("SELECT SUM(amount) FROM payments WHERE tenant_id = ? AND MONTH(paid_at) = ? AND YEAR(paid_at) = ?", [$tenantId, $m, $y]) ?? 0;
    $chartIncome[] = ($inc1 + $inc2) / 1000000; // in millions
    
    // Expense from cashflow
    $exp = $db->fetchColumn("SELECT SUM(amount) FROM cashflows WHERE tenant_id = ? AND type = 'keluar' AND MONTH(transaction_date) = ? AND YEAR(transaction_date) = ?", [$tenantId, $m, $y]) ?? 0;
    $chartExpense[] = $exp / 1000000; // in millions
}
?>

<div class="app-layout">
  <?php include 'includes/sidebar.php'; ?>

  <div class="main-wrapper">
    <?php include 'includes/topbar.php'; ?>

    <main class="main-content">
      <!-- Welcome Banner -->
      <div class="card card-glass animate-fadeIn" style="margin-bottom: 24px; background: linear-gradient(135deg, rgba(99,102,241,0.15), rgba(6,182,212,0.1)); border-color: rgba(99,102,241,0.2);">
        <div style="display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 16px;">
          <div>
            <h2 style="margin-bottom: 4px;">Selamat Datang, <?= htmlspecialchars($currentUser['name']) ?>! 👋</h2>
            <p class="text-muted"><?= htmlspecialchars($tenant['name'] ?? 'Perumahan Warga') ?> — Periode <?= date('F Y') ?></p>
          </div>
          <?php if($currentUser['role'] === 'admin'): ?>
          <div style="display: flex; gap: 10px;">
            <a href="warga.php" class="btn btn-primary btn-sm"><i data-lucide="user-plus" style="width:16px;height:16px;"></i> Tambah Warga</a>
            <a href="kas.php" class="btn btn-secondary btn-sm"><i data-lucide="plus-circle" style="width:16px;height:16px;"></i> Input Kas</a>
          </div>
          <?php endif; ?>
        </div>
      </div>

      <!-- Statistics Cards -->
      <div class="stats-grid">
        <div class="stat-card animate-fadeIn stagger-1">
          <div class="stat-icon blue"><i data-lucide="users"></i></div>
          <div class="stat-info">
            <div class="stat-label">Total Warga</div>
            <div class="stat-value"><?= number_format($totalResidents) ?></div>
            <div class="stat-change">Terdaftar</div>
          </div>
        </div>
        <div class="stat-card animate-fadeIn stagger-2">
          <div class="stat-icon purple"><i data-lucide="home"></i></div>
          <div class="stat-info">
            <div class="stat-label">Total Rumah</div>
            <div class="stat-value"><?= number_format($totalHouses) ?></div>
            <div class="stat-change">Unit Properti</div>
          </div>
        </div>
        <div class="stat-card animate-fadeIn stagger-3">
          <div class="stat-icon green"><i data-lucide="wallet"></i></div>
          <div class="stat-info">
            <div class="stat-label">Kas Terkumpul</div>
            <div class="stat-value">Rp <?= number_format($totalKasBulanIni, 0, ',', '.') ?></div>
            <div class="stat-change">Bulan Ini</div>
          </div>
        </div>
        <div class="stat-card animate-fadeIn stagger-4">
          <div class="stat-icon yellow"><i data-lucide="file-text"></i></div>
          <div class="stat-info">
            <div class="stat-label">Layanan Surat</div>
            <div class="stat-value"><?= number_format($pendingLetters) ?></div>
            <div class="stat-change">Antrian</div>
          </div>
        </div>
      </div>

      <div class="grid-2" style="margin-bottom: 24px;">
        <!-- Cashflow Chart -->
        <div class="card animate-fadeIn">
          <div class="card-header">
            <h3 class="card-title">Arus Kas Bulanan</h3>
            <select class="form-control" style="width:auto; padding:6px 28px 6px 12px; font-size:0.8rem;">
              <option>2026</option>
              <option>2025</option>
            </select>
          </div>
          <div class="chart-container">
            <canvas id="cashflowChart"></canvas>
          </div>
        </div>

        <!-- Iuran Status -->
        <div class="card animate-fadeIn">
          <div class="card-header">
            <h3 class="card-title">Status Iuran Maret 2026</h3>
            <a href="iuran.php" class="btn btn-outline btn-sm">Lihat Semua</a>
          </div>
          <div style="display:flex; align-items:center; gap:24px; margin-bottom:20px;">
            <div style="position:relative; width:140px; height:140px;">
              <canvas id="iuranDonut"></canvas>
            </div>
            <div>
              <div style="margin-bottom:12px;">
                <div style="display:flex;align-items:center;gap:8px;margin-bottom:4px;">
                  <span style="width:12px;height:12px;border-radius:50%;background:var(--success);display:inline-block;"></span>
                  <span class="text-muted" style="font-size:0.85rem;">Lunas</span>
                </div>
                <span style="font-size:1.3rem;font-weight:700;"><?= number_format($paidInvoices) ?> <small class="text-muted" style="font-weight:400;">rumah</small></span>
              </div>
              <div style="margin-bottom:12px;">
                <div style="display:flex;align-items:center;gap:8px;margin-bottom:4px;">
                  <span style="width:12px;height:12px;border-radius:50%;background:var(--warning);display:inline-block;"></span>
                  <span class="text-muted" style="font-size:0.85rem;">Tagihan</span>
                </div>
                <span style="font-size:1.3rem;font-weight:700;"><?= number_format($unpaidInvoices) ?> <small class="text-muted" style="font-weight:400;">rumah</small></span>
              </div>
              <div>
                <div style="display:flex;align-items:center;gap:8px;margin-bottom:4px;">
                  <span style="width:12px;height:12px;border-radius:50%;background:var(--danger);display:inline-block;"></span>
                  <span class="text-muted" style="font-size:0.85rem;">Menunggak</span>
                </div>
                <span style="font-size:1.3rem;font-weight:700;"><?= number_format($lateInvoices) ?> <small class="text-muted" style="font-weight:400;">rumah</small></span>
              </div>
            </div>
          </div>
          <div class="progress-bar" style="margin-bottom: 8px;">
            <div class="progress-fill" style="width: <?= $iuranProgress ?>%;"></div>
          </div>
          <p class="text-muted" style="font-size:0.82rem;"><?= $iuranProgress ?>% target iuran tercapai</p>
        </div>
      </div>

      <div class="grid-2">
        <!-- Quick Actions -->
        <div class="card animate-fadeIn">
          <div class="card-header">
            <h3 class="card-title">Aksi Cepat</h3>
          </div>
          <div class="quick-actions">
            <a href="warga.php" class="quick-action-btn">
              <span class="action-icon">👥</span>
              Data Warga
            </a>
            <a href="iuran.php" class="quick-action-btn">
              <span class="action-icon">💰</span>
              Tagih Iuran
            </a>
            <a href="surat.php" class="quick-action-btn">
              <span class="action-icon">📄</span>
              Buat Surat
            </a>
            <a href="kas.php" class="quick-action-btn">
              <span class="action-icon">📊</span>
              Laporan Kas
            </a>
            <a href="keamanan.php" class="quick-action-btn">
              <span class="action-icon">🚨</span>
              Keamanan
            </a>
            <a href="pengaduan.php" class="quick-action-btn">
              <span class="action-icon">💬</span>
              Pengaduan
            </a>
          </div>
        </div>

        <!-- Recent News -->
        <div class="card animate-fadeIn">
          <div class="card-header">
            <h3 class="card-title">Berita Terbaru</h3>
            <a href="berita.php" class="btn btn-outline btn-sm">Semua Berita</a>
          </div>
          <div style="display:flex; flex-direction:column; gap:16px;">
            <?php if(empty($recentNews)): ?>
                <p class="text-muted">Belum ada pengumuman terbaru.</p>
            <?php else: ?>
                <?php foreach($recentNews as $news): ?>
                <div style="display:flex; gap:14px; padding-bottom:16px; border-bottom:1px solid var(--border-color);">
                  <div style="width:64px;height:64px;border-radius:var(--radius-md);background:rgba(99,102,241,0.1);overflow:hidden;flex-shrink:0;">
                    <?php if(!empty($news['image_url'])): ?>
                        <img src="<?= htmlspecialchars($news['image_url']) ?>" style="width:100%;height:100%;object-fit:cover;">
                    <?php else: ?>
                        <div style="width:100%;height:100%;display:flex;align-items:center;justify-content:center;font-size:1.5rem;">📢</div>
                    <?php endif; ?>
                  </div>
                  <div style="flex:1;">
                    <h4 style="font-size:0.92rem;margin-bottom:4px;"><?= htmlspecialchars($news['title']) ?></h4>
                    <p class="text-muted" style="font-size:0.8rem;display:-webkit-box;-webkit-line-clamp:1;-webkit-box-orient:vertical;overflow:hidden;"><?= htmlspecialchars(strip_tags($news['content'] ?? '')) ?></p>
                    <span class="text-muted" style="font-size:0.75rem;"><?= date('d M Y', strtotime($news['created_at'])) ?></span>
                  </div>
                </div>
                <?php endforeach; ?>
            <?php endif; ?>
          </div>
        </div>
      </div>

      <!-- Recent Payments Table -->
      <div class="card animate-fadeIn" style="margin-top: 24px;">
        <div class="card-header">
          <h3 class="card-title">Pembayaran Terakhir</h3>
          <a href="pembayaran.php" class="btn btn-outline btn-sm">Lihat Semua</a>
        </div>
        <div class="table-container">
          <table class="table">
            <thead>
              <tr>
                <th>Nama Warga</th>
                <th>Blok/Rumah</th>
                <th>Jenis</th>
                <th>Jumlah</th>
                <th>Metode</th>
                <th>Tanggal</th>
                <th>Status</th>
              </tr>
            </thead>
            <tbody>
              <?php if(empty($recentPayments)): ?>
                <tr><td colspan="7" style="text-align:center;padding:40px;color:var(--text-muted);">Belum ada pembayaran terbaru.</td></tr>
              <?php else: ?>
                <?php foreach($recentPayments as $pay): ?>
                <tr>
                    <td><strong><?= htmlspecialchars($pay['resident_name'] ?? 'Warga') ?></strong></td>
                    <td><?= htmlspecialchars(($pay['block_name'] ?? '') . '/' . ($pay['house_number'] ?? '')) ?></td>
                    <td><?= htmlspecialchars($pay['invoice_title'] ?? 'Iuran') ?></td>
                    <td>Rp <?= number_format($pay['amount'] ?? 0, 0, ',', '.') ?></td>
                    <td><?= strtoupper($pay['method'] ?? '-') ?></td>
                    <td><?= date('d M Y', strtotime($pay['paid_at'] ?? 'now')) ?></td>
                    <td><span class="badge badge-success">Lunas</span></td>
                </tr>
                <?php endforeach; ?>
              <?php endif; ?>
            </tbody>
          </table>
        </div>
      </div>
    </main>
  </div>
</div>

<?php
$extraScripts = <<<'JS'
<script>
  // Cashflow Chart
  const cashflowCtx = document.getElementById('cashflowChart').getContext('2d');
  new Chart(cashflowCtx, {
    type: 'bar',
    data: {
      labels: <?= json_encode($chartLabels) ?>,
      datasets: [
        {
          label: 'Pemasukan',
          data: <?= json_encode($chartIncome) ?>,
          backgroundColor: 'rgba(16, 185, 129, 0.7)',
          borderRadius: 6,
          borderSkipped: false,
        },
        {
          label: 'Pengeluaran',
          data: <?= json_encode($chartExpense) ?>,
          backgroundColor: 'rgba(239, 68, 68, 0.7)',
          borderRadius: 6,
          borderSkipped: false,
        }
      ]
    },
    options: {
      responsive: true,
      maintainAspectRatio: false,
      plugins: {
        legend: {
          labels: { color: '#94a3b8', font: { family: 'Inter' } }
        }
      },
      scales: {
        y: {
          grid: { color: 'rgba(148,163,184,0.08)' },
          ticks: {
            color: '#64748b',
            callback: val => 'Rp' + val + 'jt'
          }
        },
        x: {
          grid: { display: false },
          ticks: { color: '#64748b' }
        }
      }
    }
  });

  // Iuran Donut
  const iuranCtx = document.getElementById('iuranDonut').getContext('2d');
  new Chart(iuranCtx, {
    type: 'doughnut',
    data: {
      labels: ['Lunas', 'Tagihan', 'Menunggak'],
      datasets: [{
        data: [<?= $paidInvoices ?>, <?= $unpaidInvoices ?>, <?= $lateInvoices ?>],
        backgroundColor: ['#10b981', '#f59e0b', '#ef4444'],
        borderWidth: 0,
        cutout: '72%'
      }]
    },
    options: {
      responsive: true,
      maintainAspectRatio: true,
      plugins: {
        legend: { display: false }
      }
    }
  });
</script>
JS;
?>

<?php include 'includes/footer.php'; ?>
