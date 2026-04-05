<?php $pageTitle = 'Upload Foto'; ?>
<?php include 'includes/header.php'; ?>
<div class="app-layout">
  <?php include 'includes/sidebar.php'; ?>
  <div class="main-wrapper">
    <?php include 'includes/topbar.php'; ?>
    <main class="main-content">
      <div class="page-header">
        <div>
          <h1>Upload Foto</h1>
          <div class="breadcrumb">
            <a href="index.php">Dashboard</a><span class="separator">/</span>
            <a href="galeri.php">Galeri</a><span class="separator">/</span>
            <span>Upload</span>
          </div>
        </div>
        <a href="galeri.php" class="btn btn-secondary btn-sm">← Kembali</a>
      </div>

      <div class="card" style="padding:24px;">
        <div class="form-row">
          <div class="form-group">
            <label class="form-label">Album</label>
            <select class="form-control">
              <option>Kerja Bakti Maret 2026</option>
              <option>Kegiatan Warga</option>
              <option>Perayaan Hari Besar</option>
              <option>Buat Album Baru...</option>
            </select>
          </div>
          <div class="form-group">
            <label class="form-label">Tanggal Kegiatan</label>
            <input type="date" class="form-control">
          </div>
        </div>

        <!-- Upload Zone -->
        <div class="form-group">
          <label class="form-label">Upload Foto (Multiple)</label>
          <div id="dropZone" style="border:2px dashed var(--border-color);border-radius:var(--radius-lg);padding:60px 24px;text-align:center;cursor:pointer;transition:all 0.3s ease;">
            <i data-lucide="upload-cloud" style="width:56px;height:56px;color:var(--text-muted);margin-bottom:16px;"></i>
            <div style="font-size:1.1rem;font-weight:600;margin-bottom:8px;">Drag & drop foto di sini</div>
            <div class="text-muted" style="font-size:0.88rem;margin-bottom:16px;">atau klik untuk memilih file</div>
            <div class="text-muted" style="font-size:0.78rem;">Format: JPG, PNG, WEBP • Maks 10MB per file • Maks 20 file sekaligus</div>
            <input type="file" accept="image/*" multiple style="display:none;" id="fileInput">
          </div>
        </div>

        <!-- Preview (simulated) -->
        <div class="form-group">
          <label class="form-label">Preview Foto</label>
          <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(160px,1fr));gap:12px;">
            <?php for ($i = 1; $i <= 4; $i++): ?>
            <div style="position:relative;aspect-ratio:1;background:linear-gradient(135deg,rgba(99,102,241,0.12),rgba(6,182,212,0.08));border-radius:var(--radius-md);display:flex;align-items:center;justify-content:center;">
              <span style="font-size:2.5rem;">📸</span>
              <button style="position:absolute;top:6px;right:6px;width:24px;height:24px;border-radius:50%;background:rgba(239,68,68,0.9);color:white;border:none;cursor:pointer;font-size:0.7rem;display:flex;align-items:center;justify-content:center;">✕</button>
              <div style="position:absolute;bottom:0;left:0;right:0;padding:6px;background:rgba(0,0,0,0.6);border-radius:0 0 var(--radius-md) var(--radius-md);color:white;font-size:0.7rem;text-align:center;">
                foto_<?= $i ?>.jpg (2.4 MB)
              </div>
            </div>
            <?php endfor; ?>
          </div>
        </div>

        <div class="form-group">
          <label class="form-label">Keterangan Umum</label>
          <textarea class="form-control" rows="3" placeholder="Keterangan umum untuk semua foto yang diupload..."></textarea>
        </div>

        <!-- Progress -->
        <div style="margin-bottom:20px;">
          <div style="display:flex;justify-content:space-between;margin-bottom:8px;font-size:0.85rem;">
            <span>Upload progress</span>
            <span>0 / 4 foto</span>
          </div>
          <div class="progress-bar" style="height:10px;">
            <div class="progress-fill" style="width:0%;"></div>
          </div>
        </div>

        <div style="display:flex;justify-content:flex-end;gap:12px;">
          <a href="galeri.php" class="btn btn-secondary">Batal</a>
          <button class="btn btn-primary" onclick="showToast('4 Foto berhasil diupload!','success')">📤 Upload Semua</button>
        </div>
      </div>
    </main>
  </div>
</div>
<?php include 'includes/footer.php'; ?>
