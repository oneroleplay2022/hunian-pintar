<?php
date_default_timezone_set('Asia/Jakarta');
require_once 'classes/Auth.php';
require_once 'classes/Database.php';
require_once 'classes/Notification.php';
require_once 'classes/PlanHelper.php';

// Initialize Database & Auth
$db = Database::getInstance();
Auth::requireRole('superadmin');

// Auto-suspend expired tenants and generate invoices
$expired_tenants = $db->fetchAll("SELECT id FROM tenants WHERE expired_at < CURDATE() AND subscription_status IN ('active', 'trial')");
if (!empty($expired_tenants)) {
    foreach ($expired_tenants as $t) {
        $db->update('tenants', ['subscription_status' => 'suspended'], 'id = ?', [$t['id']]);
        
        $pending = $db->fetchColumn("SELECT COUNT(*) FROM subscriptions WHERE tenant_id = ? AND payment_status = 'pending'", [$t['id']]);
        if ($pending == 0) {
            $db->insert('subscriptions', [
                'tenant_id' => $t['id'],
                'amount' => getTenantPlanPrice($t['id']),
                'payment_status' => 'pending',
                'expired_at' => date('Y-m-d', strtotime('+30 days'))
            ]);
        }
    }
}
 
// Handle "Login As" / Impersonation
if (isset($_GET['impersonate_user'])) {
    $target_uid = (int)$_GET['impersonate_user'];
    if (Auth::impersonate($target_uid)) {
        header('Location: index.php'); // Go to tenant dashboard
        exit;
    }
}
 
// Fetch All Plans
$plans = $db->fetchAll("SELECT * FROM subscription_plans ORDER BY price ASC");

