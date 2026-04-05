<?php
/**
 * SaaS Role Form - Add/Edit Permissions
 */
require_once 'classes/Auth.php';
require_once 'classes/Database.php';
require_once 'classes/Helpers.php';

Auth::requireRole('superadmin');
$db = Database::getInstance();

$id = isset($_GET['id']) ? (int)$_GET['id'] : null;
$role = $id ? $db->fetch("SELECT * FROM saas_roles WHERE id = ?", [$id]) : null;

if ($id && !$role) {
    Helpers::redirect('saas_roles.php', 'danger', 'Data role tidak ditemukan.');
}

$pageTitle = $id ? 'Edit Konfigurasi Role' : 'Buat Role Baru';

$available_menus = [
    'saas_dashboard.php' => ['name' => 'Dashboard SaaS', 'icon' => 'layout-dashboard'],
    'saas_tenants.php' => ['name' => 'Daftar Klien (Tenants)', 'icon' => 'building'],
    'saas_tickets.php' => ['name' => 'Tiket Support', 'icon' => 'help-circle'],
    'saas_broadcast.php' => ['name' => 'Broadcast WA/Email', 'icon' => 'megaphone'],
    'saas_announcements.php' => ['name' => 'Pengumuman Sistem (Lonceng)', 'icon' => 'bell'],
    'saas_billing.php' => ['name' => 'Tagihan SaaS', 'icon' => 'credit-card'],
    'saas_pricing.php' => ['name' => 'Harga & Paket', 'icon' => 'tag'],
    'saas_audit.php' => ['name' => 'Audit Log Sistem', 'icon' => 'shield-alert'],
    'saas_users.php' => ['name' => 'Manajemen Admin Pusat', 'icon' => 'user-plus'],
    'saas_roles.php' => ['name' => 'Pengaturan Role', 'icon' => 'lock'],
    'saas_settings.php' => ['name' => 'Pengaturan Global', 'icon' => 'settings'],
];

// Handle Submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['role_name']);
    $role_key = strtolower(trim($_POST['role_key']));
    $description = trim($_POST['description']);
    $permissions = $_POST['permissions'] ?? [];

    // Validation
    $exists = $db->fetch("SELECT id FROM saas_roles WHERE role_key = ? AND id != ?", [$role_key, $id ?: 0]);
    
    if ($exists) {
        Helpers::flash('danger', 'Role Key sudah digunakan oleh role lain.');
    } else {
        $data = [
            'role_name' => $name,
            'role_key' => $role_key,
            'description' => $description,
            'permissions' => json_encode($permissions)
        ];

        // Specific handling for superadmin
        if ($role_key === 'superadmin') {
            $data['permissions'] = json_encode(['all']);
        }

        if ($id) {
            $db->update('saas_roles', $data, 'id = ?', [$id]);
            Helpers::redirect('saas_roles.php', 'success', 'Konfigurasi role berhasil diperbarui.');
        } else {
            $db->insert('saas_roles', $data);
            Helpers::redirect('saas_roles.php', 'success', 'Role baru berhasil ditambahkan ke sistem.');
        }
    }
}

$current_perms = $role ? json_decode($role['permissions'], true) : [];
?>
<?php include 'includes/header.php'; ?>

<style>
    .perm-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(280px, 1fr)); gap: 15px; margin-top: 20px; }
    .perm-item { background: white; border: 1px solid var(--border-color); padding: 15px; border-radius: 12px; display: flex; align-items: center; gap: 12px; cursor: pointer; transition: all 0.2s; }
    .perm-item:hover { border-color: var(--primary); background: rgba(37, 99, 235, 0.02); }
    .perm-item input[type="checkbox"] { width: 18px; height: 18px; cursor: pointer; }
    .perm-icon { width: 32px; height: 32px; border-radius: 8px; background: var(--bg-light); display: flex; align-items: center; justify-content: center; color: var(--text-muted); }
    .perm-item.active { border-color: var(--primary); background: rgba(37, 99, 235, 0.05); }
    .perm-item.active .perm-icon { background: var(--primary); color: white; }
</style>

