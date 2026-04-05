<?php $pageTitle = 'Statistik Warga'; ?>
<?php include 'includes/header.php'; ?>

<div class="app-layout">
  <?php include 'includes/sidebar.php'; ?>

  <div class="main-wrapper">
    <?php include 'includes/topbar.php'; ?>

    <main class="main-content">
      <div class="page-header">
        <div>
          <h1>Statistik Kependudukan</h1>
          <div class="breadcrumb">
            <a href="index.php">Dashboard</a>
            <span class="separator">/</span>
            <span>Kependudukan</span>
            <span class="separator">/</span>
            <span>Statistik</span>
          </div>
        </div>
        <button class="btn btn-secondary btn-sm"><i data-lucide="download" style="width:16px;height:16px;"></i> Download Laporan</button>
      </div>

      <!-- Summary Stats -->
      <div class="stats-grid">
        <div class="stat-card animate-fadeIn stagger-1">
          <div class="stat-icon blue"><i data-lucide="users"></i></div>
          <div class="stat-info">
            <div class="stat-label">Total Populasi</div>
            <div class="stat-value">1,247</div>
          </div>
        </div>
        <div class="stat-card animate-fadeIn stagger-2">
          <div class="stat-icon green"><i data-lucide="home"></i></div>
          <div class="stat-info">
            <div class="stat-label">Total KK</div>
            <div class="stat-value">400</div>
          </div>
        </div>
        <div class="stat-card animate-fadeIn stagger-3">
          <div class="stat-icon purple"><i data-lucide="baby"></i></div>
          <div class="stat-info">
            <div class="stat-label">Balita (0-5)</div>
            <div class="stat-value">87</div>
          </div>
        </div>
        <div class="stat-card animate-fadeIn stagger-4">
          <div class="stat-icon yellow"><i data-lucide="heart-handshake"></i></div>
          <div class="stat-info">
            <div class="stat-label">Lansia (60+)</div>
            <div class="stat-value">124</div>
          </div>
        </div>
      </div>

      <!-- Charts Row 1 -->
      <div class="grid-2" style="margin-bottom:24px;">
        <div class="card">
          <div class="card-header">
            <h3 class="card-title">Distribusi Usia</h3>
          </div>
          <div class="chart-container">
            <canvas id="ageChart"></canvas>
          </div>
        </div>
        <div class="card">
          <div class="card-header">
            <h3 class="card-title">Jenis Kelamin</h3>
          </div>
          <div style="display:flex;align-items:center;gap:32px;padding:20px 0;">
            <div style="width:180px;height:180px;">
              <canvas id="genderChart"></canvas>
            </div>
            <div>
              <div style="margin-bottom:16px;">
                <div style="display:flex;align-items:center;gap:8px;margin-bottom:4px;">
                  <span style="width:14px;height:14px;border-radius:4px;background:#6366f1;display:inline-block;"></span>
                  <span style="font-size:0.9rem;">Laki-laki</span>
                </div>
                <span style="font-size:1.5rem;font-weight:700;">642</span>
                <span class="text-muted" style="font-size:0.85rem;"> (51.5%)</span>
              </div>
              <div>
                <div style="display:flex;align-items:center;gap:8px;margin-bottom:4px;">
                  <span style="width:14px;height:14px;border-radius:4px;background:#ec4899;display:inline-block;"></span>
                  <span style="font-size:0.9rem;">Perempuan</span>
                </div>
                <span style="font-size:1.5rem;font-weight:700;">605</span>
                <span class="text-muted" style="font-size:0.85rem;"> (48.5%)</span>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Charts Row 2 -->
      <div class="grid-2" style="margin-bottom:24px;">
        <div class="card">
          <div class="card-header">
            <h3 class="card-title">Status Domisili</h3>
          </div>
          <div class="chart-container" style="height:250px;">
            <canvas id="domicileChart"></canvas>
          </div>
        </div>
        <div class="card">
          <div class="card-header">
            <h3 class="card-title">Profesi Warga</h3>
          </div>
          <div class="chart-container" style="height:250px;">
            <canvas id="professionChart"></canvas>
          </div>
        </div>
      </div>

      <!-- Additional Stats -->
      <div class="grid-3">
        <div class="card text-center" style="padding:32px 20px;">
          <div style="font-size:2.5rem;margin-bottom:8px;">🗳️</div>
          <div style="font-size:1.8rem;font-weight:800;margin-bottom:4px;">892</div>
          <div class="text-muted">Berhak Pemilu (17+)</div>
        </div>
        <div class="card text-center" style="padding:32px 20px;">
          <div style="font-size:2.5rem;margin-bottom:8px;">🤲</div>
          <div style="font-size:1.8rem;font-weight:800;margin-bottom:4px;">45</div>
          <div class="text-muted">Warga Kurang Mampu</div>
        </div>
        <div class="card text-center" style="padding:32px 20px;">
          <div style="font-size:2.5rem;margin-bottom:8px;">🌏</div>
          <div style="font-size:1.8rem;font-weight:800;margin-bottom:4px;">8</div>
          <div class="text-muted">W.N.A</div>
        </div>
      </div>
    </main>
  </div>
