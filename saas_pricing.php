<?php
require_once 'classes/Auth.php';
require_once 'classes/Database.php';
require_once 'classes/PlanHelper.php';

// Initialize Database & Auth
$db = Database::getInstance();
Auth::requireRole('superadmin');

$success_msg = '';
$error_msg = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    
    if ($action === 'add_plan') {
        $name = trim($_POST['name'] ?? '');
        $price = (float)($_POST['price'] ?? 0);
        $max_houses = (int)($_POST['max_houses'] ?? 0);
        
        if ($name && $max_houses > 0) {
            try {
                $db->insert('subscription_plans', [
                    'name' => $name,
                    'price' => $price,
                    'max_houses' => $max_houses
                ]);
                $success_msg = "Paket berlangganan '$name' berhasil ditambahkan.";
                
                // Re-evaluasi semua tenant agar paket otomatis menyesuaikan
                $reassign = reassignAllTenantPlans();
                if ($reassign['changed'] > 0) {
                    $success_msg .= " ({$reassign['changed']} tenant otomatis disesuaikan paketnya.)";
                }
                
                $db->insert('audit_logs', [
                    'user_id' => Auth::user()['id'],
                    'action' => 'Add Plan',
                    'new_values' => json_encode(['description' => "Menambahkan paket SaaS baru: $name"]),
                    'ip_address' => $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1'
                ]);
            } catch (Exception $e) {
                $error_msg = "Gagal menambahkan paket: " . $e->getMessage();
            }
        } else {
            $error_msg = "Nama Paket dan Maksimal Rumah wajib diisi dengan benar!";
        }
    } elseif ($action === 'edit_plan') {
        $plan_id = (int)($_POST['plan_id'] ?? 0);
        $name = trim($_POST['name'] ?? '');
        $price = (float)($_POST['price'] ?? 0);
        $max_houses = (int)($_POST['max_houses'] ?? 0);
        
        if ($plan_id && $name && $max_houses > 0) {
            try {
                $db->update('subscription_plans', [
                    'name' => $name,
                    'price' => $price,
                    'max_houses' => $max_houses
                ], 'id = ?', [$plan_id]);
                $success_msg = "Paket '$name' berhasil diperbarui.";
                
                // Re-evaluasi semua tenant agar paket otomatis menyesuaikan
                $reassign = reassignAllTenantPlans();
                if ($reassign['changed'] > 0) {
                    $success_msg .= " ({$reassign['changed']} tenant otomatis disesuaikan paketnya.)";
                }
                
                $db->insert('audit_logs', [
                    'user_id' => Auth::user()['id'],
                    'action' => 'Edit Plan',
                    'new_values' => json_encode(['description' => "Memperbarui paket SaaS ID $plan_id ($name)"]),
                    'ip_address' => $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1'
                ]);
            } catch (Exception $e) {
                $error_msg = "Gagal memperbarui paket: " . $e->getMessage();
            }
        } else {
            $error_msg = "Data tidak valid untuk pembaruan paket!";
        }
    } elseif ($action === 'delete_plan') {
        $plan_id = (int)($_POST['plan_id'] ?? 0);
        
        if ($plan_id) {
            $db->delete('subscription_plans', 'id = ?', [$plan_id]);
            
            // Re-evaluasi semua tenant agar paket otomatis pindah ke paket lain yang sesuai
            $reassign = reassignAllTenantPlans();
            $success_msg = "Paket berlangganan berhasil dihapus.";
            if ($reassign['changed'] > 0) {
                $success_msg .= " ({$reassign['changed']} tenant otomatis dipindahkan ke paket lain.)";
            }
            
            $db->insert('audit_logs', [
                'user_id' => Auth::user()['id'],
                'action' => 'Delete Plan',
                'new_values' => json_encode(['description' => "Menghapus paket SaaS ID $plan_id, {$reassign['changed']} tenant di-reassign"]),
                'ip_address' => $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1'
            ]);
        }
    }
}

