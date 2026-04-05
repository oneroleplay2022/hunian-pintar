<?php $pageTitle = 'Pendataan Tamu'; ?>
<?php include 'includes/header.php'; ?>

<div class="app-layout">
  <?php include 'includes/sidebar.php'; ?>

  <div class="main-wrapper">
    <?php include 'includes/topbar.php'; ?>

    <main class="main-content">
      <div class="page-header">
        <div>
          <h1>Pendataan Tamu</h1>
          <div class="breadcrumb">
            <a href="index.php">Dashboard</a>
            <span class="separator">/</span>
            <span>Kependudukan</span>
            <span class="separator">/</span>
            <span>Tamu</span>
          </div>
        </div>
        <a href="tamu_checkin.php" class="btn btn-primary btn-sm"><i data-lucide="user-plus" style="width:16px;height:16px;"></i> Check-in Tamu</a>
      </div>

      <!-- Stats -->
      <div class="stats-grid" style="grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));">
        <div class="stat-card">
          <div class="stat-icon blue"><i data-lucide="users"></i></div>
          <div class="stat-info">
            <div class="stat-label">Tamu Hari Ini</div>
            <div class="stat-value">5</div>
          </div>
        </div>
        <div class="stat-card">
          <div class="stat-icon green"><i data-lucide="log-in"></i></div>
          <div class="stat-info">
            <div class="stat-label">Masih Berkunjung</div>
            <div class="stat-value">2</div>
          </div>
        </div>
        <div class="stat-card">
          <div class="stat-icon purple"><i data-lucide="calendar"></i></div>
          <div class="stat-info">
            <div class="stat-label">Total Bulan Ini</div>
            <div class="stat-value">87</div>
          </div>
        </div>
      </div>

      <!-- Filter -->
      <div class="filter-bar">
        <div class="filter-search">
          <span class="search-icon"><i data-lucide="search"></i></span>
          <input type="text" class="form-control" placeholder="Cari nama tamu...">
        </div>
        <select class="form-control" style="width:auto;">
          <option>Hari Ini</option>
          <option>Minggu Ini</option>
          <option>Bulan Ini</option>
          <option>Semua</option>
        </select>
        <select class="form-control" style="width:auto;">
          <option value="">Semua Status</option>
          <option>Masih Berkunjung</option>
          <option>Sudah Keluar</option>
        </select>
      </div>

      <!-- Table -->
      <div class="card">
        <div class="table-container">
          <table class="table">
            <thead>
              <tr>
                <th>Nama Tamu</th>
                <th>No. Identitas</th>
                <th>Tujuan Rumah</th>
                <th>Keperluan</th>
                <th>Check-in</th>
                <th>Check-out</th>
                <th>Status</th>
                <th>Aksi</th>
              </tr>
            </thead>
            <tbody>
              <?php
              $tamu = [
                ['Andi Pratama', '3201****0045', 'A/12 (Budi S.)', 'Silaturahmi', '28 Mar 10:30', '-', 'Berkunjung', 'info'],
                ['Ny. Sari', '3201****0078', 'B/05 (Siti R.)', 'Arisan keluarga', '28 Mar 09:00', '-', 'Berkunjung', 'info'],
                ['Kurir JNE', '-', 'C/08 (Ahmad F.)', 'Pengiriman paket', '28 Mar 08:45', '28 Mar 08:52', 'Keluar', 'success'],
                ['Tukang AC', '-', 'A/22 (Dewi L.)', 'Service AC', '28 Mar 08:00', '28 Mar 11:30', 'Keluar', 'success'],
                ['Bp. Herianto', '3201****0123', 'D/15 (Riko P.)', 'Tamu menginap', '27 Mar 15:00', '-', 'Bermalam', 'warning'],
                ['Nn. Lisa', '3201****0099', 'B/11 (Nur H.)', 'Kunjungan kerja', '27 Mar 13:00', '27 Mar 17:30', 'Keluar', 'success'],
                ['Tukang Ledeng', '-', 'C/19 (Maya S.)', 'Perbaikan pipa', '27 Mar 10:00', '27 Mar 14:00', 'Keluar', 'success'],
                ['Tn. Farhan', '3201****0156', 'A/03 (Joko W.)', 'Kunjungan keluarga', '27 Mar 09:30', '27 Mar 20:00', 'Keluar', 'success'],
              ];
              foreach ($tamu as $t): ?>
              <tr>
                <td><strong><?= $t[0] ?></strong></td>
                <td style="font-family:monospace;font-size:0.82rem;"><?= $t[1] ?></td>
                <td><?= $t[2] ?></td>
                <td><?= $t[3] ?></td>
                <td style="white-space:nowrap;font-size:0.85rem;"><?= $t[4] ?></td>
                <td style="white-space:nowrap;font-size:0.85rem;"><?= $t[5] ?></td>
                <td><span class="badge badge-<?= $t[7] ?>"><?= $t[6] ?></span></td>
                <td>
                  <?php if ($t[6] !== 'Keluar'): ?>
                  <button class="btn btn-sm btn-success" style="padding:4px 10px;font-size:0.78rem;">Check-out</button>
                  <?php else: ?>
                  <span class="text-muted">-</span>
                  <?php endif; ?>
                </td>
              </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        </div>
      </div>
    </main>
  </div>
</div>

<!-- Check-in Modal -->
<div class="modal-overlay" id="checkinModal">
  <div class="modal">
    <div class="modal-header">
      <h3 class="modal-title">Check-in Tamu</h3>
      <button class="modal-close">✕</button>
    </div>
    <div class="modal-body">
      <div class="form-group">
        <label class="form-label">Nama Tamu</label>
        <input type="text" class="form-control" placeholder="Nama lengkap tamu">
      </div>
      <div class="form-row">
        <div class="form-group">
          <label class="form-label">No. Identitas (KTP/SIM)</label>
          <input type="text" class="form-control" placeholder="Opsional">
        </div>
        <div class="form-group">
          <label class="form-label">Rumah Tujuan</label>
          <input type="text" class="form-control" placeholder="Contoh: A/12">
        </div>
      </div>
      <div class="form-group">
        <label class="form-label">Keperluan</label>
        <select class="form-control">
          <option>Silaturahmi</option>
          <option>Pengiriman Paket</option>
          <option>Service/Perbaikan</option>
          <option>Tamu Menginap</option>
          <option>Lainnya</option>
        </select>
      </div>
      <div class="form-group">
        <label class="form-label">Foto Identitas</label>
        <input type="file" class="form-control" accept="image/*">
        <small class="text-muted" style="font-size:0.78rem;">Foto KTP/SIM untuk dokumentasi keamanan</small>
      </div>
    </div>
    <div class="modal-footer">
      <button class="btn btn-secondary modal-cancel">Batal</button>
      <button class="btn btn-primary">Check-in</button>
    </div>
  </div>
</div>

<?php include 'includes/footer.php'; ?>
