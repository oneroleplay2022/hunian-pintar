<?php $pageTitle = 'Keamanan & Darurat'; ?>
<?php include 'includes/header.php'; ?>

<div class="app-layout">
  <?php include 'includes/sidebar.php'; ?>

  <div class="main-wrapper">
    <?php include 'includes/topbar.php'; ?>

    <main class="main-content">
      <div class="page-header">
        <div>
          <h1>Keamanan & Darurat</h1>
          <div class="breadcrumb">
            <a href="index.php">Dashboard</a>
            <span class="separator">/</span>
            <span>Keamanan</span>
          </div>
        </div>
      </div>

      <!-- Panic Button Section -->
      <div class="card card-glass animate-fadeIn" style="margin-bottom:24px; text-align:center; padding:40px;">
        <h3 style="margin-bottom:8px;">🚨 Tombol Darurat</h3>
        <p class="text-muted" style="margin-bottom:24px;font-size:0.9rem;">Tekan tombol di bawah untuk mengirim notifikasi darurat ke pos keamanan dan warga sekitar</p>
        <div style="display:flex;justify-content:center;">
          <button class="panic-btn" onclick="triggerPanic()">
            <span style="font-size:2.5rem;">🆘</span>
            <span>DARURAT</span>
          </button>
        </div>
        <p class="text-muted" style="margin-top:16px;font-size:0.78rem;">Gunakan hanya untuk keadaan darurat yang sesungguhnya</p>
      </div>

      <!-- Stats -->
      <div class="stats-grid">
        <div class="stat-card">
          <div class="stat-icon red"><i data-lucide="alert-triangle"></i></div>
          <div class="stat-info">
            <div class="stat-label">Alert Bulan Ini</div>
            <div class="stat-value">2</div>
          </div>
        </div>
        <div class="stat-card">
          <div class="stat-icon green"><i data-lucide="shield-check"></i></div>
          <div class="stat-info">
            <div class="stat-label">Ronda Aktif</div>
            <div class="stat-value">4</div>
          </div>
        </div>
        <div class="stat-card">
          <div class="stat-icon blue"><i data-lucide="scan-eye"></i></div>
          <div class="stat-info">
            <div class="stat-label">CCTV Aktif</div>
            <div class="stat-value">12</div>
          </div>
        </div>
        <div class="stat-card">
          <div class="stat-icon yellow"><i data-lucide="user-check"></i></div>
          <div class="stat-info">
            <div class="stat-label">Petugas Jaga</div>
            <div class="stat-value">3</div>
          </div>
        </div>
      </div>

      <div class="grid-2" style="margin-bottom:24px;">
        <!-- Ronda Log -->
        <div class="card">
          <div class="card-header">
            <h3 class="card-title">Log Ronda Malam</h3>
            <span class="badge badge-success">Live</span>
          </div>
          <div class="timeline">
            <?php
            $rondaLog = [
              ['23:45', 'Checkpoint Gerbang Utama', 'Satpam Andi', 'completed'],
              ['23:15', 'Checkpoint Blok C', 'Satpam Andi', 'completed'],
              ['22:50', 'Checkpoint Taman Blok B', 'Satpam Budi', 'completed'],
              ['22:30', 'Checkpoint Pos Belakang', 'Satpam Budi', 'completed'],
              ['22:00', 'Mulai Shift Malam', 'Satpam Andi & Budi', 'completed'],
            ];
            foreach ($rondaLog as $r): ?>
            <div class="timeline-item <?= $r[3] ?>">
              <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:4px;">
                <strong style="font-size:0.9rem;"><?= $r[1] ?></strong>
                <span class="text-muted" style="font-size:0.8rem;"><?= $r[0] ?></span>
              </div>
              <div style="font-size:0.82rem;color:var(--text-secondary);">
                <i data-lucide="user" style="width:12px;height:12px;display:inline;vertical-align:middle;"></i> <?= $r[2] ?>
              </div>
            </div>
            <?php endforeach; ?>
          </div>
        </div>

        <!-- Alert History -->
        <div class="card">
          <div class="card-header">
            <h3 class="card-title">Riwayat Alert</h3>
          </div>
          <div style="display:flex;flex-direction:column;gap:12px;">
            <?php
            $alerts = [
              ['Panic Button Aktif', 'Rumah A/15 - Bapak Hasan', '25 Mar 2026 21:30', 'Ditangani', 'success', 'Tawuran remaja di depan rumah, satpam telah menangani'],
              ['Panic Button Aktif', 'Rumah C/04 - Ibu Mira', '18 Mar 2026 03:15', 'False Alarm', 'warning', 'Alarm palsu - kucing masuk rumah'],
              ['Early Warning', 'Potensi Banjir', '10 Mar 2026 16:00', 'Ditangani', 'success', 'Hujan lebat, saluran air telah dicek satpam'],
              ['Panic Button Aktif', 'Rumah B/20 - Bapak Toni', '5 Mar 2026 22:45', 'Ditangani', 'success', 'Percobaan pencurian motor, pelaku kabur'],
            ];
            foreach ($alerts as $a): ?>
            <div style="padding:16px;background:var(--bg-input);border-radius:var(--radius-md);border-left:3px solid <?= $a[4] == 'success' ? 'var(--success)' : 'var(--warning)' ?>;">
              <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:6px;">
                <div style="display:flex;align-items:center;gap:8px;">
                  <span style="font-size:1.1rem;">🚨</span>
                  <strong style="font-size:0.9rem;"><?= $a[0] ?></strong>
                  <span class="badge badge-<?= $a[4] ?>"><?= $a[3] ?></span>
                </div>
                <span class="text-muted" style="font-size:0.78rem;"><?= $a[2] ?></span>
              </div>
              <div style="font-size:0.85rem;color:var(--text-secondary);margin-bottom:4px;"><?= $a[1] ?></div>
              <div style="font-size:0.8rem;color:var(--text-muted);"><?= $a[5] ?></div>
            </div>
            <?php endforeach; ?>
          </div>
        </div>
      </div>

      <!-- Security Posts -->
      <div class="card">
        <div class="card-header">
          <h3 class="card-title">Status Pos Keamanan</h3>
        </div>
        <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(280px,1fr));gap:16px;">
          <?php
          $posts = [
            ['Pos Gerbang Utama', 'Satpam Andi', 'Aktif', 'success', '🏢'],
            ['Pos Gerbang Belakang', 'Satpam Budi', 'Aktif', 'success', '🏠'],
            ['Pos Taman Blok B', 'Satpam Calvin', 'Aktif', 'success', '🌳'],
          ];
          foreach ($posts as $p): ?>
          <div style="padding:20px;background:var(--bg-input);border-radius:var(--radius-md);display:flex;align-items:center;gap:14px;">
            <div style="width:48px;height:48px;border-radius:var(--radius-md);background:rgba(16,185,129,0.1);display:flex;align-items:center;justify-content:center;font-size:1.5rem;"><?= $p[4] ?></div>
            <div style="flex:1;">
              <div style="font-weight:600;font-size:0.92rem;margin-bottom:2px;"><?= $p[0] ?></div>
              <div style="font-size:0.82rem;color:var(--text-secondary);"><?= $p[1] ?></div>
            </div>
            <span class="badge badge-<?= $p[3] ?>"><?= $p[2] ?></span>
          </div>
          <?php endforeach; ?>
        </div>
      </div>
    </main>
  </div>
</div>

<?php
$extraScripts = <<<'JS'
<script>
  function triggerPanic() {
    if (confirm('⚠️ Anda yakin ingin mengirim ALERT DARURAT?\n\nNotifikasi akan dikirim ke seluruh pos keamanan dan warga sekitar.')) {
      showToast('🚨 Alert darurat telah dikirim ke pos keamanan!', 'error');
    }
  }
</script>
JS;
?>

<?php include 'includes/footer.php'; ?>
