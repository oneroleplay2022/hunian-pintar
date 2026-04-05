<?php $pageTitle = 'Detail Surat'; ?>
<?php include 'includes/header.php'; ?>
<div class="app-layout">
  <?php include 'includes/sidebar.php'; ?>
  <div class="main-wrapper">
    <?php include 'includes/topbar.php'; ?>
    <main class="main-content">
      <div class="page-header">
        <div><h1>Detail Permohonan Surat</h1>
          <div class="breadcrumb"><a href="index.php">Dashboard</a><span class="separator">/</span><a href="surat.php">E-Surat</a><span class="separator">/</span><span>Detail</span></div>
        </div>
        <a href="surat.php" class="btn btn-secondary btn-sm">← Kembali</a>
      </div>

      <div class="grid-2" style="gap:24px;">
        <div>
          <div class="card" style="padding:24px;">
            <div style="display:flex;align-items:center;gap:16px;margin-bottom:24px;">
              <div style="width:56px;height:56px;border-radius:var(--radius-lg);background:rgba(99,102,241,0.1);display:flex;align-items:center;justify-content:center;font-size:2rem;">📄</div>
              <div>
                <h2 style="font-size:1.2rem;">Surat Pengantar RT</h2>
                <span class="badge badge-warning">Diproses</span>
              </div>
            </div>
            <div style="display:flex;flex-direction:column;gap:12px;font-size:0.88rem;">
              <div style="display:flex;justify-content:space-between;"><span class="text-muted">No. Surat</span><strong>SRT-2026-03-015</strong></div>
              <div style="display:flex;justify-content:space-between;"><span class="text-muted">Pemohon</span><strong>Budi Santoso (A/12)</strong></div>
              <div style="display:flex;justify-content:space-between;"><span class="text-muted">Jenis Surat</span><strong>Surat Pengantar</strong></div>
              <div style="display:flex;justify-content:space-between;"><span class="text-muted">Keperluan</span><strong>Pengurusan KTP di Kelurahan</strong></div>
              <div style="display:flex;justify-content:space-between;"><span class="text-muted">Tgl Pengajuan</span><strong>25 Maret 2026</strong></div>
            </div>
          </div>

          <!-- Progress Timeline -->
          <div class="card" style="padding:24px;margin-top:16px;">
            <h4 style="margin-bottom:16px;">📊 Progress</h4>
            <?php
            $steps = [
              ['Diajukan', '25 Mar, 10:00', 'Permohonan dikirim melalui aplikasi', true],
              ['Diverifikasi', '25 Mar, 14:30', 'Data pemohon telah diverifikasi oleh admin', true],
              ['Diproses', '26 Mar, 09:00', 'Surat sedang dicetak dan ditandatangani', true],
              ['Siap Diambil', '', 'Surat siap diambil di pos RT', false],
              ['Selesai', '', 'Surat telah diserahkan ke pemohon', false],
            ];
            foreach ($steps as $i => $s): ?>
            <div style="display:flex;gap:16px;padding-bottom:20px;<?= $i < count($steps)-1 ? '' : 'padding-bottom:0;' ?>">
              <div style="display:flex;flex-direction:column;align-items:center;">
                <div style="width:32px;height:32px;border-radius:50%;background:<?= $s[3] ? 'var(--primary)' : 'var(--bg-input)' ?>;display:flex;align-items:center;justify-content:center;color:<?= $s[3] ? 'white' : 'var(--text-muted)' ?>;font-size:0.75rem;font-weight:700;"><?= $i+1 ?></div>
                <?php if ($i < count($steps)-1): ?><div style="width:2px;flex:1;background:<?= $s[3] ? 'var(--primary)' : 'var(--border-color)' ?>;margin-top:4px;"></div><?php endif; ?>
              </div>
              <div style="flex:1;padding-bottom:4px;">
                <strong style="font-size:0.9rem;"><?= $s[0] ?></strong>
                <?php if ($s[1]): ?><span class="text-muted" style="font-size:0.78rem;margin-left:8px;"><?= $s[1] ?></span><?php endif; ?>
                <div class="text-muted" style="font-size:0.82rem;margin-top:2px;"><?= $s[2] ?></div>
              </div>
            </div>
            <?php endforeach; ?>
          </div>
        </div>

        <div>
          <!-- Admin Actions -->
          <div class="card" style="padding:20px;margin-bottom:16px;">
            <h4 style="margin-bottom:16px;">⚙️ Aksi Admin</h4>
            <div class="form-group">
              <label class="form-label">Update Status</label>
              <select class="form-control">
                <option>Diajukan</option><option>Diverifikasi</option><option selected>Diproses</option>
                <option>Siap Diambil</option><option>Selesai</option><option>Ditolak</option>
              </select>
            </div>
            <div class="form-group">
              <label class="form-label">Catatan Admin</label>
              <textarea class="form-control" rows="3" placeholder="Catatan untuk pemohon atau internal..."></textarea>
            </div>
            <div class="form-group">
              <label class="form-label">Upload Surat (PDF)</label>
              <input type="file" class="form-control" accept=".pdf">
            </div>
            <button class="btn btn-primary w-full" onclick="showToast('Status surat diperbarui!','success')">✅ Update Status</button>
          </div>

          <div class="card" style="padding:20px;">
            <h4 style="margin-bottom:12px;">📎 Lampiran</h4>
            <div style="padding:12px;background:var(--bg-input);border-radius:var(--radius-md);display:flex;align-items:center;justify-content:space-between;margin-bottom:8px;">
              <span style="font-size:0.85rem;">📄 fotokopi_ktp.jpg</span>
              <button class="btn btn-sm btn-outline">Unduh</button>
            </div>
            <div style="padding:12px;background:var(--bg-input);border-radius:var(--radius-md);display:flex;align-items:center;justify-content:space-between;">
              <span style="font-size:0.85rem;">📄 fotokopi_kk.jpg</span>
              <button class="btn btn-sm btn-outline">Unduh</button>
            </div>
          </div>
        </div>
      </div>
    </main>
  </div>
</div>
<?php include 'includes/footer.php'; ?>
