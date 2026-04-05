<?php
require_once 'classes/Auth.php';
require_once 'classes/Database.php';
require_once 'classes/Helpers.php';

Auth::requireLogin();
$db = Database::getInstance();
$user = Auth::user();

$success_msg = '';
$error_msg = '';

// Handle Profile Update
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['update_profile'])) {
        $name = trim($_POST['name']);
        $email = trim($_POST['email']);
        $phone = trim($_POST['phone']);
        
        if ($db->update('users', ['name' => $name, 'email' => $email, 'phone' => $phone], 'id = ?', [$user['id']])) {
            $_SESSION['user_name'] = $name; // Update session
            Helpers::redirect('profil.php', 'success', 'Profil Anda berhasil diperbarui.');
        }
    }

    if (isset($_FILES['avatar']) && $_FILES['avatar']['error'] == 0) {
        $allowed = ['jpg', 'jpeg', 'png', 'webp'];
        $filename = $_FILES['avatar']['name'];
        $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
        
        if (in_array($ext, $allowed)) {
            $newName = 'avatar_' . $user['id'] . '_' . time() . '.' . $ext;
            $dest = 'uploads/avatars/' . $newName;
            
            if (move_uploaded_file($_FILES['avatar']['tmp_name'], $dest)) {
                if ($user['avatar'] && file_exists($user['avatar'])) { @unlink($user['avatar']); }
                $db->update('users', ['avatar' => $dest], 'id = ?', [$user['id']]);
                Helpers::redirect('profil.php', 'success', 'Foto profil berhasil diperbarui.');
            }
        } else {
            $error_msg = "Format file tidak didukung (Gunakan JPG, PNG, atau WEBP).";
        }
    }

    if (isset($_POST['update_password'])) {
        $old_pass = $_POST['old_password'];
        $new_pass = $_POST['new_password'];
        $confirm_pass = $_POST['confirm_password'];
        
        if (!password_verify($old_pass, $user['password'])) {
            $error_msg = "Kata sandi lama tidak sesuai.";
        } elseif ($new_pass !== $confirm_pass) {
            $error_msg = "Konfirmasi kata sandi tidak cocok.";
        } elseif (strlen($new_pass) < 6) {
            $error_msg = "Kata sandi baru minimal 6 karakter.";
        } else {
            $hashed = password_hash($new_pass, PASSWORD_DEFAULT);
            if ($db->update('users', ['password' => $hashed], 'id = ?', [$user['id']])) {
                Helpers::redirect('profil.php', 'success', 'Kata sandi Anda berhasil diubah.');
            }
        }
    }
}

$pageTitle = 'Profil Saya';
include 'includes/header.php';

// Get Flash Messages
$success_msg = Helpers::getFlash('success');
$error_msg = $error_msg ?: Helpers::getFlash('error');

// Generate Initials
$parts = explode(' ', $user['name']);
$initials = strtoupper(substr($parts[0], 0, 1) . (isset($parts[1]) ? substr($parts[1], 0, 1) : ''));
?>

