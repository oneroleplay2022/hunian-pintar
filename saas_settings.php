<?php
require_once 'classes/Auth.php';
require_once 'classes/Database.php';

Auth::requireRole('superadmin');
$db = Database::getInstance();

$pageTitle = 'Pengaturan Global SaaS';

$success_msg = '';
$error_msg = '';

$user = Auth::user();

// Load System Settings
$settingsFile = __DIR__ . '/config/app_settings.json';
$appSettings = ['app_name' => 'WargaKu', 'currency' => 'IDR', 'maintenance_mode' => false];
if (file_exists($settingsFile)) {
    $appSettings = array_merge($appSettings, json_decode(file_get_contents($settingsFile), true) ?: []);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    
    if ($action === 'update_profile') {
        $name = trim($_POST['name'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';
        
        if ($name && $email) {
            $updateData = ['name' => $name, 'email' => $email];
            if ($password) {
                $updateData['password'] = password_hash($password, PASSWORD_BCRYPT);
            }
            
            try {
                $db->update('users', $updateData, 'id = ?', [$user['id']]);
                // Refresh user session data
                $_SESSION['user_name'] = $name;
                Helpers::redirect('saas_settings.php', 'success', 'Profil akun Developer berhasil diperbarui.');
            } catch (Exception $e) {
                $error_msg = "Gagal memperbarui profil. Email mungkin sudah digunakan.";
            }
        } else {
            $error_msg = "Nama dan Email wajib diisi.";
        }
    } elseif ($action === 'update_system') {
        // Hanya perbarui field yang dikirim melalui POST untuk menghindari penimpaan data lama dengan nilai kosong
        $fieldsToUpdate = [
            'app_name', 'currency', 'support_email', 'support_phone', 'maintenance_mode',
            'smtp_host', 'smtp_port', 'smtp_user', 'smtp_pass', 'smtp_encryption',
            'whacenter_device_id', 'whacenter_api_key',
            'payment_instructions', 'payment_gateway_provider',
            'pg_merchant_id', 'pg_client_key', 'pg_server_key', 'pg_mode'
        ];

        foreach ($fieldsToUpdate as $field) {
            if (isset($_POST[$field])) {
                if ($field === 'maintenance_mode') {
                    $appSettings[$field] = true;
                } else {
                    $appSettings[$field] = trim($_POST[$field]);
                }
            } elseif ($field === 'maintenance_mode' && isset($_POST['action']) && $_POST['action'] === 'update_system' && isset($_POST['app_name'])) {
                // Khusus maintenance_mode, jika form config sistem disubmit tapi checkbox tidak dicentang
                $appSettings[$field] = false;
            }
        }

        // Banking (Multi-Account) - Hanya jika ada datanya di POST
        if (isset($_POST['bank_names'])) {
            $bank_names = $_POST['bank_names'] ?? [];
            $bank_numbers = $_POST['bank_numbers'] ?? [];
            $bank_holders = $_POST['bank_holders'] ?? [];
            $bank_accounts = [];
            foreach($bank_names as $index => $name) {
                if(!empty($name)) {
                    $bank_accounts[] = [
                        'name' => $name,
                        'number' => $bank_numbers[$index] ?? '',
                        'holder' => $bank_holders[$index] ?? ''
                    ];
                }
            }
            $appSettings['bank_accounts'] = $bank_accounts;
        }
        
        // Handle Branding Uploads
        $uploadDir = __DIR__ . '/uploads/branding/';
        if (!is_dir($uploadDir)) mkdir($uploadDir, 0755, true);
        
        $brandingUpdated = false;
        if (isset($_FILES['logo']) && $_FILES['logo']['error'] === UPLOAD_ERR_OK) {
            $ext = pathinfo($_FILES['logo']['name'], PATHINFO_EXTENSION);
            $logoName = 'logo_' . time() . '.' . $ext;
            if (move_uploaded_file($_FILES['logo']['tmp_name'], $uploadDir . $logoName)) {
                $appSettings['logo_path'] = 'uploads/branding/' . $logoName;
                $brandingUpdated = true;
            }
        }
        
        if (isset($_FILES['favicon']) && $_FILES['favicon']['error'] === UPLOAD_ERR_OK) {
            $ext = pathinfo($_FILES['favicon']['name'], PATHINFO_EXTENSION);
            $favName = 'favicon_' . time() . '.' . $ext;
            if (move_uploaded_file($_FILES['favicon']['tmp_name'], $uploadDir . $favName)) {
                $appSettings['favicon_path'] = 'uploads/branding/' . $favName;
                $brandingUpdated = true;
            }
        }
        
        if (!is_dir(__DIR__ . '/config')) {
            mkdir(__DIR__ . '/config', 0755, true);
        }
        
        if (file_put_contents($settingsFile, json_encode($appSettings, JSON_PRETTY_PRINT))) {
            $success_msg = "Pengaturan sistem dan branding berhasil disimpan!";
            
            // Catat ke Audit Log
            $db->insert('audit_logs', [
                'user_id' => $user['id'],
                'action' => 'Update Settings',
                'new_values' => json_encode(['description' => 'Memperbarui konfigurasi sistem atau branding.']),
                'ip_address' => $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1'
            ]);
        } else {
            $error_msg = "Gagal menyimpan pengaturan ke dalam file konfigurasi.";
        }
    }
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
          <h1>Pengaturan Sistem</h1>
          <div class="breadcrumb">
            <span class="separator">/</span>
            <span>Konfigurasi & Profil Developer</span>
          </div>
        </div>
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

      <!-- ROW 2: PROFILE & BRANDING (Tetap di atas atau sudah Anda sukai) -->
      <div class="grid-2" style="display:grid; grid-template-columns: 1fr 1fr; gap:20px;">
        <div class="card">
          <div class="card-header border-bottom">
            <h3 class="card-title">Profil Akun Developer (Pusat)</h3>
          </div>
          <div style="padding:20px;">
            <form method="POST">
              <input type="hidden" name="action" value="update_profile">
              <div class="form-group" style="margin-bottom:15px;">
                <label class="form-label">Nama Pengembang</label>
                <input type="text" name="name" class="form-control" value="<?= htmlspecialchars($user['name']) ?>" required>
              </div>
              <div class="form-group" style="margin-bottom:15px;">
                <label class="form-label">Email Login</label>
                <input type="email" name="email" class="form-control" value="<?= htmlspecialchars($user['email']) ?>" required>
              </div>
              <button type="submit" class="btn btn-primary" style="width:100%;">SIMPAN PROFIL</button>
            </form>
          </div>
        </div>

        <div class="card">
          <div class="card-header border-bottom">
            <h3 class="card-title">Branding Logo & Favicon</h3>
          </div>
          <div style="padding:20px;">
            <form method="POST" enctype="multipart/form-data">
              <input type="hidden" name="action" value="update_system">
              <div style="display:flex; gap:20px; margin-bottom:15px;">
                <div style="flex:1; text-align:center;">
                    <label class="form-label">Logo</label>
                    <img src="<?= $appSettings['logo_path'] ?? 'assets/img/logo-placeholder.png' ?>" style="max-height:50px; display:block; margin:10px auto;">
                    <input type="file" name="logo" class="form-control" accept="image/*" style="font-size:0.75rem;">
                </div>
                <div style="flex:1; text-align:center;">
                    <label class="form-label">Favicon</label>
                    <img src="<?= $appSettings['favicon_path'] ?? 'favicon.ico' ?>" style="height:35px; width:35px; display:block; margin:10px auto;">
                    <input type="file" name="favicon" class="form-control" accept="image/*" style="font-size:0.75rem;">
                </div>
              </div>
              <button type="submit" class="btn btn-primary" style="width:100%;">SIMPAN BRANDING</button>
            </form>
          </div>
        </div>
      </div>

      <!-- ROW 3: SYSTEM CONFIG & AUDIT LOG -->
      <div class="grid-2" style="display:grid; grid-template-columns: 1.2fr 0.8fr; gap:20px; margin-top:20px;">
        <div class="card">
          <div class="card-header border-bottom">
            <h3 class="card-title">Konfigurasi Sistem & Support</h3>
          </div>
          <div style="padding:20px;">
            <form method="POST">
              <input type="hidden" name="action" value="update_system">
              <div style="display:grid; grid-template-columns: 1fr 1fr; gap:15px; margin-bottom:15px;">
                  <div class="form-group">
                    <label class="form-label">Nama App</label>
                    <input type="text" name="app_name" class="form-control" value="<?= htmlspecialchars($appSettings['app_name']) ?>" required>
                  </div>
                  <div class="form-group">
                    <label class="form-label">Mata Uang</label>
                    <input type="text" name="currency" class="form-control" value="<?= htmlspecialchars($appSettings['currency']) ?>" required>
                  </div>
              </div>
              <div style="display:grid; grid-template-columns: 1fr 1fr; gap:15px; margin-bottom:15px;">
                  <div class="form-group">
                    <label class="form-label">Email Support</label>
                    <input type="email" name="support_email" class="form-control" value="<?= htmlspecialchars($appSettings['support_email'] ?? '') ?>">
                  </div>
                  <div class="form-group">
                    <label class="form-label">WhatsApp Support</label>
                    <input type="text" name="support_phone" class="form-control" value="<?= htmlspecialchars($appSettings['support_phone'] ?? '') ?>">
                  </div>
              </div>
              <label class="checkbox-label" style="background:rgba(239,68,68,0.05); padding:10px; border-radius:8px; display:flex; align-items:center; gap:10px;">
                <input type="checkbox" name="maintenance_mode" <?= ($appSettings['maintenance_mode'] ?? false) ? 'checked' : '' ?>> 
                <strong>Maintenance Mode Global (Kunci seluruh akses client)</strong>
              </label>
              <button type="submit" class="btn btn-primary" style="width:100%; margin-top:15px;">SIMPAN KONFIGURASI</button>
            </form>
          </div>
        </div>

        <div class="card">
          <div class="card-header border-bottom">
            <h3 class="card-title">Audit Log Aktivitas Pusat</h3>
          </div>
          <div class="table-container" style="max-height:285px; overflow-y:auto;">
            <table class="table" style="font-size:0.8rem;">
                <thead>
                    <tr><th>Waktu</th><th>User</th><th>Aksi</th></tr>
                </thead>
                <tbody>
                    <?php 
                    $logs = $db->fetchAll("SELECT a.*, u.name as user_name FROM audit_logs a LEFT JOIN users u ON a.user_id = u.id ORDER BY a.created_at DESC LIMIT 20");
                    if (empty($logs)): ?>
                        <tr><td colspan="3" style="text-align:center;padding:20px;">Belum ada log aktivitas.</td></tr>
                    <?php endif; 
                    foreach ($logs as $l): ?>
                        <tr>
                            <td><?= date('d/m H:i', strtotime($l['created_at'])) ?></td>
                            <td><strong><?= htmlspecialchars($l['user_name'] ?? 'System') ?></strong></td>
                            <td><span class="badge badge-success"><?= htmlspecialchars($l['action']) ?></span></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
          </div>
        </div>
      </div>

      <!-- ROW 4: MULTI-BANK & PG -->
      <div class="grid-2" style="display:grid; grid-template-columns: 1fr 1fr; gap:20px; margin-top:20px;">
        <!-- Multi Bank Accounts -->
        <div class="card">
          <div class="card-header border-bottom" style="display:flex; justify-content:space-between;">
            <h3 class="card-title">Rekening Bank Pusat (Manual)</h3>
            <button type="button" class="btn btn-sm btn-secondary" onclick="addBankRow()"><i data-lucide="plus"></i> Tambah</button>
          </div>
          <div style="padding:20px;">
            <form method="POST">
              <input type="hidden" name="action" value="update_system">
              <div id="bank-list-container">
                <?php 
                $bankAccounts = $appSettings['bank_accounts'] ?? [];
                if(empty($bankAccounts)) $bankAccounts[] = ['name' => '', 'number' => '', 'holder' => ''];
                foreach($bankAccounts as $index => $bank): 
                ?>
                <div class="bank-row" style="display:grid; grid-template-columns: 1fr 1.5fr 1.5fr 40px; gap:8px; margin-bottom:10px; align-items:center;">
                    <input type="text" name="bank_names[]" class="form-control" placeholder="Bank" value="<?= htmlspecialchars($bank['name'] ?? '') ?>" style="font-size:0.8rem; padding:8px;">
                    <input type="text" name="bank_numbers[]" class="form-control" placeholder="No. Rek" value="<?= htmlspecialchars($bank['number'] ?? '') ?>" style="font-size:0.8rem; padding:8px;">
                    <input type="text" name="bank_holders[]" class="form-control" placeholder="A/N" value="<?= htmlspecialchars($bank['holder'] ?? '') ?>" style="font-size:0.8rem; padding:8px;">
                    <button type="button" class="btn btn-icon btn-sm" style="color:var(--danger);" onclick="this.parentElement.remove()">&times;</button>
                </div>
                <?php endforeach; ?>
              </div>
              <div class="form-group" style="margin-top:15px;">
                <label class="form-label">Instruksi Pembayaran Manual</label>
                <textarea name="payment_instructions" class="form-control" rows="3" style="font-size:0.8rem;"><?= htmlspecialchars($appSettings['payment_instructions'] ?? '') ?></textarea>
              </div>
              <button type="submit" class="btn btn-primary" style="width:100%;">SIMPAN DATA REKENING</button>
            </form>
          </div>
        </div>

        <!-- Payment Gateway -->
        <div class="card">
          <div class="card-header border-bottom">
            <h3 class="card-title">Integrasi Payment Gateway (PG)</h3>
          </div>
          <div style="padding:20px;">
            <form method="POST">
                <input type="hidden" name="action" value="update_system">
                <div style="display:flex; gap:10px; margin-bottom:10px;">
                    <div style="flex:1;">
                        <label class="form-label">Provider</label>
                        <select name="payment_gateway_provider" class="form-control">
                            <option value="none" <?= ($appSettings['payment_gateway_provider'] ?? 'none') === 'none' ? 'selected' : '' ?>>Nonaktif</option>
                            <option value="midtrans" <?= ($appSettings['payment_gateway_provider'] ?? 'none') === 'midtrans' ? 'selected' : '' ?>>Midtrans</option>
                            <option value="xendit" <?= ($appSettings['payment_gateway_provider'] ?? 'none') === 'xendit' ? 'selected' : '' ?>>Xendit</option>
                        </select>
                    </div>
                    <div style="flex:1;">
                        <label class="form-label">Environment</label>
                        <select name="pg_mode" class="form-control">
                            <option value="sandbox" <?= ($appSettings['pg_mode'] ?? 'sandbox') === 'sandbox' ? 'selected' : '' ?>>Sandbox</option>
                            <option value="production" <?= ($appSettings['pg_mode'] ?? 'sandbox') === 'production' ? 'selected' : '' ?>>Production</option>
                        </select>
                    </div>
                </div>
                <div class="form-group" style="margin-bottom:8px;">
                    <label class="form-label">Client Key</label>
                    <input type="text" name="pg_client_key" class="form-control" value="<?= htmlspecialchars($appSettings['pg_client_key'] ?? '') ?>" style="font-size:0.8rem; padding:8px;">
                </div>
                <div class="form-group" style="margin-bottom:15px;">
                    <label class="form-label">Server Key (Secret)</label>
                    <input type="password" name="pg_server_key" class="form-control" value="<?= htmlspecialchars($appSettings['pg_server_key'] ?? '') ?>" style="font-size:0.8rem; padding:8px;">
                </div>
                <button type="submit" class="btn btn-primary" style="width:100%;">SIMPAN API GATEWAY</button>
            </form>
          </div>
        </div>
      </div>

      <!-- ROW 5: SMTP & WHACENTER -->
      <div class="grid-2" style="display:grid; grid-template-columns: 1fr 1fr; gap:20px; margin-top:20px;">
        <!-- SMTP Settings -->
        <div class="card">
          <div class="card-header border-bottom">
            <h3 class="card-title">Konfigurasi Email (SMTP)</h3>
          </div>
          <div style="padding:20px;">
            <form method="POST">
                <input type="hidden" name="action" value="update_system">
                <div style="display:grid; grid-template-columns: 2fr 1fr; gap:10px; margin-bottom:10px;">
                    <div>
                        <label class="form-label">SMTP Host</label>
                        <input type="text" name="smtp_host" class="form-control" placeholder="mail.wargaku.com" value="<?= htmlspecialchars($appSettings['smtp_host'] ?? '') ?>" style="font-size:0.8rem; padding:8px;">
                    </div>
                    <div>
                        <label class="form-label">Port</label>
                        <input type="text" name="smtp_port" class="form-control" placeholder="465" value="<?= htmlspecialchars($appSettings['smtp_port'] ?? '') ?>" style="font-size:0.8rem; padding:8px;">
                    </div>
                </div>
                <div style="display:grid; grid-template-columns: 1.5fr 1fr; gap:10px; margin-bottom:10px;">
                    <div>
                        <label class="form-label">Username (Email)</label>
                        <input type="text" name="smtp_user" class="form-control" placeholder="no-reply@wargaku.com" value="<?= htmlspecialchars($appSettings['smtp_user'] ?? '') ?>" style="font-size:0.8rem; padding:8px;">
                    </div>
                    <div>
                        <label class="form-label">Encryption</label>
                        <select name="smtp_encryption" class="form-control" style="font-size:0.8rem; padding:8px;">
                            <option value="tls" <?= ($appSettings['smtp_encryption'] ?? 'tls') === 'tls' ? 'selected' : '' ?>>TLS (587)</option>
                            <option value="ssl" <?= ($appSettings['smtp_encryption'] ?? 'tls') === 'ssl' ? 'selected' : '' ?>>SSL (465)</option>
                        </select>
                    </div>
                </div>
                <div class="form-group" style="margin-bottom:15px;">
                    <label class="form-label">SMTP Password</label>
                    <input type="password" name="smtp_pass" class="form-control" value="<?= htmlspecialchars($appSettings['smtp_pass'] ?? '') ?>" style="font-size:0.8rem; padding:8px;">
                </div>
                <button type="submit" class="btn btn-primary" style="width:100%;">SIMPAN SETTING EMAIL</button>
            </form>
          </div>
        </div>

        <!-- WHACenter Settings -->
        <div class="card">
          <div class="card-header border-bottom">
            <h3 class="card-title">Notifikasi WhatsApp (Whacenter)</h3>
          </div>
          <div style="padding:20px;">
            <form method="POST">
                <input type="hidden" name="action" value="update_system">
                <div class="form-group" style="margin-bottom:10px;">
                    <label class="form-label">Device ID</label>
                    <input type="text" name="whacenter_device_id" class="form-control" placeholder="Contoh: 812xxx" value="<?= htmlspecialchars($appSettings['whacenter_device_id'] ?? '') ?>" style="font-size:0.8rem; padding:8px;">
                </div>
                <div class="form-group" style="margin-bottom:15px;">
                    <label class="form-label">API Key (Whacenter)</label>
                    <input type="password" name="whacenter_api_key" class="form-control" value="<?= htmlspecialchars($appSettings['whacenter_api_key'] ?? '') ?>" style="font-size:0.8rem; padding:8px;">
                </div>
                <div style="padding:15px; border-radius:8px; background:rgba(16,185,129,0.05); border:1px solid rgba(16,185,129,0.1); margin-bottom:15px;">
                    <div style="font-size:0.75rem; color:var(--success); font-weight:600;">WHACenter status: Active Integration</div>
                    <div style="font-size:0.7rem; color:var(--text-muted); margin-top:5px;">Notifikasi akan terkirim otomatis saat ada pendaftaran atau tagihan baru.</div>
                </div>
                <button type="submit" class="btn btn-primary" style="width:100%;">SIMPAN API WHATSAPP</button>
            </form>
          </div>
        </div>
      </div>
 
      <div class="card" style="margin-top:20px; border:1px solid var(--danger); background:rgba(239,68,68,0.01);">
          <div style="padding:20px; display:flex; justify-content:space-between; align-items:center;">
              <div>
                  <h4 style="margin:0; color:var(--danger);"><i data-lucide="database" style="width:18px; margin-right:8px;"></i>Pusat Keamanan: Backup Database</h4>
                  <p style="margin:5px 0 0 0; font-size:0.8rem; color:var(--text-muted);">Unduh salinan lengkap database sistem (.sql) untuk cadangan.</p>
              </div>
              <a href="saas_backup.php" class="btn btn-sm" style="background:var(--danger); color:white; border:none; padding:8px 20px; border-radius:8px;">Buka Panel Backup</a>
          </div>
      </div>
      </div>
    </main>
  </div>
</div>

<script>
function addBankRow() {
    const container = document.getElementById('bank-list-container');
    const div = document.createElement('div');
    div.className = 'bank-row';
    div.style = 'display:grid; grid-template-columns: 1fr 1.5fr 1.5fr 40px; gap:8px; margin-bottom:10px; align-items:center;';
    div.innerHTML = `
        <input type="text" name="bank_names[]" class="form-control" placeholder="Bank" style="font-size:0.8rem; padding:8px;">
        <input type="text" name="bank_numbers[]" class="form-control" placeholder="No. Rek" style="font-size:0.8rem; padding:8px;">
        <input type="text" name="bank_holders[]" class="form-control" placeholder="A/N" style="font-size:0.8rem; padding:8px;">
        <button type="button" class="btn btn-icon btn-sm" style="color:var(--danger);" onclick="this.parentElement.remove()">&times;</button>
    `;
    container.appendChild(div);
}
</script>

<?php include 'includes/footer.php'; ?>
