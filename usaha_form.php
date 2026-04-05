<?php $pageTitle = 'Daftarkan Usaha'; ?>
<?php include 'includes/header.php'; ?>
<div class="app-layout">
  <?php include 'includes/sidebar.php'; ?>
  <div class="main-wrapper">
    <?php include 'includes/topbar.php'; ?>
    <main class="main-content">
      <div class="page-header">
        <div><h1>Daftarkan Usaha</h1>
          <div class="breadcrumb"><a href="index.php">Dashboard</a><span class="separator">/</span><a href="usaha.php">Usaha</a><span class="separator">/</span><span>Daftar</span></div>
        </div>
        <a href="usaha.php" class="btn btn-secondary btn-sm">← Kembali</a>
      </div>

      <div class="card" style="padding:24px;">
        <div class="form-group"><label class="form-label">Nama Usaha / Lapak</label><input type="text" class="form-control" placeholder="Contoh: Warung Makan Bu Siti"></div>
        <div class="form-row">
          <div class="form-group">
            <label class="form-label">Kategori Usaha</label>
            <select class="form-control"><option>Kuliner</option><option>Jasa</option><option>Retail</option><option>Online Shop</option><option>Lainnya</option></select>
          </div>
          <div class="form-group"><label class="form-label">Rumah / Lokasi</label><input type="text" class="form-control" placeholder="Contoh: B/05"></div>
        </div>
        <div class="form-group"><label class="form-label">Deskripsi Produk / Jasa</label><textarea class="form-control" rows="4" placeholder="Jelaskan detail produk/jasa yang ditawarkan..."></textarea></div>
        <div class="form-row">
          <div class="form-group"><label class="form-label">Jam Buka</label><input type="time" class="form-control" value="07:00"></div>
          <div class="form-group"><label class="form-label">Jam Tutup</label><input type="time" class="form-control" value="21:00"></div>
        </div>
        <div class="form-group">
          <label class="form-label">Hari Buka</label>
          <div style="display:flex;flex-wrap:wrap;gap:8px;">
            <?php foreach (['Senin','Selasa','Rabu','Kamis','Jumat','Sabtu','Minggu'] as $day): ?>
            <label class="checkbox-label" style="padding:8px 14px;border:1px solid var(--border-color);border-radius:var(--radius-md);">
              <input type="checkbox" checked> <?= $day ?>
            </label>
            <?php endforeach; ?>
          </div>
        </div>
        <div class="form-row">
          <div class="form-group"><label class="form-label">No. HP / WhatsApp</label><input type="tel" class="form-control" placeholder="08xxxxxxxxxx"></div>
          <div class="form-group"><label class="form-label">Link (Grab/Gojek/Shopee)</label><input type="url" class="form-control" placeholder="https://..."></div>
        </div>
        <div class="form-group"><label class="form-label">Foto Usaha / Produk</label><input type="file" class="form-control" accept="image/*" multiple></div>
        <div style="display:flex;justify-content:flex-end;gap:12px;margin-top:24px;padding-top:20px;border-top:1px solid var(--border-color);">
          <a href="usaha.php" class="btn btn-secondary">Batal</a>
          <button class="btn btn-primary" onclick="showToast('Usaha berhasil didaftarkan!','success')">💾 Daftarkan</button>
        </div>
      </div>
    </main>
  </div>
</div>
<?php include 'includes/footer.php'; ?>
