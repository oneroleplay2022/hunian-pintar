<?php $pageTitle = 'Iuran & Tagihan'; ?>
<?php include 'includes/header.php'; ?>

<div class="app-layout">
  <?php include 'includes/sidebar.php'; ?>

  <div class="main-wrapper">
    <?php include 'includes/topbar.php'; ?>

    <main class="main-content">
      <div class="page-header">
        <div>
          <h1>Iuran & Tagihan</h1>
          <div class="breadcrumb">
            <a href="index.php">Dashboard</a>
            <span class="separator">/</span>
            <span>Keuangan</span>
            <span class="separator">/</span>
            <span>Iuran</span>
          </div>
        </div>
        <div style="display:flex;gap:10px;">
          <button class="btn btn-secondary btn-sm"><i data-lucide="send" style="width:16px;height:16px;"></i> Kirim Tagihan</button>
          <a href="iuran_form.php" class="btn btn-primary btn-sm"><i data-lucide="plus" style="width:16px;height:16px;"></i> Buat Tagihan</a>
        </div>
      </div>

      <!-- Stats -->
      <div class="stats-grid">
        <div class="stat-card animate-fadeIn stagger-1">
          <div class="stat-icon green"><i data-lucide="check-circle"></i></div>
          <div class="stat-info">
            <div class="stat-label">Sudah Bayar</div>
            <div class="stat-value">312</div>
            <div class="stat-change up">78% tercapai</div>
          </div>
        </div>
        <div class="stat-card animate-fadeIn stagger-2">
          <div class="stat-icon yellow"><i data-lucide="clock"></i></div>
          <div class="stat-info">
            <div class="stat-label">Belum Bayar</div>
            <div class="stat-value">68</div>
            <div class="stat-change">17% pending</div>
          </div>
        </div>
        <div class="stat-card animate-fadeIn stagger-3">
          <div class="stat-icon red"><i data-lucide="alert-triangle"></i></div>
          <div class="stat-info">
            <div class="stat-label">Tunggakan</div>
            <div class="stat-value">20</div>
            <div class="stat-change down">5% nunggak</div>
          </div>
        </div>
        <div class="stat-card animate-fadeIn stagger-4">
          <div class="stat-icon blue"><i data-lucide="wallet"></i></div>
          <div class="stat-info">
            <div class="stat-label">Total Terkumpul</div>
            <div class="stat-value">Rp 109,2jt</div>
            <div class="stat-change up">Maret 2026</div>
          </div>
        </div>
      </div>

      <!-- Tabs -->
      <div class="tabs">
        <button class="tab-btn active">Semua Tagihan</button>
        <button class="tab-btn">Belum Bayar</button>
        <button class="tab-btn">Lunas</button>
        <button class="tab-btn">Tunggakan</button>
      </div>

      <!-- Filter -->
      <div class="filter-bar">
        <div class="filter-search">
          <span class="search-icon"><i data-lucide="search"></i></span>
          <input type="text" class="form-control" placeholder="Cari nama atau no rumah...">
        </div>
        <select class="form-control" style="width:auto;">
          <option>Maret 2026</option>
          <option>Februari 2026</option>
          <option>Januari 2026</option>
        </select>
        <select class="form-control" style="width:auto;">
          <option value="">Semua Blok</option>
          <option>Blok A</option>
          <option>Blok B</option>
          <option>Blok C</option>
          <option>Blok D</option>
        </select>
        <select class="form-control" style="width:auto;">
          <option value="">Jenis Iuran</option>
          <option>IPL</option>
          <option>Keamanan</option>
          <option>Kebersihan</option>
        </select>
      </div>

      <!-- Table -->
      <div class="card">
        <div class="table-container">
          <table class="table">
            <thead>
              <tr>
                <th>Rumah</th>
                <th>Nama KK</th>
                <th>Jenis</th>
                <th>Periode</th>
                <th>Jumlah</th>
                <th>Jatuh Tempo</th>
                <th>Status</th>
                <th>Aksi</th>
              </tr>
            </thead>
            <tbody>
              <?php
              $tagihan = [
                ['A/01', 'Hadi Wijaya', 'IPL', 'Mar 2026', 350000, '31 Mar 2026', 'Lunas', 'success'],
                ['A/02', 'Rina Susanti', 'IPL', 'Mar 2026', 350000, '31 Mar 2026', 'Lunas', 'success'],
                ['A/03', 'Joko Widodo', 'IPL', 'Mar 2026', 350000, '31 Mar 2026', 'Belum', 'warning'],
                ['A/05', 'Sari Mulyani', 'IPL', 'Mar 2026', 350000, '31 Mar 2026', 'Lunas', 'success'],
                ['A/06', 'Andi (Kontrak)', 'IPL', 'Mar 2026', 350000, '31 Mar 2026', 'Belum', 'warning'],
                ['A/07', 'Bambang S.', 'IPL', 'Feb-Mar 2026', 700000, '31 Mar 2026', 'Nunggak', 'danger'],
                ['A/08', 'Lestari N.', 'IPL', 'Mar 2026', 350000, '31 Mar 2026', 'Lunas', 'success'],
                ['A/10', 'Putri A.', 'IPL', 'Mar 2026', 350000, '31 Mar 2026', 'Lunas', 'success'],
                ['A/11', 'Wahyu K.', 'IPL', 'Mar 2026', 350000, '31 Mar 2026', 'Belum', 'warning'],
                ['A/12', 'Budi Santoso', 'IPL', 'Mar 2026', 350000, '31 Mar 2026', 'Lunas', 'success'],
              ];
              foreach ($tagihan as $t): ?>
              <tr>
                <td><strong><?= $t[0] ?></strong></td>
                <td><?= $t[1] ?></td>
                <td><?= $t[2] ?></td>
                <td><?= $t[3] ?></td>
                <td>Rp <?= number_format($t[4], 0, ',', '.') ?></td>
                <td><?= $t[5] ?></td>
                <td><span class="badge badge-<?= $t[7] ?>"><?= $t[6] ?></span></td>
                <td>
                  <div style="display:flex;gap:4px;">
                    <?php if ($t[6] !== 'Lunas'): ?>
                    <a href="iuran_bayar.php" class="btn btn-sm btn-primary" style="padding:4px 10px;font-size:0.78rem;">Bayar</a>
                    <a href="iuran_detail.php" class="btn btn-sm btn-secondary" style="padding:4px 10px;font-size:0.78rem;">Detail</a>
                    <?php else: ?>
                    <button class="btn btn-sm btn-secondary" style="padding:4px 10px;font-size:0.78rem;">Receipt</button>
                    <?php endif; ?>
                  </div>
                </td>
              </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        </div>
        <div style="display:flex;align-items:center;justify-content:space-between;margin-top:16px;flex-wrap:wrap;gap:12px;">
          <span class="text-muted" style="font-size:0.85rem;">Menampilkan 1-10 dari 400 tagihan</span>
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

