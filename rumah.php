<?php 
require_once 'classes/Auth.php';
require_once 'classes/Database.php';
require_once 'classes/Helpers.php';

Auth::requireLogin();
$tenant_id = Auth::tenantId();
$db = Database::getInstance();

$pageTitle = 'Data Rumah'; 

// 1. Fetch Stats
$totalRumah = $db->fetchColumn("SELECT COUNT(*) FROM houses WHERE tenant_id = ?", [$tenant_id]);
$berpenghuni = $db->fetchColumn("SELECT COUNT(*) FROM houses WHERE tenant_id = ? AND status = 'occupied'", [$tenant_id]);
$kosong = $db->fetchColumn("SELECT COUNT(*) FROM houses WHERE tenant_id = ? AND status = 'empty'", [$tenant_id]);
$dikontrakkan = $db->fetchColumn("SELECT COUNT(*) FROM houses WHERE tenant_id = ? AND status = 'rented'", [$tenant_id]);

// 2. Fetch Blocks & Houses
$blocks = $db->fetchAll("SELECT * FROM blocks WHERE tenant_id = ? ORDER BY block_name", [$tenant_id]);
?>
<?php include 'includes/header.php'; ?>

<div class="app-layout">
  <?php include 'includes/sidebar.php'; ?>

  <div class="main-wrapper">
    <?php include 'includes/topbar.php'; ?>

    <main class="main-content">
      <div class="page-header">
        <div>
          <h1>Data Rumah</h1>
          <div class="breadcrumb">
            <a href="index.php">Dashboard</a>
            <span class="separator">/</span>
            <span>Kependudukan</span>
            <span class="separator">/</span>
            <span>Data Rumah</span>
          </div>
        </div>
        <div style="display:flex;gap:10px;">
          <a href="block_list.php" class="btn btn-secondary btn-sm"><i data-lucide="layers" style="width:16px;height:16px;"></i> Manajemen Blok</a>
          <a href="rumah_form.php" class="btn btn-primary btn-sm"><i data-lucide="plus" style="width:16px;height:16px;"></i> Tambah Rumah</a>
        </div>
      </div>

      <!-- Stats -->
      <div class="stats-grid" style="grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));">
        <div class="stat-card animate-fadeIn stagger-1">
          <div class="stat-icon blue"><i data-lucide="home"></i></div>
          <div class="stat-info">
            <div class="stat-label">Total Rumah</div>
            <div class="stat-value"><?= number_format($totalRumah) ?></div>
          </div>
        </div>
        <div class="stat-card animate-fadeIn stagger-2">
          <div class="stat-icon green"><i data-lucide="check-circle"></i></div>
          <div class="stat-info">
            <div class="stat-label">Berpenghuni</div>
            <div class="stat-value"><?= number_format($berpenghuni) ?></div>
          </div>
        </div>
        <div class="stat-card animate-fadeIn stagger-3">
          <div class="stat-icon yellow"><i data-lucide="home"></i></div>
          <div class="stat-info">
            <div class="stat-label">Kosong</div>
            <div class="stat-value"><?= number_format($kosong) ?></div>
          </div>
        </div>
        <div class="stat-card animate-fadeIn stagger-4">
          <div class="stat-icon purple"><i data-lucide="key"></i></div>
          <div class="stat-info">
            <div class="stat-label">Dikontrakkan</div>
            <div class="stat-value"><?= number_format($dikontrakkan) ?></div>
          </div>
        </div>
      </div>

      <!-- Card Grid View -->
      <div id="gridView" style="margin-top:24px;">
        <?php if (empty($blocks)): ?>
            <div class="card" style="text-align:center; padding:60px;">
                <div class="text-muted" style="margin-bottom:15px;">Belum ada data blok atau rumah yang terdaftar.</div>
                <a href="rumah_form.php" class="btn btn-primary btn-sm">Mulai Tambah Data</a>
            </div>
        <?php endif; ?>

        <?php foreach ($blocks as $block): 
            $houses = $db->fetchAll("
                SELECT h.*, 
                (SELECT COUNT(*) FROM residents WHERE house_id = h.id AND deleted_at IS NULL) as resident_count,
                (SELECT full_name FROM residents WHERE house_id = h.id AND family_status = 'kepala_keluarga' AND deleted_at IS NULL LIMIT 1) as head_of_family
                FROM houses h 
                WHERE h.block_id = ? AND h.tenant_id = ? 
                ORDER BY h.house_number
            ", [$block['id'], $tenant_id]);
        ?>
        <div class="menu-label" style="padding-left:0; margin-top:20px; display:flex; justify-content:space-between; align-items:center;">
            <span>Blok <?= htmlspecialchars($block['block_name']) ?> — <?= count($houses) ?> Unit</span>
        </div>
        
        <div style="display:grid; grid-template-columns: repeat(auto-fill, minmax(220px, 1fr)); gap:16px; margin-bottom:32px;">
          <?php foreach ($houses as $h): 
             $statusLabel = Helpers::statusLabel($h['status']);
             $statusBadge = Helpers::statusBadge($h['status']);
             
             // Logic: Prioritaskan owner_name, jika dummy/kosong gunakan head_of_family
             $displayName = ($h['owner_name'] && $h['owner_name'] !== '-') ? $h['owner_name'] : ($h['head_of_family'] ?: 'Belum Ada Pemilik');
          ?>
          <a href="rumah_detail.php?id=<?= $h['id'] ?>" class="card animate-fadeIn" style="padding:16px; cursor:pointer; transition: all 0.2s; text-decoration:none; color:inherit; display:block; border-left: 4px solid var(--<?= $statusBadge ?>);">
            <div style="display:flex; align-items:flex-start; justify-content:space-between; margin-bottom:12px;">
              <div>
                <span style="font-size:0.75rem; color:var(--text-muted); display:block; text-transform:uppercase;">No. Rumah</span>
                <span style="font-size:1.3rem; font-weight:800;"><?= htmlspecialchars($h['house_number']) ?></span>
              </div>
              <span class="badge badge-<?= $statusBadge ?>" style="font-size:0.7rem;"><?= $statusLabel ?></span>
            </div>
            
            <div style="font-size:0.85rem; color:var(--text-secondary); margin-bottom:6px; white-space:nowrap; overflow:hidden; text-overflow:ellipsis;" title="<?= htmlspecialchars($displayName) ?>">
              <i data-lucide="user" style="width:14px; height:14px; display:inline; vertical-align:middle; margin-right:4px;"></i> 
              <?= htmlspecialchars($displayName) ?>
            </div>
            
            <div style="display:flex; justify-content:space-between; align-items:center; margin-top:12px; padding-top:10px; border-top:1px solid rgba(255,255,255,0.05);">
                <div style="font-size:0.8rem; color:var(--text-muted);">
                  <i data-lucide="users" style="width:14px; height:14px; display:inline; vertical-align:middle; margin-right:2px;"></i> <?= $h['resident_count'] ?> Warga
                </div>
                <div style="font-size:0.75rem; font-weight:600; color:var(--primary);">Detail →</div>
            </div>
          </a>
          <?php endforeach; ?>
        </div>
        <?php endforeach; ?>
      </div>
    </main>
  </div>
</div>
<?php include 'includes/footer.php'; ?>
