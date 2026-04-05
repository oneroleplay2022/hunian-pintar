<?php
require_once 'classes/Auth.php';
require_once 'classes/Database.php';

Auth::requireLogin();
$user = Auth::user();
$db = Database::getInstance();

$pageTitle = 'Notifikasi Saya';
include 'includes/header.php';

$allNotifications = $db->fetchAll("SELECT * FROM notifications WHERE user_id = ? ORDER BY created_at DESC", [$user['id']]);
?>

<div class="app-layout">
  <?php 
    if ($user['role'] === 'superadmin') {
        include 'includes/sidebar_saas.php';
    } else {
        include 'includes/sidebar.php';
    }
  ?>
  
  <div class="main-wrapper">
    <?php include 'includes/topbar.php'; ?>
    <main class="main-content">
      <div class="page-header">
        <h1>Bilah Pemberitahuan</h1>
        <a href="mark_read.php" class="btn btn-secondary btn-sm">Tandai Semua Sudah Dibaca</a>
      </div>
      
      <div class="card">
        <div style="padding:20px;">
          <?php if (empty($allNotifications)): ?>
            <div style="padding:100px 0; text-align:center; color:var(--text-muted);">
                <i data-lucide="bell-off" style="width:60px; height:60px; margin-bottom:15px; opacity:0.2;"></i>
                <h3>Tidak ada notifikasi</h3>
                <p>Seluruh aktivitas Anda akan muncul di sini.</p>
            </div>
          <?php else: ?>
            <div class="notif-list">
                <?php foreach($allNotifications as $n): ?>
                <a href="<?= $n['link'] ?: '#' ?>" class="notif-item <?= $n['is_read'] ? '' : 'unread' ?>" style="display:flex; gap:16px; padding:20px; text-decoration:none; border-bottom:1px solid var(--border-color); position:relative; transition: background 0.2s;">
                    <div class="notif-icon" style="width:45px; height:45px; border-radius:50%; background:rgba(16,185,129,0.1); display:flex; align-items:center; justify-content:center; color:var(--success);">
                        <i data-lucide="info"></i>
                    </div>
                    <div style="flex:1;">
                        <h4 style="margin:0 0 5px 0; color:var(--text-main);"><?= htmlspecialchars($n['title']) ?></h4>
                        <p style="margin:0; color:var(--text-muted); font-size:0.9rem; line-height:1.5;"><?= htmlspecialchars($n['message']) ?></p>
                        <small style="color:var(--text-muted); display:block; margin-top:8px;"><?= date('d M Y, H:i', strtotime($n['created_at'])) ?></small>
                    </div>
                    <?php if (!$n['is_read']): ?>
                        <div style="width:10px; height:10px; background:var(--primary); border-radius:50%; position:absolute; right:20px; top:25px;"></div>
                    <?php endif; ?>
                </a>
                <?php endforeach; ?>
            </div>
          <?php endif; ?>
        </div>
      </div>
    </main>
  </div>
</div>

<style>
.notif-item:hover { background:var(--bg-card); }
.notif-item.unread { background:rgba(16,185,129,0.02); }
</style>

<?php include 'includes/footer.php'; ?>
