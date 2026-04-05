<?php
/**
 * SaaS User Form - Add/Edit Superadmin
 */
require_once 'classes/Auth.php';
require_once 'classes/Database.php';
require_once 'classes/Helpers.php';

Auth::requireRole('superadmin');
$db = Database::getInstance();

$id = isset($_GET['id']) ? (int)$_GET['id'] : null;
$admin = $id ? $db->fetch("SELECT * FROM users WHERE id = ?", [$id]) : null;

if ($id && !$admin) {
    Helpers::redirect('saas_users.php', 'danger', 'Data admin tidak ditemukan.');
}

// Fetch available roles from the system
$roles = $db->fetchAll("SELECT role_name, role_key FROM saas_roles ORDER BY id ASC");

$pageTitle = $id ? 'Edit Admin Pusat' : 'Tambah Admin Pusat';

// Handle Submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $phone = trim($_POST['phone']);
    $password = $_POST['password'];
    $role = $_POST['role'] ?? 'superadmin';

    // Check email uniqueness
    $exists = $db->fetch("SELECT id FROM users WHERE email = ? AND id != ?", [$email, $id ?: 0]);
    
    if ($exists) {
        Helpers::flash('danger', 'Email sudah digunakan oleh admin lain.');
    } else {
        $data = [
            'name' => $name,
            'email' => $email,
            'phone' => $phone,
            'role' => $role,
            'is_active' => 1, // Penting agar bisa langsung login
            'tenant_id' => null
        ];

        if ($id) {
            if (!empty($password)) {
                $data['password'] = password_hash($password, PASSWORD_BCRYPT);
            }
            try {
                $db->update('users', $data, 'id = ?', [$id]);
                Helpers::redirect('saas_users.php', 'success', 'Data admin pusat berhasil diperbarui.');
            } catch (PDOException $e) {
                Helpers::flash('danger', 'Gagal update: ' . $e->getMessage());
            }
        } else {
            if (empty($password)) {
                Helpers::flash('danger', 'Kata sandi wajib diisi untuk admin baru.');
            } else {
                $data['password'] = password_hash($password, PASSWORD_BCRYPT);
                try {
                    $insertId = $db->insert('users', $data);
                    if ($insertId) {
                        Helpers::redirect('saas_users.php', 'success', "Admin pusat '$name' berhasil ditambahkan.");
                    } else {
                        Helpers::flash('danger', 'Gagal menyimpan: Error tidak diketahui.');
                    }
                } catch (PDOException $e) {
                    Helpers::flash('danger', 'Gagal menyimpan: ' . $e->getMessage());
                }
            }
        }
    }
}
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
          <div class="breadcrumb">
            <a href="saas_dashboard.php">Dashboard</a><span class="separator">/</span>
            <a href="saas_users.php">Admin Pusat</a><span class="separator">/</span>
            <span><?= $id ? 'Edit' : 'Tambah' ?></span>
          </div>
        </div>
        <a href="saas_users.php" class="btn btn-secondary btn-sm">← Kembali ke Daftar</a>
      </div>

      <div class="card" style="max-width: 800px; margin: 0 auto; padding: 40px; border-top: 4px solid var(--primary);">
        <?php 
        $msg_success = Helpers::getFlash('success');
        $msg_danger = Helpers::getFlash('danger');
        ?>
        <?php if ($msg_success): ?>
            <div class="alert alert-success" style="margin-bottom:20px;">✅ <?= $msg_success ?></div>
        <?php endif; ?>
        <?php if ($msg_danger): ?>
            <div class="alert alert-danger" style="margin-bottom:20px;">⚠️ <?= $msg_danger ?></div>
        <?php endif; ?>

        <form method="POST">
            <div style="display:grid; grid-template-columns: 1fr 1fr; gap:30px; margin-bottom:30px;">
                <!-- Section: Personal Info -->
                <div class="form-section">
                    <h4 style="margin-bottom:20px; color:var(--primary); display:flex; align-items:center; gap:10px;">
                        <i data-lucide="user" style="width:20px;"></i> Data Identitas
                    </h4>
                    
                    <div class="form-group" style="margin-bottom:20px;">
                        <label class="form-label">Nama Lengkap</label>
                        <input type="text" name="name" class="form-control" placeholder="Contoh: Super Admin Utama" value="<?= htmlspecialchars($admin['name'] ?? '') ?>" required style="padding:12px;">
                    </div>

                    <div class="form-group" style="margin-bottom:20px;">
                        <label class="form-label">Nomor Telepon / WA</label>
                        <input type="tel" name="phone" class="form-control" placeholder="08xxxxxxxxxx" value="<?= htmlspecialchars($admin['phone'] ?? '') ?>" style="padding:12px;">
                    </div>

                    <div class="form-group">
                        <label class="form-label">Tingkat Hak Akses (Role)</label>
                        <select name="role" class="form-control" style="padding:12px; width:100%;">
                            <?php foreach ($roles as $r): ?>
                                <option value="<?= $r['role_key'] ?>" <?= ($admin['role'] ?? '') == $r['role_key'] ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($r['role_name']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <small class="text-muted" style="display:block; margin-top:5px;">Konfigurasi menu untuk role ini dapat diatur di <a href="saas_roles.php">Pengaturan Role</a>.</small>
                    </div>
                </div>

                <!-- Section: Login Info -->
                <div class="form-section">
                    <h4 style="margin-bottom:20px; color:var(--primary); display:flex; align-items:center; gap:10px;">
                        <i data-lucide="key" style="width:20px;"></i> Kredensial Login
                    </h4>

                    <div class="form-group" style="margin-bottom:20px;">
                        <label class="form-label">Alamat Email</label>
                        <input type="email" name="email" class="form-control" placeholder="email@perusahaan.com" value="<?= htmlspecialchars($admin['email'] ?? '') ?>" required style="padding:12px;">
                    </div>

                    <div class="form-group">
                        <label class="form-label">Kata Sandi</label>
                        <input type="password" name="password" class="form-control" placeholder="<?= $id ? 'Kosongkan jika tidak ingin diubah' : 'Minimal 6 karakter' ?>" <?= $id ? '' : 'required' ?> style="padding:12px;">
                        <?php if ($id): ?>
                            <small class="text-muted" style="display:block; margin-top:8px;">Gunakan kolom ini hanya jika Anda ingin mereset password admin ini.</small>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- Privilege Info Notification -->
            <div style="background:rgba(59,130,246,0.05); border:1px solid rgba(59,130,246,0.1); border-radius:12px; padding:20px; margin-bottom:40px; display:flex; gap:15px; align-items:flex-start;">
                <div style="width:45px; height:45px; border-radius:50%; background:var(--primary); color:white; display:flex; align-items:center; justify-content:center; flex-shrink:0;">
                    <i data-lucide="shield-check"></i>
                </div>
                <div>
                    <strong style="display:block; margin-bottom:5px;">Keamanan Hak Akses</strong>
                    <p style="font-size:0.85rem; color:var(--text-secondary); line-height:1.6; margin:0;">Role yang Anda pilih menentukan menu apa saja yang dapat diakses oleh admin ini di dashboard pusat. Pastikan memberikan akses sesuai dengan tanggung jawab masing-masing tim.</p>
                </div>
            </div>

            <div style="display:flex; justify-content:flex-end; gap:12px; padding-top:30px; border-top:1px solid var(--border-color);">
                <a href="saas_users.php" class="btn btn-secondary" style="padding:12px 30px;">Batal</a>
                <button type="submit" class="btn btn-primary" style="padding:12px 40px; font-weight:700;">
                    <i data-lucide="save" style="width:18px; margin-right:8px; vertical-align:middle;"></i>
                    <?= $id ? 'Simpan Perubahan' : 'Daftarkan Admin Baru' ?>
                </button>
            </div>
        </form>
      </div>
    </main>
  </div>
</div>

<?php include 'includes/footer.php'; ?>
