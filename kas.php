<?php $pageTitle = 'Kas & Transparansi'; ?>
<?php include 'includes/header.php'; ?>

<div class="app-layout">
  <?php include 'includes/sidebar.php'; ?>

  <div class="main-wrapper">
    <?php include 'includes/topbar.php'; ?>

    <main class="main-content">
      <div class="page-header">
        <div>
          <h1>Kas & Transparansi</h1>
          <div class="breadcrumb">
            <a href="index.php">Dashboard</a>
            <span class="separator">/</span>
            <span>Keuangan</span>
            <span class="separator">/</span>
            <span>Kas</span>
          </div>
        </div>
        <div style="display:flex;gap:10px;">
          <button class="btn btn-secondary btn-sm"><i data-lucide="download" style="width:16px;height:16px;"></i> Download Laporan</button>
          <a href="kas_form.php" class="btn btn-primary btn-sm"><i data-lucide="plus" style="width:16px;height:16px;"></i> Input Transaksi</a>
        </div>
      </div>

      <!-- Saldo Card -->
      <div class="card card-glass animate-fadeIn" style="margin-bottom:24px; background: linear-gradient(135deg, rgba(16,185,129,0.12), rgba(6,182,212,0.08)); border-color: rgba(16,185,129,0.2);">
        <div style="display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:20px;">
          <div>
            <div class="text-muted" style="font-size:0.85rem;margin-bottom:4px;">Saldo Kas RT Saat Ini</div>
            <div style="font-size:2.2rem;font-weight:800;">Rp 156.750.000</div>
            <div class="stat-change up" style="font-size:0.85rem;margin-top:4px;">↑ Rp 23.500.000 dari bulan lalu</div>
          </div>
          <div style="display:flex;gap:16px;">
            <div style="text-align:center;padding:16px 24px;background:var(--success-light);border-radius:var(--radius-md);">
              <div style="font-size:0.78rem;color:var(--success);font-weight:600;">Pemasukan</div>
              <div style="font-size:1.2rem;font-weight:700;color:var(--success);">Rp 48,5jt</div>
              <div style="font-size:0.75rem;color:var(--text-muted);">Maret 2026</div>
            </div>
            <div style="text-align:center;padding:16px 24px;background:var(--danger-light);border-radius:var(--radius-md);">
              <div style="font-size:0.78rem;color:var(--danger);font-weight:600;">Pengeluaran</div>
              <div style="font-size:1.2rem;font-weight:700;color:var(--danger);">Rp 25,0jt</div>
              <div style="font-size:0.75rem;color:var(--text-muted);">Maret 2026</div>
            </div>
          </div>
        </div>
      </div>

      <!-- Cashflow Chart -->
      <div class="card" style="margin-bottom:24px;">
        <div class="card-header">
          <h3 class="card-title">Grafik Arus Kas 2026</h3>
          <select class="form-control" style="width:auto;padding:6px 28px 6px 12px;font-size:0.8rem;">
            <option>2026</option>
            <option>2025</option>
          </select>
        </div>
        <div class="chart-container" style="height:320px;">
          <canvas id="kasChart"></canvas>
        </div>
      </div>

      <!-- Filter -->
      <div class="filter-bar">
        <div class="filter-search">
          <span class="search-icon"><i data-lucide="search"></i></span>
          <input type="text" class="form-control" placeholder="Cari transaksi...">
        </div>
        <select class="form-control" style="width:auto;">
          <option value="">Semua Jenis</option>
          <option>Pemasukan</option>
          <option>Pengeluaran</option>
        </select>
        <select class="form-control" style="width:auto;">
          <option value="">Semua Kategori</option>
          <option>Iuran</option>
          <option>Keamanan</option>
          <option>Kebersihan</option>
          <option>Pemeliharaan</option>
          <option>Lainnya</option>
        </select>
        <select class="form-control" style="width:auto;">
          <option>Maret 2026</option>
          <option>Februari 2026</option>
          <option>Januari 2026</option>
        </select>
      </div>

      <!-- Transactions Table -->
      <div class="card">
        <div class="card-header">
          <h3 class="card-title">Transaksi Kas - Maret 2026</h3>
        </div>
        <div class="table-container">
          <table class="table">
            <thead>
              <tr>
                <th>Tanggal</th>
                <th>Jenis</th>
                <th>Kategori</th>
                <th>Deskripsi</th>
                <th>Jumlah</th>
                <th>Bukti</th>
                <th>Oleh</th>
              </tr>
            </thead>
            <tbody>
              <?php
              $kasData = [
                ['27 Mar', 'Masuk', 'Iuran', 'Pembayaran IPL Maret (15 rumah)', 5250000, true, 'Bendahara'],
                ['26 Mar', 'Keluar', 'Kebersihan', 'Gaji tukang sampah bulan Maret', 3000000, true, 'Bendahara'],
                ['25 Mar', 'Keluar', 'Pemeliharaan', 'Perbaikan lampu jalan Blok C', 850000, true, 'Ketua RT'],
                ['24 Mar', 'Masuk', 'Iuran', 'Pembayaran IPL Maret (22 rumah)', 7700000, false, 'Bendahara'],
                ['23 Mar', 'Keluar', 'Keamanan', 'Gaji satpam bulan Maret', 8000000, true, 'Bendahara'],
                ['22 Mar', 'Masuk', 'Iuran', 'Pembayaran IPL Maret (18 rumah)', 6300000, false, 'Bendahara'],
                ['20 Mar', 'Keluar', 'Pemeliharaan', 'Cat ulang pos jaga & gerbang', 2500000, true, 'Ketua RT'],
                ['18 Mar', 'Keluar', 'Lainnya', 'ATK dan perlengkapan kantor', 450000, true, 'Sekretaris'],
                ['15 Mar', 'Masuk', 'Iuran', 'Pembayaran IPL Maret (30 rumah)', 10500000, false, 'Bendahara'],
                ['12 Mar', 'Keluar', 'Kebersihan', 'Pembelian alat kebersihan', 750000, true, 'Bendahara'],
              ];
              foreach ($kasData as $k): ?>
              <tr>
                <td><?= $k[0] ?></td>
                <td>
                  <span class="badge <?= $k[1] == 'Masuk' ? 'badge-success' : 'badge-danger' ?>">
                    <?= $k[1] == 'Masuk' ? '↓ Masuk' : '↑ Keluar' ?>
                  </span>
                </td>
                <td><?= $k[2] ?></td>
                <td><?= $k[3] ?></td>
                <td style="font-weight:600; color: <?= $k[1] == 'Masuk' ? 'var(--success)' : 'var(--danger)' ?>">
                  <?= $k[1] == 'Masuk' ? '+' : '-' ?>Rp <?= number_format($k[4], 0, ',', '.') ?>
                </td>
                <td>
                  <?php if ($k[5]): ?>
                    <a href="kas_detail.php" class="btn btn-sm btn-outline" style="padding:2px 8px;font-size:0.75rem;">📎 Lihat</a>
                  <?php else: ?>
                    <span class="text-muted" style="font-size:0.8rem;">-</span>
                  <?php endif; ?>
                </td>
                <td class="text-muted"><?= $k[6] ?></td>
              </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        </div>
        <div style="display:flex;align-items:center;justify-content:space-between;margin-top:16px;flex-wrap:wrap;gap:12px;">
          <span class="text-muted" style="font-size:0.85rem;">Menampilkan 1-10 dari 45 transaksi</span>
          <div class="pagination" style="margin-top:0;">
            <button class="page-btn">←</button>
            <button class="page-btn active">1</button>
            <button class="page-btn">2</button>
            <button class="page-btn">→</button>
          </div>
        </div>
      </div>
    </main>
  </div>
