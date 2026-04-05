<?php $pageTitle = 'Bantuan Sosial'; ?>
<?php include 'includes/header.php'; ?>

<div class="app-layout">
  <?php include 'includes/sidebar.php'; ?>

  <div class="main-wrapper">
    <?php include 'includes/topbar.php'; ?>

    <main class="main-content">
      <div class="page-header">
        <div>
          <h1>Bantuan Sosial</h1>
          <div class="breadcrumb">
            <a href="index.php">Dashboard</a>
            <span class="separator">/</span>
            <span>Pelayanan</span>
            <span class="separator">/</span>
            <span>Bantuan Sosial</span>
          </div>
        </div>
        <div style="display:flex;gap:10px;">
          <button class="btn btn-secondary btn-sm"><i data-lucide="download" style="width:16px;height:16px;"></i> Export Data</button>
          <a href="bansos_form.php" class="btn btn-primary btn-sm"><i data-lucide="plus" style="width:16px;height:16px;"></i> Tambah Penerima</a>
        </div>
      </div>

      <!-- Stats -->
      <div class="stats-grid">
        <div class="stat-card animate-fadeIn stagger-1">
          <div class="stat-icon blue"><i data-lucide="heart-handshake"></i></div>
          <div class="stat-info">
            <div class="stat-label">Total Penerima</div>
            <div class="stat-value">45</div>
          </div>
        </div>
        <div class="stat-card animate-fadeIn stagger-2">
          <div class="stat-icon green"><i data-lucide="check-circle"></i></div>
          <div class="stat-info">
            <div class="stat-label">Sudah Tersalurkan</div>
            <div class="stat-value">42</div>
          </div>
        </div>
        <div class="stat-card animate-fadeIn stagger-3">
          <div class="stat-icon yellow"><i data-lucide="clock"></i></div>
          <div class="stat-info">
            <div class="stat-label">Menunggu</div>
            <div class="stat-value">3</div>
          </div>
        </div>
        <div class="stat-card animate-fadeIn stagger-4">
          <div class="stat-icon purple"><i data-lucide="package"></i></div>
          <div class="stat-info">
            <div class="stat-label">Program Aktif</div>
            <div class="stat-value">4</div>
          </div>
        </div>
      </div>

      <!-- Active Programs -->
      <div class="menu-label" style="padding-left:0;">Program Bantuan Aktif</div>
      <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(250px,1fr));gap:16px;margin-bottom:24px;">
        <?php
        $programs = [
          ['PKH (Program Keluarga Harapan)', 12, 'Pemerintah', '🏛️', 'Rp 3.000.000/thn'],
          ['Sembako (BPNT)', 18, 'Pemerintah', '🍚', 'Rp 200.000/bln'],
          ['Bantuan RT', 10, 'Kas RT', '🏘️', 'Rp 500.000/event'],
          ['Zakat & Infaq', 5, 'Donatur Warga', '🤲', 'Sesuai donasi'],
        ];
        foreach ($programs as $p): ?>
        <div class="card" style="padding:20px;border-left:3px solid var(--primary);">
          <div style="display:flex;align-items:center;gap:12px;margin-bottom:12px;">
            <span style="font-size:1.8rem;"><?= $p[3] ?></span>
            <div>
              <div style="font-weight:700;font-size:0.95rem;"><?= $p[0] ?></div>
              <div class="text-muted" style="font-size:0.8rem;">Sumber: <?= $p[2] ?></div>
            </div>
          </div>
          <div style="display:flex;justify-content:space-between;align-items:center;padding-top:12px;border-top:1px solid var(--border-color);font-size:0.85rem;">
            <span><strong><?= $p[1] ?></strong> penerima</span>
            <span class="text-muted"><?= $p[4] ?></span>
          </div>
        </div>
        <?php endforeach; ?>
      </div>

      <!-- Filter -->
      <div class="filter-bar">
        <div class="filter-search">
          <span class="search-icon"><i data-lucide="search"></i></span>
          <input type="text" class="form-control" placeholder="Cari nama penerima...">
        </div>
        <select class="form-control" style="width:auto;">
          <option value="">Semua Program</option>
          <option>PKH</option>
          <option>Sembako (BPNT)</option>
          <option>Bantuan RT</option>
          <option>Zakat & Infaq</option>
        </select>
        <select class="form-control" style="width:auto;">
          <option value="">Status Penyaluran</option>
          <option>Sudah Tersalurkan</option>
          <option>Menunggu</option>
        </select>
      </div>

      <!-- Table -->
      <div class="card">
        <div class="card-header">
          <h3 class="card-title">Data Penerima Bantuan Sosial</h3>
        </div>
        <div class="table-container">
          <table class="table">
            <thead>
              <tr>
                <th>No</th>
                <th>Nama KK</th>
                <th>Rumah</th>
                <th>Program</th>
                <th>Nilai Bantuan</th>
                <th>Periode</th>
                <th>Status</th>
                <th>Aksi</th>
              </tr>
            </thead>
            <tbody>
              <?php
              $penerima = [
                ['Sukarno', 'C/03', 'PKH', 'Rp 3.000.000', '2026', 'Tersalurkan', 'success'],
                ['Ngadimin', 'D/21', 'PKH', 'Rp 3.000.000', '2026', 'Tersalurkan', 'success'],
                ['Parjo', 'A/28', 'Sembako', 'Rp 200.000', 'Mar 2026', 'Tersalurkan', 'success'],
                ['Suminah', 'B/14', 'Sembako', 'Rp 200.000', 'Mar 2026', 'Tersalurkan', 'success'],
                ['Darmi', 'C/25', 'Sembako', 'Rp 200.000', 'Mar 2026', 'Menunggu', 'warning'],
                ['Tukiman', 'D/09', 'Bantuan RT', 'Rp 500.000', 'Mar 2026', 'Tersalurkan', 'success'],
                ['Lasmi', 'A/17', 'Bantuan RT', 'Rp 500.000', 'Mar 2026', 'Menunggu', 'warning'],
                ['Suroto', 'B/30', 'Zakat', 'Rp 750.000', 'Mar 2026', 'Tersalurkan', 'success'],
              ];
              foreach ($penerima as $i => $p): ?>
              <tr>
                <td><?= $i + 1 ?></td>
                <td><strong><?= $p[0] ?></strong></td>
                <td><?= $p[1] ?></td>
                <td><span class="badge badge-info"><?= $p[2] ?></span></td>
                <td><?= $p[3] ?></td>
                <td><?= $p[4] ?></td>
                <td><span class="badge badge-<?= $p[6] ?>"><?= $p[5] ?></span></td>
                <td>
                  <?php if ($p[5] == 'Menunggu'): ?>
                  <a href="bansos_salurkan.php" class="btn btn-sm btn-success" style="padding:4px 10px;font-size:0.78rem;">Salurkan</a>
                  <?php else: ?>
                  <button class="btn btn-sm btn-outline" style="padding:4px 10px;font-size:0.78rem;">📥 Bukti</button>
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

