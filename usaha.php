<?php $pageTitle = 'Usaha & Lapak Warga'; ?>
<?php include 'includes/header.php'; ?>

<div class="app-layout">
  <?php include 'includes/sidebar.php'; ?>

  <div class="main-wrapper">
    <?php include 'includes/topbar.php'; ?>

    <main class="main-content">
      <div class="page-header">
        <div>
          <h1>Usaha & Lapak Warga</h1>
          <div class="breadcrumb">
            <a href="index.php">Dashboard</a>
            <span class="separator">/</span>
            <span>Kependudukan</span>
            <span class="separator">/</span>
            <span>Usaha</span>
          </div>
        </div>
        <a href="usaha_form.php" class="btn btn-primary btn-sm"><i data-lucide="plus" style="width:16px;height:16px;"></i> Daftarkan Usaha</a>
      </div>

      <!-- Stats -->
      <div class="stats-grid" style="grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));">
        <div class="stat-card">
          <div class="stat-icon blue"><i data-lucide="store"></i></div>
          <div class="stat-info">
            <div class="stat-label">Total Usaha</div>
            <div class="stat-value">32</div>
          </div>
        </div>
        <div class="stat-card">
          <div class="stat-icon green"><i data-lucide="utensils"></i></div>
          <div class="stat-info">
            <div class="stat-label">Kuliner</div>
            <div class="stat-value">14</div>
          </div>
        </div>
        <div class="stat-card">
          <div class="stat-icon purple"><i data-lucide="wrench"></i></div>
          <div class="stat-info">
            <div class="stat-label">Jasa</div>
            <div class="stat-value">11</div>
          </div>
        </div>
        <div class="stat-card">
          <div class="stat-icon yellow"><i data-lucide="shopping-bag"></i></div>
          <div class="stat-info">
            <div class="stat-label">Retail</div>
            <div class="stat-value">7</div>
          </div>
        </div>
      </div>

      <!-- Filter -->
      <div class="filter-bar">
        <div class="filter-search">
          <span class="search-icon"><i data-lucide="search"></i></span>
          <input type="text" class="form-control" placeholder="Cari usaha atau pemilik...">
        </div>
        <select class="form-control" style="width:auto;">
          <option value="">Semua Kategori</option>
          <option>Kuliner</option>
          <option>Jasa</option>
          <option>Retail</option>
          <option>Online Shop</option>
        </select>
      </div>

      <!-- Business Cards Grid -->
      <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(300px,1fr));gap:16px;">
        <?php
        $usaha = [
          ['Warung Makan Bu Siti', 'Siti Rahayu', 'B/05', 'Kuliner', 'Masakan rumahan, nasi padang, pecel', '07:00 - 21:00', '📱 0812-xxxx-1234', 'utensils', 'green'],
          ['Toko Kelontong Pak Budi', 'Budi Santoso', 'A/12', 'Retail', 'Kebutuhan sehari-hari, snack, minuman', '06:00 - 22:00', '📱 0813-xxxx-5678', 'shopping-bag', 'yellow'],
          ['Bengkel Motor Arif', 'Arif Rahman', 'D/07', 'Jasa', 'Service motor, ganti oli, tambal ban', '08:00 - 17:00', '📱 0815-xxxx-9012', 'wrench', 'purple'],
          ['Laundry Express Maya', 'Maya Sari', 'C/19', 'Jasa', 'Laundry kiloan, dry clean, setrika', '07:00 - 20:00', '📱 0858-xxxx-3456', 'shirt', 'blue'],
          ['Kedai Kopi Riko', 'Riko Pratama', 'D/15', 'Kuliner', 'Kopi manual brew, snack, makanan ringan', '10:00 - 23:00', '📱 0821-xxxx-7890', 'coffee', 'green'],
          ['Salon Cantik Dewi', 'Dewi Lestari', 'A/22', 'Jasa', 'Potong rambut, creambath, facial', '09:00 - 18:00', '📱 0856-xxxx-2345', 'scissors', 'purple'],
          ['Toko ATK & Print', 'Nur Hidayah', 'B/11', 'Retail', 'Alat tulis, fotokopi, print, scan', '08:00 - 20:00', '📱 0822-xxxx-6789', 'printer', 'yellow'],
          ['Catering Lestari', 'Lestari N.', 'A/08', 'Kuliner', 'Catering nasi box, prasmanan, kue', 'By order', '📱 0819-xxxx-0123', 'chef-hat', 'green'],
          ['Warkop Bamz', 'Bambang S.', 'A/07', 'Kuliner', 'Warung kopi, indomie, gorengan, free wifi', '16:00 - 01:00', '📱 0878-xxxx-4567', 'coffee', 'green'],
        ];
        foreach ($usaha as $u): ?>
        <div class="card" style="padding:20px;">
          <div style="display:flex;align-items:flex-start;gap:14px;margin-bottom:14px;">
            <div style="width:52px;height:52px;border-radius:var(--radius-md);background:rgba(99,102,241,0.1);display:flex;align-items:center;justify-content:center;flex-shrink:0;">
              <i data-lucide="<?= $u[7] ?>" style="width:24px;height:24px;color:var(--<?= $u[8] ?>);"></i>
            </div>
            <div style="flex:1;">
              <div style="font-weight:700;font-size:1rem;margin-bottom:2px;"><?= $u[0] ?></div>
              <div style="font-size:0.82rem;color:var(--text-secondary);">
                <span><?= $u[1] ?></span> — <span><?= $u[2] ?></span>
              </div>
              <span class="badge badge-<?= $u[8] ?>" style="margin-top:4px;"><?= $u[3] ?></span>
            </div>
          </div>
          <p style="font-size:0.85rem;color:var(--text-muted);margin-bottom:12px;"><?= $u[4] ?></p>
          <div style="display:flex;justify-content:space-between;align-items:center;padding-top:12px;border-top:1px solid var(--border-color);font-size:0.82rem;">
            <span class="text-muted">🕐 <?= $u[5] ?></span>
            <span style="color:var(--primary-light);"><?= $u[6] ?></span>
          </div>
        </div>
        <?php endforeach; ?>
      </div>
    </main>
  </div>
