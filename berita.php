<?php $pageTitle = 'Berita & Pengumuman'; ?>
<?php include 'includes/header.php'; ?>

<div class="app-layout">
  <?php include 'includes/sidebar.php'; ?>

  <div class="main-wrapper">
    <?php include 'includes/topbar.php'; ?>

    <main class="main-content">
      <div class="page-header">
        <div>
          <h1>Berita & Pengumuman</h1>
          <div class="breadcrumb">
            <a href="index.php">Dashboard</a>
            <span class="separator">/</span>
            <span>Berita</span>
          </div>
        </div>
        <a href="berita_form.php" class="btn btn-primary"><i data-lucide="plus" style="width:16px;height:16px;"></i> Tambah Berita</a>
      </div>

      <!-- Tabs -->
      <div class="tabs">
        <button class="tab-btn active" data-tab="allNews">Semua</button>
        <button class="tab-btn" data-tab="announcements">Pengumuman</button>
        <button class="tab-btn" data-tab="events">Kegiatan</button>
        <button class="tab-btn" data-tab="emergency">Darurat</button>
      </div>

      <!-- Filter -->
      <div class="filter-bar">
        <div class="filter-search">
          <span class="search-icon"><i data-lucide="search"></i></span>
          <input type="text" class="form-control" placeholder="Cari berita...">
        </div>
        <select class="form-control" style="width:auto;">
          <option>Semua Bulan</option>
          <option>Maret 2026</option>
          <option>Februari 2026</option>
          <option>Januari 2026</option>
        </select>
      </div>

      <!-- News Grid -->
      <div class="news-grid" id="allNews">
        <a href="berita_detail.php" class="news-card" style="text-decoration:none;color:inherit;">
          <div class="news-image" style="background: linear-gradient(135deg, rgba(99,102,241,0.3), rgba(6,182,212,0.2)); display:flex;align-items:center;justify-content:center;font-size:3rem;">📢</div>
          <div class="news-body">
            <div class="news-meta">
              <span class="badge badge-info">Pengumuman</span>
              <span>22 Mar 2026</span>
            </div>
            <h3 class="news-title">Jadwal Kerja Bakti Bulan April 2026</h3>
            <p class="news-excerpt">Kerja bakti akan dilaksanakan di area taman blok A-C pada hari Minggu, 5 April 2026 pukul 07.00 WIB. Diharapkan seluruh warga dapat berpartisipasi.</p>
          </div>
        </a>

        <a href="berita_detail.php" class="news-card" style="text-decoration:none;color:inherit;">
          <div class="news-image" style="background: linear-gradient(135deg, rgba(245,158,11,0.3), rgba(239,68,68,0.2)); display:flex;align-items:center;justify-content:center;font-size:3rem;">⚠️</div>
          <div class="news-body">
            <div class="news-meta">
              <span class="badge badge-warning">Penting</span>
              <span>20 Mar 2026</span>
            </div>
            <h3 class="news-title">Perbaikan Jalan di Blok D - Info Jalur Alternatif</h3>
            <p class="news-excerpt">Pengerjaan perbaikan jalan utama Blok D dimulai 25 Maret 2026. Warga Blok D dapat menggunakan jalur alternatif melalui Blok E.</p>
          </div>
        </a>

        <a href="berita_detail.php" class="news-card" style="text-decoration:none;color:inherit;">
          <div class="news-image" style="background: linear-gradient(135deg, rgba(16,185,129,0.3), rgba(6,182,212,0.2)); display:flex;align-items:center;justify-content:center;font-size:3rem;">🏆</div>
          <div class="news-body">
            <div class="news-meta">
              <span class="badge badge-success">Kegiatan</span>
              <span>18 Mar 2026</span>
            </div>
            <h3 class="news-title">Lomba HUT RI ke-81 - Pendaftaran Dibuka!</h3>
            <p class="news-excerpt">Daftarkan keluarga Anda untuk mengikuti berbagai lomba menarik dalam rangka HUT RI ke-81. Pendaftaran gratis melalui aplikasi.</p>
          </div>
        </a>

        <a href="berita_detail.php" class="news-card" style="text-decoration:none;color:inherit;">
          <div class="news-image" style="background: linear-gradient(135deg, rgba(139,92,246,0.3), rgba(99,102,241,0.2)); display:flex;align-items:center;justify-content:center;font-size:3rem;">💧</div>
          <div class="news-body">
            <div class="news-meta">
              <span class="badge badge-info">Pengumuman</span>
              <span>15 Mar 2026</span>
            </div>
            <h3 class="news-title">Jadwal Penyemprotan Anti Nyamuk DBD</h3>
            <p class="news-excerpt">Penyemprotan fogging akan dilakukan di seluruh area perumahan pada tanggal 20 Maret 2026 mulai pukul 06.00 WIB.</p>
          </div>
        </a>

        <a href="berita_detail.php" class="news-card" style="text-decoration:none;color:inherit;">
          <div class="news-image" style="background: linear-gradient(135deg, rgba(239,68,68,0.3), rgba(245,158,11,0.2)); display:flex;align-items:center;justify-content:center;font-size:3rem;">🔒</div>
          <div class="news-body">
            <div class="news-meta">
              <span class="badge badge-danger">Darurat</span>
              <span>12 Mar 2026</span>
            </div>
            <h3 class="news-title">Waspada Pencurian di Sekitar Perumahan</h3>
            <p class="news-excerpt">Meningkatkan kewaspadaan terhadap pencurian motor. Pastikan kendaraan terkunci dan CCTV aktif.</p>
          </div>
        </a>

        <a href="berita_detail.php" class="news-card" style="text-decoration:none;color:inherit;">
          <div class="news-image" style="background: linear-gradient(135deg, rgba(16,185,129,0.3), rgba(99,102,241,0.2)); display:flex;align-items:center;justify-content:center;font-size:3rem;">💰</div>
          <div class="news-body">
            <div class="news-meta">
              <span class="badge badge-success">Keuangan</span>
              <span>10 Mar 2026</span>
            </div>
            <h3 class="news-title">Laporan Keuangan Februari 2026 Telah Tersedia</h3>
            <p class="news-excerpt">Laporan keuangan bulan Februari telah di-upload. Warga dapat mengunduh dan memeriksa detail pengeluaran.</p>
          </div>
        </a>
      </div>

      <!-- Pagination -->
      <div class="pagination">
        <button class="page-btn">←</button>
        <button class="page-btn active">1</button>
        <button class="page-btn">2</button>
        <button class="page-btn">3</button>
        <button class="page-btn">→</button>
      </div>
    </main>
  </div>
