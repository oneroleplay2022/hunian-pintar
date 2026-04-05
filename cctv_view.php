<?php $pageTitle = 'CCTV Fullscreen'; ?>
<?php include 'includes/header.php'; ?>
<div class="app-layout">
  <?php include 'includes/sidebar.php'; ?>
  <div class="main-wrapper">
    <?php include 'includes/topbar.php'; ?>
    <main class="main-content">
      <div class="page-header">
        <div><h1>CAM-01 — Gerbang Utama</h1>
          <div class="breadcrumb"><a href="index.php">Dashboard</a><span class="separator">/</span><a href="cctv.php">CCTV</a><span class="separator">/</span><span>CAM-01</span></div>
        </div>
        <div style="display:flex;gap:10px;">
          <a href="cctv.php" class="btn btn-secondary btn-sm">← Grid View</a>
          <a href="cctv_playback.php" class="btn btn-outline btn-sm">📼 Playback</a>
          <span class="badge badge-success" style="display:flex;align-items:center;gap:4px;">
            <span style="width:8px;height:8px;border-radius:50%;background:currentColor;animation:pulse-dot 1.5s infinite;"></span> LIVE
          </span>
        </div>
      </div>

      <div class="grid-2" style="gap:24px;">
        <div style="grid-column:span 2;">
          <!-- Fullscreen View -->
          <div class="card" style="overflow:hidden;position:relative;">
            <div style="aspect-ratio:16/9;background:linear-gradient(135deg,#0a0e1a,#141b2d);display:flex;align-items:center;justify-content:center;">
              <div style="text-align:center;color:rgba(255,255,255,0.4);">
                <i data-lucide="video" style="width:64px;height:64px;margin-bottom:16px;"></i>
                <div style="font-size:1.3rem;">Live Feed — Gerbang Utama</div>
                <div id="cctvClock" style="font-size:2rem;font-family:monospace;margin-top:8px;color:rgba(255,255,255,0.6);"></div>
              </div>
            </div>
            <!-- Controls -->
            <div style="display:flex;justify-content:space-between;align-items:center;padding:14px 20px;background:rgba(15,23,42,0.95);">
              <div style="display:flex;align-items:center;gap:12px;color:white;font-size:0.88rem;">
                <span style="font-weight:600;">CAM-01</span>
                <span class="text-muted">|</span>
                <span>Gerbang Utama</span>
                <span class="text-muted">|</span>
                <span>1080p</span>
              </div>
              <div style="display:flex;gap:8px;">
                <button class="btn btn-sm" style="background:rgba(255,255,255,0.1);color:white;border:none;" title="Screenshot">📸</button>
                <button class="btn btn-sm" style="background:rgba(255,255,255,0.1);color:white;border:none;" title="Record">⏺️</button>
                <button class="btn btn-sm" style="background:rgba(255,255,255,0.1);color:white;border:none;" title="Zoom In">🔍</button>
                <button class="btn btn-sm" style="background:rgba(239,68,68,0.8);color:white;border:none;" title="REC">● REC</button>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Camera Info -->
      <div class="grid-2" style="gap:24px;margin-top:24px;">
        <div class="card" style="padding:20px;">
          <h4 style="margin-bottom:12px;">📊 Info Kamera</h4>
          <div style="display:flex;flex-direction:column;gap:10px;font-size:0.88rem;">
            <div style="display:flex;justify-content:space-between;"><span class="text-muted">ID</span><strong>CAM-01</strong></div>
            <div style="display:flex;justify-content:space-between;"><span class="text-muted">Merk / Model</span><strong>Hikvision DS-2CD2143</strong></div>
            <div style="display:flex;justify-content:space-between;"><span class="text-muted">Resolusi</span><strong>1080p (1920x1080)</strong></div>
            <div style="display:flex;justify-content:space-between;"><span class="text-muted">Uptime</span><strong>30 hari</strong></div>
            <div style="display:flex;justify-content:space-between;"><span class="text-muted">IP Address</span><strong style="font-family:monospace;">192.168.1.101</strong></div>
            <div style="display:flex;justify-content:space-between;"><span class="text-muted">Status</span><span class="badge badge-success">Online</span></div>
          </div>
        </div>

        <div class="card" style="padding:20px;">
          <h4 style="margin-bottom:12px;">💾 Penyimpanan</h4>
          <div style="display:flex;flex-direction:column;gap:10px;font-size:0.88rem;">
            <div style="display:flex;justify-content:space-between;"><span class="text-muted">Terpakai</span><strong>850 GB / 1 TB</strong></div>
            <div style="margin-bottom:8px;">
              <div class="progress-bar" style="height:10px;"><div class="progress-fill" style="width:85%;background:var(--warning);"></div></div>
            </div>
            <div style="display:flex;justify-content:space-between;"><span class="text-muted">Retensi Rekaman</span><strong>30 hari</strong></div>
            <div style="display:flex;justify-content:space-between;"><span class="text-muted">Format</span><strong>H.265 / HEVC</strong></div>
          </div>
        </div>
      </div>
    </main>
  </div>
</div>
<?php
$extraScripts = '<script>
  function updateClock() {
    const el = document.getElementById("cctvClock");
    if (el) { el.textContent = new Date().toLocaleTimeString("id-ID"); }
  }
  setInterval(updateClock, 1000); updateClock();
</script>';
?>
<?php include 'includes/footer.php'; ?>
