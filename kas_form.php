<?php $pageTitle = 'Input Transaksi Kas'; ?>
<?php include 'includes/header.php'; ?>
<div class="app-layout">
  <?php include 'includes/sidebar.php'; ?>
  <div class="main-wrapper">
    <?php include 'includes/topbar.php'; ?>
    <main class="main-content">
      <div class="page-header">
        <div><h1>Input Transaksi Kas</h1>
          <div class="breadcrumb"><a href="index.php">Dashboard</a><span class="separator">/</span><a href="kas.php">Kas</a><span class="separator">/</span><span>Input</span></div>
        </div>
        <a href="kas.php" class="btn btn-secondary btn-sm">← Kembali</a>
      </div>

      <div class="card" style="padding:24px;">
        <div class="form-group">
          <label class="form-label">Jenis Transaksi</label>
          <div style="display:grid;grid-template-columns:repeat(2,1fr);gap:12px;">
            <label style="padding:16px;border:2px solid var(--border-color);border-radius:var(--radius-md);text-align:center;cursor:pointer;">
              <input type="radio" name="type" value="in" checked style="display:none;">
              <div style="font-size:2rem;margin-bottom:6px;">📥</div>
              <div style="font-weight:600;color:var(--success);">Pemasukan</div>
            </label>
            <label style="padding:16px;border:2px solid var(--border-color);border-radius:var(--radius-md);text-align:center;cursor:pointer;">
              <input type="radio" name="type" value="out" style="display:none;">
              <div style="font-size:2rem;margin-bottom:6px;">📤</div>
              <div style="font-weight:600;color:var(--danger);">Pengeluaran</div>
            </label>
          </div>
        </div>
        <div class="form-group"><label class="form-label">Keterangan</label><input type="text" class="form-control" placeholder="Contoh: Pembelian alat kebersihan"></div>
        <div class="form-row">
          <div class="form-group"><label class="form-label">Jumlah (Rp)</label><input type="number" class="form-control" placeholder="0"></div>
          <div class="form-group"><label class="form-label">Tanggal</label><input type="date" class="form-control"></div>
        </div>
        <div class="form-group">
          <label class="form-label">Kategori</label>
          <select class="form-control">
            <option>Iuran Warga</option><option>Sumbangan</option><option>Operasional</option>
            <option>Pemeliharaan</option><option>Keamanan</option><option>Kebersihan</option>
            <option>Kegiatan</option><option>Lainnya</option>
          </select>
        </div>
        <div class="form-group"><label class="form-label">Upload Bukti / Kuitansi</label><input type="file" class="form-control" accept="image/*,.pdf"></div>
        <div class="form-group"><label class="form-label">Catatan Tambahan</label><textarea class="form-control" rows="3" placeholder="Catatan internal..."></textarea></div>
        <div style="display:flex;justify-content:flex-end;gap:12px;margin-top:24px;padding-top:20px;border-top:1px solid var(--border-color);">
          <a href="kas.php" class="btn btn-secondary">Batal</a>
          <button class="btn btn-primary" onclick="showToast('Transaksi kas berhasil dicatat!','success')">💾 Simpan Transaksi</button>
        </div>
      </div>
    </main>
  </div>
</div>
<?php include 'includes/footer.php'; ?>
