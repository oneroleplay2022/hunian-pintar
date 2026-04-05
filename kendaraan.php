<?php $pageTitle = 'Data Kendaraan'; ?>
<?php include 'includes/header.php'; ?>

<div class="app-layout">
  <?php include 'includes/sidebar.php'; ?>

  <div class="main-wrapper">
    <?php include 'includes/topbar.php'; ?>

    <main class="main-content">
      <div class="page-header">
        <div>
          <h1>Data Kendaraan</h1>
          <div class="breadcrumb">
            <a href="index.php">Dashboard</a>
            <span class="separator">/</span>
            <span>Kependudukan</span>
            <span class="separator">/</span>
            <span>Kendaraan</span>
          </div>
        </div>
        <div style="display:flex;gap:10px;">
          <button class="btn btn-secondary btn-sm"><i data-lucide="download" style="width:16px;height:16px;"></i> Export</button>
          <a href="kendaraan_form.php" class="btn btn-primary btn-sm"><i data-lucide="plus" style="width:16px;height:16px;"></i> Tambah Kendaraan</a>
        </div>
      </div>

      <!-- Stats -->
      <div class="stats-grid" style="grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));">
        <div class="stat-card">
          <div class="stat-icon blue"><i data-lucide="car"></i></div>
          <div class="stat-info">
            <div class="stat-label">Total Kendaraan</div>
            <div class="stat-value">856</div>
          </div>
        </div>
        <div class="stat-card">
          <div class="stat-icon purple"><i data-lucide="car"></i></div>
          <div class="stat-info">
            <div class="stat-label">Mobil</div>
            <div class="stat-value">342</div>
          </div>
        </div>
        <div class="stat-card">
          <div class="stat-icon green"><i data-lucide="bike"></i></div>
          <div class="stat-info">
            <div class="stat-label">Motor</div>
            <div class="stat-value">498</div>
          </div>
        </div>
        <div class="stat-card">
          <div class="stat-icon yellow"><i data-lucide="badge-check"></i></div>
          <div class="stat-info">
            <div class="stat-label">Stiker Terpasang</div>
            <div class="stat-value">780</div>
          </div>
        </div>
      </div>

      <!-- Filter -->
      <div class="filter-bar">
        <div class="filter-search">
          <span class="search-icon"><i data-lucide="search"></i></span>
          <input type="text" class="form-control" placeholder="Cari plat nomor, merk, atau pemilik...">
        </div>
        <select class="form-control" style="width:auto;">
          <option value="">Semua Jenis</option>
          <option>Mobil</option>
          <option>Motor</option>
        </select>
        <select class="form-control" style="width:auto;">
          <option value="">Semua Blok</option>
          <option>Blok A</option>
          <option>Blok B</option>
          <option>Blok C</option>
          <option>Blok D</option>
        </select>
        <select class="form-control" style="width:auto;">
          <option value="">Stiker</option>
          <option>Sudah Stiker</option>
          <option>Belum Stiker</option>
        </select>
      </div>

      <!-- Table -->
      <div class="card">
        <div class="table-container">
          <table class="table">
            <thead>
              <tr>
                <th>No</th>
                <th>Plat Nomor</th>
                <th>Jenis</th>
                <th>Merk / Type</th>
                <th>Warna</th>
                <th>Pemilik</th>
                <th>Rumah</th>
                <th>Stiker</th>
                <th>Aksi</th>
              </tr>
            </thead>
            <tbody>
              <?php
              $kendaraan = [
                ['B 1234 ABC', 'Mobil', 'Toyota Avanza', 'Silver', 'Budi Santoso', 'A/12', true],
                ['B 5678 DEF', 'Motor', 'Honda Beat', 'Merah', 'Budi Santoso', 'A/12', true],
                ['B 9012 GHI', 'Mobil', 'Honda Jazz', 'Hitam', 'Siti Rahayu', 'B/05', true],
                ['B 3456 JKL', 'Motor', 'Yamaha NMAX', 'Biru', 'Siti Rahayu', 'B/05', true],
                ['B 7890 MNO', 'Mobil', 'Toyota Fortuner', 'Putih', 'Ahmad Fauzi', 'C/08', true],
                ['B 2345 PQR', 'Motor', 'Honda Vario', 'Hitam', 'Dewi Lestari', 'A/22', false],
                ['B 6789 STU', 'Mobil', 'Mitsubishi Xpander', 'Abu-abu', 'Riko Pratama', 'D/15', true],
                ['B 1011 VWX', 'Motor', 'Suzuki Satria', 'Hitam', 'Nur Hidayah', 'B/11', true],
                ['D 4567 YZA', 'Mobil', 'Daihatsu Sigra', 'Putih', 'Maya Sari', 'C/19', false],
                ['B 8910 BCD', 'Motor', 'Yamaha Aerox', 'Kuning', 'Arif Rahman', 'D/07', true],
              ];
              foreach ($kendaraan as $i => $k): ?>
              <tr>
                <td><?= $i + 1 ?></td>
                <td><strong style="font-family:monospace;"><?= $k[0] ?></strong></td>
                <td>
                  <span class="badge <?= $k[1] == 'Mobil' ? 'badge-info' : 'badge-success' ?>"><?= $k[1] == 'Mobil' ? '🚗' : '🏍️' ?> <?= $k[1] ?></span>
                </td>
                <td><?= $k[2] ?></td>
                <td><?= $k[3] ?></td>
                <td><?= $k[4] ?></td>
                <td><?= $k[5] ?></td>
                <td>
                  <?php if ($k[6]): ?>
                    <span class="badge badge-success">✓ Terpasang</span>
                  <?php else: ?>
                    <span class="badge badge-warning">Belum</span>
                  <?php endif; ?>
                </td>
                <td>
                  <div style="display:flex;gap:4px;">
                    <button class="btn btn-icon btn-sm btn-secondary"><i data-lucide="pencil" style="width:14px;height:14px;"></i></button>
                    <button class="btn btn-icon btn-sm btn-secondary" style="color:var(--danger);"><i data-lucide="trash-2" style="width:14px;height:14px;"></i></button>
                  </div>
                </td>
              </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        </div>
        <div style="display:flex;align-items:center;justify-content:space-between;margin-top:16px;flex-wrap:wrap;gap:12px;">
          <span class="text-muted" style="font-size:0.85rem;">Menampilkan 1-10 dari 856 kendaraan</span>
          <div class="pagination" style="margin-top:0;">
            <button class="page-btn">←</button>
            <button class="page-btn active">1</button>
            <button class="page-btn">2</button>
            <button class="page-btn">3</button>
            <button class="page-btn">→</button>
          </div>
        </div>
      </div>
    </main>
  </div>
