<?php $pageTitle = 'Galeri Kegiatan'; ?>
<?php include 'includes/header.php'; ?>

<div class="app-layout">
  <?php include 'includes/sidebar.php'; ?>

  <div class="main-wrapper">
    <?php include 'includes/topbar.php'; ?>

    <main class="main-content">
      <div class="page-header">
        <div>
          <h1>Galeri Kegiatan</h1>
          <div class="breadcrumb">
            <a href="index.php">Dashboard</a>
            <span class="separator">/</span>
            <span>Galeri</span>
          </div>
        </div>
        <a href="galeri_upload.php" class="btn btn-primary"><i data-lucide="upload" style="width:16px;height:16px;"></i> Upload Foto</a>
      </div>

      <!-- Filter -->
      <div class="filter-bar">
        <div class="filter-search">
          <span class="search-icon"><i data-lucide="search"></i></span>
          <input type="text" class="form-control" placeholder="Cari kegiatan...">
        </div>
        <select class="form-control" style="width:auto;">
          <option>Semua Kategori</option>
          <option>Kerja Bakti</option>
          <option>Perayaan</option>
          <option>Rapat</option>
          <option>Olahraga</option>
        </select>
        <select class="form-control" style="width:auto;">
          <option>Terbaru</option>
          <option>Terlama</option>
        </select>
      </div>

      <!-- Gallery Grid -->
      <div class="gallery-grid">
        <?php
        $galeriItems = [
          ['title' => 'Kerja Bakti Taman Blok A', 'date' => '15 Mar 2026', 'emoji' => '🌳', 'color' => 'rgba(16,185,129,0.2)'],
          ['title' => 'Rapat Bulanan Pengurus RT', 'date' => '10 Mar 2026', 'emoji' => '🤝', 'color' => 'rgba(99,102,241,0.2)'],
          ['title' => 'Senam Pagi Minggu ke-2', 'date' => '8 Mar 2026', 'emoji' => '🏃', 'color' => 'rgba(245,158,11,0.2)'],
          ['title' => 'Bazar UMKM Warga', 'date' => '1 Mar 2026', 'emoji' => '🏪', 'color' => 'rgba(139,92,246,0.2)'],
          ['title' => 'Posyandu Balita & Lansia', 'date' => '28 Feb 2026', 'emoji' => '🏥', 'color' => 'rgba(6,182,212,0.2)'],
          ['title' => 'Perayaan Maulid Nabi', 'date' => '20 Feb 2026', 'emoji' => '🕌', 'color' => 'rgba(16,185,129,0.2)'],
          ['title' => 'Turnamen Badminton Antar Blok', 'date' => '15 Feb 2026', 'emoji' => '🏸', 'color' => 'rgba(239,68,68,0.2)'],
          ['title' => 'Pelatihan Tanggap Bencana', 'date' => '10 Feb 2026', 'emoji' => '🚒', 'color' => 'rgba(245,158,11,0.2)'],
          ['title' => 'Donor Darah PMI', 'date' => '5 Feb 2026', 'emoji' => '🩸', 'color' => 'rgba(239,68,68,0.2)'],
          ['title' => 'Halal Bihalal 2026', 'date' => '28 Jan 2026', 'emoji' => '🤲', 'color' => 'rgba(99,102,241,0.2)'],
          ['title' => 'Workshop Hidroponik', 'date' => '20 Jan 2026', 'emoji' => '🥬', 'color' => 'rgba(16,185,129,0.2)'],
          ['title' => 'Perayaan Tahun Baru', 'date' => '1 Jan 2026', 'emoji' => '🎆', 'color' => 'rgba(139,92,246,0.2)'],
        ];
        foreach ($galeriItems as $item): ?>
        <a href="galeri_detail.php" class="gallery-item" style="text-decoration:none;color:inherit;">
          <div style="width:100%;height:100%;background:<?= $item['color'] ?>;display:flex;align-items:center;justify-content:center;font-size:4rem;">
            <?= $item['emoji'] ?>
          </div>
          <div class="gallery-overlay">
            <div>
              <div class="gallery-title"><?= $item['title'] ?></div>
              <div style="color:rgba(255,255,255,0.7);font-size:0.78rem;"><?= $item['date'] ?></div>
            </div>
          </div>
        </a>
        <?php endforeach; ?>
      </div>

      <div class="pagination">
        <button class="page-btn">←</button>
        <button class="page-btn active">1</button>
        <button class="page-btn">2</button>
        <button class="page-btn">→</button>
      </div>
    </main>
  </div>
</div>

<!-- Upload Modal -->
<div class="modal-overlay" id="uploadModal">
  <div class="modal">
    <div class="modal-header">
      <h3 class="modal-title">Upload Foto Kegiatan</h3>
      <button class="modal-close">✕</button>
    </div>
    <div class="modal-body">
      <div class="form-group">
        <label class="form-label">Nama Kegiatan</label>
        <input type="text" class="form-control" placeholder="Contoh: Kerja Bakti Blok A">
      </div>
      <div class="form-row">
        <div class="form-group">
          <label class="form-label">Tanggal Kegiatan</label>
          <input type="date" class="form-control">
        </div>
        <div class="form-group">
          <label class="form-label">Kategori</label>
          <select class="form-control">
            <option>Kerja Bakti</option>
            <option>Perayaan</option>
            <option>Rapat</option>
            <option>Olahraga</option>
            <option>Lainnya</option>
          </select>
        </div>
      </div>
      <div class="form-group">
        <label class="form-label">Upload Foto (max 10)</label>
        <input type="file" class="form-control" accept="image/*" multiple>
      </div>
      <div class="form-group">
        <label class="form-label">Deskripsi</label>
        <textarea class="form-control" rows="3" placeholder="Deskripsi kegiatan..."></textarea>
      </div>
    </div>
    <div class="modal-footer">
      <button class="btn btn-secondary modal-cancel">Batal</button>
      <button class="btn btn-primary">Upload</button>
    </div>
  </div>
</div>

<?php include 'includes/footer.php'; ?>
