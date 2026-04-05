<?php $pageTitle = 'Buat Pengaduan'; ?>
<?php include 'includes/header.php'; ?>
<div class="app-layout">
  <?php include 'includes/sidebar.php'; ?>
  <div class="main-wrapper">
    <?php include 'includes/topbar.php'; ?>
    <main class="main-content">
      <div class="page-header">
        <div><h1>Buat Pengaduan / Saran</h1>
          <div class="breadcrumb"><a href="index.php">Dashboard</a><span class="separator">/</span><a href="pengaduan.php">Pengaduan</a><span class="separator">/</span><span>Buat</span></div>
        </div>
        <a href="pengaduan.php" class="btn btn-secondary btn-sm">← Kembali</a>
      </div>

      <div class="card" style="padding:24px;">
        <div class="form-group">
          <label class="form-label">Jenis Laporan</label>
          <div style="display:grid;grid-template-columns:repeat(3,1fr);gap:12px;">
            <?php
            $types = [['🚨','Keluhan','Laporkan masalah'],['💡','Saran','Berikan saran'],['📢','Aspirasi','Sampaikan aspirasi']];
            foreach ($types as $t): ?>
            <label style="padding:14px;border:2px solid var(--border-color);border-radius:var(--radius-md);text-align:center;cursor:pointer;">
              <input type="radio" name="type" style="display:none;">
              <div style="font-size:1.5rem;margin-bottom:4px;"><?= $t[0] ?></div>
              <div style="font-weight:600;font-size:0.9rem;"><?= $t[1] ?></div>
              <div class="text-muted" style="font-size:0.75rem;"><?= $t[2] ?></div>
            </label>
            <?php endforeach; ?>
          </div>
        </div>
        <div class="form-group"><label class="form-label">Judul</label><input type="text" class="form-control" placeholder="Ringkasan masalah / saran"></div>
        <div class="form-group">
          <label class="form-label">Kategori</label>
          <select class="form-control"><option>Infrastruktur</option><option>Keamanan</option><option>Kebersihan</option><option>Kebisingan</option><option>Sosial</option><option>Lainnya</option></select>
        </div>
        <div class="form-group"><label class="form-label">Lokasi Kejadian</label><input type="text" class="form-control" placeholder="Contoh: Jalan Blok C depan rumah 18"></div>
        <div class="form-group"><label class="form-label">Deskripsi Lengkap</label><textarea class="form-control" rows="5" placeholder="Jelaskan masalah secara detail..."></textarea></div>
        <div class="form-group"><label class="form-label">Prioritas</label>
          <select class="form-control"><option>Rendah</option><option selected>Sedang</option><option>Tinggi</option><option>Darurat</option></select>
        </div>
        <div class="form-group"><label class="form-label">Foto Bukti (opsional)</label><input type="file" class="form-control" accept="image/*" multiple></div>
        <div class="form-group">
          <label class="checkbox-label"><input type="checkbox"> Kirim sebagai anonim (nama tidak ditampilkan)</label>
        </div>
        <div style="display:flex;justify-content:flex-end;gap:12px;margin-top:24px;padding-top:20px;border-top:1px solid var(--border-color);">
          <a href="pengaduan.php" class="btn btn-secondary">Batal</a>
          <button class="btn btn-primary" onclick="showToast('Pengaduan berhasil dikirim!','success')">📤 Kirim Pengaduan</button>
        </div>
      </div>
    </main>
  </div>
</div>
<?php include 'includes/footer.php'; ?>
