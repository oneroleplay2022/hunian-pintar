<?php 
require_once 'classes/Auth.php';
require_once 'classes/Database.php';
require_once 'classes/Helpers.php';

Auth::requireLogin();
$tenant_id = Auth::tenantId();
$db = Database::getInstance();

$pageTitle = 'Data Warga'; 

// 1. Handle Delete Action (Soft Delete)
if (isset($_POST['action']) && $_POST['action'] === 'delete_resident') {
    $resId = (int)$_POST['resident_id'];
    $db->update('residents', ['deleted_at' => date('Y-m-d H:i:s')], 'id = ? AND tenant_id = ?', [$resId, $tenant_id]);
    Helpers::flash('success', 'Data warga berhasil dihapus.');
    header("Location: warga.php");
    exit;
}

// 2. Filters & Search
$search = $_GET['search'] ?? '';
$filterBlock = $_GET['block'] ?? '';
$where = "r.tenant_id = ? AND r.deleted_at IS NULL";
$params = [$tenant_id];

if ($search) {
    $where .= " AND (r.full_name LIKE ? OR r.nik LIKE ?)";
    $params[] = "%$search%";
    $params[] = "%$search%";
}
if ($filterBlock) {
    $where .= " AND b.id = ?";
    $params[] = $filterBlock;
}

// 3. Get Stats (Gunakan LEFT JOIN agar warga tanpa rumah tetap terhitung)
$totalWarga = $db->fetchColumn("SELECT COUNT(*) FROM residents r LEFT JOIN houses h ON r.house_id = h.id LEFT JOIN blocks b ON h.block_id = b.id WHERE $where", $params);
$totalKK = $db->fetchColumn("SELECT COUNT(*) FROM residents r LEFT JOIN houses h ON r.house_id = h.id LEFT JOIN blocks b ON h.block_id = b.id WHERE $where AND r.family_status = 'kepala_keluarga'", $params);
$totalDomisili = $db->fetchColumn("SELECT COUNT(*) FROM residents r LEFT JOIN houses h ON r.house_id = h.id LEFT JOIN blocks b ON h.block_id = b.id WHERE $where AND r.domicile_status = 'domisili'", $params);
$totalLuar = $db->fetchColumn("SELECT COUNT(*) FROM residents r LEFT JOIN houses h ON r.house_id = h.id LEFT JOIN blocks b ON h.block_id = b.id WHERE $where AND r.domicile_status != 'domisili'", $params);

// 4. Get Blocks for Filter
$blocks = $db->fetchAll("SELECT id, block_name FROM blocks WHERE tenant_id = ? ORDER BY block_name", [$tenant_id]);
?>
<?php include 'includes/header.php'; ?>