// Handle form submissions
$success_msg = '';
$error_msg = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    
    if ($action === 'add_tenant') {
        $tenant_name = trim($_POST['tenant_name'] ?? '');
        $address = trim($_POST['address'] ?? '');
        $admin_name = trim($_POST['admin_name'] ?? '');
        $admin_email = trim($_POST['admin_email'] ?? '');
        $admin_phone = trim($_POST['admin_phone'] ?? '');
        $admin_password = $_POST['admin_password'] ?? '';
        
        if ($tenant_name && $admin_email && $admin_password) {
            // Check if email already exists
            $emailCheck = (int) $db->fetchColumn("SELECT COUNT(*) FROM users WHERE email = ?", [$admin_email]);
            if ($emailCheck > 0) {
                $error_msg = "Pendaftaran Gagal: Email '$admin_email' sudah terdaftar di sistem. Gunakan email lain.";
            } else {
                try {
                $db->beginTransaction();
                
                // 1. Insert Tenant
                $tenant_id = $db->insert('tenants', [
                    'name' => $tenant_name,
                    'address' => $address,
                    'subscription_status' => 'trial',
                    'expired_at' => date('Y-m-d', strtotime('+30 days'))
                ]);
                
                // 2. Insert Admin User
                $admin_id = $db->insert('users', [
                    'tenant_id' => $tenant_id,
                    'name' => $admin_name,
                    'email' => $admin_email,
                    'password' => password_hash($admin_password, PASSWORD_BCRYPT),
                    'role' => 'admin'
                ]);
                
                // 3. Insert Default Block
                $block_id = $db->insert('blocks', [
                    'tenant_id' => $tenant_id,
                    'block_name' => 'Blok A',
                    'description' => 'Blok Default Sistem'
                ]);

                // 4. Insert Default House (Contoh Rumah Pengurus)
                $house_id = $db->insert('houses', [
                    'tenant_id' => $tenant_id,
                    'block_id' => $block_id,
                    'house_number' => '01',
                    'owner_name' => $admin_name,
                    'status' => 'occupied'
                ]);

                // 5. Insert Admin AS Resident (Agar data kependudukan sinkron)
                $db->insert('residents', [
                    'tenant_id' => $tenant_id,
                    'house_id' => $house_id,
                    'full_name' => $admin_name,
                    'nik' => '0000000000000000',
                    'phone' => $admin_phone,
                    'family_status' => 'kepala_keluarga',
                    'domicile_status' => 'domisili'
                ]);

                // 6. Insert Initial Settings for Tenant
                $db->insert('settings', [
                    'tenant_id' => $tenant_id,
                    'setting_key' => 'community_name',
                    'setting_value' => $tenant_name
                ]);
                
                $db->commit();
                
                // Auto-assign plan based on house count
                autoAssignPlan($tenant_id);
                
                // 7. Send Welcome WhatsApp Notification
                $notifResult = Notification::welcomeTenant($admin_email, $admin_phone, $tenant_name, $admin_name, $admin_password);
                $notifStatus = $notifResult['status'] ? " (WA Notifikasi Terkirim)" : " (WA Gagal: " . $notifResult['message'] . ")";
                
                $success_msg = "Perumahan '$tenant_name' terdaftar! Anda telah membuat satu data contoh rumah & warga pengurus secara otomatis." . $notifStatus;

                // Catat ke Audit Log
                $db->insert('audit_logs', [
                    'user_id' => Auth::user()['id'],
                    'action' => 'Add Tenant',
                    'new_values' => json_encode(['description' => "Mendaftarkan perumahan baru: $tenant_name"]),
                    'ip_address' => $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1'
                ]);
            } catch (Exception $e) {
                $db->rollback();
                $error_msg = "Gagal menambahkan data: " . $e->getMessage();
            }
        }
        } else {
            $error_msg = "Nama Perumahan, Email Admin, & Password wajib diisi!";
        }
    } elseif ($action === 'toggle_status') {
        $tenant_id = (int)($_POST['tenant_id'] ?? 0);
        $new_status = $_POST['new_status'] ?? '';
        
        if ($tenant_id && in_array($new_status, ['active', 'trial', 'suspended'])) {
            $db->update('tenants', ['subscription_status' => $new_status], 'id = ?', [$tenant_id]);
            $success_msg = "Status pelanggan berhasil diubah menjadi $new_status.";

            // Catat ke Audit Log
            $db->insert('audit_logs', [
                'user_id' => Auth::user()['id'],
                'action' => 'Toggle Tenant Status',
                'new_values' => json_encode(['description' => "Mengubah status tenant ID $tenant_id menjadi $new_status"]),
                'ip_address' => $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1'
            ]);
        }
    } elseif ($action === 'edit_tenant') {
        $tenant_id = (int)($_POST['tenant_id'] ?? 0);
        $tenant_name = trim($_POST['tenant_name'] ?? '');
        $address = trim($_POST['address'] ?? '');
        $expired_at = $_POST['expired_at'] ?? '';
        $sub_status = $_POST['subscription_status'] ?? 'trial';
        
        if ($tenant_id && $tenant_name) {
            $db->update('tenants', [
                'name' => $tenant_name,
                'address' => $address,
                'subscription_status' => $sub_status,
                'expired_at' => $expired_at ?: null
            ], 'id = ?', [$tenant_id]);
            
            // Auto-assign plan based on actual house count
            autoAssignPlan($tenant_id);
            $success_msg = "Data perumahan berhasil diperbarui.";

            // Catat ke Audit Log
            $db->insert('audit_logs', [
                'user_id' => Auth::user()['id'],
                'action' => 'Edit Tenant',
                'new_values' => json_encode(['description' => "Memperbarui data perumahan: $tenant_name (ID: $tenant_id)"]),
                'ip_address' => $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1'
            ]);
        } else {
            $error_msg = "ID dan Nama Perumahan wajib diisi.";
        }
    } elseif ($action === 'delete_tenant') {
        $tenant_id = (int)($_POST['tenant_id'] ?? 0);
        $confirm_name = trim($_POST['confirm_name'] ?? '');
        
        $tenant = $db->fetch("SELECT name FROM tenants WHERE id = ?", [$tenant_id]);
        if ($tenant && strtolower($tenant['name']) === strtolower($confirm_name)) {
            $db->delete('tenants', 'id = ?', [$tenant_id]);
            $success_msg = "Perumahan '{$tenant['name']}' beserta seluruh datanya telah dihapus permanen.";

            // Catat ke Audit Log
            $db->insert('audit_logs', [
                'user_id' => Auth::user()['id'],
                'action' => 'Delete Tenant',
                'new_values' => json_encode(['description' => "Menghapus permanen tenant: {$tenant['name']} (ID: $tenant_id)"]),
                'ip_address' => $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1'
            ]);
        } else {
            $error_msg = "Penghapusan dibatalkan: Konfirmasi nama perumahan tidak cocok.";
        }
    }
}

