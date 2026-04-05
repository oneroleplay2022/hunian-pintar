<?php $pageTitle = 'Tambah Penerima Bansos'; ?>
<?php include 'includes/header.php'; ?>
<div class="app-layout">
  <?php include 'includes/sidebar.php'; ?>
  <div class="main-wrapper">
    <?php include 'includes/topbar.php'; ?>
    <main class="main-content">
      <div class="page-header">
        <div><h1>Tambah Penerima Bantuan</h1>
          <div class="breadcrumb"><a href="index.php">Dashboard</a><span class="separator">/</span><a href="bansos.php">Bansos</a><span class="separator">/</span><span>Tambah Penerima</span></div>
        </div>
        <a href="bansos.php" class="btn btn-secondary btn-sm">← Kembali</a>
      </div>

      <div class="card" style="padding:24px;">
        <div class="form-group"><label class="form-label">Nama Penerima</label><input type="text" class="form-control" placeholder="Nama lengkap sesuai KTP"></div>
        <div class="form-row">
          <div class="form-group"><label class="form-label">NIK</label><input type="text" class="form-control" placeholder="16 digit NIK"></div>
          <div class="form-group"><label class="form-label">Rumah</label><input type="text" class="form-control" placeholder="Contoh: B/23"></div>
        </div>
        <div class="form-row">
          <div class="form-group"><label class="form-label">Usia</label><input type="number" class="form-control" placeholder="Tahun"></div>
          <div class="form-group"><label class="form-label">Pekerjaan</label><input type="text" class="form-control" placeholder="Pekerjaan saat ini"></div>
        </div>
        <div class="form-row">
          <div class="form-group"><label class="form-label">Jumlah Tanggungan</label><input type="number" class="form-control" value="1" min="0"></div>
          <div class="form-group">
            <label class="form-label">Penghasilan per Bulan</label>
            <select class="form-control"><option>< Rp 500.000</option><option>Rp 500.000 - 1.000.000</option><option>Rp 1.000.000 - 2.000.000</option><option>> Rp 2.000.000</option></select>
          </div>
        </div>
        <div class="form-group">
          <label class="form-label">Program Bantuan</label>
          <div style="display:flex;flex-direction:column;gap:8px;">
            <label class="checkbox-label" style="padding:12px;border:1px solid var(--border-color);border-radius:var(--radius-md);"><input type="checkbox"> 🏠 PKH (Program Keluarga Harapan)</label>
            <label class="checkbox-label" style="padding:12px;border:1px solid var(--border-color);border-radius:var(--radius-md);"><input type="checkbox"> 🍚 BPNT (Bantuan Pangan Non-Tunai)</label>
            <label class="checkbox-label" style="padding:12px;border:1px solid var(--border-color);border-radius:var(--radius-md);"><input type="checkbox"> 🤲 Zakat RT</label>
            <label class="checkbox-label" style="padding:12px;border:1px solid var(--border-color);border-radius:var(--radius-md);"><input type="checkbox"> 🎁 Hari Raya</label>
          </div>
        </div>
        <div class="form-group"><label class="form-label">Alasan / Keterangan</label><textarea class="form-control" rows="3" placeholder="Jelaskan kondisi penerima dan alasan pemberian bantuan..."></textarea></div>
        <div class="form-group"><label class="form-label">Dokumen Pendukung</label><input type="file" class="form-control" accept="image/*,.pdf" multiple><small class="text-muted">SKTM, foto kondisi rumah, dll.</small></div>
        <div style="display:flex;justify-content:flex-end;gap:12px;margin-top:24px;padding-top:20px;border-top:1px solid var(--border-color);">
          <a href="bansos.php" class="btn btn-secondary">Batal</a>
          <button class="btn btn-primary" onclick="showToast('Penerima bantuan berhasil didaftarkan!','success')">💾 Simpan</button>
        </div>
      </div>
    </main>
  </div>
</div>
<?php include 'includes/footer.php'; ?>