</div>

<!-- Modal -->
<div class="modal-overlay" id="addKendaraanModal">
  <div class="modal">
    <div class="modal-header">
      <h3 class="modal-title">Tambah Data Kendaraan</h3>
      <button class="modal-close">✕</button>
    </div>
    <div class="modal-body">
      <div class="form-row">
        <div class="form-group">
          <label class="form-label">Plat Nomor</label>
          <input type="text" class="form-control" placeholder="B 1234 ABC">
        </div>
        <div class="form-group">
          <label class="form-label">Jenis Kendaraan</label>
          <select class="form-control">
            <option>Mobil</option>
            <option>Motor</option>
          </select>
        </div>
      </div>
      <div class="form-row">
        <div class="form-group">
          <label class="form-label">Merk / Type</label>
          <input type="text" class="form-control" placeholder="Contoh: Toyota Avanza">
        </div>
        <div class="form-group">
          <label class="form-label">Warna</label>
          <input type="text" class="form-control" placeholder="Warna kendaraan">
        </div>
      </div>
      <div class="form-row">
        <div class="form-group">
          <label class="form-label">Rumah</label>
          <input type="text" class="form-control" placeholder="Contoh: A/12">
        </div>
        <div class="form-group">
          <label class="form-label">Status Stiker</label>
          <select class="form-control">
            <option>Sudah Terpasang</option>
            <option>Belum Terpasang</option>
          </select>
        </div>
      </div>
    </div>
    <div class="modal-footer">
      <button class="btn btn-secondary modal-cancel">Batal</button>
      <button class="btn btn-primary">Simpan</button>
    </div>
  </div>
</div>

<?php include 'includes/footer.php'; ?>