<!-- Modal -->
<div class="modal-overlay" id="addBansosModal">
  <div class="modal">
    <div class="modal-header">
      <h3 class="modal-title">Tambah Penerima Bansos</h3>
      <button class="modal-close">✕</button>
    </div>
    <div class="modal-body">
      <div class="form-group">
        <label class="form-label">Nama Kepala Keluarga</label>
        <input type="text" class="form-control" placeholder="Nama lengkap KK">
      </div>
      <div class="form-row">
        <div class="form-group">
          <label class="form-label">Rumah</label>
          <input type="text" class="form-control" placeholder="Contoh: A/12">
        </div>
        <div class="form-group">
          <label class="form-label">NIK</label>
          <input type="text" class="form-control" placeholder="16 digit NIK">
        </div>
      </div>
      <div class="form-group">
        <label class="form-label">Program Bantuan</label>
        <select class="form-control">
          <option>PKH (Program Keluarga Harapan)</option>
          <option>Sembako (BPNT)</option>
          <option>Bantuan RT</option>
          <option>Zakat & Infaq</option>
        </select>
      </div>
      <div class="form-group">
        <label class="form-label">Alasan / Kriteria</label>
        <textarea class="form-control" rows="3" placeholder="Alasan penerima memenuhi kriteria..."></textarea>
      </div>
      <div class="form-group">
        <label class="form-label">Dokumen Pendukung</label>
        <input type="file" class="form-control" accept="image/*,.pdf" multiple>
      </div>
    </div>
    <div class="modal-footer">
      <button class="btn btn-secondary modal-cancel">Batal</button>
      <button class="btn btn-primary">Simpan</button>
    </div>
  </div>
</div>

<?php include 'includes/footer.php'; ?>