<div class="app-layout">
  <?php include 'includes/sidebar.php'; ?>

  <div class="main-wrapper">
    <?php include 'includes/topbar.php'; ?>

    <main class="main-content">
      <div class="page-header">
        <div>
          <h1>Data Warga</h1>
          <div class="breadcrumb">
            <a href="index.php">Dashboard</a>
            <span class="separator">/</span>
            <span>Kependudukan</span>
            <span class="separator">/</span>
            <span>Data Warga</span>
          </div>
        </div>
        <div style="display: flex; gap: 10px;">
          <button class="btn btn-secondary btn-sm"><i data-lucide="download" style="width:16px;height:16px;"></i> Export</button>
          <a href="warga_form.php" class="btn btn-primary btn-sm"><i data-lucide="user-plus" style="width:16px;height:16px;"></i> Tambah Warga</a>
        </div>
      </div>

      <?php if ($msg = Helpers::getFlash('success')): ?>
      <div style="background:rgba(16,185,129,0.1); border:1px solid rgba(16,185,129,0.3); color:var(--success); padding:12px 16px; border-radius:var(--radius-md); margin-bottom:20px; font-size:0.9rem;">
        ✅ <?= $msg ?>
      </div>
      <?php endif; ?>

      <!-- Stats Row -->
      <div class="stats-grid" style="grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));">
        <div class="stat-card">
          <div class="stat-icon blue"><i data-lucide="users"></i></div>
          <div class="stat-info">
            <div class="stat-label">Total Warga</div>
            <div class="stat-value"><?= number_format($totalWarga) ?></div>
          </div>
        </div>
        <div class="stat-card">
          <div class="stat-icon green"><i data-lucide="home"></i></div>
          <div class="stat-info">
            <div class="stat-label">Kepala Keluarga</div>
            <div class="stat-value"><?= number_format($totalKK) ?></div>
          </div>
        </div>
        <div class="stat-card">
          <div class="stat-icon purple"><i data-lucide="user-check"></i></div>
          <div class="stat-info">
            <div class="stat-label">Warga Tetap</div>
            <div class="stat-value"><?= number_format($totalDomisili) ?></div>
          </div>
        </div>
        <div class="stat-card">
          <div class="stat-icon yellow"><i data-lucide="user-x"></i></div>
          <div class="stat-info">
            <div class="stat-label">Warga Kontrak/Lain</div>
            <div class="stat-value"><?= number_format($totalLuar) ?></div>
          </div>
        </div>
      </div>

      <!-- Filter Bar -->
      <div class="card">
        <form method="GET" class="filter-bar" style="margin-bottom:0;">
          <div class="filter-search" style="flex:1;">
            <span class="search-icon"><i data-lucide="search"></i></span>
            <input type="text" name="search" class="form-control" placeholder="Cari nama atau NIK..." value="<?= htmlspecialchars($search) ?>">
          </div>
          <select name="block" class="form-control" style="width:auto;" onchange="this.form.submit()">
            <option value="">Semua Blok</option>
            <?php foreach($blocks as $b): ?>
            <option value="<?= $b['id'] ?>" <?= $filterBlock == $b['id'] ? 'selected' : '' ?>><?= htmlspecialchars($b['block_name']) ?></option>
            <?php endforeach; ?>
          </select>
          <button type="submit" class="btn btn-secondary">Filter</button>
        </form>
      </div>

      <!-- Data Table -->
      <div class="card" style="margin-top:16px;">
        <div class="table-container">
          <table class="table">
            <thead>
              <tr>
                <th>No</th>
                <th>Nama Lengkap</th>
                <th>NIK</th>
                <th>Blok/Rumah</th>
                <th>L/P</th>
                <th>Usia</th>
                <th>Status</th>
                <th>HP</th>
                <th>Aksi</th>
              </tr>
            </thead>
            <tbody>
              <?php
              $wargaList = $db->fetchAll("
                SELECT r.*, h.house_number, b.block_name
                FROM residents r 
                LEFT JOIN houses h ON r.house_id = h.id 
                LEFT JOIN blocks b ON h.block_id = b.id 
                WHERE $where
                ORDER BY r.created_at DESC
              ", $params);

              if (empty($wargaList)): ?>
                <tr><td colspan="9" style="text-align:center; padding:40px; color:var(--text-muted);">Tidak ada data warga ditemukan.</td></tr>
              <?php endif;

              foreach ($wargaList as $i => $w): 
                  // Hitung Usia
                  $usia = '-';
                  if ($w['birth_date']) {
                      $birthDate = new DateTime($w['birth_date']);
                      $today = new DateTime('today');
                      $usia = $birthDate->diff($today)->y;
                  }
              ?>
              <tr>
                <td><?= $i + 1 ?></td>
                <td>
                    <strong><?= htmlspecialchars($w['full_name']) ?></strong>
                    <?php if(($w['family_status'] ?? '') === 'kepala_keluarga'): ?>
                        <span class="badge badge-info" style="font-size:0.65rem; padding:2px 6px; margin-left:5px;">KK</span>
                    <?php endif; ?>
                </td>
                <td style="font-family:monospace;font-size:0.82rem;"><?= htmlspecialchars($w['nik']) ?></td>
                <td><?= htmlspecialchars($w['block_name'] . '/' . $w['house_number']) ?></td>
                <td><?= ($w['gender'] ?? '') == 'L' ? 'L' : 'P' ?></td>
                <td><?= $usia ?> thn</td>
                <td>
                  <span class="badge badge-<?= Helpers::statusBadge($w['domicile_status'] ?? 'domisili') ?>">
                    <?= Helpers::statusLabel($w['domicile_status'] ?? 'domisili') ?>
                  </span>
                </td>
                <td><?= htmlspecialchars($w['phone'] ?? '-') ?></td>
                <td>
                  <div style="display:flex;gap:4px;">
                    <a href="warga_form.php?id=<?= $w['id'] ?>" class="btn btn-icon btn-sm btn-secondary" title="Edit"><i data-lucide="pencil" style="width:14px;height:14px;"></i></a>
                    <form method="POST" onsubmit="return confirm('Hapus warga ini? Data tidak akan hilang permanen namun tidak muncul di daftar.')" style="display:inline;">
                        <input type="hidden" name="action" value="delete_resident">
                        <input type="hidden" name="resident_id" value="<?= $w['id'] ?>">
                        <button type="submit" class="btn btn-icon btn-sm btn-secondary" style="color:var(--danger);"><i data-lucide="trash-2" style="width:14px;height:14px;"></i></button>
                    </form>
                  </div>
                </td>
              </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        </div>
      </div>

        <?php if (!empty($wargaList)): ?>
        <div style="display:flex;align-items:center;justify-content:space-between;margin-top:16px;flex-wrap:wrap;gap:12px;">
          <span class="text-muted" style="font-size:0.85rem;">Menampilkan <?= count($wargaList) ?> warga</span>
        </div>
        <?php endif; ?>
      </div>
    </main>
  </div>
</div>

<!-- Add Warga Modal -->
<div class="modal-overlay" id="addWargaModal">
  <div class="modal" style="max-width:640px;">
    <div class="modal-header">
      <h3 class="modal-title">Tambah Data Warga</h3>
      <button class="modal-close">✕</button>
    </div>
    <div class="modal-body">
      <div class="form-row">
        <div class="form-group">
          <label class="form-label">Nama Lengkap</label>
          <input type="text" class="form-control" placeholder="Nama sesuai KTP">
        </div>
        <div class="form-group">
          <label class="form-label">NIK</label>
          <input type="text" class="form-control" placeholder="16 digit NIK" maxlength="16">
        </div>
      </div>
      <div class="form-row">
        <div class="form-group">
          <label class="form-label">Jenis Kelamin</label>
          <select class="form-control">
            <option>Laki-laki</option>
            <option>Perempuan</option>
          </select>
        </div>
        <div class="form-group">
          <label class="form-label">Tanggal Lahir</label>
          <input type="date" class="form-control">
        </div>
      </div>
      <div class="form-row">
        <div class="form-group">
          <label class="form-label">Blok</label>
          <select class="form-control">
            <option>Blok A</option>
            <option>Blok B</option>
            <option>Blok C</option>
            <option>Blok D</option>
          </select>
        </div>
        <div class="form-group">
          <label class="form-label">No. Rumah</label>
          <input type="text" class="form-control" placeholder="No rumah">
        </div>
      </div>
      <div class="form-row">
        <div class="form-group">
          <label class="form-label">Status Domisili</label>
          <select class="form-control">
            <option>Domisili Setempat</option>
            <option>Domisili Luar</option>
            <option>KK Luar</option>
          </select>
        </div>
        <div class="form-group">
          <label class="form-label">Profesi</label>
          <input type="text" class="form-control" placeholder="Pekerjaan">
        </div>
      </div>
      <div class="form-row">
        <div class="form-group">
          <label class="form-label">No. HP / WhatsApp</label>
          <input type="tel" class="form-control" placeholder="08xxxxxxxxxx">
        </div>
        <div class="form-group">
          <label class="form-label">No. KK</label>
          <input type="text" class="form-control" placeholder="No Kartu Keluarga">
        </div>
      </div>
    </div>
    <div class="modal-footer">
      <button class="btn btn-secondary modal-cancel">Batal</button>
      <button class="btn btn-primary">Simpan Data</button>
    </div>
  </div>
</div>

<?php include 'includes/footer.php'; ?>