<div class="app-layout">
  <?php 
    if ($user['role'] === 'superadmin') {
        include 'includes/sidebar_saas.php';
    } else {
        include 'includes/sidebar.php';
    }
  ?>
  
  <div class="main-wrapper">
    <?php include 'includes/topbar.php'; ?>
    <main class="main-content">
      <div class="page-header">
        <h1>Pengaturan Akun</h1>
        <div class="text-muted" style="font-size:0.9rem;">Kelola informasi profil dan keamanan akun Anda</div>
      </div>

      <?php if($success_msg): ?><div class="alert alert-success animate-fadeIn">✅ <?= $success_msg ?></div><?php endif; ?>
      <?php if($error_msg): ?><div class="alert alert-danger animate-fadeIn">❌ <?= $error_msg ?></div><?php endif; ?>

      <div class="grid-2">
        <!-- Dashboard Content Column -->
        <div style="display:flex; flex-direction:column; gap:24px;">
            <!-- Profile Info Card -->
            <div class="card animate-fadeIn">
              <div class="card-header border-bottom">
                <h3 class="card-title">Informasi Dasar</h3>
              </div>
              <div style="padding:24px;">
                <form method="POST" enctype="multipart/form-data">
                <input type="hidden" name="update_profile" value="1">
                
                <div style="display:flex; align-items:center; gap:20px; margin-bottom:30px;">
                    <div style="position:relative; cursor:pointer;" onclick="document.getElementById('avatar-input').click()">
                        <?php if ($user['avatar'] && file_exists($user['avatar'])): ?>
                            <img src="<?= $user['avatar'] ?>" style="width:80px; height:80px; border-radius:50%; object-fit:cover; border:3px solid var(--white); box-shadow:var(--shadow-md);">
                        <?php else: ?>
                            <div style="width:80px; height:80px; border-radius:50%; background:var(--primary); color:white; display:flex; align-items:center; justify-content:center; font-size:1.8rem; font-weight:700; border:3px solid var(--white); box-shadow:var(--shadow-md);">
                                <?= $initials ?>
                            </div>
                        <?php endif; ?>
                        <div style="position:absolute; bottom:0; right:0; background:var(--bg-card); width:28px; height:28px; border-radius:50%; display:flex; align-items:center; justify-content:center; border:1px solid var(--border-color); box-shadow:var(--shadow-sm);">
                            <i data-lucide="camera" style="width:14px; color:var(--text-main);"></i>
                        </div>
                    </div>
                    <input type="file" id="avatar-input" name="avatar" style="display:none" onchange="this.form.submit()">
                    <div>
                        <h3 style="margin:0;"><?= htmlspecialchars($user['name']) ?></h3>
                        <div style="display:flex; gap:8px; align-items:center;">
                            <div class="badge badge-primary"><?= strtoupper($user['role']) ?></div>
                            <span style="font-size:0.75rem; color:var(--text-muted);">Klik foto untuk ganti</span>
                        </div>
                    </div>
                </div>

                <div class="form-group" style="margin-bottom:16px;">
                        <label class="form-label">Nama Lengkap</label>
                        <input type="text" name="name" class="form-control" value="<?= htmlspecialchars($user['name']) ?>" required>
                    </div>
                    <div class="form-group" style="margin-bottom:16px;">
                        <label class="form-label">Email</label>
                        <input type="email" name="email" class="form-control" value="<?= htmlspecialchars($user['email']) ?>" required>
                    </div>
                    <div class="form-group" style="margin-bottom:24px;">
                        <label class="form-label">No. WhatsApp</label>
                        <input type="text" name="phone" class="form-control" value="<?= htmlspecialchars($user['phone'] ?? '') ?>" placeholder="62812xxx">
                    </div>
                    <button type="submit" class="btn btn-primary w-100">SIMPAN PERUBAHAN</button>
                </form>
              </div>
            </div>

            <!-- Notification Settings -->
            <div class="card animate-fadeIn">
              <div class="card-header border-bottom">
                <h3 class="card-title">Preferensi Notifikasi</h3>
              </div>
              <div style="padding:24px;">
                <div style="display:flex; flex-direction:column; gap:16px;">
                    <div style="display:flex; justify-content:space-between; align-items:center;">
                        <div>
                            <div style="font-weight:600; font-size:0.92rem;">Notifikasi WhatsApp</div>
                            <div style="font-size:0.75rem; color:var(--text-muted);">Dapatkan update pembayaran & darurat via WA</div>
                        </div>
                        <label class="switch"><input type="checkbox" checked><span class="slider"></span></label>
                    </div>
                    <div style="display:flex; justify-content:space-between; align-items:center;">
                        <div>
                            <div style="font-weight:600; font-size:0.92rem;">Laporan Email</div>
                            <div style="font-size:0.75rem; color:var(--text-muted);">Terima ringkasan keuangan mingguan</div>
                        </div>
                        <label class="switch"><input type="checkbox"><span class="slider"></span></label>
                    </div>
                    <div style="display:flex; justify-content:space-between; align-items:center;">
                        <div>
                            <div style="font-weight:600; font-size:0.92rem;">Pesan Broadast</div>
                            <div style="font-size:0.75rem; color:var(--text-muted);">Informasi penting dari pengelola</div>
                        </div>
                        <label class="switch"><input type="checkbox" checked><span class="slider"></span></label>
                    </div>
                </div>
              </div>
            </div>
        </div>

        <div style="display:flex; flex-direction:column; gap:24px;">
            <!-- Tenant info (Neighborhood) -->
            <?php 
            $tenant = $db->fetch("SELECT * FROM tenants WHERE id = ?", [$user['tenant_id']]);
            if ($tenant): ?>
            <div class="card animate-fadeIn stagger-1" style="background:var(--bg-main); border-style:dashed;">
              <div class="card-header border-bottom">
                <h3 class="card-title">Informasi Lingkungan</h3>
              </div>
              <div style="padding:20px;">
                <div style="display:flex; align-items:center; gap:12px; margin-bottom:12px;">
                    <div style="padding:8px; background:var(--bg-card); border-radius:8px;"><i data-lucide="map-pin" style="color:var(--primary);"></i></div>
                    <div>
                        <div style="font-size:0.75rem; color:var(--text-muted);">Mengelola Tenant:</div>
                        <div style="font-weight:700; color:var(--text-main);"><?= htmlspecialchars($tenant['name']) ?></div>
                    </div>
                </div>
                <div style="font-size:0.85rem; color:var(--text-muted); line-height:1.4;">
                    Alamat: <?= htmlspecialchars($tenant['address']) ?><br>
                    Status Langganan: <span class="badge badge-success"><?= strtoupper($tenant['subscription_status']) ?></span>
                </div>
              </div>
            </div>
            <?php endif; ?>

            <!-- Security Card -->
            <div class="card animate-fadeIn">
              <div class="card-header border-bottom">
                <h3 class="card-title">Ubah Kata Sandi</h3>
              </div>
              <div style="padding:24px;">
                <form method="POST">
                    <input type="hidden" name="update_password" value="1">
                    <div class="form-group" style="margin-bottom:16px;">
                        <label class="form-label">Kata Sandi Lama</label>
                        <input type="password" name="old_password" class="form-control" required placeholder="••••••••">
                    </div>
                    <div class="form-group" style="margin-bottom:16px;">
                        <label class="form-label">Kata Sandi Baru</label>
                        <input type="password" name="new_password" class="form-control" required placeholder="••••••••">
                    </div>
                    <div class="form-group" style="margin-bottom:24px;">
                        <label class="form-label">Konfirmasi Kata Sandi Baru</label>
                        <input type="password" name="confirm_password" class="form-control" required placeholder="••••••••">
                    </div>
                    <button type="submit" class="btn btn-secondary w-100">UBAH KATA SANDI</button>
                </form>
              </div>
            </div>

            <!-- Personal Activity Log -->
            <?php 
                $logs = $db->fetchAll("SELECT * FROM audit_logs WHERE user_id = ? ORDER BY created_at DESC LIMIT 5", [$user['id']]);
            ?>
            <div class="card animate-fadeIn stagger-2">
              <div class="card-header border-bottom">
                <h3 class="card-title">Aktivitas Terakhir</h3>
              </div>
              <div style="padding:0;">
                <?php if (empty($logs)): ?>
                    <div style="padding:24px; text-align:center; color:var(--text-muted); font-size:0.85rem;">Belum ada riwayat aktivitas.</div>
                <?php else: ?>
                    <?php foreach($logs as $log): ?>
                        <div style="padding:12px 20px; border-bottom:1px solid var(--border-color); display:flex; align-items:center; gap:12px;">
                            <div style="width:8px; height:8px; border-radius:50%; background:var(--primary);"></div>
                            <div style="flex:1;">
                                <div style="font-size:0.85rem; font-weight:600; color:var(--text-main);"><?= strtoupper($log['action']) ?> pada <?= $log['table_name'] ?></div>
                                <div style="font-size:0.75rem; color:var(--text-muted);"><?= date('d M Y, H:i', strtotime($log['created_at'])) ?></div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
              </div>
            </div>
        </div>
      </div>
      
      <!-- Account Info Footer -->
      <div style="margin-top:40px; text-align:center; color:var(--text-muted); font-size:0.8rem; padding-bottom:40px;">
          Akun dibuat pada: <?= date('d F Y, H:i', strtotime($user['created_at'])) ?><br>
          Terakhir login: <?= $user['last_login'] ? date('d F Y, H:i', strtotime($user['last_login'])) : '-' ?>
      </div>
    </main>
  </div>
</div>

<style>
.w-100 { width: 100%; display: flex; justify-content: center; align-items: center; gap: 8px; }
/* Switch UI */
.switch { position: relative; display: inline-block; width: 44px; height: 22px; }
.switch input { opacity: 0; width: 0; height: 0; }
.slider { position: absolute; cursor: pointer; top: 0; left: 0; right: 0; bottom: 0; background-color: #ccc; transition: .4s; border-radius: 22px; }
.slider:before { position: absolute; content: ""; height: 16px; width: 16px; left: 3px; bottom: 3px; background-color: white; transition: .4s; border-radius: 50%; }
input:checked + .slider { background-color: var(--primary); }
input:checked + .slider:before { transform: translateX(22px); }
</style>
    </main>
  </div>
</div>

<style>
.w-100 { width: 100%; display: flex; justify-content: center; align-items: center; gap: 8px; }
</style>

<?php include 'includes/footer.php'; ?>
