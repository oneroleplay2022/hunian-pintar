<?php $pageTitle = 'Detail Transaksi Kas'; ?>
<?php include 'includes/header.php'; ?>
<div class="app-layout">
  <?php include 'includes/sidebar.php'; ?>
  <div class="main-wrapper">
    <?php include 'includes/topbar.php'; ?>
    <main class="main-content">
      <div class="page-header">
        <div><h1>Detail Transaksi Kas</h1>
          <div class="breadcrumb"><a href="index.php">Dashboard</a><span class="separator">/</span><a href="kas.php">Kas</a><span class="separator">/</span><span>Detail</span></div>
        </div>
        <div style="display:flex;gap:10px;">
          <a href="kas.php" class="btn btn-secondary btn-sm">← Kembali</a>
          <button class="btn btn-outline btn-sm" onclick="window.print()">🖨️ Cetak</button>
        </div>
      </div>

      <div class="card" style="padding:24px;">
        <div style="text-align:center;padding:20px;margin-bottom:24px;background:rgba(239,68,68,0.06);border-radius:var(--radius-lg);border:1px solid rgba(239,68,68,0.12);">
          <div style="font-size:0.85rem;color:var(--text-muted);margin-bottom:4px;">Pengeluaran</div>
          <div style="font-size:2rem;font-weight:800;color:var(--danger);">– Rp 2.500.000</div>
        </div>

        <div style="display:flex;flex-direction:column;gap:12px;font-size:0.88rem;margin-bottom:24px;">
          <div style="display:flex;justify-content:space-between;"><span class="text-muted">No. Ref</span><strong>KAS-2026-03-025</strong></div>
          <div style="display:flex;justify-content:space-between;"><span class="text-muted">Tanggal</span><strong>20 Maret 2026</strong></div>
          <div style="display:flex;justify-content:space-between;"><span class="text-muted">Jenis</span><span class="badge badge-danger">Pengeluaran</span></div>
          <div style="display:flex;justify-content:space-between;"><span class="text-muted">Kategori</span><strong>Pemeliharaan</strong></div>
          <div style="display:flex;justify-content:space-between;"><span class="text-muted">Keterangan</span><strong>Perbaikan pompa air taman Blok B</strong></div>
          <div style="display:flex;justify-content:space-between;"><span class="text-muted">Dicatat Oleh</span><strong>Dewi Lestari (Bendahara)</strong></div>
        </div>

        <!-- Receipt -->
        <div style="margin-bottom:20px;">
          <h4 style="margin-bottom:12px;">📎 Bukti / Kuitansi</h4>
          <div style="aspect-ratio:4/3;background:linear-gradient(135deg,rgba(99,102,241,0.1),rgba(6,182,212,0.05));border-radius:var(--radius-md);display:flex;align-items:center;justify-content:center;">
            <div style="text-align:center;">
              <span style="font-size:3rem;">🧾</span>
              <div class="text-muted" style="margin-top:8px;font-size:0.85rem;">kuitansi_pompa_air.jpg</div>
            </div>
          </div>
        </div>

        <!-- Audit Log -->
        <div>
          <h4 style="margin-bottom:12px;">📝 Log Audit</h4>
          <div style="display:flex;flex-direction:column;gap:8px;font-size:0.82rem;">
            <div style="padding:10px 14px;background:var(--bg-input);border-radius:var(--radius-sm);display:flex;justify-content:space-between;">
              <span>Transaksi dicatat oleh Dewi Lestari</span><span class="text-muted">20 Mar 14:30</span>
            </div>
            <div style="padding:10px 14px;background:var(--bg-input);border-radius:var(--radius-sm);display:flex;justify-content:space-between;">
              <span>Diverifikasi oleh H. Supriadi (Ketua RT)</span><span class="text-muted">20 Mar 15:00</span>
            </div>
          </div>
        </div>
      </div>
    </main>
  </div>
</div>
<?php include 'includes/footer.php'; ?>