// Fetch all plans
$plans = $db->fetchAll("
    SELECT p.*, 
    (SELECT COUNT(*) FROM tenants t WHERE t.plan_id = p.id) as user_count 
    FROM subscription_plans p 
    ORDER BY price ASC
");

?>
<?php include 'includes/header.php'; ?>

<div class="app-layout">
  <?php include 'includes/sidebar_saas.php'; ?>

  <div class="main-wrapper">
    <?php include 'includes/topbar.php'; ?>

    <main class="main-content">
      <div class="page-header">
        <div>
          <h1>Pengaturan Harga & Paket</h1>
          <div class="breadcrumb">
            <span class="separator">/</span>
            <span>Manajemen Paket SaaS</span>
          </div>
        </div>
        <button class="btn btn-primary btn-sm" onclick="document.getElementById('addPlanModal').classList.add('active')">
            <i data-lucide="plus" style="width:16px;height:16px;"></i> Tambah Paket
        </button>
      </div>

      <?php if ($success_msg): ?>
      <div style="background:rgba(16,185,129,0.1);border:1px solid rgba(16,185,129,0.3);color:var(--success);padding:12px 16px;border-radius:var(--radius-md);margin-bottom:20px;font-size:0.9rem;">
        ✅ <?= htmlspecialchars($success_msg) ?>
      </div>
      <?php endif; ?>
      <?php if ($error_msg): ?>
      <div style="background:rgba(239,68,68,0.1);border:1px solid rgba(239,68,68,0.3);color:var(--danger);padding:12px 16px;border-radius:var(--radius-md);margin-bottom:20px;font-size:0.9rem;">
        ⚠️ <?= htmlspecialchars($error_msg) ?>
      </div>
      <?php endif; ?>

      <div class="card" style="margin-top:20px;">
        <div class="card-header border-bottom">
          <h3 class="card-title">Daftar Paket Harga</h3>
        </div>
        <div class="table-container">
          <table class="table">
            <thead>
              <tr>
                <th>ID</th>
                <th>Nama Paket / Tier</th>
                <th>Harga (Rp)</th>
                <th>Max Rumah</th>
                <th>Klien Aktif</th>
                <th>Aksi</th>
              </tr>
            </thead>
            <tbody>
              <?php if (empty($plans)): ?>
              <tr>
                <td colspan="6" style="text-align:center;padding:20px;color:var(--text-muted);">Belum ada paket berlangganan.</td>
              </tr>
              <?php endif; ?>
              
              <?php foreach ($plans as $p): ?>
              <tr>
                <td><strong>#<?= $p['id'] ?></strong></td>
                <td>
                    <div style="font-weight:700;color:var(--text-main);"><?= htmlspecialchars($p['name']) ?></div>
                </td>
                <td>
                    <div style="font-size:1.05rem;font-weight:600;color:var(--success);">
                        <?= $p['price'] == 0 ? 'Gratis' : 'Rp ' . number_format($p['price'], 0, ',', '.') ?>
                    </div>
                </td>
                <td><span style="font-weight:600;"><?= number_format($p['max_houses']) ?> Unit</span></td>
                <td><span class="badge badge-info"><?= $p['user_count'] ?> Klien</span></td>
                <td>
                    <div style="display:flex;gap:4px;">
                      <button type="button" class="btn btn-icon btn-sm btn-secondary" title="Edit Data" 
                          onclick="openEditModal(<?= $p['id'] ?>, '<?= addslashes(htmlspecialchars($p['name'])) ?>', <?= $p['price'] ?>, <?= $p['max_houses'] ?>)">
                          <i data-lucide="pencil" style="width:14px;height:14px;"></i>
                      </button>
                      <button type="button" class="btn btn-icon btn-sm btn-secondary" title="Hapus Paket" 
                          onclick="openDeleteModal(<?= $p['id'] ?>, '<?= addslashes(htmlspecialchars($p['name'])) ?>')" style="color:var(--danger);">
                          <i data-lucide="trash-2" style="width:14px;height:14px;"></i>
                      </button>
                    </div>
                </td>
              </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        </div>
      </div>
    </main>
  </div>
</div>

<!-- Modal Add Plan -->
<div class="modal-overlay" id="addPlanModal">
  <div class="modal card" style="width:100%;max-width:500px;background:var(--bg-card);border-radius:var(--radius-lg);padding:0;overflow:hidden;box-shadow:var(--shadow-lg);">
    <form method="POST">
      <input type="hidden" name="action" value="add_plan">
      
      <div class="modal-header border-bottom" style="display:flex;justify-content:space-between;align-items:center;padding:15px 20px;">
        <h3 style="margin:0;font-size:1.1rem;color:var(--text-main);">Tambah Paket Baru</h3>
        <button type="button" onclick="document.getElementById('addPlanModal').classList.remove('active')" style="background:none;border:none;cursor:pointer;font-size:1.2rem;color:var(--text-muted);">&times;</button>
      </div>
      
      <div class="modal-body" style="padding:20px;text-align:left;">
        <div class="form-group" style="margin-bottom:15px;">
          <label class="form-label" style="display:block;margin-bottom:5px;font-size:0.85rem;">Nama Paket (Contoh: Lite, Pro, Bisnis) *</label>
          <input type="text" name="name" class="form-control" required style="width:100%;">
        </div>
        <div class="form-group" style="margin-bottom:15px;">
          <label class="form-label" style="display:block;margin-bottom:5px;font-size:0.85rem;">Harga Langganan Bulanan (Rp)</label>
          <input type="number" name="price" class="form-control" value="0" min="0" required style="width:100%;">
          <small style="color:var(--text-muted);">Isi 0 untuk paket Gratis/Default.</small>
        </div>
        <div class="form-group" style="margin-bottom:15px;">
          <label class="form-label" style="display:block;margin-bottom:5px;font-size:0.85rem;">Maksimal Kuota Rumah Tercatat *</label>
          <input type="number" name="max_houses" class="form-control" value="50" min="1" required style="width:100%;">
        </div>
      </div>
      
      <div class="modal-footer border-top" style="display:flex;justify-content:flex-end;gap:10px;padding:15px 20px;">
        <button type="button" class="btn btn-secondary" onclick="document.getElementById('addPlanModal').classList.remove('active')">Batal</button>
        <button type="submit" class="btn btn-primary" style="padding:8px 20px;">Simpan Paket</button>
      </div>
    </form>
  </div>
</div>

<!-- Modal Edit Plan -->
<div class="modal-overlay" id="editPlanModal">
  <div class="modal card" style="width:100%;max-width:500px;background:var(--bg-card);border-radius:var(--radius-lg);padding:0;overflow:hidden;box-shadow:var(--shadow-lg);">
    <form method="POST">
      <input type="hidden" name="action" value="edit_plan">
      <input type="hidden" name="plan_id" value="">
      
      <div class="modal-header border-bottom" style="display:flex;justify-content:space-between;align-items:center;padding:15px 20px;">
        <h3 style="margin:0;font-size:1.1rem;color:var(--text-main);">Edit Paket Harga</h3>
        <button type="button" onclick="document.getElementById('editPlanModal').classList.remove('active')" style="background:none;border:none;cursor:pointer;font-size:1.2rem;color:var(--text-muted);">&times;</button>
      </div>
      
      <div class="modal-body" style="padding:20px;text-align:left;">
        <div class="form-group" style="margin-bottom:15px;">
          <label class="form-label">Nama Paket</label>
          <input type="text" name="name" class="form-control" required style="width:100%;">
        </div>
        <div class="form-group" style="margin-bottom:15px;">
          <label class="form-label">Harga (Rp)</label>
          <input type="number" name="price" class="form-control" min="0" required style="width:100%;">
        </div>
        <div class="form-group" style="margin-bottom:15px;">
          <label class="form-label">Maksimal Rumah</label>
          <input type="number" name="max_houses" class="form-control" min="1" required style="width:100%;">
        </div>
      </div>
      
      <div class="modal-footer border-top" style="display:flex;justify-content:flex-end;gap:10px;padding:15px 20px;">
        <button type="button" class="btn btn-secondary" onclick="document.getElementById('editPlanModal').classList.remove('active')">Batal</button>
        <button type="submit" class="btn btn-primary" style="padding:8px 20px;">Simpan Perubahan</button>
      </div>
    </form>
  </div>
</div>

<!-- Modal Delete Plan -->
<div class="modal-overlay" id="deletePlanModal">
  <div class="modal card" style="width:100%;max-width:400px;background:var(--bg-card);border-radius:var(--radius-lg);padding:0;overflow:hidden;box-shadow:var(--shadow-lg);">
    <form method="POST">
      <input type="hidden" name="action" value="delete_plan">
      <input type="hidden" name="plan_id" value="">
      
      <div class="modal-header border-bottom" style="display:flex;justify-content:space-between;align-items:center;padding:15px 20px;">
        <h3 style="margin:0;font-size:1.1rem;color:var(--danger);">Hapus Paket?</h3>
        <button type="button" onclick="document.getElementById('deletePlanModal').classList.remove('active')" style="background:none;border:none;cursor:pointer;font-size:1.2rem;color:var(--text-muted);">&times;</button>
      </div>
      
      <div class="modal-body" style="padding:20px;text-align:center;">
        <div style="background:rgba(239,68,68,0.1);color:var(--danger);padding:15px;border-radius:var(--radius-md);margin-bottom:15px;">
          Anda yakin ingin menghapus paket <strong id="deletePlanLabel"></strong>? Data ini tidak bisa dikembalikan.
        </div>
      </div>
      
      <div class="modal-footer border-top" style="display:flex;justify-content:space-between;padding:15px 20px;">
        <button type="button" class="btn btn-secondary" onclick="document.getElementById('deletePlanModal').classList.remove('active')">Batal</button>
        <button type="submit" class="btn" style="background:var(--danger);color:white;border:none;">Ya, Hapus</button>
      </div>
    </form>
  </div>
</div>

<script>
function openEditModal(id, name, price, max_houses) {
  const modal = document.getElementById('editPlanModal');
  modal.querySelector('input[name="plan_id"]').value = id;
  modal.querySelector('input[name="name"]').value = name;
  modal.querySelector('input[name="price"]').value = price;
  modal.querySelector('input[name="max_houses"]').value = max_houses;
  modal.classList.add('active');
}

function openDeleteModal(id, name) {
  const modal = document.getElementById('deletePlanModal');
  modal.querySelector('input[name="plan_id"]').value = id;
  modal.querySelector('#deletePlanLabel').textContent = name;
  modal.classList.add('active');
}
</script>

<?php include 'includes/footer.php'; ?>
