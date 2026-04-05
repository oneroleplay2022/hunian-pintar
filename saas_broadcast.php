<?php
require_once 'classes/Auth.php';
require_once 'classes/Database.php';
require_once 'classes/Notification.php';

Auth::requireRole('superadmin');
$db = Database::getInstance();

$success_msg = '';
$error_msg = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $subject = trim($_POST['subject'] ?? '');
    $message = trim($_POST['message'] ?? '');
    $target = $_POST['target'] ?? 'all'; // all, active
    
    if ($subject && $message) {
        $where = "role = 'admin'";
        if ($target === 'active') {
            $where .= " AND tenant_id IN (SELECT id FROM tenants WHERE subscription_status = 'active')";
        }
        
        $admins = $db->fetchAll("SELECT name, email, phone FROM users WHERE $where");
        $count = count($admins);

        foreach ($admins as $admin) {
            Notification::sendEmail($admin['email'], $subject, $message);
            $waText = "*PENGUMUMAN PUSAT:*\n\n" . strip_tags($message);
            Notification::sendWA($admin['phone'], $waText);
        }

        // Log Audit
        $db->insert('audit_logs', [
            'tenant_id' => 0,
            'user_id' => Auth::user()['id'],
            'action' => 'Global Broadcast',
            'table_name' => 'notifications',
            'new_values' => json_encode(['count' => $count, 'subject' => $subject]),
            'ip_address' => $_SERVER['REMOTE_ADDR']
        ]);
        
        Helpers::redirect('saas_broadcast.php', 'success', "Pengumuman berhasil terkirim ke $count Admin Perumahan.");
    } else {
        $error_msg = "Subjek dan Isi Pesan wajib diisi.";
    }
}

$pageTitle = 'Broadcast Global';
include 'includes/header.php';
$success_msg = Helpers::getFlash('success');
$error_msg = $error_msg ?: Helpers::getFlash('error');
?>

<div class="app-layout">
  <?php include 'includes/sidebar_saas.php'; ?>

  <div class="main-wrapper">
    <?php include 'includes/topbar.php'; ?>
    <main class="main-content">
      <div class="page-header"><h1>Kirim Pengumuman Massal</h1></div>

      <?php if ($success_msg): ?><div class="alert alert-success">✅ <?= $success_msg ?></div><?php endif; ?>
      <?php if ($error_msg): ?><div class="alert alert-danger">⚠️ <?= $error_msg ?></div><?php endif; ?>

      <div class="card">
        <div class="card-header border-bottom"><h3 class="card-title">Buat Pengumuman Baru</h3></div>
        <div style="padding:20px;">
          <form method="POST">
            <div class="form-group" style="margin-bottom:15px;">
              <label class="form-label">Target Penerima</label>
              <select name="target" class="form-control" style="width:100%;">
                <option value="all">Seluruh Admin Perumahan (Tenant)</option>
                <option value="active">Tenant Aktif Saja</option>
              </select>
            </div>
            <div class="form-group" style="margin-bottom:15px;">
              <label class="form-label">Subjek Email</label>
              <input type="text" name="subject" class="form-control" placeholder="Contoh: Pemberitahuan Maintenance Sistem" required style="width:100%;">
            </div>
            <div class="form-group" style="margin-bottom:15px;">
              <label class="form-label">Isi Pengumuman (Mendukung HTML untuk Email)</label>
              <textarea name="message" class="form-control" rows="10" placeholder="Ketik pesan Anda di sini..." required style="width:100%;"></textarea>
            </div>
            <div style="background:rgba(59,130,246,0.05); padding:15px; border-radius:8px; margin-bottom:20px; font-size:0.85rem;">
                <strong>Catatan:</strong> Pesan WA akan dikirim secara otomatis sebagai teks biasa (HTML akan dihapus), sedangkan Email akan dikirim dengan format HTML lengkap.
            </div>
            <button type="submit" class="btn btn-primary" onclick="return confirm('Kirim pesan massal ini sekarang?')"><i data-lucide="send" style="width:16px; margin-right:8px;"></i> BLAST SEKARANG</button>
          </form>
        </div>
      </div>
    </main>
  </div>
</div>
<?php include 'includes/footer.php'; ?>
