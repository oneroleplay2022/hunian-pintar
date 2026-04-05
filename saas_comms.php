<?php
/**
 * SaaS Communications (Unified Broadcast) - Superadmin Panel
 */
require_once 'classes/Auth.php';
require_once 'classes/Database.php';
require_once 'classes/Helpers.php';
require_once 'classes/Notification.php';

Auth::requireRole('superadmin');
$db = Database::getInstance();

$pageTitle = 'Pusat Komunikasi Hub';
$success = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'send_comm') {
    $channels = $_POST['channels'] ?? []; // ['system', 'wa', 'email']
    $target = $_POST['target_tenant']; // 'all', 'active', or id
    $subject = trim($_POST['subject']);
    $message = trim($_POST['message']);
    $link = trim($_POST['link'] ?? '');

    if (empty($channels)) {
        $error = "Pilih minimal satu saluran pengiriman (System, WA, atau Email).";
    } elseif (empty($subject) || empty($message)) {
        $error = "Subjek dan isi pengumuman tidak boleh kosong.";
    } else {
        // Build query
        $where = "role = 'admin'";
        $params = [];
        
        if ($target === 'active') {
            $where .= " AND tenant_id IN (SELECT id FROM tenants WHERE subscription_status = 'active')";
        } elseif ($target !== 'all') {
            $where .= " AND tenant_id = ?";
            $params[] = (int)$target;
        }

        $admins = $db->fetchAll("SELECT id, name, email, phone, tenant_id FROM users WHERE $where", $params);
        $count = count($admins);

        if ($count > 0) {
            foreach ($admins as $adm) {
                // ... (Existing delivery code)
                if (in_array('system', $channels)) {
                    $db->insert('notifications', [
                        'tenant_id' => $adm['tenant_id'], 'user_id' => $adm['id'],
                        'title' => $subject, 'message' => $message,
                        'link' => $link, 'is_read' => 0
                    ]);
                }
                if (in_array('wa', $channels) && !empty($adm['phone'])) {
                    $waText = "*[PENGUMUMAN PUSAT]*\n\n*{$subject}*\n\n" . strip_tags($message);
                    if ($link) $waText .= "\n\nLihat selengkapnya: " . $link;
                    Notification::sendWA($adm['phone'], $waText);
                }
                if (in_array('email', $channels) && !empty($adm['email'])) {
                    Notification::sendEmail($adm['email'], $subject, "<h2>{$subject}</h2><p>{$message}</p>");
                }
            }

            // --- SAVE HISTORY ---
            $targetName = ($target === 'all') ? 'Semua Admin' : (($target === 'active') ? 'Hanya Admin Aktif' : ($db->fetchColumn("SELECT name FROM tenants WHERE id = ?", [$target]) ?: 'Tenant ID: '.$target));

            $db->insert('saas_communications', [
                'sender_id' => Auth::user()['id'],
                'channels' => json_encode($channels),
                'target_type' => ($target === 'all' || $target === 'active') ? $target : 'tenant',
                'target_name' => $targetName,
                'subject' => $subject,
                'message' => $message,
                'recipient_count' => $count
            ]);

            $success = "Pesan berhasil diproses ke $count Admin melalui " . implode(', ', array_map('strtoupper', $channels));
        } else {
            $error = "Tidak ada admin yang ditemukan untuk kriteria target tersebut.";
        }
    }
}

// Fetch History
$history = $db->fetchAll("SELECT * FROM saas_communications ORDER BY created_at DESC LIMIT 50");
$tenants = $db->fetchAll("SELECT id, name FROM tenants ORDER BY name");
?>
<?php include 'includes/header.php'; ?>

