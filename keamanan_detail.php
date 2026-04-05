<?php $pageTitle = 'Detail Alert Keamanan'; ?>
<?php include 'includes/header.php'; ?>
<div class="app-layout">
  <?php include 'includes/sidebar.php'; ?>
  <div class="main-wrapper">
    <?php include 'includes/topbar.php'; ?>
    <main class="main-content">
      <div class="page-header">
        <div><h1>Detail Alert Keamanan</h1>
          <div class="breadcrumb"><a href="index.php">Dashboard</a><span class="separator">/</span><a href="keamanan.php">Keamanan</a><span class="separator">/</span><span>Detail</span></div>
        </div>
        <a href="keamanan.php" class="btn btn-secondary btn-sm">← Kembali</a>
      </div>

      <div class="grid-2" style="gap:24px;">
        <div>
          <div class="card" style="padding:24px;">
            <div style="text-align:center;padding:20px;margin-bottom:20px;background:rgba(239,68,68,0.08);border-radius:var(--radius-lg);border:1px solid rgba(239,68,68,0.15);">
              <div style="font-size:3rem;margin-bottom:8px;">🚨</div>
              <h2 style="color:var(--danger);font-size:1.3rem;">PANIC BUTTON AKTIF</h2>
              <div class="text-muted" style="margin-top:4px;">24 Maret 2026, 23:15 WIB</div>
            </div>

            <div style="display:flex;flex-direction:column;gap:12px;font-size:0.88rem;">
              <div style="display:flex;justify-content:space-between;"><span class="text-muted">No. Alert</span><strong>ALR-2026-03-003</strong></div>
              <div style="display:flex;justify-content:space-between;"><span class="text-muted">Pelapor</span><strong>Budi Santoso (A/12)</strong></div>
              <div style="display:flex;justify-content:space-between;"><span class="text-muted">Jenis</span><span class="badge badge-danger">Darurat - Keamanan</span></div>
              <div style="display:flex;justify-content:space-between;"><span class="text-muted">Lokasi</span><strong>Blok A, depan rumah No. 12</strong></div>
              <div style="display:flex;justify-content:space-between;"><span class="text-muted">Status</span><span class="badge badge-success">Ditangani</span></div>
            </div>

            <div style="margin-top:20px;padding-top:16px;border-top:1px solid var(--border-color);">
              <h4 style="margin-bottom:8px;">Kronologi</h4>
              <p style="font-size:0.88rem;color:var(--text-secondary);line-height:1.7;">Ada orang tidak dikenal mencoba masuk melalui pagar samping rumah A/12 pada pukul 23:15. Warga menekan panic button, petugas keamanan merespons dalam 3 menit.</p>
            </div>
          </div>
        </div>

        <div>
          <!-- Action Timeline -->
          <div class="card" style="margin-bottom:16px;">
            <div class="card-header"><h3 class="card-title">📊 Log Tindakan</h3></div>
            <div style="padding:20px;display:flex;flex-direction:column;gap:16px;">
              <?php
              $timeline = [
                ['23:15', 'Panic button ditekan', 'Budi Santoso (A/12)', 'danger'],
                ['23:16', 'Notifikasi broadcast ke semua warga', 'Sistem', 'info'],
                ['23:18', 'Petugas keamanan tiba di lokasi', 'Satpam — Heri', 'warning'],
                ['23:25', 'Situasi diamankan, orang asing ditangani', 'Satpam — Heri', 'success'],
                ['23:30', 'Laporan selesai, alert closed', 'Admin Satu', 'success'],
              ];
              foreach ($timeline as $t): ?>
              <div style="display:flex;gap:12px;align-items:flex-start;">
                <div style="min-width:50px;text-align:right;font-weight:700;font-family:monospace;font-size:0.85rem;color:var(--<?= $t[3] ?>);"><?= $t[0] ?></div>
                <div style="width:10px;height:10px;border-radius:50%;background:var(--<?= $t[3] ?>);margin-top:5px;flex-shrink:0;"></div>
                <div>
                  <div style="font-weight:600;font-size:0.88rem;"><?= $t[1] ?></div>
                  <div class="text-muted" style="font-size:0.78rem;"><?= $t[2] ?></div>
                </div>
              </div>
              <?php endforeach; ?>
            </div>
          </div>

          <!-- Admin Action -->
          <div class="card" style="padding:20px;">
            <h4 style="margin-bottom:12px;">⚙️ Tindak Lanjut</h4>
            <div class="form-group"><label class="form-label">Status</label>
              <select class="form-control"><option>Aktif</option><option>Diproses</option><option selected>Ditangani</option><option>Selesai</option></select>
            </div>
            <div class="form-group"><label class="form-label">Catatan Petugas</label>
              <textarea class="form-control" rows="3">Orang tidak dikenal telah diamankan dan diserahkan ke pihak kepolisian Polsek setempat.</textarea>
            </div>
            <button class="btn btn-primary btn-sm w-full" onclick="showToast('Status alert diperbarui!','success')">✅ Update</button>
          </div>
        </div>
      </div>
    </main>
  </div>
</div>
<?php include 'includes/footer.php'; ?>
