<?php $pageTitle = 'Edit Struktur Organisasi'; ?>
<?php include 'includes/header.php'; ?>
<div class="app-layout">
  <?php include 'includes/sidebar.php'; ?>
  <div class="main-wrapper">
    <?php include 'includes/topbar.php'; ?>
    <main class="main-content">
      <div class="page-header">
        <div>
          <h1>Edit Struktur Organisasi</h1>
          <div class="breadcrumb">
            <a href="index.php">Dashboard</a><span class="separator">/</span>
            <a href="profil.php">Profil</a><span class="separator">/</span>
            <span>Edit Struktur</span>
          </div>
        </div>
        <div style="display:flex;gap:10px;">
          <a href="profil.php" class="btn btn-secondary btn-sm">← Kembali</a>
          <button class="btn btn-primary btn-sm" onclick="showToast('Struktur organisasi berhasil disimpan!','success')">💾 Simpan</button>
        </div>
      </div>

      <!-- Ketua -->
      <div class="card" style="margin-bottom:16px;">
        <div class="card-header"><h3 class="card-title">👑 Ketua RT</h3></div>
        <div class="modal-body" style="padding:20px;">
          <div class="form-row">
            <div class="form-group">
              <label class="form-label">Nama Lengkap</label>
              <input type="text" class="form-control" value="H. Supriadi, S.H.">
            </div>
            <div class="form-group">
              <label class="form-label">No. HP</label>
              <input type="tel" class="form-control" value="0812-xxxx-xxxx">
            </div>
          </div>
          <div class="form-row">
            <div class="form-group">
              <label class="form-label">Rumah</label>
              <input type="text" class="form-control" value="A/01">
            </div>
            <div class="form-group">
              <label class="form-label">Foto</label>
              <input type="file" class="form-control" accept="image/*">
            </div>
          </div>
        </div>
      </div>

      <!-- Pengurus Inti -->
      <div class="card" style="margin-bottom:16px;">
        <div class="card-header">
          <h3 class="card-title">📋 Pengurus Inti</h3>
        </div>
        <?php
        $pengurus = [
          ['Wakil Ketua', 'Ahmad Fauzi', '0813-xxxx-1234', 'C/08'],
          ['Sekretaris', 'Siti Rahayu', '0815-xxxx-5678', 'B/05'],
          ['Bendahara', 'Dewi Lestari', '0856-xxxx-9012', 'A/22'],
        ];
        foreach ($pengurus as $p): ?>
        <div style="padding:20px;border-bottom:1px solid var(--border-color);">
          <div class="form-row">
            <div class="form-group">
              <label class="form-label"><?= $p[0] ?></label>
              <input type="text" class="form-control" value="<?= $p[1] ?>">
            </div>
            <div class="form-group">
              <label class="form-label">No. HP</label>
              <input type="tel" class="form-control" value="<?= $p[2] ?>">
            </div>
            <div class="form-group">
              <label class="form-label">Rumah</label>
              <input type="text" class="form-control" value="<?= $p[3] ?>">
            </div>
          </div>
        </div>
        <?php endforeach; ?>
      </div>

      <!-- Seksi -->
      <div class="card">
        <div class="card-header">
          <h3 class="card-title">🏛️ Seksi-Seksi</h3>
          <button class="btn btn-sm btn-outline" onclick="showToast('Form seksi baru ditambahkan','info')"><i data-lucide="plus" style="width:14px;height:14px;"></i> Tambah Seksi</button>
        </div>
        <?php
        $seksi = [
          ['Seksi Keamanan', 'Budi Santoso', '0817-xxxx-3456', 'A/12'],
          ['Seksi Kebersihan', 'Maya Sari', '0858-xxxx-7890', 'C/19'],
          ['Seksi Sosial', 'Arif Rahman', '0821-xxxx-2345', 'D/07'],
          ['Seksi Pembangunan', 'Riko Pratama', '0878-xxxx-6789', 'D/15'],
          ['Seksi Pemuda', 'Lina Marlina', '0822-xxxx-0123', 'B/23'],
          ['Seksi Kesehatan', 'Nur Hidayah', '0812-xxxx-4567', 'B/11'],
        ];
        foreach ($seksi as $s): ?>
        <div style="padding:16px 20px;border-bottom:1px solid var(--border-color);display:flex;align-items:center;gap:12px;flex-wrap:wrap;">
          <div style="min-width:140px;">
            <input type="text" class="form-control" value="<?= $s[0] ?>" style="font-weight:600;">
          </div>
          <div style="flex:1;min-width:150px;">
            <input type="text" class="form-control" value="<?= $s[1] ?>" placeholder="Nama">
          </div>
          <div style="min-width:140px;">
            <input type="tel" class="form-control" value="<?= $s[2] ?>" placeholder="No. HP">
          </div>
          <div style="min-width:70px;">
            <input type="text" class="form-control" value="<?= $s[3] ?>" placeholder="Rumah">
          </div>
          <button class="btn btn-icon btn-sm btn-secondary" style="color:var(--danger);"><i data-lucide="trash-2" style="width:14px;height:14px;"></i></button>
        </div>
        <?php endforeach; ?>
      </div>
    </main>
  </div>
</div>
<?php include 'includes/footer.php'; ?>
