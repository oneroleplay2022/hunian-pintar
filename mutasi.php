<?php $pageTitle = 'Mutasi Warga'; ?>
<?php include 'includes/header.php'; ?>

<div class="app-layout">
  <?php include 'includes/sidebar.php'; ?>

  <div class="main-wrapper">
    <?php include 'includes/topbar.php'; ?>

    <main class="main-content">
      <div class="page-header">
        <div>
          <h1>Mutasi Warga</h1>
          <div class="breadcrumb">
            <a href="index.php">Dashboard</a>
            <span class="separator">/</span>
            <span>Kependudukan</span>
            <span class="separator">/</span>
            <span>Mutasi</span>
          </div>
        </div>
        <a href="mutasi_form.php" class="btn btn-primary btn-sm"><i data-lucide="plus" style="width:16px;height:16px;"></i> Catat Mutasi</a>
      </div>

      <!-- Stats -->
      <div class="stats-grid" style="grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));">
        <div class="stat-card">
          <div class="stat-icon green"><i data-lucide="log-in"></i></div>
          <div class="stat-info">
            <div class="stat-label">Warga Masuk</div>
            <div class="stat-value">18</div>
            <div class="stat-change">Tahun 2026</div>
          </div>
        </div>
        <div class="stat-card">
          <div class="stat-icon yellow"><i data-lucide="log-out"></i></div>
          <div class="stat-info">
            <div class="stat-label">Warga Pindah</div>
            <div class="stat-value">6</div>
            <div class="stat-change">Tahun 2026</div>
          </div>
        </div>
        <div class="stat-card">
          <div class="stat-icon red"><i data-lucide="heart-off"></i></div>
          <div class="stat-info">
            <div class="stat-label">Meninggal</div>
            <div class="stat-value">2</div>
            <div class="stat-change">Tahun 2026</div>
          </div>
        </div>
      </div>

      <!-- Tabs -->
      <div class="tabs">
        <button class="tab-btn active">Semua</button>
        <button class="tab-btn">Masuk</button>
        <button class="tab-btn">Pindah</button>
        <button class="tab-btn">Meninggal</button>
      </div>

      <!-- Timeline -->
      <div class="card">
        <div class="timeline" style="padding-left:32px;">
          <?php
          $mutasi = [
            ['Masuk', 'Keluarga Hendra Wijaya (4 orang) pindah ke Blok A/25', '26 Mar 2026', 'success', '🟢', 'Pindahan dari Jakarta Selatan'],
            ['Pindah', 'Tn. Surya Darma pindah dari Blok C/12', '22 Mar 2026', 'warning', '🟡', 'Pindah ke Bandung karena tugas dinas'],
            ['Masuk', 'Ny. Ratna & suami pindah ke Blok B/18', '18 Mar 2026', 'success', '🟢', 'Pendatang baru, sewa kontrak 1 tahun'],
            ['Meninggal', 'Alm. H. Suyanto (Blok A/03) wafat', '15 Mar 2026', 'danger', '🔴', 'Wafat di RS, usia 78 tahun. Innalillahi.'],
            ['Masuk', 'Keluarga Farid Anugerah (3 orang) ke Blok D/20', '10 Mar 2026', 'success', '🟢', 'Beli rumah baru'],
            ['Pindah', 'Keluarga Tono Supriyadi pindah dari Blok B/07', '5 Mar 2026', 'warning', '🟡', 'Pindah ke perumahan lain'],
            ['Masuk', 'Nn. Aisyah (mahasiswa) kos di Blok C/14', '1 Mar 2026', 'success', '🟢', 'Kos/kontrak kamar, mahasiswa UB'],
            ['Meninggal', 'Alm. Bp. Karno (Blok D/02) wafat', '20 Feb 2026', 'danger', '🔴', 'Wafat di rumah, usia 85 tahun'],
            ['Pindah', 'Tn. Dedi Santoso pindah dari Blok A/30', '15 Feb 2026', 'warning', '🟡', 'Pindah ke luar kota'],
            ['Masuk', 'Keluarga Bambang (5 orang) ke Blok A/30', '28 Jan 2026', 'success', '🟢', 'Beli rumah second'],
          ];
          foreach ($mutasi as $m): ?>
          <div class="timeline-item" style="padding-bottom:20px;">
            <div style="background:var(--bg-input);border-radius:var(--radius-md);padding:16px;border-left:3px solid <?= $m[3] == 'success' ? 'var(--success)' : ($m[3] == 'warning' ? 'var(--warning)' : 'var(--danger)') ?>;">
              <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:6px;flex-wrap:wrap;gap:8px;">
                <div style="display:flex;align-items:center;gap:8px;">
                  <span><?= $m[4] ?></span>
                  <span class="badge badge-<?= $m[3] ?>"><?= $m[0] ?></span>
                  <strong style="font-size:0.92rem;"><?= $m[1] ?></strong>
                </div>
                <span class="text-muted" style="font-size:0.82rem;"><?= $m[2] ?></span>
              </div>
              <div style="font-size:0.85rem;color:var(--text-secondary);"><?= $m[5] ?></div>
            </div>
          </div>
          <?php endforeach; ?>
        </div>
      </div>
    </main>
  </div>
</div>

<!-- Modal -->
<div class="modal-overlay" id="addMutasiModal">
  <div class="modal">
    <div class="modal-header">
      <h3 class="modal-title">Catat Mutasi Warga</h3>
      <button class="modal-close">✕</button>
    </div>
    <div class="modal-body">
      <div class="form-group">
        <label class="form-label">Jenis Mutasi</label>
        <div style="display:flex;gap:10px;">
          <label class="checkbox-label" style="flex:1;padding:10px;border:1px solid var(--border-color);border-radius:var(--radius-md);">
            <input type="radio" name="mutasi_type" value="in" checked> 🟢 Masuk
          </label>
          <label class="checkbox-label" style="flex:1;padding:10px;border:1px solid var(--border-color);border-radius:var(--radius-md);">
            <input type="radio" name="mutasi_type" value="out"> 🟡 Pindah
          </label>
          <label class="checkbox-label" style="flex:1;padding:10px;border:1px solid var(--border-color);border-radius:var(--radius-md);">
            <input type="radio" name="mutasi_type" value="death"> 🔴 Meninggal
          </label>
        </div>
      </div>
      <div class="form-group">
        <label class="form-label">Nama Warga</label>
        <input type="text" class="form-control" placeholder="Nama lengkap">
      </div>
      <div class="form-row">
        <div class="form-group">
          <label class="form-label">Blok / Rumah</label>
          <input type="text" class="form-control" placeholder="Contoh: A/12">
        </div>
        <div class="form-group">
          <label class="form-label">Tanggal</label>
          <input type="date" class="form-control">
        </div>
      </div>
      <div class="form-group">
        <label class="form-label">Keterangan</label>
        <textarea class="form-control" rows="3" placeholder="Detail mutasi..."></textarea>
      </div>
    </div>
    <div class="modal-footer">
      <button class="btn btn-secondary modal-cancel">Batal</button>
      <button class="btn btn-primary">Simpan</button>
    </div>
  </div>
</div>

<?php include 'includes/footer.php'; ?>