<style>
    .channel-card {
        border: 2px solid var(--border-color);
        border-radius: 12px;
        padding: 15px;
        cursor: pointer;
        transition: all 0.2s;
        display: flex;
        align-items: center;
        gap: 12px;
        background: var(--bg-card);
    }
    .channel-card input { width: 18px; height: 18px; cursor: pointer; }
    .channel-card:hover { border-color: var(--primary-light); background: rgba(99,102,241,0.02); }
    .channel-card.active { border-color: var(--primary); background: rgba(99,102,241,0.05); }
    .channel-icon { 
        width: 40px; height: 40px; border-radius: 10px; 
        display: flex; align-items: center; justify-content: center; font-size: 1.2rem;
    }
</style>

<div class="app-layout">
  <?php include 'includes/sidebar_saas.php'; ?>

  <div class="main-wrapper">
    <?php include 'includes/topbar.php'; ?>

    <main class="main-content">
      <div class="page-header">
        <div>
          <h1>Halaman Broadcast Terpadu</h1>
          <p style="color:var(--text-muted); font-size:0.9rem;">Kirimkan pesan massal melalui berbagai saluran komunikasi sekaligus.</p>
        </div>
      </div>

      <?php if ($success): ?>
        <div class="alert alert-success" style="margin-bottom:20px;">✅ <?= $success ?></div>
      <?php endif; ?>
      <?php if ($error): ?>
        <div class="alert alert-danger" style="margin-bottom:20px;">⚠️ <?= $error ?></div>
      <?php endif; ?>

      <form method="POST">
        <input type="hidden" name="action" value="send_comm">
        
        <div class="grid-2-1" style="display:grid; grid-template-columns: 1.8fr 1fr; gap:30px; align-items:start;">
            <!-- Left: Message & Channels -->
            <div class="card" style="padding:30px;">
                <div style="margin-bottom:30px; background:rgba(0,0,0,0.02); padding:25px; border-radius:15px; border:1px dashed var(--border-color);">
                    <label class="form-label" style="font-weight:900; font-size:1.1rem; color:var(--primary); margin-bottom:20px; display:flex; align-items:center; gap:10px;">
                        <i data-lucide="layers" style="width:22px;"></i> LANGKAH 1: Pilih Saluran Pengiriman
                    </label>
                    
                    <div style="display:grid; grid-template-columns: repeat(3, 1fr); gap:15px;">
                        <!-- System Channel -->
                        <label class="channel-card active" id="label-system" style="border-width:2px; padding:20px; flex-direction:column; text-align:center;">
                            <input type="checkbox" name="channels[]" value="system" checked onchange="toggleCard(this, 'label-system')" style="position:absolute; top:12px; right:12px;">
                            <div class="channel-icon" style="width:50px; height:50px; margin-bottom:10px; background:var(--primary); color:white; border-radius:50%; box-shadow:0 10px 15px var(--primary-glow);">
                                <i data-lucide="bell" style="width:24px; height:24px;"></i>
                            </div>
                            <div style="font-weight:800; font-size:0.95rem;">Notifikasi Lonceng</div>
                            <div style="font-size:0.75rem; color:var(--text-muted);">(Dalam Aplikasi)</div>
                        </label>

                        <!-- WA Channel -->
                        <label class="channel-card" id="label-wa" style="border-width:2px; padding:20px; flex-direction:column; text-align:center;">
                            <input type="checkbox" name="channels[]" value="wa" onchange="toggleCard(this, 'label-wa')" style="position:absolute; top:12px; right:12px;">
                            <div class="channel-icon" style="width:50px; height:50px; margin-bottom:10px; background:#25d366; color:white; border-radius:50%; box-shadow:0 10px 15px rgba(37,211,102,0.2);">
                                <i data-lucide="message-circle" style="width:24px; height:24px;"></i>
                            </div>
                            <div style="font-weight:800; font-size:0.95rem;">WhatsApp Blast</div>
                            <div style="font-size:0.75rem; color:var(--text-muted);">(WHACenter)</div>
                        </label>

                        <!-- Email Channel -->
                        <label class="channel-card" id="label-email" style="border-width:2px; padding:20px; flex-direction:column; text-align:center;">
                            <input type="checkbox" name="channels[]" value="email" onchange="toggleCard(this, 'label-email')" style="position:absolute; top:12px; right:12px;">
                            <div class="channel-icon" style="width:50px; height:50px; margin-bottom:10px; background:#f59e0b; color:white; border-radius:50%; box-shadow:0 10px 15px rgba(245,158,11,0.2);">
                                <i data-lucide="mail" style="width:24px; height:24px;"></i>
                            </div>
                            <div style="font-weight:800; font-size:0.95rem;">Email Broadcast</div>
                            <div style="font-size:0.75rem; color:var(--text-muted);">(SMTP Service)</div>
                        </label>
                    </div>
                    <p style="margin-top:15px; font-size:0.8rem; color:var(--text-secondary); text-align:center;">Anda bisa memilih lebih dari satu saluran sekaligus.</p>
                </div>

                <div style="margin-bottom:25px;">
                    <label class="form-label" style="font-weight:800; font-size:1.1rem; margin-bottom:20px; display:flex; align-items:center; gap:10px;">
                        <i data-lucide="file-text" style="width:22px;"></i> LANGKAH 2: Tulis Pesan Anda
                    </label>
                    <div class="form-group" style="margin-bottom:20px;">
                        <label class="form-label">Subjek / Judul Pesan</label>
                        <input type="text" name="subject" class="form-control" placeholder="Contoh: Pemberitahuan Pemeliharaan Server" required style="width:100%; padding:14px; font-size:1rem;">
                    </div>
                    <div class="form-group" style="margin-bottom:20px;">
                        <label class="form-label">Isi Pesan</label>
                        <textarea name="message" class="form-control" rows="8" placeholder="Tuliskan detail pengumuman di sini..." required style="width:100%; padding:14px; line-height:1.6;"></textarea>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Tautan Terkait (Opsional)</label>
                        <input type="text" name="link" class="form-control" placeholder="https://..." style="width:100%; padding:14px;">
                    </div>
                </div>

                <div style="display:flex; justify-content:flex-end;">
                    <button type="submit" class="btn btn-primary" style="padding:15px 40px; font-size:1rem; font-weight:800; box-shadow:0 10px 20px var(--primary-glow);" onclick="return confirm('Kirimkan pesan ini ke seluruh target pilihan?')">
                        <i data-lucide="send" style="width:20px; margin-right:10px;"></i> PROSES & KIRIM SEKARANG
                    </button>
                </div>
            </div>

            <!-- Right: Settings & Preview -->
            <div>
                <div class="card" style="padding:25px; margin-bottom:25px; border-top:4px solid var(--primary);">
                    <label class="form-label" style="font-weight:800; margin-bottom:15px; display:block;">3. Target Penerima</label>
                    <div class="form-group">
                        <select name="target_tenant" class="form-control" style="width:100%; padding:12px;">
                            <option value="all">🚀 Semua Admin (Cakupan Luas)</option>
                            <option value="active">✅ Hanya Admin Perumahan Aktif</option>
                            <optgroup label="Pilih Satu Perumahan">
                                <?php foreach($tenants as $t): ?>
                                    <option value="<?= $t['id'] ?>"><?= htmlspecialchars($t['name']) ?></option>
                                <?php endforeach; ?>
                            </optgroup>
                        </select>
                    </div>
                    <div style="font-size:0.8rem; color:var(--text-muted); margin-top:10px; line-height:1.5;">
                        <i data-lucide="info" style="width:14px; height:14px; vertical-align:middle; margin-right:5px;"></i>
                        Pastikan data nomor WA dan Email admin perumahan sudah valid di modul manajemen pengguna.
                    </div>
                </div>

                <div class="card" style="padding:25px;">
                    <h4 style="margin-bottom:15px;">🔍 Tampilan Notifikasi Lonceng</h4>
                    <div style="background:var(--bg-light); border:1px solid var(--border-color); border-radius:12px; padding:15px;">
                        <div style="display:flex; gap:12px;">
                            <div style="width:40px; height:40px; border-radius:50%; background:var(--primary); color:white; display:flex; align-items:center; justify-content:center;">
                                <i data-lucide="bell" style="width:20px;"></i>
                            </div>
                            <div style="flex:1;">
                                <div style="font-weight:800; font-size:0.9rem; margin-bottom:2px;" id="prevSubject">Judul Pesan...</div>
                                <div style="font-size:0.8rem; color:var(--text-secondary); line-height:1.4;" id="prevMessage">Isi pesan yang Anda tulis akan muncul di sini...</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
      </form>

      <!-- History Section -->
      <div class="card" style="margin-top:40px; padding:30px;">
        <div class="card-header border-bottom" style="margin-bottom:20px; padding-bottom:15px;">
            <h3 class="card-title"><i data-lucide="history" style="width:20px; vertical-align:middle; margin-right:10px;"></i> Histori Broadcast & Pengumuman</h3>
            <p style="font-size:0.85rem; color:var(--text-muted);">Daftar pesan terakhir yang dikirim melalui sistem pusat.</p>
        </div>

        <div class="table-container">
            <table class="table">
                <thead>
                    <tr>
                        <th>Waktu Kirim</th>
                        <th>Subjek / Pesan</th>
                        <th>Target Penerima</th>
                        <th>Saluran</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($history)): ?>
                    <tr>
                        <td colspan="5" style="text-align:center; padding:40px; color:var(--text-muted);">Belum ada histori pengiriman.</td>
                    </tr>
                    <?php endif; ?>
                    <?php foreach ($history as $h): 
                        $hChannels = json_decode($h['channels'], true) ?: [];
                    ?>
                    <tr>
                        <td style="white-space:nowrap;">
                            <div style="font-weight:700;"><?= date('d M Y', strtotime($h['created_at'])) ?></div>
                            <div style="font-size:0.75rem; color:var(--text-muted);"><?= date('H:i', strtotime($h['created_at'])) ?> WIB</div>
                        </td>
                        <td>
                            <div style="font-weight:700; color:var(--primary);"><?= htmlspecialchars($h['subject']) ?></div>
                            <div style="font-size:0.8rem; color:var(--text-secondary); max-width:400px; display: -webkit-box; -webkit-line-clamp: 1; -webkit-box-orient: vertical; overflow: hidden;"><?= htmlspecialchars(strip_tags($h['message'])) ?></div>
                        </td>
                        <td>
                            <div style="font-size:0.85rem; font-weight:600;"><?= htmlspecialchars($h['target_name']) ?></div>
                            <div style="font-size:0.7rem; color:var(--text-muted);"><?= $h['recipient_count'] ?> Penerima</div>
                        </td>
                        <td>
                            <div style="display:flex; gap:8px;">
                                <?php if (in_array('system', $hChannels)): ?><span title="System" style="color:var(--primary);"><i data-lucide="bell" style="width:16px;"></i></span><?php endif; ?>
                                <?php if (in_array('wa', $hChannels)): ?><span title="WhatsApp" style="color:#16a34a;"><i data-lucide="message-circle" style="width:16px;"></i></span><?php endif; ?>
                                <?php if (in_array('email', $hChannels)): ?><span title="Email" style="color:#ca8a04;"><i data-lucide="mail" style="width:16px;"></i></span><?php endif; ?>
                            </div>
                        </td>
                        <td>
                            <span class="badge badge-success">TERKIRIM</span>
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

<script>
    function toggleCard(cb, id) {
        if(cb.checked) document.getElementById(id).classList.add('active');
        else document.getElementById(id).classList.remove('active');
    }

    const subIn = document.querySelector('input[name="subject"]');
    const msgIn = document.querySelector('textarea[name="message"]');
    const prevSub = document.getElementById('prevSubject');
    const prevMsg = document.getElementById('prevMessage');

    subIn.addEventListener('input', () => { prevSub.innerText = subIn.value || 'Judul Pesan...'; });
    msgIn.addEventListener('input', () => { prevMsg.innerText = msgIn.value || 'Isi pesan akan muncul di sini...'; });
</script>

<?php include 'includes/footer.php'; ?>
