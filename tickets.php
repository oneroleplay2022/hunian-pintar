<?php
require_once 'classes/Auth.php';
require_once 'classes/Database.php';
require_once 'classes/Helpers.php';

Auth::requireLogin();
$db = Database::getInstance();
$currentUser = Auth::user();

$success_msg = '';
$error_msg = '';

// Handle New Ticket Submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'new_ticket') {
    $subject = trim($_POST['subject'] ?? '');
    $message = trim($_POST['message'] ?? '');
    $priority = $_POST['priority'] ?? 'medium';
    
    if ($subject && $message) {
        $ticket_id = $db->insert('support_tickets', [
            'tenant_id' => $currentUser['tenant_id'],
            'user_id' => $currentUser['id'],
            'subject' => $subject,
            'priority' => $priority,
            'status' => 'open'
        ]);
        
        $db->insert('support_ticket_replies', [
            'ticket_id' => $ticket_id,
            'user_id' => $currentUser['id'],
            'message' => $message
        ]);
        
        $success_msg = "Tiket bantuan berhasil dikirim! Silakan tunggu balasan dari pengembang pusat.";
    } else {
        $error_msg = "Subjek dan Pesan wajib diisi.";
    }
}

$tickets = $db->fetchAll("SELECT * FROM support_tickets WHERE tenant_id = ? ORDER BY created_at DESC", [$currentUser['tenant_id']]);

$pageTitle = 'Pusat Bantuan (Ticket)';
include 'includes/header.php';
?>

<div class="app-layout">
  <?php include 'includes/sidebar.php'; ?>
  <div class="main-wrapper">
    <?php include 'includes/topbar.php'; ?>
    <main class="main-content">
      <div class="page-header">
        <div><h1>Pusat Bantuan Developer</h1><p style="color:var(--text-muted);font-size:0.85rem;">Ajukan bantuan teknis langsung ke pengembang WargaKu</p></div>
        <button class="btn btn-primary btn-sm" onclick="document.getElementById('newTicketModal').classList.add('active')"><i data-lucide="plus" style="width:16px;"></i> Tiket Baru</button>
      </div>

      <?php if ($success_msg): ?><div class="alert alert-success">✅ <?= $success_msg ?></div><?php endif; ?>
      <?php if ($error_msg): ?><div class="alert alert-danger">⚠️ <?= $error_msg ?></div><?php endif; ?>

      <div class="card">
        <div class="card-header border-bottom"><h3 class="card-title">Riwayat Tiket Bantuan Saya</h3></div>
        <div class="table-container">
            <table class="table">
                <thead>
                    <tr><th>ID</th><th>Subjek</th><th>Status</th><th>Prioritas</th><th>Dibuat</th><th>Aksi</th></tr>
                </thead>
                <tbody>
                    <?php if (empty($tickets)): ?>
                        <tr><td colspan="6" style="text-align:center;padding:20px;">Belum ada sejarah tiket bantuan.</td></tr>
                    <?php endif; 
                    foreach($tickets as $t): 
                        $badge = 'badge-success';
                        if ($t['status'] === 'open') $badge = 'badge-warning';
                        elseif ($t['status'] === 'replied') $badge = 'badge-info';
                    ?>
                    <tr>
                        <td><strong>#<?= $t['id'] ?></strong></td>
                        <td><?= htmlspecialchars($t['subject']) ?></td>
                        <td><span class="badge <?= $badge ?>"><?= strtoupper($t['status']) ?></span></td>
                        <td><?= ucfirst($t['priority']) ?></td>
                        <td><?= date('d/m/Y H:i', strtotime($t['created_at'])) ?></td>
                        <td><a href="ticket_detail.php?id=<?= $t['id'] ?>" class="btn btn-xs btn-secondary">Detail / Balas</a></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
      </div>
    </main>
  </div>
</div>

<!-- Modal New Ticket -->
<div class="modal-overlay" id="newTicketModal">
    <div class="modal card" style="width:100%;max-width:500px;">
        <form method="POST">
            <input type="hidden" name="action" value="new_ticket">
            <div class="modal-header border-bottom" style="padding:15px 20px;"><h3>Buat Tiket Bantuan Baru</h3></div>
            <div class="modal-body" style="padding:20px;">
                <div class="form-group" style="margin-bottom:15px;">
                    <label class="form-label">Subjek Komplain/Bantuan</label>
                    <input type="text" name="subject" class="form-control" placeholder="Contoh: Fitur CCTV tidak muncul" required style="width:100%;">
                </div>
                <div class="form-group" style="margin-bottom:15px;">
                    <label class="form-label">Prioritas</label>
                    <select name="priority" class="form-control" style="width:100%;">
                        <option value="low">Rendah (Tanya-tanya)</option>
                        <option value="medium" selected>Sedang (Bantuan Teknis)</option>
                        <option value="high">Tinggi (Error/Aplikasi Mati)</option>
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label">Detail Pesan</label>
                    <textarea name="message" class="form-control" rows="5" placeholder="Jelaskan kendala Anda sedetail mungkin..." required style="width:100%;"></textarea>
                </div>
            </div>
            <div class="modal-footer border-top" style="padding:15px 20px; display:flex; justify-content:flex-end; gap:10px;">
                <button type="button" class="btn btn-secondary" onclick="document.getElementById('newTicketModal').classList.remove('active')">Batal</button>
                <button type="submit" class="btn btn-primary">Kirim Tiket</button>
            </div>
        </form>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
