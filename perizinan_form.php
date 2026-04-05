<?php $pageTitle = 'Ajukan Perizinan'; ?>
<?php include 'includes/header.php'; ?>
<div class="app-layout">
  <?php include 'includes/sidebar.php'; ?>
  <div class="main-wrapper">
    <?php include 'includes/topbar.php'; ?>
    <main class="main-content">
      <div class="page-header">
        <div><h1>Ajukan Izin Baru</h1>
          <div class="breadcrumb"><a href="index.php">Dashboard</a><span class="separator">/</span><a href="perizinan.php">Perizinan</a><span class="separator">/</span><span>Ajukan</span></div>
        </div>
        <a href="perizinan.php" class="btn btn-secondary btn-sm">← Kembali</a>
      </div>

      <div class="card" style="padding:24px;">
        <div class="form-group">
          <label class="form-label">Jenis Izin</label>
          <select class="form-control"><option>Renovasi Bangunan</option><option>Acara / Keramaian</option><option>Pemasangan Instalasi</option><option>Penggunaan Fasilitas Umum</option></select>
        </div>
        <div class="form-group"><label class="form-label">Judul / Deskripsi Singkat</label><input type="text" class="form-control" placeholder="Contoh: Renovasi fasad dan pagar depan"></div>
        <div class="form-row">
          <div class="form-group"><label class="form-label">Nama Pemohon</label><input type="text" class="form-control" placeholder="Nama lengkap"></div>
          <div class="form-group"><label class="form-label">Rumah</label><input type="text" class="form-control" placeholder="Contoh: A/12"></div>
        </div>
        <div class="form-row">
          <div class="form-group"><label class="form-label">Rencana Mulai</label><input type="date" class="form-control"></div>
          <div class="form-group"><label class="form-label">Estimasi Selesai</label><input type="date" class="form-control"></div>
        </div>
        <div class="form-group"><label class="form-label">Detail Pekerjaan / Kegiatan</label><textarea class="form-control" rows="5" placeholder="Jelaskan secara detail rencana renovasi / kegiatan..."></textarea></div>
        <div class="form-group"><label class="form-label">Nama Kontraktor / Penanggung Jawab</label><input type="text" class="form-control" placeholder="Nama kontraktor atau PIC"></div>
        <div class="form-group"><label class="form-label">Lampiran Dokumen</label><input type="file" class="form-control" accept="image/*,.pdf" multiple><small class="text-muted">Denah, foto, atau dokumen pendukung lainnya</small></div>
        <div style="display:flex;justify-content:flex-end;gap:12px;margin-top:24px;padding-top:20px;border-top:1px solid var(--border-color);">
          <a href="perizinan.php" class="btn btn-secondary">Batal</a>
          <button class="btn btn-primary" onclick="showToast('Pengajuan izin berhasil dikirim!','success')">📤 Ajukan Izin</button>
        </div>
      </div>
    </main>
  </div>
</div>
<?php include 'includes/footer.php'; ?>
