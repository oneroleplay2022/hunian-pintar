<?php
/**
 * SaaS Announcements - Superadmin Panel
 */
require_once 'classes/Auth.php';
require_once 'classes/Database.php';
require_once 'classes/Helpers.php';
require_once 'classes/Notification.php';

Auth::requireRole('superadmin');
$db = Database::getInstance();

$pageTitle = 'Pengumuman Sistem';
$success = '';
$error = '';

// Handle Sending Announcement
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'send_announcement') {
    $target = $_POST['target_tenant']; // 'all' or id
    $title = trim($_POST['title']);
    $message = trim($_POST['message']);
    $link = trim($_POST['link'] ?? '');

    if (empty($title) || empty($message)) {
        $error = "Judul dan isi pengumuman tidak boleh kosong.";
    } else {
        $targetQuery = "SELECT id, tenant_id FROM users WHERE role = 'admin'";
        $params = [];
        if ($target !== 'all') {
            $targetQuery .= " AND tenant_id = ?";
            $params[] = (int)$target;
        }

        $admins = $db->fetchAll($targetQuery, $params);
        $count = 0;

        foreach ($admins as $adm) {
            $db->insert('notifications', [
                'tenant_id' => $adm['tenant_id'],
                'user_id' => $adm['id'],
                'title' => $title,
                'message' => $message,
                'link' => $link,
                'is_read' => 0
            ]);
            $count++;
        }

        if ($count > 0) {
            $success = "Pengumuman berhasil terkirim ke $count Admin Perumahan.";
        } else {
            $error = "Tidak ada admin yang ditemukan untuk target ini.";
        }
    }
}

// Fetch all tenants for the dropdown
$tenants = $db->fetchAll("SELECT id, name FROM tenants ORDER BY name");

// Fetch recent announcements? Actually, notifications are individual records.
// In a real system we might have an 'announcements' table too to track history.
// For now, let's just make the UI beautiful.
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
          <p style="color:var(--text-muted); font-size:0.9rem;">Kirimkan pemberitahuan sistem ke seluruh atau spesifik Admin Perumahan.</p>
        </div>
      </div>

      <div class="grid-2-1" style="display:grid; grid-template-columns: 1.5fr 1fr; gap:25px;">
        <!-- Left: Form -->
        <div class="card" style="padding:30px;">
            <div class="card-header border-bottom" style="padding-bottom:15px; margin-bottom:25px;">
                <h3 class="card-title">📝 Buat Pengumuman Baru</h3>
            </div>

            <?php if ($success): ?>
            <div style="background:rgba(16,185,129,0.1); border:1px solid rgba(16,185,129,0.3); color:var(--success); padding:15px; border-radius:8px; margin-bottom:20px;">
                ✅ <?= $success ?>
            </div>
            <?php endif; ?>

            <?php if ($error): ?>
            <div style="background:rgba(239,68,68,0.1); border:1px solid rgba(239,68,68,0.3); color:var(--danger); padding:15px; border-radius:8px; margin-bottom:20px;">
                ⚠️ <?= $error ?>
            </div>
            <?php endif; ?>

            <form method="POST">
                <input type="hidden" name="action" value="send_announcement">
                
                <div class="form-group" style="margin-bottom:20px;">
                    <label class="form-label" style="font-weight:700;">Target Penerima</label>
                    <select name="target_tenant" class="form-control" style="width:100%; padding:12px;">
                        <option value="all">Semua Admin Perumahan (Broadcast)</option>
                        <optgroup label="Spesifik Perumahan">
                            <?php foreach($tenants as $t): ?>
                                <option value="<?= $t['id'] ?>"><?= htmlspecialchars($t['name']) ?></option>
                            <?php endforeach; ?>
                        </optgroup>
                    </select>
                </div>

                <div class="form-group" style="margin-bottom:20px;">
                    <label class="form-label" style="font-weight:700;">Subjek / Judul</label>
                    <input type="text" name="title" class="form-control" placeholder="Contoh: Pemeliharaan Sistem Mendatang" required style="width:100%; padding:12px;">
                </div>

                <div class="form-group" style="margin-bottom:20px;">
                    <label class="form-label" style="font-weight:700;">Isi Pesan Pengumuman</label>
                    <textarea name="message" class="form-control" rows="6" placeholder="Tulis rincian pengumuman di sini..." required style="width:100%; padding:12px;"></textarea>
                    <small class="text-muted">Gunakan bahasa yang jelas dan profesional.</small>
                </div>

                <div class="form-group" style="margin-bottom:25px;">
                    <label class="form-label" style="font-weight:700;">Tautan Tindakan (Opsional)</label>
                    <input type="text" name="link" class="form-control" placeholder="Contoh: saas_payment.php" style="width:100%; padding:12px;">
                    <small class="text-muted">Masukkan path halaman jika ingin mengarahkan user saat notifikasi diklik.</small>
                </div>

                <div style="display:flex; justify-content:flex-end;">
                    <button type="submit" class="btn btn-primary" style="padding:12px 30px; font-weight:700;">
                        <i data-lucide="send" style="width:16px;height:16px;margin-right:8px;vertical-align:middle;"></i>
                        Sebarkan Sekarang
                    </button>
                </div>
            </form>
        </div>

        <!-- Right: Tip & Preview -->
        <div>
            <div class="card" style="padding:25px; margin-bottom:25px; border-left:4px solid var(--primary);">
                <h4 style="margin-bottom:15px; color:var(--primary);">💡 Tips Broadcast</h4>
                <ul style="font-size:0.85rem; color:var(--text-secondary); padding-left:20px; line-height:1.6;">
                    <li>Pengumuman akan muncul secara real-time di ikon lonceng dashboard Admin.</li>
                    <li>Gunakan jika ada update fitur penting atau informasi tagihan.</li>
                    <li>Hindari pengiriman berulang dalam waktu singkat agar tidak mengganggu user.</li>
                </ul>
            </div>

            <div class="card" style="padding:25px;">
                <h4 style="margin-bottom:15px;">🔍 Preview Tampilan</h4>
                <div style="background:var(--bg-light); border:1px solid var(--border-color); border-radius:12px; padding:15px;">
                    <div style="display:flex; gap:12px;">
                        <div style="width:40px; height:40px; border-radius:50%; background:var(--primary); color:white; display:flex; align-items:center; justify-content:center;">
                            <i data-lucide="bell" style="width:20px;"></i>
                        </div>
                        <div style="flex:1;">
                            <div style="font-weight:800; font-size:0.9rem; margin-bottom:2px;" id="prevTitle">Subjek Notifikasi</div>
                            <div style="font-size:0.8rem; color:var(--text-secondary); line-height:1.4;" id="prevMsg">Isi pesan yang Anda tulis akan muncul seperti ini di lonceng notifikasi user penerima...</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
      </div>
    </main>
  </div>
</div>

<script>
    const titleIn = document.querySelector('input[name="title"]');
    const msgIn = document.querySelector('textarea[name="message"]');
    const prevTitle = document.getElementById('prevTitle');
    const prevMsg = document.getElementById('prevMsg');

    titleIn.addEventListener('input', () => { prevTitle.innerText = titleIn.value || 'Subjek Notifikasi'; });
    msgIn.addEventListener('input', () => { prevMsg.innerText = msgIn.value || 'Isi pesan akan muncul di sini...'; });
</script>

<?php include 'includes/footer.php'; ?>
