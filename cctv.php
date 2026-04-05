<?php $pageTitle = 'Monitor CCTV'; ?>
<?php include 'includes/header.php'; ?>

<div class="app-layout">
  <?php include 'includes/sidebar.php'; ?>

  <div class="main-wrapper">
    <?php include 'includes/topbar.php'; ?>

    <main class="main-content">
      <div class="page-header">
        <div>
          <h1>Monitor CCTV</h1>
          <div class="breadcrumb">
            <a href="index.php">Dashboard</a>
            <span class="separator">/</span>
            <span>Keamanan</span>
            <span class="separator">/</span>
            <span>CCTV</span>
          </div>
        </div>
        <div style="display:flex;gap:10px;align-items:center;">
          <span class="badge badge-success" style="padding:6px 14px;">● LIVE</span>
          <select class="form-control" style="width:auto;padding:6px 28px 6px 12px;">
            <option>Grid 2x2</option>
            <option>Grid 3x2</option>
            <option>Single View</option>
          </select>
        </div>
      </div>

      <!-- CCTV Grid -->
      <div style="display:grid;grid-template-columns:repeat(2,1fr);gap:12px;margin-bottom:24px;">
        <?php
        $cameras = [
          ['CAM-01', 'Gerbang Utama', 'Online', 'success', '1080p'],
          ['CAM-02', 'Gerbang Belakang', 'Online', 'success', '1080p'],
          ['CAM-03', 'Taman Blok A', 'Online', 'success', '720p'],
          ['CAM-04', 'Taman Blok B', 'Online', 'success', '720p'],
        ];
        foreach ($cameras as $cam): ?>
        <div class="card cctv-card" style="padding:0;overflow:hidden;">
          <!-- Video Placeholder -->
          <div style="aspect-ratio:16/9;background:linear-gradient(135deg,#0a0f1a,#131b2e);display:flex;flex-direction:column;align-items:center;justify-content:center;position:relative;">
            <!-- Simulated CCTV overlay -->
            <div style="position:absolute;top:0;left:0;right:0;padding:8px 12px;display:flex;align-items:center;justify-content:space-between;background:linear-gradient(180deg,rgba(0,0,0,0.6),transparent);z-index:2;">
              <div style="display:flex;align-items:center;gap:6px;">
                <span style="width:8px;height:8px;border-radius:50%;background:<?= $cam[3] == 'success' ? '#10b981' : '#ef4444' ?>;display:inline-block;animation:pulse-dot 2s infinite;"></span>
                <span style="font-size:0.75rem;font-weight:600;color:white;"><?= $cam[0] ?></span>
              </div>
              <span style="font-size:0.7rem;color:rgba(255,255,255,0.7);"><?= $cam[4] ?></span>
            </div>
            
            <div style="text-align:center;z-index:1;">
              <div style="margin-bottom:12px;">
                <i data-lucide="video" style="width:48px;height:48px;color:rgba(255,255,255,0.15);"></i>
              </div>
              <div style="font-size:0.82rem;color:rgba(255,255,255,0.25);">Live Feed — <?= $cam[1] ?></div>
              <div style="font-size:2.2rem;font-weight:300;letter-spacing:2px;font-family:monospace;color:rgba(255,255,255,0.12);margin-top:8px;" id="cctv-time-<?= $cam[0] ?>">
                <?= date('H:i:s') ?>
              </div>
            </div>

            <div style="position:absolute;bottom:0;left:0;right:0;padding:8px 12px;display:flex;align-items:center;justify-content:space-between;background:linear-gradient(0deg,rgba(0,0,0,0.6),transparent);z-index:2;">
              <span style="font-size:0.78rem;color:rgba(255,255,255,0.8);font-weight:600;"><?= $cam[1] ?></span>
              <div style="display:flex;gap:8px;">
                <button style="background:rgba(255,255,255,0.15);border:none;border-radius:4px;color:white;padding:4px 8px;cursor:pointer;font-size:0.7rem;" title="Fullscreen">⛶</button>
                <button style="background:rgba(255,255,255,0.15);border:none;border-radius:4px;color:white;padding:4px 8px;cursor:pointer;font-size:0.7rem;" title="Snapshot">📸</button>
                <button style="background:rgba(239,68,68,0.8);border:none;border-radius:4px;color:white;padding:4px 8px;cursor:pointer;font-size:0.7rem;" title="Record">● REC</button>
              </div>
            </div>
          </div>
        </div>
        <?php endforeach; ?>
      </div>

      <!-- Camera Status List -->
      <div class="card" style="margin-bottom:24px;">
        <div class="card-header">
          <h3 class="card-title">Status Seluruh Kamera</h3>
          <span class="text-muted" style="font-size:0.85rem;">12 kamera terpasang</span>
        </div>
        <div class="table-container">
          <table class="table">
            <thead>
              <tr>
                <th>ID</th>
                <th>Lokasi</th>
                <th>Resolusi</th>
                <th>Penyimpanan</th>
                <th>Uptime</th>
                <th>Status</th>
                <th>Aksi</th>
              </tr>
            </thead>
            <tbody>
              <?php
              $allCams = [
                ['CAM-01', 'Gerbang Utama', '1080p', '85%', '30 hari', 'Online', 'success'],
                ['CAM-02', 'Gerbang Belakang', '1080p', '85%', '30 hari', 'Online', 'success'],
                ['CAM-03', 'Taman Blok A', '720p', '72%', '28 hari', 'Online', 'success'],
                ['CAM-04', 'Taman Blok B', '720p', '72%', '28 hari', 'Online', 'success'],
                ['CAM-05', 'Jalan Utama Blok A', '1080p', '90%', '30 hari', 'Online', 'success'],
                ['CAM-06', 'Jalan Utama Blok B', '1080p', '88%', '30 hari', 'Online', 'success'],
                ['CAM-07', 'Jalan Utama Blok C', '720p', '65%', '25 hari', 'Online', 'success'],
                ['CAM-08', 'Jalan Utama Blok D', '720p', '70%', '25 hari', 'Online', 'success'],
                ['CAM-09', 'Pos Jaga Depan', '1080p', '92%', '30 hari', 'Online', 'success'],
                ['CAM-10', 'Pos Jaga Belakang', '1080p', '90%', '30 hari', 'Online', 'success'],
                ['CAM-11', 'Area Parkir Tamu', '720p', '55%', '20 hari', 'Offline', 'danger'],
                ['CAM-12', 'Musholla', '720p', '60%', '22 hari', 'Online', 'success'],
              ];
              foreach ($allCams as $c): ?>
              <tr>
                <td><strong><?= $c[0] ?></strong></td>
                <td><?= $c[1] ?></td>
                <td><?= $c[2] ?></td>
                <td>
                  <div style="display:flex;align-items:center;gap:8px;">
                    <div class="progress-bar" style="width:80px;">
                      <div class="progress-fill" style="width:<?= $c[3] ?>;"></div>
                    </div>
                    <span style="font-size:0.8rem;"><?= $c[3] ?></span>
                  </div>
                </td>
                <td><?= $c[4] ?></td>
                <td><span class="badge badge-<?= $c[6] ?>"><?= $c[5] ?></span></td>
                <td>
                  <div style="display:flex;gap:4px;">
                    <a href="cctv_view.php" class="btn btn-sm btn-secondary" style="padding:4px 8px;"><i data-lucide="eye" style="width:14px;height:14px;"></i></a>
                    <button class="btn btn-sm btn-secondary" style="padding:4px 8px;"><i data-lucide="film" style="width:14px;height:14px;"></i></button>
                  </div>
                </td>
              </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        </div>
      </div>

      <!-- Storage Info -->
      <div class="card">
        <div class="card-header">
          <h3 class="card-title">💾 Penyimpanan Rekaman</h3>
        </div>
        <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(200px,1fr));gap:16px;">
          <div style="text-align:center;padding:20px;background:var(--bg-input);border-radius:var(--radius-md);">
            <div style="font-size:2rem;font-weight:800;margin-bottom:4px;">1.2 TB</div>
            <div class="text-muted" style="font-size:0.85rem;">Total Digunakan</div>
            <div class="progress-bar" style="margin-top:8px;">
              <div class="progress-fill" style="width:60%;"></div>
            </div>
            <div class="text-muted" style="font-size:0.78rem;margin-top:4px;">dari 2 TB</div>
          </div>
          <div style="text-align:center;padding:20px;background:var(--bg-input);border-radius:var(--radius-md);">
            <div style="font-size:2rem;font-weight:800;margin-bottom:4px;">30</div>
            <div class="text-muted" style="font-size:0.85rem;">Hari Retensi</div>
          </div>
          <div style="text-align:center;padding:20px;background:var(--bg-input);border-radius:var(--radius-md);">
            <div style="font-size:2rem;font-weight:800;margin-bottom:4px;">11/12</div>
            <div class="text-muted" style="font-size:0.85rem;">Kamera Online</div>
          </div>
        </div>
      </div>
    </main>
  </div>
</div>

<?php
$extraScripts = <<<'JS'
<script>
  // Simulate CCTV clock
  setInterval(() => {
    const now = new Date();
    const fmt = now.toLocaleTimeString('id-ID', { hour12: false });
    document.querySelectorAll('[id^="cctv-time-"]').forEach(el => el.textContent = fmt);
  }, 1000);
</script>
JS;
?>

<?php include 'includes/footer.php'; ?>
