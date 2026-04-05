<?php $pageTitle = 'Perizinan'; ?>
<?php include 'includes/header.php'; ?>

<div class="app-layout">
  <?php include 'includes/sidebar.php'; ?>

  <div class="main-wrapper">
    <?php include 'includes/topbar.php'; ?>

    <main class="main-content">
      <div class="page-header">
        <div>
          <h1>Perizinan</h1>
          <div class="breadcrumb">
            <a href="index.php">Dashboard</a>
            <span class="separator">/</span>
            <span>Pelayanan</span>
            <span class="separator">/</span>
            <span>Perizinan</span>
          </div>
        </div>
        <a href="perizinan_form.php" class="btn btn-primary btn-sm"><i data-lucide="plus" style="width:16px;height:16px;"></i> Ajukan Izin</a>
      </div>

      <!-- Stats -->
      <div class="stats-grid" style="grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));">
        <div class="stat-card">
          <div class="stat-icon blue"><i data-lucide="clipboard-list"></i></div>
          <div class="stat-info">
            <div class="stat-label">Total Pengajuan</div>
            <div class="stat-value">45</div>
          </div>
        </div>
        <div class="stat-card">
          <div class="stat-icon yellow"><i data-lucide="clock"></i></div>
          <div class="stat-info">
            <div class="stat-label">Menunggu Verifikasi</div>
            <div class="stat-value">3</div>
          </div>
        </div>
        <div class="stat-card">
          <div class="stat-icon green"><i data-lucide="check-circle"></i></div>
          <div class="stat-info">
            <div class="stat-label">Disetujui</div>
            <div class="stat-value">39</div>
          </div>
        </div>
        <div class="stat-card">
          <div class="stat-icon red"><i data-lucide="x-circle"></i></div>
          <div class="stat-info">
            <div class="stat-label">Ditolak</div>
            <div class="stat-value">3</div>
          </div>
        </div>
      </div>

      <!-- Tabs -->
      <div class="tabs">
        <button class="tab-btn active">Semua</button>
        <button class="tab-btn">Renovasi</button>
        <button class="tab-btn">Keramaian</button>
        <button class="tab-btn">Menunggu</button>
        <button class="tab-btn">Disetujui</button>
      </div>

      <!-- Permit Cards -->
      <div style="display:flex;flex-direction:column;gap:12px;">
        <?php
        $izin = [
          ['IZ-2026-015', 'Izin Renovasi', 'Budi Santoso', 'A/12', '27 Mar 2026', 'Menunggu', 'warning',
            'Renovasi fasad depan rumah dan penambahan canopy carport', '🏗️',
            ['Mulai: 1 Apr 2026', 'Selesai: 30 Apr 2026', 'Kontraktor: CV Maju Jaya']],
          ['IZ-2026-014', 'Izin Keramaian', 'Ahmad Fauzi', 'C/08', '25 Mar 2026', 'Disetujui', 'success',
            'Acara resepsi pernikahan anak pertama', '🎉',
            ['Tanggal: 29 Mar 2026', 'Waktu: 10:00 - 22:00', 'Tamu: ±200 orang']],
          ['IZ-2026-013', 'Izin Renovasi', 'Siti Rahayu', 'B/05', '22 Mar 2026', 'Disetujui', 'success',
            'Renovasi kamar mandi dan dapur', '🏗️',
            ['Mulai: 25 Mar 2026', 'Selesai: 10 Apr 2026', 'Kontraktor: Tukang Mandiri']],
          ['IZ-2026-012', 'Izin Keramaian', 'Riko Pratama', 'D/15', '20 Mar 2026', 'Menunggu', 'warning',
            'Acara syukuran dan potong kambing', '🎉',
            ['Tanggal: 5 Apr 2026', 'Waktu: 09:00 - 15:00', 'Tamu: ±50 orang']],
          ['IZ-2026-011', 'Izin Renovasi', 'Dewi Lestari', 'A/22', '18 Mar 2026', 'Disetujui', 'success',
            'Penambahan lantai 2 (tingkat)', '🏗️',
            ['Mulai: 20 Mar 2026', 'Selesai: 20 Jun 2026', 'Kontraktor: PT Bangun Sejahtera']],
          ['IZ-2026-010', 'Izin Keramaian', 'Maya Sari', 'C/19', '15 Mar 2026', 'Ditolak', 'danger',
            'Acara live music malam', '🎉',
            ['Ditolak: Melebihi batas waktu keramaian (22:00)', 'Saran: Ubah ke siang hari']],
        ];
        foreach ($izin as $z): ?>
        <div class="card" style="padding:20px;">
          <div style="display:flex;align-items:flex-start;justify-content:space-between;gap:16px;flex-wrap:wrap;">
            <div style="display:flex;gap:14px;align-items:flex-start;">
              <div style="width:50px;height:50px;border-radius:var(--radius-md);background:<?= $z[1] == 'Izin Renovasi' ? 'rgba(99,102,241,0.1)' : 'rgba(236,72,153,0.1)' ?>;display:flex;align-items:center;justify-content:center;font-size:1.6rem;flex-shrink:0;">
                <?= $z[8] ?>
              </div>
              <div>
                <div style="display:flex;align-items:center;gap:8px;margin-bottom:4px;">
                  <strong style="font-size:0.95rem;"><?= $z[1] ?></strong>
                  <span class="badge badge-<?= $z[6] ?>"><?= $z[5] ?></span>
                </div>
                <div style="font-size:0.85rem;color:var(--text-secondary);margin-bottom:6px;">
                  <?= $z[2] ?> — <?= $z[3] ?> • <span class="text-muted"><?= $z[0] ?></span>
                </div>
                <div style="font-size:0.88rem;color:var(--text-primary);margin-bottom:8px;"><?= $z[7] ?></div>
                <div style="display:flex;flex-wrap:wrap;gap:8px;">
                  <?php foreach ($z[9] as $detail): ?>
                  <span style="font-size:0.78rem;padding:4px 10px;background:var(--bg-input);border-radius:var(--radius-sm);color:var(--text-muted);"><?= $detail ?></span>
                  <?php endforeach; ?>
                </div>
              </div>
            </div>
            <div style="display:flex;align-items:center;gap:8px;flex-shrink:0;">
              <span class="text-muted" style="font-size:0.82rem;"><?= $z[4] ?></span>
              <?php if ($z[5] == 'Menunggu'): ?>
                <button class="btn btn-sm btn-success">Setujui</button>
                <button class="btn btn-sm btn-danger">Tolak</button>
              <?php elseif ($z[5] == 'Disetujui'): ?>
                <button class="btn btn-sm btn-outline">📥 Surat Izin</button>
              <?php endif; ?>
            </div>
          </div>
        </div>
        <?php endforeach; ?>
      </div>
    </main>
  </div>
