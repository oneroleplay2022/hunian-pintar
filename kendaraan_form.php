<?php $pageTitle = 'Tambah/Edit Kendaraan'; ?>
<?php include 'includes/header.php'; ?>
<div class="app-layout">
  <?php include 'includes/sidebar.php'; ?>
  <div class="main-wrapper">
    <?php include 'includes/topbar.php'; ?>
    <main class="main-content">
      <div class="page-header">
        <div><h1>Tambah Data Kendaraan</h1>
          <div class="breadcrumb"><a href="index.php">Dashboard</a><span class="separator">/</span><a href="kendaraan.php">Kendaraan</a><span class="separator">/</span><span>Tambah</span></div>
        </div>
        <a href="kendaraan.php" class="btn btn-secondary btn-sm">← Kembali</a>
      </div>

      <div class="card" style="padding:24px;">
        <div class="form-row">
          <div class="form-group"><label class="form-label">Plat Nomor</label><input type="text" class="form-control" placeholder="B 1234 ABC" style="font-family:monospace;font-size:1.1rem;text-align:center;letter-spacing:2px;"></div>
          <div class="form-group"><label class="form-label">Jenis Kendaraan</label><select class="form-control"><option>Mobil</option><option>Motor</option></select></div>
        </div>
        <div class="form-row">
          <div class="form-group"><label class="form-label">Merk</label><input type="text" class="form-control" placeholder="Contoh: Toyota, Honda"></div>
          <div class="form-group"><label class="form-label">Type / Model</label><input type="text" class="form-control" placeholder="Contoh: Avanza, Beat"></div>
        </div>
        <div class="form-row">
          <div class="form-group"><label class="form-label">Warna</label><input type="text" class="form-control" placeholder="Warna kendaraan"></div>
          <div class="form-group"><label class="form-label">Tahun Pembuatan</label><input type="number" class="form-control" placeholder="2024"></div>
        </div>
        <div class="form-row">
          <div class="form-group">
            <label class="form-label">Pemilik</label>
            <select class="form-control">
              <option>Budi Santoso (A/12)</option><option>Siti Rahayu (B/05)</option><option>Ahmad Fauzi (C/08)</option>
              <option>Dewi Lestari (A/22)</option><option>Riko Pratama (D/15)</option>
            </select>
          </div>
          <div class="form-group">
            <label class="form-label">No. STNK</label>
            <input type="text" class="form-control" placeholder="Nomor STNK">
          </div>
        </div>
        <div class="form-group">
          <label class="form-label">Status Stiker Akses</label>
          <div style="display:flex;gap:12px;">
            <label class="checkbox-label" style="flex:1;padding:14px;border:1px solid var(--border-color);border-radius:var(--radius-md);">
              <input type="radio" name="stiker" value="yes" checked> ✅ Sudah Terpasang
            </label>
            <label class="checkbox-label" style="flex:1;padding:14px;border:1px solid var(--border-color);border-radius:var(--radius-md);">
              <input type="radio" name="stiker" value="no"> ⏳ Belum Terpasang
            </label>
          </div>
        </div>
        <div class="form-group"><label class="form-label">Foto Kendaraan</label><input type="file" class="form-control" accept="image/*"></div>
        <div style="display:flex;justify-content:flex-end;gap:12px;margin-top:24px;padding-top:20px;border-top:1px solid var(--border-color);">
          <a href="kendaraan.php" class="btn btn-secondary">Batal</a>
          <button class="btn btn-primary" onclick="showToast('Data kendaraan berhasil disimpan!','success')">💾 Simpan</button>
        </div>
      </div>
    </main>
  </div>
</div>
<?php include 'includes/footer.php'; ?>