<div class="app-layout">
  <?php include 'includes/sidebar_saas.php'; ?>

  <div class="main-wrapper">
    <?php include 'includes/topbar.php'; ?>

    <main class="main-content">
      <div class="page-header">
        <div>
          <h1><?= $pageTitle ?></h1>
          <div class="breadcrumb">
            <a href="saas_dashboard.php">Dashboard</a><span class="separator">/</span>
            <a href="saas_roles.php">Roles</a><span class="separator">/</span>
            <span><?= $id ? 'Edit' : 'Tambah' ?></span>
          </div>
        </div>
        <a href="saas_roles.php" class="btn btn-secondary btn-sm">← Kembali ke Daftar</a>
      </div>

      <form method="POST">
        <div class="grid-2-1" style="display:grid; grid-template-columns: 2fr 1fr; gap:30px; align-items:start;">
            <!-- Left: Permissions -->
            <div class="card" style="padding:30px;">
                <div class="card-header border-bottom" style="padding-bottom:15px; margin-bottom:25px;">
                    <h3 class="card-title">🛡️ Pilih Hak Akses Menu</h3>
                    <p style="color:var(--text-muted); font-size:0.85rem; margin-top:5px;">Centang menu yang boleh diakses oleh role ini.</p>
                </div>

                <?php if (($role['role_key'] ?? '') === 'superadmin'): ?>
                    <div style="background:rgba(16,185,129,0.1); border:1px solid rgba(16,185,129,0.2); border-radius:12px; padding:30px; text-align:center;">
                        <i data-lucide="shield-check" style="width:48px; height:48px; color:var(--success); margin-bottom:15px;"></i>
                        <h4 style="color:var(--success); margin-bottom:10px;">Akses Tak Terbatas</h4>
                        <p style="font-size:0.9rem; color:var(--text-secondary); max-width:400px; margin:0 auto;">Role Superadmin memiliki akses otomatis ke seluruh fitur sistem dan tidak dapat dibatasi.</p>
                    </div>
                <?php else: ?>
                    <div class="perm-grid">
                        <?php foreach ($available_menus as $file => $info): 
                            $isActive = in_array($file, $current_perms) || in_array('all', $current_perms);
                        ?>
                        <label class="perm-item <?= $isActive ? 'active' : '' ?>">
                            <input type="checkbox" name="permissions[]" value="<?= $file ?>" <?= $isActive ? 'checked' : '' ?> onchange="this.parentElement.classList.toggle('active')">
                            <div class="perm-icon">
                                <i data-lucide="<?= $info['icon'] ?>" style="width:18px;"></i>
                            </div>
                            <div style="flex:1;">
                                <div style="font-weight:700; font-size:0.88rem;"><?= $info['name'] ?></div>
                                <div style="font-size:0.75rem; color:var(--text-muted);"><?= $file ?></div>
                            </div>
                        </label>
                        <?php endforeach; ?>
                    </div>
                    
                    <div style="margin-top:30px; display:flex; gap:10px;">
                        <button type="button" class="btn btn-xs btn-secondary" onclick="toggleAll(true)">Pilih Semua</button>
                        <button type="button" class="btn btn-xs btn-secondary" onclick="toggleAll(false)">Hapus Semua</button>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Right: Identity -->
            <div class="card" style="padding:25px; border-top:4px solid var(--primary);">
                <div class="form-group" style="margin-bottom:20px;">
                    <label class="form-label">Nama Role</label>
                    <input type="text" name="role_name" class="form-control" placeholder="Contoh: Technical Support" value="<?= htmlspecialchars($role['role_name'] ?? '') ?>" required style="padding:12px; width:100%;">
                </div>

                <div class="form-group" style="margin-bottom:20px;">
                    <label class="form-label">Role Key (Sistem)</label>
                    <input type="text" name="role_key" class="form-control" placeholder="contoh: tech_support" value="<?= htmlspecialchars($role['role_key'] ?? '') ?>" required style="padding:12px; width:100%;" <?= ($role['role_key'] ?? '') === 'superadmin' ? 'readonly' : '' ?> oninput="this.value = this.value.toLowerCase().replace(/ /g,'_')">
                    <small class="text-muted">Gunakan huruf kecil & underscore tanpa spasi.</small>
                </div>

                <div class="form-group" style="margin-bottom:30px;">
                    <label class="form-label">Keterangan / Deskripsi</label>
                    <textarea name="description" class="form-control" rows="4" placeholder="Jelaskan peran divisi ini..." style="padding:12px; width:100%;"><?= htmlspecialchars($role['description'] ?? '') ?></textarea>
                </div>

                <button type="submit" class="btn btn-primary" style="width:100%; padding:14px; font-weight:800; font-size:1rem;">
                    <i data-lucide="save" style="width:20px; margin-right:10px; vertical-align:middle;"></i>
                    Simpan Konfigurasi
                </button>
                <a href="saas_roles.php" class="btn btn-secondary" style="width:100%; padding:14px; margin-top:10px;">Batal</a>
            </div>
        </div>
      </form>
    </main>
  </div>
</div>

<script>
    function toggleAll(status) {
        document.querySelectorAll('.perm-check-input').forEach(cb => {
            cb.checked = status;
            if(status) cb.parentElement.classList.add('active');
            else cb.parentElement.classList.remove('active');
        });
        // Fixing my script selector
        document.querySelectorAll('.perm-item input[type="checkbox"]').forEach(cb => {
            cb.checked = status;
            if(status) cb.parentElement.classList.add('active');
            else cb.parentElement.classList.remove('active');
        });
    }
</script>

<?php include 'includes/footer.php'; ?>
