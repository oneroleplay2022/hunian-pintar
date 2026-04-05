<?php $pageTitle = 'Ajukan Surat'; ?>
<?php include 'includes/header.php'; ?>
<div class="app-layout">
  <?php include 'includes/sidebar.php'; ?>
  <div class="main-wrapper">
    <?php include 'includes/topbar.php'; ?>
    <main class="main-content">
      <div class="page-header">
        <div><h1>Ajukan Permohonan Surat</h1>
          <div class="breadcrumb"><a href="index.php">Dashboard</a><span class="separator">/</span><a href="surat.php">E-Surat</a><span class="separator">/</span><span>Ajukan</span></div>
        </div>
        <a href="surat.php" class="btn btn-secondary btn-sm">← Kembali</a>
      </div>

      <div class="card" style="padding:24px;">
        <div class="form-group">
          <label class="form-label">Jenis Surat</label>
          <div style="display:grid;grid-template-columns:repeat(2,1fr);gap:12px;">
            <?php
            $types = [
              ['📄', 'Surat Pengantar', 'Pengantar untuk urusan ke kelurahan/instansi'],
              ['🏠', 'Surat Ket. Domisili', 'Keterangan tempat tinggal'],
              ['🚫', 'Surat Ket. Tidak Mampu', 'Keterangan status ekonomi'],
              ['💼', 'Surat Ket. Usaha', 'Keterangan usaha/dagang'],
              ['📎', 'Surat Pengantar SKCK', 'Pengantar pembuatan SKCK'],
              ['📝', 'Surat Lainnya', 'Jenis surat custom'],
            ];
            foreach ($types as $t): ?>
            <label style="padding:14px;border:1px solid var(--border-color);border-radius:var(--radius-md);cursor:pointer;transition:all 0.2s;">
              <input type="radio" name="type" style="display:none;">
              <div style="display:flex;align-items:center;gap:10px;">
                <span style="font-size:1.5rem;"><?= $t[0] ?></span>
                <div>
                  <div style="font-weight:600;font-size:0.9rem;"><?= $t[1] ?></div>
                  <div class="text-muted" style="font-size:0.75rem;"><?= $t[2] ?></div>
                </div>
              </div>
            </label>
            <?php endforeach; ?>
          </div>
        </div>
        <div class="form-group"><label class="form-label">Nama Pemohon</label><input type="text" class="form-control" placeholder="Nama sesuai KTP"></div>
        <div class="form-row">
          <div class="form-group"><label class="form-label">NIK</label><input type="text" class="form-control" placeholder="16 digit NIK"></div>
          <div class="form-group"><label class="form-label">No. HP</label><input type="tel" class="form-control" placeholder="08xxxxxxxxxx"></div>
        </div>
        <div class="form-group"><label class="form-label">Rumah</label><input type="text" class="form-control" placeholder="Contoh: A/12"></div>
        <div class="form-group"><label class="form-label">Keperluan / Tujuan</label><textarea class="form-control" rows="3" placeholder="Jelaskan keperluan pembuatan surat..."></textarea></div>
        <div class="form-group"><label class="form-label">Lampiran Pendukung</label><input type="file" class="form-control" accept="image/*,.pdf" multiple><small class="text-muted">Fotokopi KTP, KK, atau dokumen lain yang diperlukan</small></div>
        <div style="display:flex;justify-content:flex-end;gap:12px;margin-top:24px;padding-top:20px;border-top:1px solid var(--border-color);">
          <a href="surat.php" class="btn btn-secondary">Batal</a>
          <button class="btn btn-primary" onclick="showToast('Permohonan surat berhasil diajukan!','success')">📤 Ajukan</button>
        </div>
      </div>
    </main>
  </div>
</div>
<?php include 'includes/footer.php'; ?>
