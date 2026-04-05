<?php 
require_once 'classes/Auth.php';
require_once 'classes/Database.php';
require_once 'classes/Helpers.php';
require_once 'classes/PlanHelper.php';

Auth::requireLogin();
$tenant_id = Auth::tenantId();
$db = Database::getInstance();

$id = isset($_GET['id']) ? (int)$_GET['id'] : null;
$house = $id ? $db->fetch("SELECT * FROM houses WHERE id = ? AND tenant_id = ?", [$id, $tenant_id]) : null;

// Get Plan Limits
$planInfo = $db->fetch("SELECT p.max_houses, 
                        (SELECT COUNT(*) FROM houses WHERE tenant_id = ?) as current_count 
                        FROM tenants t 
                        JOIN subscription_plans p ON t.plan_id = p.id 
                        WHERE t.id = ?", [$tenant_id, $tenant_id]);
$max_houses = $planInfo['max_houses'] ?? 0;
$current_houses = $planInfo['current_count'] ?? 0;

$pageTitle = $id ? 'Edit Data Rumah' : 'Tambah Data Rumah';

// Handle Post Submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = [
        'tenant_id'      => $tenant_id,
        'block_id'       => (int)$_POST['block_id'],
        'house_number'   => trim($_POST['house_number']),
        'house_type'     => trim($_POST['house_type']),
        'area_m2'        => (float)$_POST['land_area'], // Map ke area_m2
        'status'         => $_POST['status'],
        'owner_name'     => trim($_POST['owner_name'] ?? '-'),
        'owner_phone'    => trim($_POST['owner_phone'] ?? '-'),
        'owner_address'  => trim($_POST['owner_address'] ?? '')
    ];

    if ($id) {
        $db->update('houses', $data, 'id = ? AND tenant_id = ?', [$id, $tenant_id]);
        Helpers::flash('success', 'Data rumah berhasil diperbarui.');
    } else {
        $db->insert('houses', $data);
        
        // Auto-assign plan based on new house count
        $planResult = autoAssignPlan($tenant_id);
        
        if ($planResult['changed']) {
            Helpers::flash('success', "Data rumah baru berhasil ditambahkan. Paket Anda otomatis berubah dari {$planResult['old_plan']} ➜ {$planResult['new_plan']} ({$planResult['house_count']} unit rumah).");
        } else {
            Helpers::flash('success', 'Data rumah baru berhasil ditambahkan.');
        }
    }
    header("Location: rumah.php");
    exit;
}

// Fetch Blocks
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
          <h1><?= $pageTitle ?></h1>
          <div class="breadcrumb">
            <a href="index.php">Dashboard</a><span class="separator">/</span>
            <a href="rumah.php">Data Rumah</a><span class="separator">/</span>
            <span><?= $id ? 'Edit' : 'Tambah' ?></span>
          </div>
        </div>
        <a href="rumah.php" class="btn btn-secondary btn-sm">← Kembali</a>
      </div>

      <form method="POST">
      <div class="card" style="padding:24px;">
        <div class="form-row">
          <div class="form-group">
            <label class="form-label">Blok Perumahan</label>
            <select name="block_id" class="form-control" required>
                <?php if (empty($blocks)): ?>
                    <option value="">Belum Memiliki Blok</option>
                <?php endif; ?>
                <?php foreach($blocks as $b): ?>
                    <option value="<?= $b['id'] ?>" <?= ($house['block_id'] ?? '') == $b['id'] ? 'selected' : '' ?>><?= htmlspecialchars($b['block_name']) ?></option>
                <?php endforeach; ?>
            </select>
          </div>
          <div class="form-group">
            <label class="form-label">Nomor Rumah</label>
            <input type="text" name="house_number" class="form-control" placeholder="Contoh: A-01" value="<?= htmlspecialchars($house['house_number'] ?? '') ?>" required>
          </div>
        </div>
        
        <div class="form-row">
          <div class="form-group">
            <label class="form-label">Nama Pemilik / Penghuni Utama</label>
            <input type="text" name="owner_name" class="form-control" placeholder="Contoh: Budi Santoso" value="<?= htmlspecialchars($house['owner_name'] ?? '') ?>">
          </div>
          <div class="form-group">
            <label class="form-label">No. HP / WhatsApp Pemilik</label>
            <input type="tel" name="owner_phone" class="form-control" placeholder="Contoh: 0812xxxxxxxx" value="<?= htmlspecialchars($house['owner_phone'] ?? '') ?>">
          </div>
        </div>
             <div class="form-row">
          <div class="form-group">
            <label class="form-label">Status Hunian</label>
            <select name="status" class="form-control">
                <option value="kosong" <?= ($house['status'] ?? '') == 'kosong' ? 'selected' : '' ?>>Kosong</option>
                <option value="berpenghuni" <?= ($house['status'] ?? '') == 'berpenghuni' ? 'selected' : '' ?>>Berpenghuni</option>
                <option value="dikontrakkan" <?= ($house['status'] ?? '') == 'dikontrakkan' ? 'selected' : '' ?>>Dikontrakkan</option>
            </select>
          </div>
          <div class="form-group">
            <label class="form-label">Tipe Rumah / Model</label>
            <input type="text" name="house_type" class="form-control" placeholder="Contoh: T-45" value="<?= htmlspecialchars($house['house_type'] ?? '') ?>">
          </div>
        </div>

        <div id="ownerAddressGroup" style="display: <?= (($house['status'] ?? '') == 'berpenghuni') ? 'none' : 'block' ?>; margin-bottom: 20px;">
          <label class="form-label" style="font-weight: 600; color: var(--primary);">📍 Alamat Tinggal Pemilik Saat Ini</label>
          <textarea name="owner_address" class="form-control" rows="2" placeholder="Alamat lengkap pemilik jika tidak tinggal di unit ini..."><?= htmlspecialchars($house['owner_address'] ?? '') ?></textarea>
          <small class="text-muted" style="display:block; margin-top:5px;">Isi jika pemilik tidak tinggal di unit ini (Status Kosong/Dikontrakkan).</small>
        </div>
        <div class="form-row">
          <div class="form-group">
            <label class="form-label">Luas Tanah (m²)</label>
            <input type="number" name="land_area" class="form-control" placeholder="120" value="<?= htmlspecialchars($house['land_area'] ?? '0') ?>">
          </div>
          <div class="form-group">
            <label class="form-label">Luas Bangunan (m²)</label>
            <input type="number" name="building_area" class="form-control" placeholder="85" value="<?= htmlspecialchars($house['building_area'] ?? '0') ?>">
          </div>
        </div>

        <div class="form-group" style="margin-top:10px;">
          <label class="form-label">Catatan Tambahan</label>
          <textarea name="description" class="form-control" rows="3" placeholder="Informasi tambahan rumah..."><?= htmlspecialchars($house['description'] ?? '') ?></textarea>
        </div>

        <div style="display:flex;justify-content:flex-end;gap:12px;margin-top:24px;padding-top:20px;border-top:1px solid var(--border-color);">
          <a href="rumah.php" class="btn btn-secondary">Batal</a>
          <button type="submit" class="btn btn-primary">💾 Simpan Data Rumah</button>
        </div>
      </div>
      </form>
      <script>
        document.querySelector('select[name="status"]').addEventListener('change', function() {
            const group = document.getElementById('ownerAddressGroup');
            if (this.value === 'berpenghuni') {
                group.style.display = 'none';
            } else {
                group.style.display = 'block';
            }
        });
      </script>   </script>
    </main>
  </div>
</div>
<?php include 'includes/footer.php'; ?>