<!-- Add Invoice Modal -->
<div class="modal-overlay" id="addInvoiceModal">
  <div class="modal">
    <div class="modal-header">
      <h3 class="modal-title">Buat Tagihan Baru</h3>
      <button class="modal-close">✕</button>
    </div>
    <div class="modal-body">
      <div class="form-group">
        <label class="form-label">Jenis Iuran</label>
        <select class="form-control">
          <option>IPL Bulanan</option>
          <option>Iuran Keamanan</option>
          <option>Iuran Kebersihan</option>
          <option>Iuran Khusus</option>
        </select>
      </div>
      <div class="form-row">
        <div class="form-group">
          <label class="form-label">Periode</label>
          <input type="month" class="form-control">
        </div>
        <div class="form-group">
          <label class="form-label">Jumlah (Rp)</label>
          <input type="number" class="form-control" placeholder="350000">
        </div>
      </div>
      <div class="form-group">
        <label class="form-label">Target</label>
        <select class="form-control">
          <option>Semua Rumah</option>
          <option>Blok A saja</option>
          <option>Blok B saja</option>
          <option>Blok C saja</option>
          <option>Blok D saja</option>
        </select>
      </div>
      <div class="form-group">
        <label class="form-label">Jatuh Tempo</label>
        <input type="date" class="form-control">
      </div>
      <div class="form-group">
        <label class="checkbox-label">
          <input type="checkbox" checked> Kirim notifikasi ke warga
        </label>
      </div>
    </div>
    <div class="modal-footer">
      <button class="btn btn-secondary modal-cancel">Batal</button>
      <button class="btn btn-primary">Buat & Kirim</button>
    </div>
  </div>
</div>

<?php include 'includes/footer.php'; ?>
