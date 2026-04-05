<?php
/**
 * Topbar component
 */
$user = Auth::user();
$db = Database::getInstance();

// 1. Fetch unread notifications for current user
$notifCount = $db->fetchColumn("SELECT COUNT(*) FROM notifications WHERE user_id = ? AND is_read = 0", [$user['id']]);
$notifs = $db->fetchAll("SELECT * FROM notifications WHERE user_id = ? ORDER BY created_at DESC LIMIT 5", [$user['id']]);

$initials = '';
if ($user) {
    $parts = explode(' ', $user['name']);
    $initials = strtoupper(substr($parts[0], 0, 1) . (isset($parts[1]) ? substr($parts[1], 0, 1) : ''));
}
$roleLabels = ['superadmin' => 'Superadmin', 'admin' => 'Admin', 'pengurus' => 'Pengurus', 'warga' => 'Warga', 'kolektor' => 'Kolektor', 'security' => 'Security'];
$roleLabel = $roleLabels[$user['role'] ?? 'warga'] ?? 'Warga';
?>
<header class="topbar" id="topbar">
  <div class="topbar-left">
    <button class="btn-sidebar-toggle" id="sidebarToggle" aria-label="Toggle Sidebar"><i data-lucide="menu"></i></button>
    <h1 class="topbar-title"><?= htmlspecialchars($pageTitle ?? 'Dashboard') ?></h1>
    <div class="topbar-search">
      <span class="search-icon"><i data-lucide="search"></i></span>
      <input type="text" placeholder="Cari warga, rumah, atau fitur..." id="globalSearch">
    </div>
  </div>
  <div class="topbar-right">
    <button class="topbar-btn" id="darkModeToggle" title="Toggle Theme">🌙</button>

    <!-- Notifications Container -->
    <div style="position:relative; display:flex; align-items:center;">
      <button class="topbar-btn" id="notifBtn" title="Notifikasi">
        <i data-lucide="bell"></i>
        <?php if ($notifCount > 0): ?>
          <span class="notif-dot" style="background:var(--danger); width:8px; height:8px; border-radius:50%; position:absolute; top:8px; right:8px; border:2px solid var(--white);"></span>
        <?php endif; ?>
      </button>
      
      <!-- Notifications Dropdown -->
      <div id="notifDropdown" style="display:none; position:absolute; top:100%; right:0; margin-top:12px; background:var(--bg-card); border:1px solid var(--border-color); border-radius:var(--radius-lg); width:320px; z-index:200; box-shadow:var(--shadow-lg); overflow:hidden;">
        <div style="padding:12px 16px; border-bottom:1px solid var(--border-color); display:flex; justify-content:space-between; align-items:center;">
          <h4 style="margin:0; font-size:0.9rem;">Notifikasi</h4>
          <?php if($notifCount > 0): ?>
            <a href="mark_read.php" style="font-size:0.75rem; color:var(--primary); text-decoration:none;">Tandai semua dibaca</a>
          <?php endif; ?>
        </div>
        <div style="max-height:300px; overflow-y:auto;">
          <?php if(empty($notifs)): ?>
            <div style="padding:40px 20px; text-align:center; color:var(--text-muted); font-size:0.85rem;">
               <i data-lucide="bell-off" style="width:30px; height:30px; margin-bottom:10px; opacity:0.3;"></i>
               <p>Belum ada notifikasi baru.</p>
            </div>
          <?php else: ?>
            <?php foreach($notifs as $n): ?>
            <a href="<?= $n['link'] ?: '#' ?>" style="display:block; padding:12px 16px; border-bottom:1px solid var(--border-color); text-decoration:none; transition:background 0.2s; background: <?= $n['is_read'] ? 'transparent' : 'rgba(16,185,129,0.03)' ?>;" onmouseover="this.style.background='var(--bg-hover)'" onmouseout="this.style.background='<?= $n['is_read'] ? 'transparent' : 'rgba(16,185,129,0.03)' ?>'">
              <div style="font-size:0.85rem; font-weight:700; color:var(--text-main); margin-bottom:4px;"><?= htmlspecialchars($n['title']) ?></div>
              <div style="font-size:0.75rem; color:var(--text-muted); line-height:1.4;"><?= htmlspecialchars($n['message']) ?></div>
              <div style="font-size:0.65rem; color:var(--text-muted); margin-top:6px;"><?= date('d M, H:i', strtotime($n['created_at'])) ?></div>
            </a>
            <?php endforeach; ?>
          <?php endif; ?>
        </div>
        <div style="padding:10px; text-align:center; border-top:1px solid var(--border-color); background:var(--bg-main);">
          <a href="notifications.php" style="font-size:0.8rem; font-weight:600; color:var(--text-muted); text-decoration:none;">Lihat Semua Notifikasi</a>
        </div>
      </div>
    </div>

    <!-- User Menu -->
    <div class="user-menu" id="userMenu" style="cursor:pointer; position:relative; margin-left:15px;">
      <?php if ($user['avatar'] && file_exists($user['avatar'])): ?>
        <img src="<?= $user['avatar'] ?>" class="user-avatar" style="object-fit:cover;">
      <?php else: ?>
        <div class="user-avatar"><?= $initials ?></div>
      <?php endif; ?>
      <div class="user-info"><span class="user-name"><?= htmlspecialchars($user['name'] ?? 'User') ?></span><span class="user-role"><?= $roleLabel ?></span></div>
      <div class="user-dropdown" id="userDropdown" style="display:none; position:absolute; top:100%; right:0; margin-top:8px; background:var(--bg-card); border:1px solid var(--border-color); border-radius:var(--radius-lg); padding:8px; min-width:180px; z-index:100; box-shadow:var(--shadow-lg);">
        <a href="profil.php" class="dropdown-item"><i data-lucide="user"></i> Profil Saya</a>
        <div style="height:1px; background:var(--border-color); margin:4px 0;"></div>
        <a href="logout.php" class="dropdown-item" style="color:var(--danger);"><i data-lucide="log-out"></i> Logout</a>
      </div>
    </div>
  </div>
</header>
<style>
.dropdown-item { display:flex; align-items:center; gap:8px; padding:10px 12px; border-radius:var(--radius-md); color:var(--text-primary); text-decoration:none; font-size:0.88rem; transition:background 0.2s; }
.dropdown-item:hover { background:var(--bg-hover); }
</style>
<script>
// Toggle User Dropdown
document.getElementById('userMenu').addEventListener('click', function(e) {
  e.stopPropagation();
  var dd = document.getElementById('userDropdown');
  var isVisible = dd.style.display === 'block';
  closeAllDropdowns();
  dd.style.display = isVisible ? 'none' : 'block';
});

// Toggle Notif Dropdown
document.getElementById('notifBtn').addEventListener('click', function(e) {
  e.stopPropagation();
  var dd = document.getElementById('notifDropdown');
  var isVisible = dd.style.display === 'block';
  closeAllDropdowns();
  dd.style.display = isVisible ? 'none' : 'block';
});

function closeAllDropdowns() {
    document.getElementById('userDropdown').style.display = 'none';
    document.getElementById('notifDropdown').style.display = 'none';
}

document.addEventListener('click', closeAllDropdowns);
</script>
