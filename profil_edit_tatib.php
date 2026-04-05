<?php $pageTitle = 'Edit Tata Tertib'; ?>
<?php include 'includes/header.php'; ?>
<div class="app-layout">
  <?php include 'includes/sidebar.php'; ?>
  <div class="main-wrapper">
    <?php include 'includes/topbar.php'; ?>
    <main class="main-content">
      <div class="page-header">
        <div>
          <h1>Edit Tata Tertib</h1>
          <div class="breadcrumb">
            <a href="index.php">Dashboard</a><span class="separator">/</span>
            <a href="profil.php">Profil</a><span class="separator">/</span>
            <span>Edit Tata Tertib</span>
          </div>
        </div>
        <div style="display:flex;gap:10px;">
          <a href="profil.php" class="btn btn-secondary btn-sm">← Kembali</a>
          <button class="btn btn-primary btn-sm" onclick="showToast('Tata tertib berhasil disimpan!','success')">💾 Simpan Perubahan</button>
        </div>
      </div>

      <?php
      $categories = [
        ['Keamanan', '🛡️', [
          'Setiap tamu wajib melapor ke pos keamanan dan mencatat identitas.',
          'Dilarang membuat keributan setelah pukul 22.00 WIB.',
          'Setiap rumah wajib memasang stiker kendaraan resmi dari RT.',
          'Portal gerbang ditutup pukul 23.00 - 05.00 WIB.',
        ]],
        ['Kebersihan', '🧹', [
          'Dilarang membuang sampah di saluran air dan area umum.',
          'Warga wajib memilah sampah organik dan anorganik.',
          'Pembakaran sampah di area perumahan dilarang keras.',
          'Jadwal pengangkutan sampah: Senin, Rabu, Jumat pukul 06.00 WIB.',
        ]],
        ['Iuran & Keuangan', '💰', [
          'Iuran IPL wajib dibayarkan sebelum tanggal 10 setiap bulan.',
          'Tunggakan lebih dari 3 bulan akan mendapat surat peringatan.',
          'Laporan keuangan dipublikasikan setiap bulan melalui aplikasi.',
        ]],
        ['Renovasi & Bangunan', '🏗️', [
          'Renovasi rumah wajib mendapat izin tertulis dari pengurus RT.',
          'Jam kerja renovasi: 08.00 - 17.00 WIB (hari kerja).',
          'Material bangunan tidak boleh menghalangi jalan umum.',
        ]],
      ];
      foreach ($categories as $idx => $cat): ?>
      <div class="card" style="margin-bottom:16px;">
        <div class="card-header" style="cursor:pointer;">
          <h3 class="card-title"><?= $cat[1] ?> <?= $cat[0] ?></h3>
          <div style="display:flex;gap:8px;">
            <button class="btn btn-sm btn-outline" onclick="addRule(<?= $idx ?>)"><i data-lucide="plus" style="width:14px;height:14px;"></i> Tambah Aturan</button>
          </div>
        </div>
        <div id="category-<?= $idx ?>">
          <?php foreach ($cat[2] as $ri => $rule): ?>
          <div class="rule-item" style="display:flex;align-items:flex-start;gap:12px;padding:14px;margin:0 0 8px;background:var(--bg-input);border-radius:var(--radius-md);">
            <span style="color:var(--text-muted);font-weight:600;min-width:28px;"><?= $ri + 1 ?>.</span>
            <textarea class="form-control" rows="2" style="flex:1;min-height:auto;"><?= $rule ?></textarea>
            <div style="display:flex;flex-direction:column;gap:4px;">
              <button class="btn btn-icon btn-sm btn-secondary" title="Naik"><i data-lucide="chevron-up" style="width:14px;height:14px;"></i></button>
              <button class="btn btn-icon btn-sm btn-secondary" title="Turun"><i data-lucide="chevron-down" style="width:14px;height:14px;"></i></button>
              <button class="btn btn-icon btn-sm btn-secondary" style="color:var(--danger);" title="Hapus"><i data-lucide="trash-2" style="width:14px;height:14px;"></i></button>
            </div>
          </div>
          <?php endforeach; ?>
        </div>
      </div>
      <?php endforeach; ?>

      <!-- Add new category -->
      <div class="card" style="border:2px dashed var(--border-color);text-align:center;padding:32px;cursor:pointer;" onclick="showToast('Form kategori baru dibuka','info')">
        <i data-lucide="plus-circle" style="width:32px;height:32px;color:var(--text-muted);margin-bottom:8px;"></i>
        <div style="color:var(--text-muted);">Tambah Kategori Baru</div>
      </div>
    </main>
  </div>
</div>
<?php include 'includes/footer.php'; ?>
