<?php $pageTitle = 'Inventaris & Aset'; ?>
<?php include 'includes/header.php'; ?>

<div class="app-layout">
  <?php include 'includes/sidebar.php'; ?>

  <div class="main-wrapper">
    <?php include 'includes/topbar.php'; ?>

    <main class="main-content">
      <div class="page-header">
        <div>
          <h1>Inventaris & Aset</h1>
          <div class="breadcrumb">
            <a href="index.php">Dashboard</a>
            <span class="separator">/</span>
            <span>Inventaris</span>
          </div>
        </div>
        <div style="display:flex;gap:10px;">
          <button class="btn btn-secondary btn-sm"><i data-lucide="download" style="width:16px;height:16px;"></i> Export</button>
          <button class="btn btn-primary btn-sm" data-modal="addItemModal"><i data-lucide="plus" style="width:16px;height:16px;"></i> Tambah Barang</button>
        </div>
      </div>

      <!-- Tabs -->
      <div class="tabs">
        <button class="tab-btn active" data-tab="tabBarang">Daftar Barang</button>
        <button class="tab-btn" data-tab="tabPinjam">Peminjaman</button>
      </div>

      <!-- Tab: Daftar Barang -->
      <div class="tab-panel" id="tabBarang">
        <div class="filter-bar">
          <div class="filter-search">
            <span class="search-icon"><i data-lucide="search"></i></span>
            <input type="text" class="form-control" placeholder="Cari barang...">
          </div>
          <select class="form-control" style="width:auto;">
            <option value="">Semua Kondisi</option>
            <option>Baik</option>
            <option>Rusak Ringan</option>
            <option>Rusak Berat</option>
          </select>
        </div>

        <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(280px,1fr));gap:16px;">
          <?php
          $inventory = [
            ['Tenda Kerucut 3x3m', 5, 3, 'Baik', 'success', '⛺'],
            ['Kursi Lipat Plastik', 200, 180, 'Baik', 'success', '🪑'],
            ['Meja Lipat Panjang', 20, 18, 'Baik', 'success', '🪑'],
            ['Sound System + Mic', 2, 1, 'Baik', 'success', '🔊'],
            ['Genset Portable 5000W', 1, 1, 'Baik', 'success', '⚡'],
            ['Tangga Aluminium 3m', 2, 2, 'Rusak Ringan', 'warning', '🪜'],
            ['Mesin Potong Rumput', 2, 1, 'Baik', 'success', '🌿'],
            ['Alat Pertukangan (Set)', 3, 3, 'Baik', 'success', '🔧'],
            ['Gerobak Sampah', 4, 4, 'Rusak Ringan', 'warning', '🗑️'],
            ['Pompa Air Portable', 1, 1, 'Rusak Berat', 'danger', '💧'],
            ['Tenda Posko 4x6m', 2, 2, 'Baik', 'success', '⛺'],
            ['Terpal Biru 4x5m', 10, 8, 'Baik', 'success', '🔵'],
          ];
          foreach ($inventory as $item): ?>
          <div class="card" style="padding:20px;">
            <div style="display:flex;align-items:flex-start;justify-content:space-between;margin-bottom:12px;">
              <div style="display:flex;align-items:center;gap:12px;">
                <div style="width:48px;height:48px;border-radius:var(--radius-md);background:rgba(99,102,241,0.1);display:flex;align-items:center;justify-content:center;font-size:1.5rem;"><?= $item[5] ?></div>
                <div>
                  <div style="font-weight:600;font-size:0.95rem;"><a href="inventaris_detail.php" style="color:inherit;text-decoration:none;"><?= $item[0] ?></a></div>
                  <span class="badge badge-<?= $item[4] ?>" style="margin-top:4px;"><?= $item[3] ?></span>
                </div>
              </div>
            </div>
            <div style="display:flex;justify-content:space-between;padding-top:12px;border-top:1px solid var(--border-color);font-size:0.85rem;">
              <div>
                <span class="text-muted">Total:</span> <strong><?= $item[1] ?></strong>
              </div>
              <div>
                <span class="text-muted">Tersedia:</span> <strong style="color:var(--success);"><?= $item[2] ?></strong>
              </div>
              <div>
                <span class="text-muted">Dipinjam:</span> <strong style="color:var(--warning);"><?= $item[1] - $item[2] ?></strong>
              </div>
            </div>
            <a href="inventaris_pinjam.php" class="btn btn-outline btn-sm w-full" style="margin-top:12px;">Pinjam Barang</a>
          </div>
          <?php endforeach; ?>
        </div>
      </div>

      <!-- Tab: Peminjaman -->
      <div class="tab-panel" id="tabPinjam" style="display:none;">
        <div class="card">
          <div class="card-header">
            <h3 class="card-title">Riwayat Peminjaman</h3>
          </div>
          <div class="table-container">
            <table class="table">
              <thead>
                <tr>
                  <th>Peminjam</th>
                  <th>Barang</th>
                  <th>Jumlah</th>
                  <th>Tgl Pinjam</th>
                  <th>Tgl Kembali</th>
                  <th>Status</th>
                  <th>Aksi</th>
                </tr>
              </thead>
              <tbody>
                <?php
                $loans = [
                  ['Budi Santoso', 'A/12', 'Tenda Kerucut 3x3m', 2, '25 Mar 2026', '28 Mar 2026', 'Dipinjam', 'warning'],
                  ['Siti Rahayu', 'B/05', 'Kursi Lipat Plastik', 20, '23 Mar 2026', '24 Mar 2026', 'Dikembalikan', 'success'],
                  ['Ahmad Fauzi', 'C/08', 'Sound System + Mic', 1, '20 Mar 2026', '21 Mar 2026', 'Dikembalikan', 'success'],
                  ['Riko Pratama', 'D/15', 'Meja Lipat Panjang', 2, '18 Mar 2026', '19 Mar 2026', 'Dikembalikan', 'success'],
                  ['Dewi Lestari', 'A/22', 'Mesin Potong Rumput', 1, '15 Mar 2026', '27 Mar 2026', 'Terlambat', 'danger'],
                ];
                foreach ($loans as $l): ?>
                <tr>
                  <td><strong><?= $l[0] ?></strong><br><span class="text-muted" style="font-size:0.78rem;"><?= $l[1] ?></span></td>
                  <td><?= $l[2] ?></td>
                  <td><?= $l[3] ?></td>
                  <td><?= $l[4] ?></td>
                  <td><?= $l[5] ?></td>
                  <td><span class="badge badge-<?= $l[7] ?>"><?= $l[6] ?></span></td>
                  <td>
                    <?php if ($l[6] == 'Dipinjam'): ?>
                    <button class="btn btn-sm btn-success" style="padding:4px 10px;">Kembalikan</button>
                    <?php elseif ($l[6] == 'Terlambat'): ?>
                    <button class="btn btn-sm btn-danger" style="padding:4px 10px;">Ingatkan</button>
                    <?php else: ?>
                    <span class="text-muted">-</span>
                    <?php endif; ?>
                  </td>
                </tr>
                <?php endforeach; ?>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </main>
  </div>
