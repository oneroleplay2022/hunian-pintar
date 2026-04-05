<?php $pageTitle = 'Detail Usaha'; ?>
<?php include 'includes/header.php'; ?>
<div class="app-layout">
  <?php include 'includes/sidebar.php'; ?>
  <div class="main-wrapper">
    <?php include 'includes/topbar.php'; ?>
    <main class="main-content">
      <div class="page-header">
        <div><h1>Detail Usaha</h1>
          <div class="breadcrumb"><a href="index.php">Dashboard</a><span class="separator">/</span><a href="usaha.php">Usaha</a><span class="separator">/</span><span>Detail</span></div>
        </div>
        <div style="display:flex;gap:10px;">
          <a href="usaha.php" class="btn btn-secondary btn-sm">← Kembali</a>
          <a href="usaha_form.php" class="btn btn-primary btn-sm"><i data-lucide="pencil" style="width:14px;height:14px;"></i> Edit</a>
        </div>
      </div>

      <div class="grid-2" style="gap:24px;">
        <div>
          <div class="card" style="padding:24px;">
            <div style="display:flex;align-items:center;gap:16px;margin-bottom:20px;">
              <div style="width:72px;height:72px;border-radius:var(--radius-lg);background:rgba(16,185,129,0.1);display:flex;align-items:center;justify-content:center;font-size:2.5rem;">🍽️</div>
              <div>
                <h2 style="font-size:1.3rem;">Warung Makan Bu Siti</h2>
                <span class="badge badge-success">Kuliner</span>
                <span class="badge badge-info" style="margin-left:4px;">Aktif</span>
              </div>
            </div>
            <p style="color:var(--text-secondary);font-size:0.92rem;line-height:1.7;margin-bottom:20px;">
              Warung makan rumahan yang menyajikan masakan khas Sunda dan Padang. Menu favorit: nasi rames, pecel lele, ayam goreng, dan nasi uduk. Tersedia juga aneka sambal dan lalapan segar.
            </p>
            <div style="display:flex;flex-direction:column;gap:12px;font-size:0.88rem;">
              <div style="display:flex;justify-content:space-between;"><span class="text-muted">Pemilik</span><strong>Siti Rahayu</strong></div>
              <div style="display:flex;justify-content:space-between;"><span class="text-muted">Rumah</span><strong>Blok B/05</strong></div>
              <div style="display:flex;justify-content:space-between;"><span class="text-muted">Jam Buka</span><strong>07:00 - 21:00</strong></div>
              <div style="display:flex;justify-content:space-between;"><span class="text-muted">No. HP / WA</span><strong>0812-xxxx-1234</strong></div>
              <div style="display:flex;justify-content:space-between;"><span class="text-muted">Terdaftar Sejak</span><strong>12 Jan 2025</strong></div>
            </div>
          </div>
        </div>

        <div>
          <div class="card" style="padding:20px;margin-bottom:16px;">
            <h4 style="margin-bottom:12px;">📍 Lokasi di Perumahan</h4>
            <div style="aspect-ratio:16/9;background:var(--bg-input);border-radius:var(--radius-md);display:flex;align-items:center;justify-content:center;color:var(--text-muted);">
              <div style="text-align:center;">
                <span style="font-size:2rem;">🗺️</span>
                <div style="font-size:0.85rem;margin-top:8px;">Blok B / No. 05</div>
              </div>
            </div>
          </div>

          <div class="card" style="padding:20px;">
            <h4 style="margin-bottom:12px;">🕐 Jadwal Buka Mingguan</h4>
            <?php
            $days = [['Senin', '07:00-21:00', true],['Selasa', '07:00-21:00', true],['Rabu', '07:00-21:00', true],['Kamis', '07:00-21:00', true],['Jumat', '07:00-21:00', true],['Sabtu', '07:00-22:00', true],['Minggu', 'Tutup', false]];
            foreach ($days as $d): ?>
            <div style="display:flex;justify-content:space-between;padding:8px 0;border-bottom:1px solid var(--border-color);font-size:0.85rem;">
              <span><?= $d[0] ?></span>
              <span style="color:<?= $d[2] ? 'var(--success)' : 'var(--danger)' ?>;"><?= $d[1] ?></span>
            </div>
            <?php endforeach; ?>
          </div>
        </div>
      </div>
    </main>
  </div>
</div>
<?php include 'includes/footer.php'; ?>
