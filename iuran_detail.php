<?php $pageTitle = 'Detail Tagihan'; ?>
<?php include 'includes/header.php'; ?>
<div class="app-layout">
  <?php include 'includes/sidebar.php'; ?>
  <div class="main-wrapper">
    <?php include 'includes/topbar.php'; ?>
    <main class="main-content">
      <div class="page-header">
        <div><h1>Detail Tagihan IPL</h1>
          <div class="breadcrumb"><a href="index.php">Dashboard</a><span class="separator">/</span><a href="iuran.php">Iuran</a><span class="separator">/</span><span>Detail</span></div>
        </div>
        <div style="display:flex;gap:10px;">
          <a href="iuran.php" class="btn btn-secondary btn-sm">← Kembali</a>
          <a href="iuran_bayar.php" class="btn btn-primary btn-sm">💳 Bayar Sekarang</a>
        </div>
      </div>

      <div class="grid-2" style="gap:24px;">
        <div>
          <div class="card" style="padding:24px;">
            <div style="text-align:center;padding:20px;margin-bottom:20px;background:linear-gradient(135deg,rgba(239,68,68,0.08),rgba(239,68,68,0.02));border-radius:var(--radius-lg);border:1px solid rgba(239,68,68,0.15);">
              <div class="text-muted" style="font-size:0.88rem;margin-bottom:4px;">Total Tagihan</div>
              <div style="font-size:2.2rem;font-weight:800;color:var(--danger);">Rp 350.000</div>
              <span class="badge badge-warning" style="margin-top:8px;">Belum Bayar</span>
            </div>

            <div style="display:flex;flex-direction:column;gap:12px;font-size:0.88rem;">
              <div style="display:flex;justify-content:space-between;"><span class="text-muted">No. Invoice</span><strong>INV-2026-03-012</strong></div>
              <div style="display:flex;justify-content:space-between;"><span class="text-muted">Periode</span><strong>Maret 2026</strong></div>
              <div style="display:flex;justify-content:space-between;"><span class="text-muted">Jatuh Tempo</span><strong style="color:var(--danger);">10 Maret 2026</strong></div>
              <div style="display:flex;justify-content:space-between;"><span class="text-muted">Rumah</span><strong>Blok A / No. 12</strong></div>
              <div style="display:flex;justify-content:space-between;"><span class="text-muted">Atas Nama</span><strong>Budi Santoso</strong></div>
            </div>

            <div style="margin-top:20px;padding-top:16px;border-top:1px solid var(--border-color);">
              <h4 style="margin-bottom:12px;">Rincian Tagihan</h4>
              <div style="display:flex;flex-direction:column;gap:8px;font-size:0.88rem;">
                <div style="display:flex;justify-content:space-between;"><span>Iuran IPL Bulanan</span><strong>Rp 300.000</strong></div>
                <div style="display:flex;justify-content:space-between;"><span>Iuran Keamanan</span><strong>Rp 30.000</strong></div>
                <div style="display:flex;justify-content:space-between;"><span>Iuran Kebersihan</span><strong>Rp 20.000</strong></div>
                <div style="display:flex;justify-content:space-between;padding-top:12px;border-top:1px solid var(--border-color);font-weight:700;font-size:1rem;">
                  <span>TOTAL</span><span style="color:var(--danger);">Rp 350.000</span>
                </div>
              </div>
            </div>
          </div>
        </div>

        <div>
          <!-- Payment History for this house -->
          <div class="card">
            <div class="card-header"><h3 class="card-title">📊 Riwayat Pembayaran IPL — A/12</h3></div>
            <?php
            $history = [
              ['Februari 2026', 'Rp 350.000', '8 Feb 2026', 'QRIS', 'Lunas', 'success'],
              ['Januari 2026', 'Rp 350.000', '5 Jan 2026', 'VA BCA', 'Lunas', 'success'],
              ['Desember 2025', 'Rp 350.000', '9 Des 2025', 'Transfer', 'Lunas', 'success'],
              ['November 2025', 'Rp 350.000', '10 Nov 2025', 'QRIS', 'Lunas', 'success'],
              ['Oktober 2025', 'Rp 350.000', '3 Okt 2025', 'VA Mandiri', 'Lunas', 'success'],
            ];
            foreach ($history as $h): ?>
            <div style="padding:14px 20px;border-bottom:1px solid var(--border-color);display:flex;align-items:center;justify-content:space-between;">
              <div>
                <strong style="font-size:0.9rem;"><?= $h[0] ?></strong>
                <div class="text-muted" style="font-size:0.78rem;"><?= $h[2] ?> via <?= $h[3] ?></div>
              </div>
              <div style="text-align:right;">
                <div style="font-weight:600;"><?= $h[1] ?></div>
                <span class="badge badge-<?= $h[5] ?>"><?= $h[4] ?></span>
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