</div>

<!-- Add News Modal -->
<div class="modal-overlay" id="addNewsModal">
  <div class="modal">
    <div class="modal-header">
      <h3 class="modal-title">Tambah Berita</h3>
      <button class="modal-close">✕</button>
    </div>
    <div class="modal-body">
      <div class="form-group">
        <label class="form-label">Judul Berita</label>
        <input type="text" class="form-control" placeholder="Masukkan judul berita...">
      </div>
      <div class="form-row">
        <div class="form-group">
          <label class="form-label">Kategori</label>
          <select class="form-control">
            <option>Pengumuman</option>
            <option>Kegiatan</option>
            <option>Darurat</option>
            <option>Keuangan</option>
          </select>
        </div>
        <div class="form-group">
          <label class="form-label">Target Blok</label>
          <select class="form-control">
            <option>Semua Blok</option>
            <option>Blok A</option>
            <option>Blok B</option>
            <option>Blok C</option>
            <option>Blok D</option>
          </select>
        </div>
      </div>
      <div class="form-group">
        <label class="form-label">Isi Berita</label>
        <textarea class="form-control" rows="5" placeholder="Tulis isi berita..."></textarea>
      </div>
      <div class="form-group">
        <label class="form-label">Upload Gambar</label>
        <input type="file" class="form-control" accept="image/*">
      </div>
      <div class="form-group">
        <label class="checkbox-label">
          <input type="checkbox"> Kirim notifikasi ke semua warga
        </label>
      </div>
    </div>
    <div class="modal-footer">
      <button class="btn btn-secondary modal-cancel">Batal</button>
      <button class="btn btn-primary">Publikasikan</button>
    </div>
  </div>
</div>

<?php include 'includes/footer.php'; ?>
