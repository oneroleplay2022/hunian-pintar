<?php $pageTitle = 'Check-in Tamu'; ?>
<?php include 'includes/header.php'; ?>
<div class="app-layout">
  <?php include 'includes/sidebar.php'; ?>
  <div class="main-wrapper">
    <?php include 'includes/topbar.php'; ?>
    <main class="main-content">
      <div class="page-header">
        <div><h1>Check-in Tamu</h1>
          <div class="breadcrumb"><a href="index.php">Dashboard</a><span class="separator">/</span><a href="tamu.php">Tamu</a><span class="separator">/</span><span>Check-in</span></div>
        </div>
        <a href="tamu.php" class="btn btn-secondary btn-sm">← Kembali</a>
      </div>

      <div class="card" style="padding:24px;">
        <div style="text-align:center;margin-bottom:24px;">
          <div style="width:80px;height:80px;border-radius:50%;background:linear-gradient(135deg,var(--primary),var(--accent));display:flex;align-items:center;justify-content:center;font-size:2.5rem;color:white;margin:0 auto 12px;">🚪</div>
          <h3>Form Check-in Tamu</h3>
          <p class="text-muted" style="font-size:0.88rem;">Catat data tamu yang berkunjung ke perumahan</p>
        </div>

        <div class="form-group"><label class="form-label">Nama Tamu</label><input type="text" class="form-control" placeholder="Nama lengkap tamu"></div>
        <div class="form-row">
          <div class="form-group"><label class="form-label">No. Identitas (KTP/SIM)</label><input type="text" class="form-control" placeholder="Opsional"></div>
          <div class="form-group"><label class="form-label">No. HP</label><input type="tel" class="form-control" placeholder="Opsional"></div>
        </div>
        <div class="form-row">
          <div class="form-group">
            <label class="form-label">Rumah Tujuan</label>
            <select class="form-control">
              <option value="">Pilih rumah...</option>
              <option>A/01 - H. Supriadi</option><option>A/03 - Joko W.</option><option>A/12 - Budi Santoso</option>
              <option>A/22 - Dewi Lestari</option><option>B/05 - Siti Rahayu</option><option>B/11 - Nur Hidayah</option>
              <option>C/08 - Ahmad Fauzi</option><option>C/19 - Maya Sari</option><option>D/07 - Arif Rahman</option>
              <option>D/15 - Riko Pratama</option>
            </select>
          </div>
          <div class="form-group">
            <label class="form-label">Keperluan</label>
            <select class="form-control">
              <option>Silaturahmi</option><option>Pengiriman Paket</option><option>Service / Perbaikan</option>
              <option>Tamu Menginap</option><option>Kunjungan Kerja</option><option>Lainnya</option>
            </select>
          </div>
        </div>
        <div class="form-group">
          <label class="form-label">Jumlah Tamu</label>
          <input type="number" class="form-control" value="1" min="1">
        </div>
        <div class="form-group">
          <label class="form-label">Kendaraan Tamu</label>
          <input type="text" class="form-control" placeholder="Plat nomor kendaraan (opsional)">
        </div>
        <div class="form-group">
          <label class="form-label">Foto Identitas / Kendaraan</label>
          <input type="file" class="form-control" accept="image/*">
          <small class="text-muted" style="font-size:0.78rem;">Foto KTP/SIM/plat nomor untuk keamanan</small>
        </div>
        <div class="form-group">
          <label class="form-label">Catatan Tambahan</label>
          <textarea class="form-control" rows="2" placeholder="Catatan dari petugas..."></textarea>
        </div>
        <div style="display:flex;justify-content:flex-end;gap:12px;margin-top:24px;padding-top:20px;border-top:1px solid var(--border-color);">
          <a href="tamu.php" class="btn btn-secondary">Batal</a>
          <button class="btn btn-primary" onclick="showToast('Tamu berhasil check-in!','success')">✅ Check-in</button>
        </div>
      </div>
    </main>
  </div>
</div>
<?php include 'includes/footer.php'; ?>