</div>

<!-- Modal -->
<div class="modal-overlay" id="addIzinModal">
  <div class="modal">
    <div class="modal-header">
      <h3 class="modal-title">Ajukan Izin</h3>
      <button class="modal-close">✕</button>
    </div>
    <div class="modal-body">
      <div class="form-group">
        <label class="form-label">Jenis Izin</label>
        <select class="form-control">
          <option>Izin Renovasi / Pembangunan</option>
          <option>Izin Keramaian / Hajatan</option>
        </select>
      </div>
      <div class="form-group">
        <label class="form-label">Deskripsi Kegiatan</label>
        <textarea class="form-control" rows="3" placeholder="Jelaskan detail kegiatan..."></textarea>
      </div>
      <div class="form-row">
        <div class="form-group">
          <label class="form-label">Tanggal Mulai</label>
          <input type="date" class="form-control">
        </div>
        <div class="form-group">
          <label class="form-label">Tanggal Selesai</label>
          <input type="date" class="form-control">
        </div>
      </div>
      <div class="form-group">
        <label class="form-label">Upload Dokumen Pendukung</label>
        <input type="file" class="form-control" accept="image/*,.pdf" multiple>
        <small class="text-muted" style="font-size:0.78rem;">Denah renovasi, undangan, dll</small>
      </div>
    </div>
    <div class="modal-footer">
      <button class="btn btn-secondary modal-cancel">Batal</button>
      <button class="btn btn-primary">Ajukan</button>
    </div>
  </div>
</div>

<?php include 'includes/footer.php'; ?>
