<?php $pageTitle = 'Detail Transaksi'; ?>
<?php include 'includes/header.php'; ?>
<div class="app-layout">
  <?php include 'includes/sidebar.php'; ?>
  <div class="main-wrapper">
    <?php include 'includes/topbar.php'; ?>
    <main class="main-content">
      <div class="page-header">
        <div><h1>Detail Transaksi</h1>
          <div class="breadcrumb"><a href="index.php">Dashboard</a><span class="separator">/</span><a href="pembayaran.php">Pembayaran</a><span class="separator">/</span><span>Detail</span></div>
        </div>
        <div style="display:flex;gap:10px;">
          <a href="pembayaran.php" class="btn btn-secondary btn-sm">← Kembali</a>
          <button class="btn btn-outline btn-sm" onclick="window.print()">🖨️ Cetak</button>
        </div>
      </div>

      <div class="card" style="padding:32px;">
        <!-- Receipt -->
        <div style="text-align:center;margin-bottom:24px;">
          <div style="font-size:3rem;margin-bottom:8px;">✅</div>
          <h2 style="color:var(--success);">Pembayaran Berhasil</h2>
          <p class="text-muted" style="font-size:0.88rem;">Transaksi telah berhasil dikonfirmasi</p>
        </div>

        <div style="padding:20px;background:var(--bg-input);border-radius:var(--radius-lg);margin-bottom:24px;">
          <div style="text-align:center;margin-bottom:16px;">
            <div class="text-muted" style="font-size:0.85rem;">Jumlah Pembayaran</div>
            <div style="font-size:2rem;font-weight:800;">Rp 350.000</div>
          </div>
          <div style="display:flex;flex-direction:column;gap:10px;font-size:0.88rem;">
            <div style="display:flex;justify-content:space-between;"><span class="text-muted">No. Transaksi</span><strong style="font-family:monospace;">TRX-2026030812</strong></div>
            <div style="display:flex;justify-content:space-between;"><span class="text-muted">Tanggal</span><strong>8 Maret 2026, 14:32</strong></div>
            <div style="display:flex;justify-content:space-between;"><span class="text-muted">Metode</span><strong>QRIS — GoPay</strong></div>
            <div style="display:flex;justify-content:space-between;"><span class="text-muted">Keterangan</span><strong>IPL Maret 2026</strong></div>
            <div style="display:flex;justify-content:space-between;"><span class="text-muted">Rumah</span><strong>A/12 — Budi Santoso</strong></div>
            <div style="display:flex;justify-content:space-between;"><span class="text-muted">Status</span><span class="badge badge-success">Sukses</span></div>
          </div>
        </div>

        <h4 style="margin-bottom:12px;">Rincian</h4>
        <div style="display:flex;flex-direction:column;gap:8px;font-size:0.88rem;margin-bottom:20px;">
          <div style="display:flex;justify-content:space-between;"><span>Iuran IPL</span><span>Rp 300.000</span></div>
          <div style="display:flex;justify-content:space-between;"><span>Iuran Keamanan</span><span>Rp 30.000</span></div>
          <div style="display:flex;justify-content:space-between;"><span>Iuran Kebersihan</span><span>Rp 20.000</span></div>
          <div style="display:flex;justify-content:space-between;padding-top:10px;border-top:1px solid var(--border-color);font-weight:700;">
            <span>Total</span><span>Rp 350.000</span>
          </div>
        </div>

        <div style="display:flex;gap:12px;">
          <button class="btn btn-outline w-full">📥 Download PDF</button>
          <button class="btn btn-outline w-full">📤 Kirim via WA</button>
        </div>
      </div>
    </main>
  </div>
</div>
<?php include 'includes/footer.php'; ?>
