<?php $pageTitle = 'Riwayat Pembayaran'; ?>
<?php include 'includes/header.php'; ?>

<div class="app-layout">
  <?php include 'includes/sidebar.php'; ?>

  <div class="main-wrapper">
    <?php include 'includes/topbar.php'; ?>

    <main class="main-content">
      <div class="page-header">
        <div>
          <h1>Riwayat Pembayaran</h1>
          <div class="breadcrumb">
            <a href="index.php">Dashboard</a>
            <span class="separator">/</span>
            <span>Keuangan</span>
            <span class="separator">/</span>
            <span>Pembayaran</span>
          </div>
        </div>
        <button class="btn btn-secondary btn-sm"><i data-lucide="download" style="width:16px;height:16px;"></i> Export CSV</button>
      </div>

      <!-- Stats -->
      <div class="stats-grid" style="grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));">
        <div class="stat-card">
          <div class="stat-icon green"><i data-lucide="trending-up"></i></div>
          <div class="stat-info">
            <div class="stat-label">Total Masuk (Maret)</div>
            <div class="stat-value">Rp 48,5jt</div>
          </div>
        </div>
        <div class="stat-card">
          <div class="stat-icon blue"><i data-lucide="smartphone"></i></div>
          <div class="stat-info">
            <div class="stat-label">Via QRIS</div>
            <div class="stat-value">187 trx</div>
          </div>
        </div>
        <div class="stat-card">
          <div class="stat-icon purple"><i data-lucide="landmark"></i></div>
          <div class="stat-info">
            <div class="stat-label">Via VA Bank</div>
            <div class="stat-value">98 trx</div>
          </div>
        </div>
        <div class="stat-card">
          <div class="stat-icon yellow"><i data-lucide="banknote"></i></div>
          <div class="stat-info">
            <div class="stat-label">Via Tunai</div>
            <div class="stat-value">27 trx</div>
          </div>
        </div>
      </div>

      <!-- Filter -->
      <div class="filter-bar">
        <div class="filter-search">
          <span class="search-icon"><i data-lucide="search"></i></span>
          <input type="text" class="form-control" placeholder="Cari transaksi...">
        </div>
        <select class="form-control" style="width:auto;">
          <option>Semua Metode</option>
          <option>QRIS</option>
          <option>VA BCA</option>
          <option>VA Mandiri</option>
          <option>VA BNI</option>
          <option>Tunai</option>
        </select>
        <input type="date" class="form-control" style="width:auto;" value="2026-03-01">
        <span class="text-muted">s/d</span>
        <input type="date" class="form-control" style="width:auto;" value="2026-03-28">
      </div>

      <!-- Table -->
      <div class="card">
        <div class="table-container">
          <table class="table">
            <thead>
              <tr>
                <th>ID Transaksi</th>
                <th>Nama</th>
                <th>Rumah</th>
                <th>Jenis</th>
                <th>Jumlah</th>
                <th>Biaya Admin</th>
                <th>Metode</th>
                <th>Waktu</th>
                <th>Status</th>
              </tr>
            </thead>
            <tbody>
              <?php
              $payments = [
                ['TRX-20260327001', 'Budi Santoso', 'A/12', 'IPL Mar', 350000, 2500, 'QRIS', '27 Mar 16:42', 'Berhasil', 'success'],
                ['TRX-20260326012', 'Siti Rahayu', 'B/05', 'IPL Mar', 350000, 4000, 'VA BCA', '26 Mar 09:15', 'Berhasil', 'success'],
                ['TRX-20260325008', 'Ahmad Fauzi', 'C/08', 'IPL Mar', 350000, 0, 'Tunai', '25 Mar 14:30', 'Berhasil', 'success'],
                ['TRX-20260324003', 'Dewi Lestari', 'A/22', 'IPL Feb-Mar', 700000, 4000, 'VA Mandiri', '24 Mar 11:22', 'Pending', 'warning'],
                ['TRX-20260324007', 'Riko Pratama', 'D/15', 'IPL Mar', 350000, 2500, 'QRIS', '24 Mar 08:55', 'Berhasil', 'success'],
                ['TRX-20260323015', 'Nur Hidayah', 'B/11', 'IPL Mar', 350000, 4000, 'VA BNI', '23 Mar 19:08', 'Berhasil', 'success'],
                ['TRX-20260322009', 'Maya Sari', 'C/19', 'IPL Mar', 350000, 2500, 'QRIS', '22 Mar 12:33', 'Berhasil', 'success'],
                ['TRX-20260321002', 'Arif Rahman', 'D/07', 'IPL Mar', 350000, 0, 'Tunai', '21 Mar 10:00', 'Berhasil', 'success'],
                ['TRX-20260320011', 'Lina Marlina', 'B/23', 'IPL Mar', 350000, 2500, 'QRIS', '20 Mar 15:47', 'Berhasil', 'success'],
                ['TRX-20260319006', 'Putri A.', 'A/10', 'IPL Mar', 350000, 4000, 'VA BCA', '19 Mar 13:21', 'Berhasil', 'success'],
              ];
              foreach ($payments as $p): ?>
              <tr>
                <td style="font-family:monospace;font-size:0.8rem;"><?= $p[0] ?></td>
                <td><strong><?= $p[1] ?></strong></td>
                <td><?= $p[2] ?></td>
                <td><?= $p[3] ?></td>
                <td>Rp <?= number_format($p[4], 0, ',', '.') ?></td>
                <td>Rp <?= number_format($p[5], 0, ',', '.') ?></td>
                <td><span class="badge badge-neutral"><?= $p[6] ?></span></td>
                <td style="white-space:nowrap;"><?= $p[7] ?></td>
                <td><span class="badge badge-<?= $p[9] ?>"><?= $p[8] ?></span></td>
              </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        </div>
        <div style="display:flex;align-items:center;justify-content:space-between;margin-top:16px;flex-wrap:wrap;gap:12px;">
          <span class="text-muted" style="font-size:0.85rem;">Menampilkan 1-10 dari 312 transaksi</span>
          <div class="pagination" style="margin-top:0;">
            <button class="page-btn">←</button>
            <button class="page-btn active">1</button>
            <button class="page-btn">2</button>
            <button class="page-btn">3</button>
            <button class="page-btn">→</button>
          </div>
        </div>
      </div>
    </main>
  </div>
</div>

<?php include 'includes/footer.php'; ?>
