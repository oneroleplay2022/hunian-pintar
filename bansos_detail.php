<?php $pageTitle = 'Detail Penerima Bansos'; ?>
<?php include 'includes/header.php'; ?>
<div class="app-layout">
  <?php include 'includes/sidebar.php'; ?>
  <div class="main-wrapper">
    <?php include 'includes/topbar.php'; ?>
    <main class="main-content">
      <div class="page-header">
        <div><h1>Detail Penerima Bantuan</h1>
          <div class="breadcrumb"><a href="index.php">Dashboard</a><span class="separator">/</span><a href="bansos.php">Bansos</a><span class="separator">/</span><span>Detail</span></div>
        </div>
        <a href="bansos.php" class="btn btn-secondary btn-sm">← Kembali</a>
      </div>

      <div class="grid-2" style="gap:24px;">
        <div>
          <div class="card" style="padding:24px;">
            <div style="display:flex;align-items:center;gap:16px;margin-bottom:24px;">
              <div style="width:64px;height:64px;border-radius:50%;background:linear-gradient(135deg,var(--primary),var(--accent));display:flex;align-items:center;justify-content:center;color:white;font-size:1.5rem;font-weight:700;">SN</div>
              <div>
                <h2 style="font-size:1.2rem;">Sari Nurhayati</h2>
                <div class="text-muted" style="font-size:0.88rem;">NIK: 3201****7856 • Blok B/23</div>
              </div>
            </div>
            <div style="display:flex;flex-direction:column;gap:12px;font-size:0.88rem;">
              <div style="display:flex;justify-content:space-between;"><span class="text-muted">Status</span><span class="badge badge-success">Penerima Aktif</span></div>
              <div style="display:flex;justify-content:space-between;"><span class="text-muted">Usia</span><strong>65 Tahun</strong></div>
              <div style="display:flex;justify-content:space-between;"><span class="text-muted">Pekerjaan</span><strong>Tidak Bekerja</strong></div>
              <div style="display:flex;justify-content:space-between;"><span class="text-muted">Jumlah Tanggungan</span><strong>3 orang</strong></div>
              <div style="display:flex;justify-content:space-between;"><span class="text-muted">Penghasilan/bulan</span><strong>< Rp 1.000.000</strong></div>
              <div style="display:flex;justify-content:space-between;"><span class="text-muted">Terdaftar Sejak</span><strong>Jan 2025</strong></div>
            </div>
          </div>

          <!-- Programs Enrolled -->
          <div class="card" style="margin-top:16px;">
            <div class="card-header"><h3 class="card-title">📋 Program yang Diikuti</h3></div>
            <?php
            $programs = [
              ['PKH', 'Rp 3.000.000/tahun', 'Aktif', 'success', '🏠'],
              ['BPNT', 'Rp 200.000/bulan', 'Aktif', 'success', '🍚'],
              ['Zakat RT', 'Rp 500.000/tahun', 'Aktif', 'success', '🤲'],
            ];
            foreach ($programs as $p): ?>
            <div style="padding:14px 20px;border-bottom:1px solid var(--border-color);display:flex;align-items:center;justify-content:space-between;">
              <div style="display:flex;align-items:center;gap:12px;">
                <span style="font-size:1.3rem;"><?= $p[4] ?></span>
                <div>
                  <strong style="font-size:0.9rem;"><?= $p[0] ?></strong>
                  <div class="text-muted" style="font-size:0.78rem;"><?= $p[1] ?></div>
                </div>
              </div>
              <span class="badge badge-<?= $p[3] ?>"><?= $p[2] ?></span>
            </div>
            <?php endforeach; ?>
          </div>
        </div>

        <div>
          <!-- Distribution History -->
          <div class="card">
            <div class="card-header"><h3 class="card-title">📦 Riwayat Penyaluran</h3></div>
            <?php
            $dist = [
              ['BPNT — Maret 2026', 'Rp 200.000', '5 Mar 2026', 'Disalurkan', 'success'],
              ['PKH — Q1 2026', 'Rp 750.000', '15 Jan 2026', 'Disalurkan', 'success'],
              ['BPNT — Februari 2026', 'Rp 200.000', '5 Feb 2026', 'Disalurkan', 'success'],
              ['Zakat RT — 2025', 'Rp 500.000', '10 Apr 2025', 'Disalurkan', 'success'],
              ['BPNT — Januari 2026', 'Rp 200.000', '5 Jan 2026', 'Disalurkan', 'success'],
              ['PKH — Q4 2025', 'Rp 750.000', '15 Okt 2025', 'Disalurkan', 'success'],
            ];
            foreach ($dist as $d): ?>
            <div style="padding:14px 20px;border-bottom:1px solid var(--border-color);">
              <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:4px;">
                <strong style="font-size:0.88rem;"><?= $d[0] ?></strong>
                <span style="font-weight:700;color:var(--success);"><?= $d[1] ?></span>
              </div>
              <div style="display:flex;justify-content:space-between;font-size:0.78rem;">
                <span class="text-muted"><?= $d[2] ?></span>
                <span class="badge badge-<?= $d[4] ?>"><?= $d[3] ?></span>
              </div>
            </div>
            <?php endforeach; ?>
          </div>
        </div>
      </div>
    </main>
  </div>
</div>
<?php include 'includes/footer.php'; ?>