</div>

<!-- Modal -->
<div class="modal-overlay" id="addUsahaModal">
  <div class="modal">
    <div class="modal-header">
      <h3 class="modal-title">Daftarkan Usaha Warga</h3>
      <button class="modal-close">✕</button>
    </div>
    <div class="modal-body">
      <div class="form-group">
        <label class="form-label">Nama Usaha</label>
        <input type="text" class="form-control" placeholder="Nama usaha / lapak">
      </div>
      <div class="form-row">
        <div class="form-group">
          <label class="form-label">Kategori</label>
          <select class="form-control">
            <option>Kuliner</option>
            <option>Jasa</option>
            <option>Retail</option>
            <option>Online Shop</option>
            <option>Lainnya</option>
          </select>
        </div>
        <div class="form-group">
          <label class="form-label">Rumah / Lokasi</label>
          <input type="text" class="form-control" placeholder="Contoh: A/12">
        </div>
      </div>
      <div class="form-group">
        <label class="form-label">Deskripsi Produk / Jasa</label>
        <textarea class="form-control" rows="3" placeholder="Jelaskan produk atau jasa..."></textarea>
      </div>
      <div class="form-row">
        <div class="form-group">
          <label class="form-label">Jam Operasional</label>
          <input type="text" class="form-control" placeholder="Contoh: 08:00 - 20:00">
        </div>
        <div class="form-group">
          <label class="form-label">No. HP / WhatsApp</label>
          <input type="tel" class="form-control" placeholder="08xxxxxxxxxx">
        </div>
      </div>
    </div>
    <div class="modal-footer">
      <button class="btn btn-secondary modal-cancel">Batal</button>
      <button class="btn btn-primary">Daftarkan</button>
    </div>
  </div>
</div>

<?php include 'includes/footer.php'; ?>
