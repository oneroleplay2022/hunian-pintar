<?php $pageTitle = 'Playback Rekaman'; ?>
<?php include 'includes/header.php'; ?>
<div class="app-layout">
  <?php include 'includes/sidebar.php'; ?>
  <div class="main-wrapper">
    <?php include 'includes/topbar.php'; ?>
    <main class="main-content">
      <div class="page-header">
        <div><h1>Playback Rekaman</h1>
          <div class="breadcrumb"><a href="index.php">Dashboard</a><span class="separator">/</span><a href="cctv.php">CCTV</a><span class="separator">/</span><span>Playback</span></div>
        </div>
        <a href="cctv.php" class="btn btn-secondary btn-sm">← Kembali</a>
      </div>

      <!-- Filter -->
      <div class="card" style="padding:20px;margin-bottom:20px;">
        <div style="display:flex;gap:16px;align-items:flex-end;flex-wrap:wrap;">
          <div class="form-group" style="margin:0;">
            <label class="form-label">Kamera</label>
            <select class="form-control" style="min-width:180px;">
              <option selected>CAM-01 — Gerbang Utama</option><option>CAM-02 — Gerbang Belakang</option>
              <option>CAM-03 — Taman Blok A</option><option>CAM-04 — Taman Blok B</option>
              <option>CAM-05 — Jalan Utama A</option>
            </select>
          </div>
          <div class="form-group" style="margin:0;">
            <label class="form-label">Tanggal</label>
            <input type="date" class="form-control" value="2026-03-24">
          </div>
          <div class="form-group" style="margin:0;">
            <label class="form-label">Jam Mulai</label>
            <input type="time" class="form-control" value="23:00">
          </div>
          <div class="form-group" style="margin:0;">
            <label class="form-label">Jam Selesai</label>
            <input type="time" class="form-control" value="23:59">
          </div>
          <button class="btn btn-primary btn-sm">🔍 Cari Rekaman</button>
        </div>
      </div>

      <!-- Player -->
      <div class="card" style="overflow:hidden;margin-bottom:20px;">
        <div style="aspect-ratio:16/9;background:linear-gradient(135deg,#0a0e1a,#141b2d);display:flex;align-items:center;justify-content:center;">
          <div style="text-align:center;color:rgba(255,255,255,0.4);">
            <i data-lucide="play-circle" style="width:64px;height:64px;margin-bottom:16px;"></i>
            <div style="font-size:1.2rem;">CAM-01 — 24 Maret 2026</div>
            <div style="font-family:monospace;font-size:1.5rem;margin-top:8px;color:rgba(255,255,255,0.5);">23:15:00</div>
          </div>
        </div>
        <!-- Timeline Bar -->
        <div style="padding:12px 20px;background:rgba(15,23,42,0.95);">
          <div style="display:flex;align-items:center;gap:12px;color:white;">
            <button class="btn btn-sm" style="background:rgba(255,255,255,0.1);color:white;border:none;">⏮</button>
            <button class="btn btn-sm" style="background:rgba(255,255,255,0.1);color:white;border:none;">⏪</button>
            <button class="btn btn-sm" style="background:var(--primary);color:white;border:none;">▶️</button>
            <button class="btn btn-sm" style="background:rgba(255,255,255,0.1);color:white;border:none;">⏩</button>
            <button class="btn btn-sm" style="background:rgba(255,255,255,0.1);color:white;border:none;">⏭</button>
            <div style="flex:1;height:6px;background:rgba(255,255,255,0.1);border-radius:3px;position:relative;cursor:pointer;">
              <div style="width:35%;height:100%;background:var(--primary);border-radius:3px;"></div>
              <div style="position:absolute;top:-3px;left:35%;width:12px;height:12px;background:white;border-radius:50%;"></div>
            </div>
            <span style="font-size:0.82rem;font-family:monospace;min-width:80px;">23:15 / 23:59</span>
            <select style="background:rgba(255,255,255,0.1);color:white;border:none;border-radius:4px;padding:4px 8px;font-size:0.82rem;">
              <option>1x</option><option>2x</option><option>4x</option><option>8x</option>
            </select>
            <button class="btn btn-sm" style="background:rgba(255,255,255,0.1);color:white;border:none;">📸</button>
            <button class="btn btn-sm" style="background:rgba(255,255,255,0.1);color:white;border:none;" onclick="showToast('Mengunduh klip...','info')">💾</button>
          </div>
        </div>
      </div>

      <!-- Event Markers -->
      <div class="card">
        <div class="card-header"><h3 class="card-title">🔖 Event Log — 24 Maret 2026</h3></div>
        <?php
        $events = [
          ['23:00', 'Kendaraan masuk', 'B 1234 ABC — Terdeteksi plate', 'info'],
          ['23:10', 'Kendaraan keluar', 'B 8765 XYZ — Tamu', 'neutral'],
          ['23:15', '🚨 Motion alert', 'Gerakan terdeteksi di area pagar samping', 'danger'],
          ['23:18', 'Petugas tiba', 'Satpam Heri merespons alert', 'success'],
          ['23:30', 'Situasi aman', 'Alert closed by Admin', 'success'],
        ];
        foreach ($events as $e): ?>
        <div style="padding:12px 20px;border-bottom:1px solid var(--border-color);display:flex;align-items:center;gap:12px;cursor:pointer;" onclick="showToast('Melompat ke <?= $e[0] ?>','info')">
          <span style="font-family:monospace;font-weight:700;min-width:50px;color:var(--<?= $e[3] == 'neutral' ? 'text-muted' : $e[3] ?>);"><?= $e[0] ?></span>
          <div style="flex:1;">
            <strong style="font-size:0.88rem;"><?= $e[1] ?></strong>
            <div class="text-muted" style="font-size:0.78rem;"><?= $e[2] ?></div>
          </div>
          <button class="btn btn-sm btn-outline">▶️</button>
        </div>
        <?php endforeach; ?>
      </div>
    </main>
  </div>
</div>
<?php include 'includes/footer.php'; ?>
