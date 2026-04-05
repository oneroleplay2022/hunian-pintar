<?php $pageTitle = 'Detail Foto'; ?>
<?php include 'includes/header.php'; ?>
<div class="app-layout">
  <?php include 'includes/sidebar.php'; ?>
  <div class="main-wrapper">
    <?php include 'includes/topbar.php'; ?>
    <main class="main-content">
      <div class="page-header">
        <div>
          <h1>Detail Foto</h1>
          <div class="breadcrumb">
            <a href="index.php">Dashboard</a><span class="separator">/</span>
            <a href="galeri.php">Galeri</a><span class="separator">/</span>
            <span>Detail</span>
          </div>
        </div>
        <div style="display:flex;gap:10px;">
          <a href="galeri.php" class="btn btn-secondary btn-sm">← Kembali</a>
          <button class="btn btn-danger btn-sm" onclick="confirmAction('Hapus foto ini?')"><i data-lucide="trash-2" style="width:14px;height:14px;"></i> Hapus</button>
        </div>
      </div>

      <div class="grid-2" style="gap:24px;">
        <!-- Photo -->
        <div class="card" style="overflow:hidden;">
          <div style="aspect-ratio:4/3;background:linear-gradient(135deg,rgba(99,102,241,0.15),rgba(6,182,212,0.1));display:flex;align-items:center;justify-content:center;">
            <span style="font-size:6rem;">📸</span>
          </div>
          <div style="padding:16px;display:flex;justify-content:center;gap:12px;">
            <button class="btn btn-outline btn-sm">⬅️ Sebelumnya</button>
            <button class="btn btn-outline btn-sm">Selanjutnya ➡️</button>
          </div>
        </div>

        <!-- Info -->
        <div>
          <div class="card" style="padding:20px;margin-bottom:16px;">
            <h3 style="margin-bottom:16px;">Kerja Bakti Taman Blok A</h3>
            <p style="color:var(--text-secondary);font-size:0.9rem;line-height:1.6;margin-bottom:16px;">
              Dokumentasi kegiatan kerja bakti bersama warga Blok A. Kegiatan meliputi pembersihan taman, penanaman bunga baru, dan pengecatan pagar.
            </p>
            <div style="display:flex;flex-direction:column;gap:10px;font-size:0.88rem;">
              <div style="display:flex;justify-content:space-between;"><span class="text-muted">Album</span><strong>Kerja Bakti Maret 2026</strong></div>
              <div style="display:flex;justify-content:space-between;"><span class="text-muted">Tanggal</span><strong>5 Mar 2026</strong></div>
              <div style="display:flex;justify-content:space-between;"><span class="text-muted">Diunggah oleh</span><strong>Admin Satu</strong></div>
              <div style="display:flex;justify-content:space-between;"><span class="text-muted">Resolusi</span><strong>3024 × 4032</strong></div>
              <div style="display:flex;justify-content:space-between;"><span class="text-muted">Ukuran File</span><strong>2.4 MB</strong></div>
            </div>
          </div>

          <div class="card" style="padding:20px;">
            <h4 style="margin-bottom:12px;">✏️ Edit Info Foto</h4>
            <div class="form-group">
              <label class="form-label">Judul</label>
              <input type="text" class="form-control" value="Kerja Bakti Taman Blok A">
            </div>
            <div class="form-group">
              <label class="form-label">Album</label>
              <select class="form-control">
                <option selected>Kerja Bakti Maret 2026</option>
                <option>Kegiatan Warga</option>
                <option>Perayaan Hari Besar</option>
              </select>
            </div>
            <div class="form-group">
              <label class="form-label">Keterangan</label>
              <textarea class="form-control" rows="3">Dokumentasi kegiatan kerja bakti bersama warga Blok A.</textarea>
            </div>
            <button class="btn btn-primary btn-sm w-full" onclick="showToast('Info foto diperbarui!','success')">💾 Simpan</button>
          </div>
        </div>
      </div>
    </main>
  </div>
</div>
<?php include 'includes/footer.php'; ?>
