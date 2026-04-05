<?php $pageTitle = 'E-Surat'; ?>
<?php include 'includes/header.php'; ?>

<div class="app-layout">
  <?php include 'includes/sidebar.php'; ?>

  <div class="main-wrapper">
    <?php include 'includes/topbar.php'; ?>

    <main class="main-content">
      <div class="page-header">
        <div>
          <h1>E-Surat</h1>
          <div class="breadcrumb">
            <a href="index.php">Dashboard</a>
            <span class="separator">/</span>
            <span>Pelayanan</span>
            <span class="separator">/</span>
            <span>E-Surat</span>
          </div>
        </div>
        <a href="surat_form.php" class="btn btn-primary btn-sm"><i data-lucide="file-plus" style="width:16px;height:16px;"></i> Ajukan Surat</a>
      </div>

      <!-- Stats -->
      <div class="stats-grid" style="grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));">
        <div class="stat-card">
          <div class="stat-icon blue"><i data-lucide="file-text"></i></div>
          <div class="stat-info">
            <div class="stat-label">Total Pengajuan</div>
            <div class="stat-value">156</div>
          </div>
        </div>
        <div class="stat-card">
          <div class="stat-icon yellow"><i data-lucide="clock"></i></div>
          <div class="stat-info">
            <div class="stat-label">Menunggu Proses</div>
            <div class="stat-value">7</div>
          </div>
        </div>
        <div class="stat-card">
          <div class="stat-icon green"><i data-lucide="check-circle"></i></div>
          <div class="stat-info">
            <div class="stat-label">Selesai</div>
            <div class="stat-value">145</div>
          </div>
        </div>
        <div class="stat-card">
          <div class="stat-icon red"><i data-lucide="x-circle"></i></div>
          <div class="stat-info">
            <div class="stat-label">Ditolak</div>
            <div class="stat-value">4</div>
          </div>
        </div>
      </div>

      <!-- Tabs -->
      <div class="tabs">
        <button class="tab-btn active">Semua</button>
        <button class="tab-btn">Menunggu</button>
        <button class="tab-btn">Diproses</button>
        <button class="tab-btn">Selesai</button>
        <button class="tab-btn">Ditolak</button>
      </div>

      <!-- Surat List -->
      <div style="display:flex;flex-direction:column;gap:12px;">
        <?php
        $suratData = [
          ['SR-2026-0042', 'Surat Pengantar RT', 'Budi Santoso', 'A/12', '27 Mar 2026', 'Menunggu', 'warning', 'Pengantar untuk pembuatan KTP baru'],
          ['SR-2026-0041', 'Surat Keterangan Domisili', 'Siti Rahayu', 'B/05', '26 Mar 2026', 'Diproses', 'info', 'Keterangan domisili untuk keperluan bank'],
          ['SR-2026-0040', 'Surat Keterangan Tidak Mampu', 'Ahmad Fauzi', 'C/08', '25 Mar 2026', 'Menunggu', 'warning', 'Keringanan biaya BPJS'],
          ['SR-2026-0039', 'Surat Pengantar RT', 'Dewi Lestari', 'A/22', '24 Mar 2026', 'Selesai', 'success', 'Pengantar pindah domisili'],
          ['SR-2026-0038', 'Izin Keramaian', 'Riko Pratama', 'D/15', '23 Mar 2026', 'Selesai', 'success', 'Acara pernikahan 29 Mar 2026'],
          ['SR-2026-0037', 'Izin Renovasi', 'Nur Hidayah', 'B/11', '22 Mar 2026', 'Diproses', 'info', 'Renovasi atap dan carport'],
          ['SR-2026-0036', 'Surat Pengantar RT', 'Maya Sari', 'C/19', '20 Mar 2026', 'Selesai', 'success', 'Pengantar untuk SKCK'],
          ['SR-2026-0035', 'Surat Keterangan Domisili', 'Arif Rahman', 'D/07', '18 Mar 2026', 'Ditolak', 'danger', 'Data tidak sesuai (NIK salah)'],
        ];
        foreach ($suratData as $s): ?>
        <div class="card" style="padding:20px;">
          <div style="display:flex;align-items:flex-start;justify-content:space-between;gap:16px;flex-wrap:wrap;">
            <div style="display:flex;gap:16px;align-items:flex-start;">
              <div style="width:48px;height:48px;border-radius:var(--radius-md);background:rgba(99,102,241,0.1);display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                <i data-lucide="file-text" style="width:22px;height:22px;color:var(--primary-light);"></i>
              </div>
              <div>
                <div style="display:flex;align-items:center;gap:8px;margin-bottom:4px;">
                  <strong style="font-size:0.95rem;"><?= $s[1] ?></strong>
                  <span class="badge badge-<?= $s[6] ?>"><?= $s[5] ?></span>
                </div>
                <div style="font-size:0.85rem;color:var(--text-secondary);margin-bottom:4px;">
                  <?= $s[2] ?> — <?= $s[3] ?> • <span class="text-muted"><?= $s[0] ?></span>
                </div>
                <div style="font-size:0.82rem;color:var(--text-muted);"><?= $s[7] ?></div>
              </div>
            </div>
            <div style="display:flex;align-items:center;gap:8px;flex-shrink:0;">
              <span class="text-muted" style="font-size:0.82rem;"><?= $s[4] ?></span>
              <?php if ($s[5] == 'Menunggu'): ?>
                <a href="surat_detail.php" class="btn btn-sm btn-success">Proses</a>
              <?php elseif ($s[5] == 'Diproses'): ?>
                <button class="btn btn-sm btn-primary">Selesaikan</button>
              <?php elseif ($s[5] == 'Selesai'): ?>
                <button class="btn btn-sm btn-outline">📥 Download</button>
              <?php endif; ?>
              <button class="btn btn-sm btn-icon btn-secondary"><i data-lucide="more-vertical" style="width:14px;height:14px;"></i></button>
            </div>
          </div>

          <?php if ($s[5] !== 'Ditolak'): ?>
          <!-- Progress Timeline -->
          <div style="display:flex;gap:0;margin-top:16px;padding-top:16px;border-top:1px solid var(--border-color);">
            <?php
            $steps = ['Diajukan', 'Diproses', 'TTD Digital', 'Selesai'];
            $activeStep = $s[5] == 'Menunggu' ? 0 : ($s[5] == 'Diproses' ? 1 : 3);
            foreach ($steps as $si => $step): ?>
            <div style="flex:1;text-align:center;position:relative;">
              <div style="width:24px;height:24px;border-radius:50%;margin:0 auto 6px;display:flex;align-items:center;justify-content:center;font-size:0.7rem;font-weight:700;
                <?= $si <= $activeStep ? 'background:var(--primary);color:white;' : 'background:var(--bg-input);color:var(--text-muted);border:1px solid var(--border-color);' ?>">
                <?= $si <= $activeStep ? '✓' : ($si + 1) ?>
              </div>
              <div style="font-size:0.72rem;color:<?= $si <= $activeStep ? 'var(--text-primary)' : 'var(--text-muted)' ?>;font-weight:<?= $si <= $activeStep ? '600' : '400' ?>;"><?= $step ?></div>
            </div>
            <?php endforeach; ?>
          </div>
          <?php endif; ?>
        </div>
        <?php endforeach; ?>
      </div>

      <div class="pagination">
        <button class="page-btn">←</button>
        <button class="page-btn active">1</button>
        <button class="page-btn">2</button>
        <button class="page-btn">→</button>
      </div>
    </main>
  </div>
