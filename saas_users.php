<?php
/**
 * SaaS User Management - Superadmin Panel
 */
require_once 'classes/Auth.php';
require_once 'classes/Database.php';
require_once 'classes/Helpers.php';

Auth::requireRole('superadmin');
$db = Database::getInstance();
$currentUser = Auth::user();

$pageTitle = 'Manajemen Admin Pusat';
$success = '';
$error = '';

// Handle Actions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    // ADD USER
    if ($action === 'add_user') {
        $name = trim($_POST['name']);
        $email = trim($_POST['email']);
        $phone = trim($_POST['phone']);
        $password = $_POST['password'];

        // Validation
        if ($db->fetch("SELECT id FROM users WHERE email = ?", [$email])) {
            $error = "Email sudah digunakan.";
        } else {
            $db->insert('users', [
                'tenant_id' => null,
                'name' => $name,
                'email' => $email,
                'phone' => $phone,
                'password' => password_hash($password, PASSWORD_BCRYPT),
                'role' => 'superadmin',
                'is_active' => 1
            ]);
            $success = "Admin pusat baru berhasil ditambahkan.";
        }
    }

    // EDIT USER
    if ($action === 'edit_user') {
        $id = (int)$_POST['id'];
        $name = trim($_POST['name']);
        $email = trim($_POST['email']);
        $phone = trim($_POST['phone']);
        $password = $_POST['password'];

        $data = [
            'name' => $name,
            'email' => $email,
            'phone' => $phone
        ];

        if (!empty($password)) {
            $data['password'] = password_hash($password, PASSWORD_BCRYPT);
        }

        $db->update('users', $data, 'id = ? AND tenant_id IS NULL', [$id]);
        $success = "Data admin pusat berhasil diperbarui.";
    }

    // DELETE USER
    if ($action === 'delete_user') {
        $id = (int)$_POST['id'];
        if ($id === (int)$currentUser['id']) {
            $error = "Anda tidak dapat menghapus akun Anda sendiri.";
        } else {
            $db->delete('users', 'id = ? AND tenant_id IS NULL', [$id]);
            $success = "Admin pusat berhasil dihapus.";
        }
    }
}

// Fetch All Superadmins (Global Admin with no specific tenant)
$admins = $db->fetchAll("SELECT * FROM users WHERE tenant_id IS NULL ORDER BY created_at DESC");
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
          <p style="color:var(--text-muted); font-size:0.9rem;">Kelola pengguna yang memiliki akses penuh ke panel kontrol SaaS.</p>
        </div>
        <a href="saas_user_form.php" class="btn btn-primary btn-sm">
          <i data-lucide="plus" style="width:16px;height:16px;margin-right:8px;"></i> Tambah Admin
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
                <th>Nama</th>
                <th>Email / Kontak</th>
                <th>Terakhir Login</th>
                <th>Tgl Terdaftar</th>
                <th>Aksi</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($admins as $adm): ?>
              <tr>
                <td>
                  <div style="display:flex; align-items:center; gap:12px;">
                    <div style="width:36px; height:36px; border-radius:50%; background:var(--primary); color:white; display:flex; align-items:center; justify-content:center; font-weight:700;">
                        <?= substr($adm['name'], 0, 1) ?>
                    </div>
                    <div>
                        <div style="font-weight:700;"><?= htmlspecialchars($adm['name']) ?></div>
                        <?php if ($adm['id'] == $currentUser['id']): ?>
                            <span class="badge badge-primary" style="font-size:0.6rem;">ANDA</span>
                        <?php endif; ?>
                    </div>
                  </div>
                </td>
                <td>
                    <div style="font-size:0.9rem;"><?= htmlspecialchars($adm['email']) ?></div>
                    <div style="font-size:0.75rem; color:var(--text-muted);"><?= htmlspecialchars($adm['phone'] ?: '-') ?></div>
                </td>
                <td>
                    <div style="font-size:0.85rem;"><?= $adm['last_login'] ? date('d M Y, H:i', strtotime($adm['last_login'])) : '<span class="text-muted">Belum pernah</span>' ?></div>
                </td>
                <td>
                    <div style="font-size:0.85rem; color:var(--text-muted);"><?= date('d M Y', strtotime($adm['created_at'])) ?></div>
                </td>
                <td>
                  <div style="display:flex; gap:10px;">
                    <a href="saas_user_form.php?id=<?= $adm['id'] ?>" class="btn btn-icon btn-xs btn-secondary">
                      <i data-lucide="pencil" style="width:14px;height:14px;"></i>
                    </a>
                    <?php if ($adm['id'] != $currentUser['id']): ?>
                    <form method="POST" style="margin:0;" onsubmit="return confirm('Hapus admin ini? Tindakan ini tidak dapat dibatalkan.')">
                        <input type="hidden" name="action" value="delete_user">
                        <input type="hidden" name="id" value="<?= $adm['id'] ?>">
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
