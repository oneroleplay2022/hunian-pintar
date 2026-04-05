<?php
/**
 * Sidebar navigation component
 * Menampilkan semua menu modul aplikasi
 */

$currentPage = basename($_SERVER['PHP_SELF']);
?>

<!-- Sidebar Overlay (Mobile) -->
<div class="sidebar-overlay" id="sidebarOverlay"></div>

<!-- Sidebar -->
<aside class="sidebar" id="sidebar">
  <div class="sidebar-brand">
    <div class="brand-icon">🏘️</div>
    <div>
      <div class="brand-text"><?= htmlspecialchars($GLOBAL_SETTINGS['app_name'] ?? 'WargaKu') ?></div>
      <div class="brand-sub">Transparansi Warga</div>
    </div>
  </div>

  <nav class="sidebar-menu">
    <!-- Menu Utama -->
    <div class="menu-label">Menu Utama</div>
    <a class="menu-item <?= $currentPage == 'index.php' ? 'active' : '' ?>" href="index.php">
      <span class="menu-icon"><i data-lucide="layout-dashboard"></i></span>
      <span>Dashboard</span>
    </a>
    <a class="menu-item <?= $currentPage == 'profil.php' ? 'active' : '' ?>" href="profil.php">
      <span class="menu-icon"><i data-lucide="landmark"></i></span>
      <span>Profil Lingkungan</span>
    </a>
    <a class="menu-item <?= $currentPage == 'berita.php' ? 'active' : '' ?>" href="berita.php">
      <span class="menu-icon"><i data-lucide="newspaper"></i></span>
      <span>Berita & Pengumuman</span>
    </a>
    <a class="menu-item <?= $currentPage == 'galeri.php' ? 'active' : '' ?>" href="galeri.php">
      <span class="menu-icon"><i data-lucide="images"></i></span>
      <span>Galeri Kegiatan</span>
    </a>
    <a class="menu-item <?= $currentPage == 'inventaris.php' ? 'active' : '' ?>" href="inventaris.php">
      <span class="menu-icon"><i data-lucide="package"></i></span>
      <span>Inventaris & Aset</span>
    </a>

    <!-- Kependudukan -->
    <div class="menu-label">Kependudukan</div>
    <a class="menu-item <?= $currentPage == 'warga.php' ? 'active' : '' ?>" href="warga.php">
      <span class="menu-icon"><i data-lucide="users"></i></span>
      <span>Data Warga</span>
    </a>
    <a class="menu-item <?= $currentPage == 'rumah.php' ? 'active' : '' ?>" href="rumah.php">
      <span class="menu-icon"><i data-lucide="home"></i></span>
      <span>Data Rumah</span>
    </a>
    <a class="menu-item <?= $currentPage == 'mutasi.php' ? 'active' : '' ?>" href="mutasi.php">
      <span class="menu-icon"><i data-lucide="arrow-left-right"></i></span>
      <span>Mutasi Warga</span>
    </a>
    <a class="menu-item <?= $currentPage == 'tamu.php' ? 'active' : '' ?>" href="tamu.php">
      <span class="menu-icon"><i data-lucide="user-check"></i></span>
      <span>Pendataan Tamu</span>
    </a>
    <a class="menu-item <?= $currentPage == 'kendaraan.php' ? 'active' : '' ?>" href="kendaraan.php">
      <span class="menu-icon"><i data-lucide="car"></i></span>
      <span>Data Kendaraan</span>
    </a>
    <a class="menu-item <?= $currentPage == 'usaha.php' ? 'active' : '' ?>" href="usaha.php">
      <span class="menu-icon"><i data-lucide="store"></i></span>
      <span>Usaha & Lapak</span>
    </a>
    <a class="menu-item <?= $currentPage == 'statistik.php' ? 'active' : '' ?>" href="statistik.php">
      <span class="menu-icon"><i data-lucide="bar-chart-3"></i></span>
      <span>Statistik Warga</span>
    </a>

    <!-- Keuangan -->
    <div class="menu-label">Keuangan</div>
    <a class="menu-item <?= $currentPage == 'iuran.php' ? 'active' : '' ?>" href="iuran.php">
      <span class="menu-icon"><i data-lucide="receipt"></i></span>
      <span>Iuran & Tagihan</span>
    </a>
    <a class="menu-item <?= $currentPage == 'pembayaran.php' ? 'active' : '' ?>" href="pembayaran.php">
      <span class="menu-icon"><i data-lucide="credit-card"></i></span>
      <span>Riwayat Pembayaran</span>
    </a>
    <a class="menu-item <?= $currentPage == 'kas.php' ? 'active' : '' ?>" href="kas.php">
      <span class="menu-icon"><i data-lucide="wallet"></i></span>
      <span>Kas & Transparansi</span>
    </a>

    <!-- Pelayanan -->
    <div class="menu-label">Pelayanan</div>
    <a class="menu-item <?= $currentPage == 'surat.php' ? 'active' : '' ?>" href="surat.php">
      <span class="menu-icon"><i data-lucide="file-text"></i></span>
      <span>E-Surat</span>
    </a>
    <a class="menu-item <?= $currentPage == 'perizinan.php' ? 'active' : '' ?>" href="perizinan.php">
      <span class="menu-icon"><i data-lucide="clipboard-check"></i></span>
      <span>Perizinan</span>
    </a>
    <a class="menu-item <?= $currentPage == 'pengaduan.php' ? 'active' : '' ?>" href="pengaduan.php">
      <span class="menu-icon"><i data-lucide="message-square"></i></span>
      <span>Pengaduan Warga</span>
    </a>
    <a class="menu-item <?= $currentPage == 'bansos.php' ? 'active' : '' ?>" href="bansos.php">
      <span class="menu-icon"><i data-lucide="heart-handshake"></i></span>
      <span>Bantuan Sosial</span>
    </a>

    <!-- Keamanan -->
    <div class="menu-label">Keamanan</div>
    <a class="menu-item <?= $currentPage == 'keamanan.php' ? 'active' : '' ?>" href="keamanan.php">
      <span class="menu-icon"><i data-lucide="shield-alert"></i></span>
      <span>Keamanan & Darurat</span>
    </a>
    <a class="menu-item <?= $currentPage == 'cctv.php' ? 'active' : '' ?>" href="cctv.php">
      <span class="menu-icon"><i data-lucide="cctv"></i></span>
      <span>Monitor CCTV</span>
    </a>

    <?php if (isset($currentUser['role']) && $currentUser['role'] === 'admin'): ?>
    <!-- Sistem <?= htmlspecialchars($GLOBAL_SETTINGS['app_name'] ?? 'Aplikasi') ?> -->
    <div class="menu-label">Sistem <?= htmlspecialchars($GLOBAL_SETTINGS['app_name'] ?? 'Aplikasi') ?></div>
    <a class="menu-item <?= $currentPage == 'saas_payment.php' ? 'active' : '' ?>" href="saas_payment.php" style="color:var(--warning);">
      <span class="menu-icon"><i data-lucide="credit-card"></i></span>
      <span>Status Langganan</span>
    </a>
    <?php endif; ?>
  </nav>
</aside>