</div>

<!-- Add Surat Modal -->
<div class="modal-overlay" id="addSuratModal">
  <div class="modal">
    <div class="modal-header">
      <h3 class="modal-title">Ajukan Surat</h3>
      <button class="modal-close">✕</button>
    </div>
    <div class="modal-body">
      <div class="form-group">
        <label class="form-label">Jenis Surat</label>
        <select class="form-control">
          <option>Surat Pengantar RT</option>
          <option>Surat Keterangan Domisili</option>
          <option>Surat Keterangan Tidak Mampu</option>
          <option>Izin Keramaian</option>
          <option>Izin Renovasi / Pembangunan</option>
          <option>Surat Keterangan Lainnya</option>
        </select>
      </div>
      <div class="form-group">
        <label class="form-label">Keperluan / Keterangan</label>
        <textarea class="form-control" rows="3" placeholder="Jelaskan keperluan pembuatan surat..."></textarea>
      </div>
      <div class="form-group">
        <label class="form-label">Upload Berkas Persyaratan</label>
        <input type="file" class="form-control" accept="image/*,.pdf" multiple>
        <small class="text-muted" style="font-size:0.78rem;">Foto KTP, KK, atau dokumen pendukung lainnya</small>
      </div>
    </div>
    <div class="modal-footer">
      <button class="btn btn-secondary modal-cancel">Batal</button>
      <button class="btn btn-primary">Ajukan Surat</button>
    </div>
  </div>
</div>

<?php include 'includes/footer.php'; ?>
