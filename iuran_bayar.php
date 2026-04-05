<?php $pageTitle = 'Pembayaran Iuran'; ?>
<?php include 'includes/header.php'; ?>
<div class="app-layout">
  <?php include 'includes/sidebar.php'; ?>
  <div class="main-wrapper">
    <?php include 'includes/topbar.php'; ?>
    <main class="main-content">
      <div class="page-header">
        <div><h1>Pembayaran Iuran</h1>
          <div class="breadcrumb"><a href="index.php">Dashboard</a><span class="separator">/</span><a href="iuran.php">Iuran</a><span class="separator">/</span><span>Bayar</span></div>
        </div>
        <a href="iuran.php" class="btn btn-secondary btn-sm">← Kembali</a>
      </div>

      <div class="card" style="padding:24px;">
        <!-- Summary -->
        <div style="text-align:center;padding:20px;margin-bottom:24px;background:linear-gradient(135deg,rgba(99,102,241,0.08),rgba(6,182,212,0.05));border-radius:var(--radius-lg);">
          <div class="text-muted" style="font-size:0.88rem;">Total yang harus dibayar</div>
          <div style="font-size:2.5rem;font-weight:800;background:linear-gradient(135deg,var(--primary),var(--accent));-webkit-background-clip:text;-webkit-text-fill-color:transparent;">Rp 350.000</div>
          <div class="text-muted" style="font-size:0.85rem;">INV-2026-03-012 • Maret 2026 • A/12</div>
        </div>

        <div class="form-group">
          <label class="form-label">Metode Pembayaran</label>
          <div style="display:grid;grid-template-columns:repeat(2,1fr);gap:12px;">
            <label style="padding:16px;border:2px solid var(--primary);border-radius:var(--radius-md);text-align:center;cursor:pointer;background:rgba(99,102,241,0.05);">
              <input type="radio" name="method" value="va" checked style="display:none;">
              <div style="font-size:1.5rem;margin-bottom:6px;">🏦</div>
              <div style="font-weight:600;font-size:0.9rem;">Virtual Account</div>
              <div class="text-muted" style="font-size:0.75rem;">Transfer bank otomatis</div>
            </label>
            <label style="padding:16px;border:2px solid var(--border-color);border-radius:var(--radius-md);text-align:center;cursor:pointer;">
              <input type="radio" name="method" value="qris" style="display:none;">
              <div style="font-size:1.5rem;margin-bottom:6px;">📱</div>
              <div style="font-weight:600;font-size:0.9rem;">QRIS</div>
              <div class="text-muted" style="font-size:0.75rem;">Scan QR code</div>
            </label>
            <label style="padding:16px;border:2px solid var(--border-color);border-radius:var(--radius-md);text-align:center;cursor:pointer;">
              <input type="radio" name="method" value="cash" style="display:none;">
              <div style="font-size:1.5rem;margin-bottom:6px;">💵</div>
              <div style="font-weight:600;font-size:0.9rem;">Tunai</div>
              <div class="text-muted" style="font-size:0.75rem;">Bayar ke bendahara</div>
            </label>
            <label style="padding:16px;border:2px solid var(--border-color);border-radius:var(--radius-md);text-align:center;cursor:pointer;">
              <input type="radio" name="method" value="transfer" style="display:none;">
              <div style="font-size:1.5rem;margin-bottom:6px;">📄</div>
              <div style="font-weight:600;font-size:0.9rem;">Transfer Manual</div>
              <div class="text-muted" style="font-size:0.75rem;">Upload bukti transfer</div>
            </label>
          </div>
        </div>

        <!-- VA Info -->
        <div style="padding:16px;background:var(--bg-input);border-radius:var(--radius-md);margin-bottom:20px;">
          <div class="text-muted" style="font-size:0.82rem;margin-bottom:8px;">Nomor Virtual Account (BCA)</div>
          <div style="display:flex;align-items:center;justify-content:space-between;">
            <span style="font-size:1.3rem;font-weight:700;font-family:monospace;letter-spacing:2px;">8888 0100 1234 5012</span>
            <button class="btn btn-sm btn-outline" onclick="showToast('Nomor VA disalin!','success')">📋 Salin</button>
          </div>
          <div class="text-muted" style="font-size:0.78rem;margin-top:8px;">a.n. PT WargaKu — Perumahan Graha Indah</div>
        </div>

        <div class="form-group">
          <label class="form-label">Upload Bukti Pembayaran</label>
          <input type="file" class="form-control" accept="image/*,.pdf">
        </div>

        <div style="display:flex;justify-content:flex-end;gap:12px;margin-top:16px;padding-top:20px;border-top:1px solid var(--border-color);">
          <a href="iuran.php" class="btn btn-secondary">Batal</a>
          <button class="btn btn-primary" onclick="showToast('Pembayaran berhasil dikonfirmasi!','success')">✅ Konfirmasi Bayar</button>
        </div>
      </div>
    </main>
  </div>
</div>
<?php include 'includes/footer.php'; ?>