</div>

<!-- Add Kas Modal -->
<div class="modal-overlay" id="addKasModal">
  <div class="modal">
    <div class="modal-header">
      <h3 class="modal-title">Input Transaksi Kas</h3>
      <button class="modal-close">✕</button>
    </div>
    <div class="modal-body">
      <div class="form-group">
        <label class="form-label">Jenis Transaksi</label>
        <div style="display:flex;gap:12px;">
          <label class="checkbox-label" style="flex:1;padding:12px;border:1px solid var(--border-color);border-radius:var(--radius-md);cursor:pointer;">
            <input type="radio" name="kas_type" value="in" checked> 💰 Pemasukan
          </label>
          <label class="checkbox-label" style="flex:1;padding:12px;border:1px solid var(--border-color);border-radius:var(--radius-md);cursor:pointer;">
            <input type="radio" name="kas_type" value="out"> 💸 Pengeluaran
          </label>
        </div>
      </div>
      <div class="form-row">
        <div class="form-group">
          <label class="form-label">Kategori</label>
          <select class="form-control">
            <option>Iuran</option>
            <option>Keamanan</option>
            <option>Kebersihan</option>
            <option>Pemeliharaan</option>
            <option>ATK</option>
            <option>Lainnya</option>
          </select>
        </div>
        <div class="form-group">
          <label class="form-label">Jumlah (Rp)</label>
          <input type="number" class="form-control" placeholder="0">
        </div>
      </div>
      <div class="form-group">
        <label class="form-label">Deskripsi</label>
        <textarea class="form-control" rows="3" placeholder="Deskripsi transaksi..."></textarea>
      </div>
      <div class="form-group">
        <label class="form-label">Upload Bukti / Kuitansi</label>
        <input type="file" class="form-control" accept="image/*,.pdf">
      </div>
    </div>
    <div class="modal-footer">
      <button class="btn btn-secondary modal-cancel">Batal</button>
      <button class="btn btn-primary">Simpan Transaksi</button>
    </div>
  </div>
</div>

<?php
$extraScripts = <<<'JS'
<script>
  new Chart(document.getElementById('kasChart'), {
    type: 'line',
    data: {
      labels: ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agt', 'Sep', 'Okt', 'Nov', 'Des'],
      datasets: [
        {
          label: 'Pemasukan',
          data: [42, 45, 48.5, null, null, null, null, null, null, null, null, null],
          borderColor: '#10b981',
          backgroundColor: 'rgba(16, 185, 129, 0.1)',
          fill: true,
          tension: 0.4,
          pointRadius: 6,
          pointHoverRadius: 8,
          borderWidth: 2.5
        },
        {
          label: 'Pengeluaran',
          data: [28, 31, 25, null, null, null, null, null, null, null, null, null],
          borderColor: '#ef4444',
          backgroundColor: 'rgba(239, 68, 68, 0.1)',
          fill: true,
          tension: 0.4,
          pointRadius: 6,
          pointHoverRadius: 8,
          borderWidth: 2.5
        },
        {
          label: 'Saldo',
          data: [110, 124, 156.75, null, null, null, null, null, null, null, null, null],
          borderColor: '#6366f1',
          backgroundColor: 'transparent',
          borderDash: [5, 5],
          tension: 0.4,
          pointRadius: 6,
          pointHoverRadius: 8,
          borderWidth: 2
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
          ticks: { color: '#64748b', callback: v => 'Rp' + v + 'jt' }
        },
        x: {
          grid: { display: false },
          ticks: { color: '#64748b' }
        }
      }
    }
  });
</script>
JS;
?>

<?php include 'includes/footer.php'; ?>