</div>

<?php
$extraScripts = <<<'JS'
<script>
  // Age Distribution
  new Chart(document.getElementById('ageChart'), {
    type: 'bar',
    data: {
      labels: ['0-5', '6-12', '13-17', '18-25', '26-35', '36-45', '46-55', '56-65', '65+'],
      datasets: [{
        label: 'Jumlah',
        data: [87, 132, 98, 156, 214, 198, 156, 124, 82],
        backgroundColor: [
          '#06b6d4', '#3b82f6', '#6366f1', '#8b5cf6',
          '#a855f7', '#d946ef', '#ec4899', '#f43f5e', '#ef4444'
        ],
        borderRadius: 8,
        borderSkipped: false,
      }]
    },
    options: {
      responsive: true,
      maintainAspectRatio: false,
      plugins: { legend: { display: false } },
      scales: {
        y: { grid: { color: 'rgba(148,163,184,0.08)' }, ticks: { color: '#64748b' } },
        x: { grid: { display: false }, ticks: { color: '#64748b' } }
      }
    }
  });

  // Gender
  new Chart(document.getElementById('genderChart'), {
    type: 'doughnut',
    data: {
      labels: ['Laki-laki', 'Perempuan'],
      datasets: [{
        data: [642, 605],
        backgroundColor: ['#6366f1', '#ec4899'],
        borderWidth: 0,
        cutout: '70%'
      }]
    },
    options: {
      responsive: true,
      maintainAspectRatio: true,
      plugins: { legend: { display: false } }
    }
  });

  // Domicile
  new Chart(document.getElementById('domicileChart'), {
    type: 'doughnut',
    data: {
      labels: ['Domisili Setempat', 'Domisili Luar', 'KK Luar'],
      datasets: [{
        data: [1100, 97, 50],
        backgroundColor: ['#10b981', '#f59e0b', '#6366f1'],
        borderWidth: 0,
        cutout: '65%'
      }]
    },
    options: {
      responsive: true,
      maintainAspectRatio: false,
      plugins: {
        legend: {
          position: 'bottom',
          labels: { color: '#94a3b8', font: { family: 'Inter', size: 12 }, padding: 16 }
        }
      }
    }
  });

  // Profession
  new Chart(document.getElementById('professionChart'), {
    type: 'bar',
    data: {
      labels: ['Karyawan', 'Wiraswasta', 'PNS', 'IRT', 'Pensiunan', 'Pelajar', 'Lainnya'],
      datasets: [{
        label: 'Jumlah',
        data: [320, 185, 98, 210, 124, 195, 115],
        backgroundColor: 'rgba(99, 102, 241, 0.7)',
        borderRadius: 6,
        borderSkipped: false,
      }]
    },
    options: {
      indexAxis: 'y',
      responsive: true,
      maintainAspectRatio: false,
      plugins: { legend: { display: false } },
      scales: {
        x: { grid: { color: 'rgba(148,163,184,0.08)' }, ticks: { color: '#64748b' } },
        y: { grid: { display: false }, ticks: { color: '#94a3b8' } }
      }
    }
  });
</script>
JS;
?>

<?php include 'includes/footer.php'; ?>
