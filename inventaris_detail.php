<?php $pageTitle = 'Detail Inventaris'; ?>
<?php include 'includes/header.php'; ?>
<div class="app-layout">
  <?php include 'includes/sidebar.php'; ?>
  <div class="main-wrapper">
    <?php include 'includes/topbar.php'; ?>
    <main class="main-content">
      <div class="page-header">
        <div>
          <h1>Detail Inventaris</h1>
          <div class="breadcrumb">
            <a href="index.php">Dashboard</a><span class="separator">/</span>
            <a href="inventaris.php">Inventaris</a><span class="separator">/</span>
            <span>Detail</span>
          </div>
        </div>
        <div style="display:flex;gap:10px;">
          <a href="inventaris.php" class="btn btn-secondary btn-sm">← Kembali</a>
          <a href="inventaris_pinjam.php" class="btn btn-primary btn-sm">📋 Pinjam Barang</a>
        </div>
      </div>

      <div class="grid-2" style="gap:24px;">
        <div>
          <div class="card" style="padding:24px;">
            <div style="display:flex;align-items:center;gap:20px;margin-bottom:24px;">
              <div style="width:80px;height:80px;border-radius:var(--radius-lg);background:rgba(99,102,241,0.1);display:flex;align-items:center;justify-content:center;font-size:3rem;">⛺</div>
              <div>
                <h2 style="font-size:1.3rem;">Tenda Kerucut 3x3m</h2>
                <span class="badge badge-success">Kondisi: Baik</span>
              </div>
            </div>
            <div style="display:grid;grid-template-columns:repeat(3,1fr);gap:16px;margin-bottom:24px;">
              <div style="text-align:center;padding:16px;background:var(--bg-input);border-radius:var(--radius-md);">
                <div style="font-size:1.6rem;font-weight:800;color:var(--primary-light);">5</div>
                <div class="text-muted" style="font-size:0.82rem;">Total Unit</div>
              </div>
              <div style="text-align:center;padding:16px;background:var(--bg-input);border-radius:var(--radius-md);">
                <div style="font-size:1.6rem;font-weight:800;color:var(--success);">3</div>
                <div class="text-muted" style="font-size:0.82rem;">Tersedia</div>
              </div>
              <div style="text-align:center;padding:16px;background:var(--bg-input);border-radius:var(--radius-md);">
                <div style="font-size:1.6rem;font-weight:800;color:var(--warning);">2</div>
                <div class="text-muted" style="font-size:0.82rem;">Dipinjam</div>
              </div>
            </div>
            <div style="display:flex;flex-direction:column;gap:10px;font-size:0.88rem;">
              <div style="display:flex;justify-content:space-between;"><span class="text-muted">Kode Barang</span><strong>INV-001</strong></div>
              <div style="display:flex;justify-content:space-between;"><span class="text-muted">Tanggal Pengadaan</span><strong>15 Jan 2024</strong></div>
              <div style="display:flex;justify-content:space-between;"><span class="text-muted">Harga per Unit</span><strong>Rp 1.500.000</strong></div>
              <div style="display:flex;justify-content:space-between;"><span class="text-muted">Total Nilai</span><strong>Rp 7.500.000</strong></div>
              <div style="display:flex;justify-content:space-between;"><span class="text-muted">Lokasi Penyimpanan</span><strong>Gudang Pos Jaga</strong></div>
            </div>
            <div style="margin-top:20px;padding-top:16px;border-top:1px solid var(--border-color);">
              <label class="form-label">Keterangan</label>
              <p style="font-size:0.88rem;color:var(--text-secondary);">Tenda kerucut waterproof ukuran 3x3m, rangka besi galvanis, kain polyester 600D. Cocok untuk acara hajatan warga.</p>
            </div>
          </div>

          <!-- Edit Form -->
          <div class="card" style="padding:24px;margin-top:16px;">
            <h4 style="margin-bottom:16px;">✏️ Edit Info Barang</h4>
            <div class="form-row">
              <div class="form-group">
                <label class="form-label">Jumlah Total</label>
                <input type="number" class="form-control" value="5">
              </div>
              <div class="form-group">
                <label class="form-label">Kondisi</label>
                <select class="form-control">
                  <option selected>Baik</option>
                  <option>Rusak Ringan</option>
                  <option>Rusak Berat</option>
                </select>
              </div>
            </div>
            <button class="btn btn-primary btn-sm" onclick="showToast('Data inventaris diperbarui!','success')">💾 Update</button>
          </div>
        </div>

        <!-- Loan History -->
        <div class="card">
          <div class="card-header"><h3 class="card-title">📋 Riwayat Peminjaman</h3></div>
          <div style="display:flex;flex-direction:column;">
            <?php
            $loans = [
              ['Budi Santoso', 'A/12', 2, '25 Mar', '28 Mar', 'Dipinjam', 'warning', 'Acara hajatan'],
              ['Ahmad Fauzi', 'C/08', 1, '15 Mar', '16 Mar', 'Dikembalikan', 'success', 'Bazar RT'],
              ['Riko Pratama', 'D/15', 3, '1 Mar', '3 Mar', 'Dikembalikan', 'success', 'Rapat warga'],
              ['Dewi Lestari', 'A/22', 1, '20 Feb', '22 Feb', 'Dikembalikan', 'success', 'Syukuran'],
              ['Maya Sari', 'C/19', 2, '10 Feb', '11 Feb', 'Dikembalikan', 'success', 'Bazar kuliner'],
            ];
            foreach ($loans as $l): ?>
            <div style="padding:16px 20px;border-bottom:1px solid var(--border-color);">
              <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:6px;">
                <strong style="font-size:0.9rem;"><?= $l[0] ?> (<?= $l[1] ?>)</strong>
                <span class="badge badge-<?= $l[6] ?>"><?= $l[5] ?></span>
              </div>
              <div class="text-muted" style="font-size:0.82rem;">
                <?= $l[2] ?> unit • <?= $l[3] ?> — <?= $l[4] ?> • <?= $l[7] ?>
              </div>
            </div>
            <?php endforeach; ?>
          </div>
        </div>
      </div>
    </main>
  </div>
</div>
<?php include 'includes/footer.php'; ?>
