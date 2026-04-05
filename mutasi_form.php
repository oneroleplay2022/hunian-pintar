<?php $pageTitle = 'Catat Mutasi'; ?>
<?php include 'includes/header.php'; ?>
<div class="app-layout">
  <?php include 'includes/sidebar.php'; ?>
  <div class="main-wrapper">
    <?php include 'includes/topbar.php'; ?>
    <main class="main-content">
      <div class="page-header">
        <div><h1>Catat Mutasi Warga</h1>
          <div class="breadcrumb"><a href="index.php">Dashboard</a><span class="separator">/</span><a href="mutasi.php">Mutasi</a><span class="separator">/</span><span>Catat</span></div>
        </div>
        <a href="mutasi.php" class="btn btn-secondary btn-sm">← Kembali</a>
      </div>

      <div class="card" style="padding:24px;">
        <div class="form-group">
          <label class="form-label">Jenis Mutasi</label>
          <div style="display:grid;grid-template-columns:repeat(3,1fr);gap:12px;">
            <label style="padding:16px;border:2px solid var(--border-color);border-radius:var(--radius-md);text-align:center;cursor:pointer;transition:all 0.2s;">
              <input type="radio" name="type" value="in" checked style="display:none;">
              <div style="font-size:2rem;margin-bottom:6px;">🟢</div>
              <div style="font-weight:600;">Masuk</div>
              <div class="text-muted" style="font-size:0.78rem;">Warga baru pindah masuk</div>
            </label>
            <label style="padding:16px;border:2px solid var(--border-color);border-radius:var(--radius-md);text-align:center;cursor:pointer;transition:all 0.2s;">
              <input type="radio" name="type" value="out" style="display:none;">
              <div style="font-size:2rem;margin-bottom:6px;">🟡</div>
              <div style="font-weight:600;">Pindah</div>
              <div class="text-muted" style="font-size:0.78rem;">Warga pindah keluar</div>
            </label>
            <label style="padding:16px;border:2px solid var(--border-color);border-radius:var(--radius-md);text-align:center;cursor:pointer;transition:all 0.2s;">
              <input type="radio" name="type" value="death" style="display:none;">
              <div style="font-size:2rem;margin-bottom:6px;">🔴</div>
              <div style="font-weight:600;">Meninggal</div>
              <div class="text-muted" style="font-size:0.78rem;">Warga meninggal dunia</div>
            </label>
          </div>
        </div>
        <div class="form-group"><label class="form-label">Nama Warga / KK</label><input type="text" class="form-control" placeholder="Nama lengkap"></div>
        <div class="form-row">
          <div class="form-group"><label class="form-label">NIK</label><input type="text" class="form-control" placeholder="16 digit NIK"></div>
          <div class="form-group"><label class="form-label">Jumlah Anggota Keluarga</label><input type="number" class="form-control" value="1" min="1"></div>
        </div>
        <div class="form-row">
          <div class="form-group"><label class="form-label">Blok / Rumah</label><input type="text" class="form-control" placeholder="Contoh: A/12"></div>
          <div class="form-group"><label class="form-label">Tanggal Mutasi</label><input type="date" class="form-control"></div>
        </div>
        <div class="form-group"><label class="form-label">Asal / Tujuan</label><input type="text" class="form-control" placeholder="Kota asal atau tujuan pindah"></div>
        <div class="form-group"><label class="form-label">Keterangan</label><textarea class="form-control" rows="4" placeholder="Detail mutasi, alasan, dsb..."></textarea></div>
        <div class="form-group"><label class="form-label">Dokumen Pendukung</label><input type="file" class="form-control" accept="image/*,.pdf" multiple></div>
        <div style="display:flex;justify-content:flex-end;gap:12px;margin-top:24px;padding-top:20px;border-top:1px solid var(--border-color);">
          <a href="mutasi.php" class="btn btn-secondary">Batal</a>
          <button class="btn btn-primary" onclick="showToast('Mutasi berhasil dicatat!','success')">💾 Simpan</button>
        </div>
      </div>
    </main>
  </div>
</div>
<?php include 'includes/footer.php'; ?>
