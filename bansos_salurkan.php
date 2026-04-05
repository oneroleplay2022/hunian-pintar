<?php $pageTitle = 'Salurkan Bantuan'; ?>
<?php include 'includes/header.php'; ?>
<div class="app-layout">
  <?php include 'includes/sidebar.php'; ?>
  <div class="main-wrapper">
    <?php include 'includes/topbar.php'; ?>
    <main class="main-content">
      <div class="page-header">
        <div><h1>Salurkan Bantuan</h1>
          <div class="breadcrumb"><a href="index.php">Dashboard</a><span class="separator">/</span><a href="bansos.php">Bansos</a><span class="separator">/</span><span>Salurkan</span></div>
        </div>
        <a href="bansos.php" class="btn btn-secondary btn-sm">← Kembali</a>
      </div>

      <div class="card" style="padding:24px;">
        <div class="form-group">
          <label class="form-label">Program Bantuan</label>
          <select class="form-control"><option>PKH (Program Keluarga Harapan)</option><option>BPNT (Bantuan Pangan Non-Tunai)</option><option>Zakat RT</option><option>Bantuan Hari Raya</option></select>
        </div>
        <div class="form-row">
          <div class="form-group"><label class="form-label">Periode</label><input type="month" class="form-control" value="2026-03"></div>
          <div class="form-group"><label class="form-label">Tanggal Penyaluran</label><input type="date" class="form-control"></div>
        </div>

        <h4 style="margin:24px 0 16px;padding-top:16px;border-top:1px solid var(--border-color);">Daftar Penerima</h4>
        <div style="margin-bottom:16px;">
          <?php
          $recipients = [
            ['Sari Nurhayati', 'B/23', 'Rp 200.000', true],
            ['Ahmad Dahlan', 'C/15', 'Rp 200.000', true],
            ['Maryam', 'A/08', 'Rp 200.000', true],
            ['Sutrisno', 'D/02', 'Rp 200.000', true],
            ['Neng Euis', 'B/17', 'Rp 200.000', false],
          ];
          foreach ($recipients as $r): ?>
          <div style="display:flex;align-items:center;gap:12px;padding:12px;border-bottom:1px solid var(--border-color);">
            <input type="checkbox" <?= $r[3] ? 'checked' : '' ?>>
            <div style="flex:1;">
              <strong style="font-size:0.9rem;"><?= $r[0] ?></strong>
              <span class="text-muted" style="font-size:0.82rem;margin-left:8px;"><?= $r[1] ?></span>
            </div>
            <span style="font-weight:600;font-size:0.88rem;"><?= $r[2] ?></span>
            <span class="badge badge-<?= $r[3] ? 'success' : 'warning' ?>"><?= $r[3] ? '✓ Hadir' : 'Belum' ?></span>
          </div>
          <?php endforeach; ?>
        </div>

        <div style="display:flex;justify-content:space-between;padding:16px;background:var(--bg-input);border-radius:var(--radius-md);margin-bottom:20px;">
          <span>Total Penyaluran (4 penerima)</span>
          <strong style="font-size:1.1rem;color:var(--primary-light);">Rp 800.000</strong>
        </div>

        <div class="form-group"><label class="form-label">Metode Penyaluran</label>
          <select class="form-control"><option>Tunai (langsung)</option><option>Transfer Rekening</option><option>Barang / Sembako</option></select>
        </div>
        <div class="form-group"><label class="form-label">Foto Dokumentasi Penyaluran</label><input type="file" class="form-control" accept="image/*" multiple></div>
        <div class="form-group"><label class="form-label">Catatan</label><textarea class="form-control" rows="2" placeholder="Catatan penyaluran..."></textarea></div>

        <div style="display:flex;justify-content:flex-end;gap:12px;margin-top:24px;padding-top:20px;border-top:1px solid var(--border-color);">
          <a href="bansos.php" class="btn btn-secondary">Batal</a>
          <button class="btn btn-primary" onclick="showToast('Bantuan berhasil disalurkan ke 4 penerima!','success')">📤 Salurkan Bantuan</button>
        </div>
      </div>
    </main>
  </div>
</div>
<?php include 'includes/footer.php'; ?>