</div>

<!-- Add Item Modal -->
<div class="modal-overlay" id="addItemModal">
  <div class="modal">
    <div class="modal-header">
      <h3 class="modal-title">Tambah Barang Inventaris</h3>
      <button class="modal-close">✕</button>
    </div>
    <div class="modal-body">
      <div class="form-group">
        <label class="form-label">Nama Barang</label>
        <input type="text" class="form-control" placeholder="Contoh: Tenda Kerucut 3x3m">
      </div>
      <div class="form-row">
        <div class="form-group">
          <label class="form-label">Jumlah Total</label>
          <input type="number" class="form-control" placeholder="0">
        </div>
        <div class="form-group">
          <label class="form-label">Kondisi</label>
          <select class="form-control">
            <option>Baik</option>
            <option>Rusak Ringan</option>
            <option>Rusak Berat</option>
          </select>
        </div>
      </div>
      <div class="form-group">
        <label class="form-label">Foto Barang</label>
        <input type="file" class="form-control" accept="image/*">
      </div>
      <div class="form-group">
        <label class="form-label">Keterangan</label>
        <textarea class="form-control" rows="2" placeholder="Keterangan tambahan..."></textarea>
      </div>
    </div>
    <div class="modal-footer">
      <button class="btn btn-secondary modal-cancel">Batal</button>
      <button class="btn btn-primary">Simpan</button>
    </div>
  </div>
</div>

<?php include 'includes/footer.php'; ?>
