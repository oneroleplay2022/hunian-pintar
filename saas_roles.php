<?php
/**
 * SaaS Role & Permission Management - Superadmin Panel
 */
require_once 'classes/Auth.php';
require_once 'classes/Database.php';
require_once 'classes/Helpers.php';

Auth::requireRole('superadmin'); // Still only real superadmin can manage roles
$db = Database::getInstance();

$pageTitle = 'Pengaturan Role & Hak Akses';
$success = '';
$error = '';

$available_menus = [
    'saas_dashboard.php' => 'Dashboard SaaS',
    'saas_tenants.php' => 'Daftar Klien (Tenants)',
    'saas_tickets.php' => 'Tiket Support',
    'saas_broadcast.php' => 'Broadcast WA/Email',
    'saas_announcements.php' => 'Pengumuman Sistem (Lonceng)',
    'saas_billing.php' => 'Tagihan SaaS',
    'saas_pricing.php' => 'Harga & Paket',
    'saas_audit.php' => 'Audit Log Sistem',
    'saas_settings.php' => 'Pengaturan Global',
    'saas_users.php' => 'Manajemen Admin Pusat',
    'saas_roles.php' => 'Pengaturan Role'
];

// Handle Actions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    // ADD / EDIT ROLE
    if ($action === 'save_role') {
        $id = isset($_POST['id']) ? (int)$_POST['id'] : null;
        $name = trim($_POST['role_name']);
        $key = strtolower(trim($_POST['role_key']));
        $desc = trim($_POST['description']);
        $perms = $_POST['permissions'] ?? [];

        if ($key === 'superadmin' && $id) {
             // Block editing superadmin perms to all for safety
             $perms = ['all'];
        }

        $data = [
            'role_name' => $name,
            'role_key' => $key,
            'description' => $desc,
            'permissions' => json_encode($perms)
        ];

        if ($id) {
            $db->update('saas_roles', $data, 'id = ?', [$id]);
            $success = "Role '$name' berhasil diperbarui.";
        } else {
            if ($db->fetch("SELECT id FROM saas_roles WHERE role_key = ?", [$key])) {
                $error = "Role Key '$key' sudah digunakan.";
            } else {
                $db->insert('saas_roles', $data);
                $success = "Role baru '$name' berhasil dibuat.";
            }
        }
    }

    // DELETE ROLE
    if ($action === 'delete_role') {
        $id = (int)$_POST['id'];
        $role = $db->fetch("SELECT role_key FROM saas_roles WHERE id = ?", [$id]);
        if ($role['role_key'] === 'superadmin') {
            $error = "Role Superadmin bawaan tidak dapat dihapus.";
        } else {
            $db->delete('saas_roles', 'id = ?', [$id]);
            $success = "Role berhasil dihapus.";
        }
    }
}

// Fetch Roles
$roles = $db->fetchAll("SELECT * FROM saas_roles ORDER BY id ASC");
?>
<?php include 'includes/header.php'; ?>

<div class="app-layout">
  <?php include 'includes/sidebar_saas.php'; ?>

  <div class="main-wrapper">
    <?php include 'includes/topbar.php'; ?>

    <main class="main-content">
      <div class="page-header">
        <div>
          <h1><?= $pageTitle ?></h1>
          <p style="color:var(--text-muted); font-size:0.9rem;">Tentukan tingkat akses yang berbeda untuk setiap divisi pengelola SaaS.</p>
        </div>
        <a href="saas_role_form.php" class="btn btn-primary btn-sm">
          <i data-lucide="plus" style="width:16px;height:16px;margin-right:8px;"></i> Buat Role Baru
        </a>
      </div>

      <?php if ($success): ?>
        <div class="alert alert-success" style="margin-bottom:20px;">✅ <?= $success ?></div>
      <?php endif; ?>
      <?php if ($error): ?>
        <div class="alert alert-danger" style="margin-bottom:20px;">⚠️ <?= $error ?></div>
      <?php endif; ?>

      <div class="card">
        <div class="table-container">
          <table class="table">
            <thead>
              <tr>
                <th>Nama Role</th>
                <th>Role Key (Sistem)</th>
                <th>Deskripsi</th>
                <th>Izin Menu</th>
                <th>Aksi</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($roles as $r): 
                $perms = json_decode($r['permissions'], true) ?: [];
              ?>
              <tr>
                <td><strong><?= htmlspecialchars($r['role_name']) ?></strong></td>
                <td><code style="background:var(--bg-input); padding:2px 6px; border-radius:4px;"><?= htmlspecialchars($r['role_key']) ?></code></td>
                <td style="font-size:0.85rem; color:var(--text-muted);"><?= htmlspecialchars($r['description'] ?: '-') ?></td>
                <td>
                    <div style="display:flex; flex-wrap:wrap; gap:5px;">
                        <?php if (in_array('all', $perms)): ?>
                            <span class="badge badge-success" style="font-size:0.65rem;">FULL ACCESS</span>
                        <?php else: ?>
                            <?php foreach ($perms as $p): ?>
                                <span class="badge badge-secondary" style="font-size:0.65rem; background:rgba(0,0,0,0.05); color:var(--text-secondary); border:1px solid #ddd;">
                                    <?= $available_menus[$p] ?? $p ?>
                                </span>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                </td>
                <td>
                  <div style="display:flex; gap:10px;">
                    <a href="saas_role_form.php?id=<?= $r['id'] ?>" class="btn btn-icon btn-xs btn-secondary">
                      <i data-lucide="pencil" style="width:14px;height:14px;"></i>
                    </a>
                    <?php if ($r['role_key'] !== 'superadmin'): ?>
                    <form method="POST" style="margin:0;" onsubmit="return confirm('Hapus role ini? User yang memiliki role ini akan kehilangan akses.')">
                        <input type="hidden" name="action" value="delete_role">
                        <input type="hidden" name="id" value="<?= $r['id'] ?>">
                        <button type="submit" class="btn btn-icon btn-xs btn-danger">
                            <i data-lucide="trash-2" style="width:14px;height:14px;"></i>
                        </button>
                    </form>
                    <?php endif; ?>
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

<?php include 'includes/footer.php'; ?>
