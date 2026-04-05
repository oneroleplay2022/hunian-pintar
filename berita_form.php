<?php $pageTitle = 'Tambah/Edit Berita'; ?>
<?php include 'includes/header.php'; ?>
<div class="app-layout">
  <?php include 'includes/sidebar.php'; ?>
  <div class="main-wrapper">
    <?php include 'includes/topbar.php'; ?>
    <main class="main-content">
      <div class="page-header">
        <div>
          <h1>Tambah Berita</h1>
          <div class="breadcrumb">
            <a href="index.php">Dashboard</a><span class="separator">/</span>
            <a href="berita.php">Berita</a><span class="separator">/</span>
            <span>Tambah</span>
          </div>
        </div>
        <a href="berita.php" class="btn btn-secondary btn-sm">← Kembali</a>
      </div>

      <div class="grid-2" style="gap:24px;">
        <div>
          <div class="card" style="padding:24px;">
            <div class="form-group">
              <label class="form-label">Judul Berita</label>
              <input type="text" class="form-control" placeholder="Tulis judul berita yang menarik..." style="font-size:1.1rem;font-weight:600;padding:14px 16px;">
            </div>
            <div class="form-group">
              <label class="form-label">Isi Berita</label>
              <!-- Toolbar -->
              <div style="display:flex;gap:4px;padding:8px;background:var(--bg-input);border:1px solid var(--border-color);border-bottom:none;border-radius:var(--radius-md) var(--radius-md) 0 0;">
                <button class="btn btn-icon btn-sm btn-secondary" title="Bold"><strong>B</strong></button>
                <button class="btn btn-icon btn-sm btn-secondary" title="Italic"><em>I</em></button>
                <button class="btn btn-icon btn-sm btn-secondary" title="Underline"><u>U</u></button>
                <span style="width:1px;background:var(--border-color);margin:0 4px;"></span>
                <button class="btn btn-icon btn-sm btn-secondary" title="Heading">H</button>
                <button class="btn btn-icon btn-sm btn-secondary" title="List">☰</button>
                <button class="btn btn-icon btn-sm btn-secondary" title="Link">🔗</button>
                <button class="btn btn-icon btn-sm btn-secondary" title="Image">🖼️</button>
              </div>
              <textarea class="form-control" rows="15" style="border-radius:0 0 var(--radius-md) var(--radius-md);min-height:300px;" placeholder="Tulis isi berita di sini..."></textarea>
            </div>
            <div class="form-group">
              <label class="form-label">Gambar Utama</label>
              <div style="border:2px dashed var(--border-color);border-radius:var(--radius-md);padding:40px;text-align:center;cursor:pointer;">
                <i data-lucide="upload-cloud" style="width:40px;height:40px;color:var(--text-muted);margin-bottom:12px;"></i>
                <div style="color:var(--text-muted);font-size:0.88rem;">Klik atau drag & drop gambar di sini</div>
                <div class="text-muted" style="font-size:0.78rem;margin-top:4px;">JPG, PNG maks 5MB</div>
                <input type="file" accept="image/*" style="display:none;">
              </div>
            </div>
          </div>
        </div>

        <!-- Settings Sidebar -->
        <div>
          <div class="card" style="padding:20px;margin-bottom:16px;">
            <h4 style="margin-bottom:16px;">⚙️ Pengaturan</h4>
            <div class="form-group">
              <label class="form-label">Kategori</label>
              <select class="form-control">
                <option>Pengumuman</option>
                <option>Kegiatan</option>
                <option>Info Penting</option>
                <option>Sosial</option>
              </select>
            </div>
            <div class="form-group">
              <label class="form-label">Status Publikasi</label>
              <select class="form-control">
                <option>Draft</option>
                <option selected>Published</option>
                <option>Scheduled</option>
              </select>
            </div>
            <div class="form-group">
              <label class="form-label">Jadwal Publikasi</label>
              <input type="datetime-local" class="form-control">
            </div>
            <div class="form-group">
              <label class="checkbox-label">
                <input type="checkbox" checked> Aktifkan komentar
              </label>
            </div>
            <div class="form-group">
              <label class="checkbox-label">
                <input type="checkbox" checked> Kirim notifikasi ke warga
              </label>
            </div>
            <div class="form-group">
              <label class="checkbox-label">
                <input type="checkbox"> Sematkan di atas (pin)
              </label>
            </div>
          </div>

          <div style="display:flex;flex-direction:column;gap:10px;">
            <button class="btn btn-primary w-full" onclick="showToast('Berita berhasil dipublikasikan!','success')">🚀 Publikasikan</button>
            <button class="btn btn-secondary w-full" onclick="showToast('Draft berhasil disimpan!','info')">💾 Simpan Draft</button>
            <button class="btn btn-outline w-full">👁️ Preview</button>
          </div>
        </div>
      </div>
    </main>
  </div>
</div>
<?php include 'includes/footer.php'; ?>