// Fetch tenants with counts and plan info
$query = "SELECT t.*, p.name as plan_name, p.price as plan_price,
          (SELECT id FROM users u WHERE u.tenant_id = t.id AND u.role = 'admin' LIMIT 1) as first_admin_id,
          (SELECT COUNT(*) FROM users u WHERE u.tenant_id = t.id AND u.role = 'admin') as admin_count,
          (SELECT COUNT(*) FROM houses h WHERE h.tenant_id = t.id) as house_count,
          (SELECT COUNT(*) FROM residents r WHERE r.tenant_id = t.id) as resident_count
          FROM tenants t 
          LEFT JOIN subscription_plans p ON t.plan_id = p.id
          ORDER BY t.created_at DESC";
$tenants = $db->fetchAll($query);



$totalTenants = count($tenants);
$activeTenants = count(array_filter($tenants, fn($t) => $t['subscription_status'] === 'active'));
$trialTenants = count(array_filter($tenants, fn($t) => $t['subscription_status'] === 'trial'));

// Financial Stats (SaaS Earnings)
$financials = $db->fetch("SELECT 
    SUM(CASE WHEN payment_status = 'paid' THEN amount ELSE 0 END) as total_revenue,
    SUM(CASE WHEN payment_status = 'pending' THEN amount ELSE 0 END) as pending_revenue
    FROM subscriptions");
$totalRevenue = $financials['total_revenue'] ?? 0;
$pendingRevenue = $financials['pending_revenue'] ?? 0;

function getRemainingDays($date) {
    if (!$date) return '-';
    $now = new DateTime('today'); // Midnight hari ini, agar hitungan hari akurat
    $expired = new DateTime($date);
    $diff = $now->diff($expired);
    if ($now > $expired) return '<span style="color:var(--danger);font-weight:700;">Expired</span>';
    return $diff->days . ' Hari';
}

?>
<?php include 'includes/header.php'; ?>

<div class="app-layout">
  <!-- Sidebar Khusus Superadmin -->
  <?php include 'includes/sidebar_saas.php'; ?>

  <div class="main-wrapper">
    <?php include 'includes/topbar.php'; ?>

    <main class="main-content">
      <div class="page-header">
        <div>
          <h1>Daftar Klien Perumahan</h1>
          <div class="breadcrumb">
            <span class="separator">/</span>
            <span>Daftar Klien</span>
          </div>
        </div>
        <button class="btn btn-primary btn-sm" onclick="document.getElementById('addTenantModal').classList.add('active')"><i data-lucide="plus" style="width:16px;height:16px;"></i> Tambah Pelanggan</button>
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

      <!-- Data Table -->
      <div class="card" style="margin-top:20px;">
        <div class="card-header border-bottom">
          <h3 class="card-title">Semua Daftar Klien / Pelanggan</h3>
        </div>
        <div class="table-container">
          <table class="table">
            <thead>
              <tr>
                <th>ID</th>
                <th>Tenant (Perumahan)</th>
                <th>Paket</th>
                <th>Biaya /bln</th>
                <th>Status</th>
                <th>Rumah</th>
                <th>Warga</th>
                <th>Sisa Aktif</th>
                <th>Aksi</th>
              </tr>
            </thead>
            <tbody>
              <?php if (empty($tenants)): ?>
              <tr>
                <td colspan="9" style="text-align:center;padding:20px;color:var(--text-muted);">Belum ada perumahan / tenant yang terdaftar.</td>
              </tr>
              <?php endif; ?>
              
              <?php foreach ($tenants as $t): 
                $badge = 'badge-secondary';
                if ($t['subscription_status'] === 'active') $badge = 'badge-success';
                elseif ($t['subscription_status'] === 'trial') $badge = 'badge-warning';
                elseif ($t['subscription_status'] === 'suspended') $badge = 'badge-danger';
              ?>
              <tr>
                <td><strong>#<?= $t['id'] ?></strong></td>
                <td>
                    <div style="font-weight:700;color:var(--text-main);"><?= htmlspecialchars($t['name']) ?></div>
                    <small style="color:var(--text-muted);display:block;max-width:180px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;"><?= htmlspecialchars($t['address'] ?? '-') ?></small>
                </td>
                <td><span class="badge badge-info" style="font-size:0.65rem; background:rgba(59,130,246,0.1); color:#3b82f6; border:1px solid rgba(59,130,246,0.2);"><?= strtoupper($t['plan_name'] ?? '-') ?></span></td>
                <td>
                    <?php
                      $price = $t['plan_price'] ?? 0;
                      $isTrial = ($t['subscription_status'] === 'trial');
                    ?>
                    <?php if ($isTrial): ?>
                        <div style="font-weight:600;font-size:0.9rem;color:var(--text-muted);">Gratis</div>
                        <small style="color:var(--text-muted);font-size:0.7rem;">Trial</small>
                    <?php else: ?>
                        <div style="font-weight:600;font-size:0.9rem;color:var(--success);">
                            Rp <?= number_format($price, 0, ',', '.') ?>
                        </div>
                    <?php endif; ?>
                </td>
                <td><span class="badge <?= $badge ?>" style="font-size:0.7rem;"><?= strtoupper($t['subscription_status']) ?></span></td>
                <td style="text-align:center;"><b><?= $t['house_count'] ?></b></td>
                <td style="text-align:center;"><b><?= $t['resident_count'] ?></b></td>
                <td>
                    <div style="font-size:0.85rem;font-weight:600;"><?= getRemainingDays($t['expired_at']) ?></div>
                    <small style="font-size:0.7rem;color:var(--text-muted);"><?= $t['expired_at'] ? date('d/m/y', strtotime($t['expired_at'])) : '-' ?></small>
                </td>
                <td>
                  <form method="POST" style="display:inline;margin:0;">
                    <input type="hidden" name="action" value="toggle_status">
                    <input type="hidden" name="tenant_id" value="<?= $t['id'] ?>">
                    
                    <div style="display:flex;gap:4px;">
                      <a href="?impersonate_user=<?= $t['first_admin_id'] ?>" class="btn btn-icon btn-sm btn-secondary" title="Login As (Intip Dashboard)" style="color:var(--primary);"><i data-lucide="user-check" style="width:14px;height:14px;"></i></a>
                      <a href="saas_tenant_detail.php?id=<?= $t['id'] ?>" class="btn btn-icon btn-sm btn-secondary" title="Statistik & Detail Warga"><i data-lucide="bar-chart-2" style="width:14px;height:14px;"></i></a>
                      <button type="button" class="btn btn-icon btn-sm btn-secondary" title="Edit Data" onclick="openEditModal(<?= $t['id'] ?>, '<?= addslashes(htmlspecialchars($t['name'])) ?>', '<?= addslashes(htmlspecialchars($t['address'] ?? '')) ?>', '<?= $t['subscription_status'] ?>', '<?= $t['expired_at'] ?: '' ?>')"><i data-lucide="pencil" style="width:14px;height:14px;"></i></button>
                      
                      <?php if ($t['subscription_status'] === 'suspended'): ?>
                        <input type="hidden" name="new_status" value="active">
                        <button type="submit" class="btn btn-icon btn-sm btn-secondary" title="Aktifkan Akun" onclick="return confirm('Kembalikan status aktif pelanggan ini?')" style="color:var(--success);"><i data-lucide="play-circle" style="width:14px;height:14px;"></i></button>
                      <?php else: ?>
                        <input type="hidden" name="new_status" value="suspended">
                        <button type="submit" class="btn btn-icon btn-sm btn-secondary" title="Tangguhkan (Suspend)" onclick="return confirm('Suspend/blokir pelanggan ini?')" style="color:var(--danger);"><i data-lucide="pause-circle" style="width:14px;height:14px;"></i></button>
                      <?php endif; ?>
                      
                      <button type="button" class="btn btn-icon btn-sm btn-secondary" title="Hapus Permanen" onclick="openDeleteModal(<?= $t['id'] ?>, '<?= addslashes(htmlspecialchars($t['name'])) ?>')" style="color:var(--danger);margin-left:8px;"><i data-lucide="trash-2" style="width:14px;height:14px;"></i></button>
                    </div>
                  </form>
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

<!-- Modal Add Tenant -->
<div class="modal-overlay" id="addTenantModal">
  <div class="modal card" style="width:100%;max-width:600px;background:var(--bg-card);border-radius:var(--radius-lg);padding:0;overflow:hidden;box-shadow:var(--shadow-lg);">
    <form method="POST">
      <input type="hidden" name="action" value="add_tenant">
      
      <div class="modal-header border-bottom" style="display:flex;justify-content:space-between;align-items:center;padding:15px 20px;">
        <h3 style="margin:0;font-size:1.1rem;color:var(--text-main);">Tambah Pelanggan Baru (Tenant)</h3>
        <button type="button" onclick="document.getElementById('addTenantModal').classList.remove('active')" style="background:none;border:none;cursor:pointer;font-size:1.2rem;color:var(--text-muted);">&times;</button>
      </div>
      
      <div class="modal-body" style="padding:20px;max-height:60vh;overflow-y:auto;text-align:left;">
        <h4 style="font-size:0.95rem;margin:0 0 10px 0;color:var(--text-secondary);">1. Informasi Perumahan</h4>
        <div class="form-group" style="margin-bottom:15px;">
          <label class="form-label" style="display:block;margin-bottom:5px;font-size:0.85rem;">Nama Perumahan/Kluster *</label>
          <input type="text" name="tenant_name" class="form-control" placeholder="Contoh: Graha Indah Residence" required style="width:100%;padding:8px 12px;border:1px solid var(--border-color);border-radius:var(--radius-md);background:var(--bg-main);color:var(--text-main);">
        </div>
        <div class="form-group" style="margin-bottom:15px;">
          <label class="form-label" style="display:block;margin-bottom:5px;font-size:0.85rem;">Alamat Lengkap</label>
          <textarea name="address" class="form-control" rows="2" style="width:100%;padding:8px 12px;border:1px solid var(--border-color);border-radius:var(--radius-md);background:var(--bg-main);color:var(--text-main);"></textarea>
        </div>
        <div style="border-top:1px dashed var(--border-color);margin:20px 0;"></div>

        <h4 style="font-size:0.95rem;margin:0 0 10px 0;color:var(--text-secondary);">2. Pembuatan Akun Admin Pertama</h4>
        <div class="form-group" style="margin-bottom:15px;">
          <label class="form-label" style="display:block;margin-bottom:5px;font-size:0.85rem;">Nama Pengurus (Admin) *</label>
          <input type="text" name="admin_name" class="form-control" placeholder="Nama Ketua/Admin Pertama" required style="width:100%;padding:8px 12px;border:1px solid var(--border-color);border-radius:var(--radius-md);background:var(--bg-main);color:var(--text-main);">
        </div>
        <div class="form-group" style="margin-bottom:15px;">
          <label class="form-label" style="display:block;margin-bottom:5px;font-size:0.85rem;">Email Login Admin *</label>
          <input type="email" name="admin_email" class="form-control" placeholder="admin@perumahan.com" required style="width:100%;padding:8px 12px;border:1px solid var(--border-color);border-radius:var(--radius-md);background:var(--bg-main);color:var(--text-main);">
        </div>
        <div class="form-group" style="margin-bottom:15px;">
          <label class="form-label" style="display:block;margin-bottom:5px;font-size:0.85rem;">No. WhatsApp Admin *</label>
          <input type="text" name="admin_phone" class="form-control" placeholder="6281xxx" required style="width:100%;padding:8px 12px;border:1px solid var(--border-color);border-radius:var(--radius-md);background:var(--bg-main);color:var(--text-main);">
        </div>
        <div class="form-group" style="margin-bottom:15px;">
          <label class="form-label" style="display:block;margin-bottom:5px;font-size:0.85rem;">Kata Sandi *</label>
          <input type="password" name="admin_password" class="form-control" placeholder="Minimal 6 karakter" required style="width:100%;padding:8px 12px;border:1px solid var(--border-color);border-radius:var(--radius-md);background:var(--bg-main);color:var(--text-main);">
        </div>
      </div>
      
      <div class="modal-footer border-top" style="display:flex;justify-content:flex-end;gap:10px;padding:15px 20px;">
        <button type="button" class="btn btn-secondary" onclick="document.getElementById('addTenantModal').classList.remove('active')">Batal</button>
        <button type="submit" class="btn btn-primary" style="padding:8px 20px;">Daftarkan Sekarang</button>
      </div>
    </form>
  </div>
</div>

<!-- Modal Edit Tenant -->
<div class="modal-overlay" id="editTenantModal">
  <div class="modal card" style="width:100%;max-width:500px;background:var(--bg-card);border-radius:var(--radius-lg);padding:0;overflow:hidden;box-shadow:var(--shadow-lg);">
    <form method="POST">
      <input type="hidden" name="action" value="edit_tenant">
      <input type="hidden" name="tenant_id" value="">
      
      <div class="modal-header border-bottom" style="display:flex;justify-content:space-between;align-items:center;padding:15px 20px;">
        <h3 style="margin:0;font-size:1.1rem;color:var(--text-main);">Edit Pelanggan</h3>
        <button type="button" onclick="document.getElementById('editTenantModal').classList.remove('active')" style="background:none;border:none;cursor:pointer;font-size:1.2rem;color:var(--text-muted);">&times;</button>
      </div>
      
      <div class="modal-body" style="padding:20px;max-height:60vh;overflow-y:auto;text-align:left;">
        <div class="form-group" style="margin-bottom:15px;">
          <label class="form-label">Nama Perumahan/Kluster *</label>
          <input type="text" name="tenant_name" class="form-control" required style="width:100%;">
        </div>
        <div class="form-group" style="margin-bottom:15px;">
          <label class="form-label">Alamat Lengkap</label>
          <textarea name="address" class="form-control" rows="2" style="width:100%;"></textarea>
        </div>
        <div class="form-group" style="margin-bottom:15px;">
          <label class="form-label">Status Langganan</label>
          <select name="subscription_status" class="form-control" style="width:100%;">
            <option value="trial">Trial (Uji Coba)</option>
            <option value="active">Active (Berlangganan Aktif)</option>
            <option value="suspended">Suspended (Ditangguhkan)</option>
            <option value="expired">Expired (Kadaluarsa)</option>
          </select>
        </div>

        <div class="form-group" style="margin-bottom:15px;">
          <label class="form-label">Tgl Kadaluarsa (Expired At)</label>
          <input type="date" name="expired_at" class="form-control" style="width:100%;">
        </div>
      </div>
      
      <div class="modal-footer border-top" style="display:flex;justify-content:flex-end;gap:10px;padding:15px 20px;">
        <button type="button" class="btn btn-secondary" onclick="document.getElementById('editTenantModal').classList.remove('active')">Batal</button>
        <button type="submit" class="btn btn-primary" style="padding:8px 20px;">Simpan Perubahan</button>
      </div>
    </form>
  </div>
</div>

<!-- Modal Delete Tenant -->
<div class="modal-overlay" id="deleteTenantModal">
  <div class="modal card" style="width:100%;max-width:500px;background:var(--bg-card);border-radius:var(--radius-lg);padding:0;overflow:hidden;box-shadow:var(--shadow-lg);">
    <form method="POST">
      <input type="hidden" name="action" value="delete_tenant">
      <input type="hidden" name="tenant_id" value="">
      
      <div class="modal-header border-bottom" style="display:flex;justify-content:space-between;align-items:center;padding:15px 20px;">
        <h3 style="margin:0;font-size:1.1rem;color:var(--danger);">Hapus Permanen Klien!</h3>
        <button type="button" onclick="document.getElementById('deleteTenantModal').classList.remove('active')" style="background:none;border:none;cursor:pointer;font-size:1.2rem;color:var(--text-muted);">&times;</button>
      </div>
      
      <div class="modal-body" style="padding:20px;max-height:60vh;overflow-y:auto;text-align:left;">
        <div style="background:rgba(239,68,68,0.1);color:var(--danger);padding:15px;border-radius:var(--radius-md);margin-bottom:20px;font-size:0.9rem;">
          <strong>PERINGATAN KERAS!</strong><br>
          Menghapus <b id="deleteTenantLabel"></b> akan turut menghapus <strong>SELURUH</strong> warganya, catatan pembayaran kas & iuran, konfigurasi blok, dan admin perumahan tersebut.<br><br>
          Ketik ulang nama perumahan di bawah ini jika Anda benar-benar yakin.
        </div>
        <div class="form-group" style="margin-bottom:15px;">
          <label class="form-label">Ketik: <span id="deleteTenantNameConfirm" style="font-weight:bold;-webkit-user-select:none;user-select:none;"></span></label>
          <input type="text" name="confirm_name" class="form-control" placeholder="Nama..." required style="width:100%;" autocomplete="off">
        </div>
      </div>
      
      <div class="modal-footer border-top" style="display:flex;justify-content:flex-end;gap:10px;padding:15px 20px;">
        <button type="button" class="btn btn-secondary" onclick="document.getElementById('deleteTenantModal').classList.remove('active')">Batal</button>
        <button type="submit" class="btn" style="padding:8px 20px;background:var(--danger);color:white;border:none;border-radius:var(--radius-md);">Ya, Hapus Permanen Sekarang</button>
      </div>
    </form>
  </div>
</div>

<script>

function openEditModal(id, name, address, status, expired) {
  const modal = document.getElementById('editTenantModal');
  modal.querySelector('input[name="tenant_id"]').value = id;
  modal.querySelector('input[name="tenant_name"]').value = name;
  modal.querySelector('textarea[name="address"]').value = address;
  modal.querySelector('select[name="subscription_status"]').value = status;
  modal.querySelector('input[name="expired_at"]').value = expired;
  modal.classList.add('active');
}

function openDeleteModal(id, name) {
  const modal = document.getElementById('deleteTenantModal');
  modal.querySelector('input[name="tenant_id"]').value = id;
  modal.querySelector('#deleteTenantLabel').textContent = '"' + name + '"';
  modal.querySelector('#deleteTenantNameConfirm').textContent = name;
  modal.querySelector('input[name="confirm_name"]').value = '';
  modal.classList.add('active');
}
</script>

<?php include 'includes/footer.php'; ?>
