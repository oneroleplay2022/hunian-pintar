<?php
require_once 'classes/Auth.php';
require_once 'classes/Database.php';
require_once 'classes/Helpers.php';

Auth::requireLogin();
$db = Database::getInstance();
$currentUser = Auth::user();

$ticket_id = (int)($_GET['id'] ?? 0);
$ticket = $db->fetch("SELECT t.*, tn.name as tenant_name FROM support_tickets t 
                      JOIN tenants tn ON t.tenant_id = tn.id 
                      WHERE t.id = ?", [$ticket_id]);

if (!$ticket) {
    die("Tiket tidak ditemukan.");
}

// Security: If not superadmin, must belong to the tenant
if ($currentUser['role'] !== 'superadmin' && $ticket['tenant_id'] !== $currentUser['tenant_id']) {
    die("Akses ditolak.");
}

$success_msg = '';
$error_msg = '';

// Handle Reply Submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['message'])) {
    $message = trim($_POST['message']);
    $close = isset($_POST['close_ticket']);
    
    if ($message) {
        $db->insert('support_ticket_replies', [
            'ticket_id' => $ticket_id,
            'user_id' => $currentUser['id'],
            'message' => $message
        ]);
        
        $status = $currentUser['role'] === 'superadmin' ? 'replied' : 'open';
        if ($close) $status = 'closed';
        
        $db->update('support_tickets', ['status' => $status], 'id = ?', [$ticket_id]);
        
        // --- Create System Notification ---
        require_once 'classes/Notification.php';
        if ($currentUser['role'] === 'superadmin') {
            // Notify Tenant Admin (Original Ticket Creator)
            // Get user_id from first reply or ticket if we store it (let's assume we notify the resident_id linked user or admin)
            // For now, simplify: Notify all admins of that tenant
            $admins = $db->fetchAll("SELECT id FROM users WHERE tenant_id = ? AND role = 'admin'", [$ticket['tenant_id']]);
            foreach($admins as $adm) {
                Notification::addSystemNotification($ticket['tenant_id'], $adm['id'], 'Balasan Tiket Support', 'Tiket #' . $ticket_id . ' telah dibalas oleh Tim WargaKu.', 'ticket_detail.php?id=' . $ticket_id);
            }
        } else {
            // Notify Superadmin
            $superadmins = $db->fetchAll("SELECT id FROM users WHERE role = 'superadmin'");
            foreach($superadmins as $sup) {
                Notification::addSystemNotification(null, $sup['id'], 'Tiket Baru/Balasan', 'Ada balasan baru pada tiket #' . $ticket_id . ' dari ' . $ticket['tenant_name'], 'ticket_detail.php?id=' . $ticket_id);
            }
        }
        
        Helpers::redirect("ticket_detail.php?id=$ticket_id", 'success', "Balasan berhasil dikirim.");
    }
}

$pageTitle = 'Detail Tiket #' . $ticket_id;
include 'includes/header.php';
$success_msg = Helpers::getFlash('success');
$error_msg = Helpers::getFlash('error');

$replies = $db->fetchAll("SELECT r.*, u.name as user_name, u.role as user_role, u.avatar 
                          FROM support_ticket_replies r 
                          JOIN users u ON r.user_id = u.id 
                          WHERE r.ticket_id = ? ORDER BY r.created_at ASC", [$ticket_id]);
?>

<div class="app-layout">
  <?php if ($currentUser['role'] === 'superadmin'): ?>
    <?php include 'includes/sidebar_saas.php'; ?>
  <?php else: ?>
    <?php include 'includes/sidebar.php'; ?>
  <?php endif; ?>

  <div class="main-wrapper">
    <?php include 'includes/topbar.php'; ?>
    <main class="main-content">
      <div class="page-header">
        <div>
           <h1>Tiket: <?= htmlspecialchars($ticket['subject']) ?></h1>
           <span class="badge badge-info" style="margin-top:5px;"><?= strtoupper($ticket['status']) ?></span>
           <span class="text-muted" style="font-size:0.8rem; margin-left:10px;">Tenant: <b><?= htmlspecialchars($ticket['tenant_name']) ?></b></span>
        </div>
        <a href="<?= $currentUser['role'] === 'superadmin' ? 'saas_tickets.php' : 'tickets.php' ?>" class="btn btn-secondary btn-sm"><i data-lucide="arrow-left" style="width:16px;"></i> Kembali</a>
      </div>

      <?php if ($success_msg): ?><div class="alert alert-success">✅ <?= $success_msg ?></div><?php endif; ?>

      <div class="card" style="margin-bottom:20px;">
        <div class="card-header border-bottom"><h3 class="card-title">Percakapan Bantuan</h3></div>
        <div style="padding:20px; background:var(--bg-main); border-radius:var(--radius-lg); margin:10px;">
            <?php foreach($replies as $r): 
                $isSuper = $r['user_role'] === 'superadmin';
                $align = $isSuper ? 'flex-start' : 'flex-end';
                $bg = $isSuper ? 'var(--bg-card)' : 'rgba(16,185,129,0.1)';
                $border = $isSuper ? 'border:1px solid var(--border-color)' : 'border:1px solid rgba(16,185,129,0.3)';
                if ($currentUser['id'] === $r['user_id']) $align = 'flex-end';
            ?>
            <div style="display:flex; flex-direction:column; align-items:<?= $align ?>; margin-bottom:15px;">
                <div style="max-width:75%; padding:12px 16px; border-radius:12px; <?= $bg ?>; <?= $border ?>; color:var(--text-main); position:relative;">
                    <div style="font-size:0.75rem; font-weight:700; color:<?= $isSuper ? 'var(--primary)' : 'var(--success)' ?>; margin-bottom:5px;">
                        <?= htmlspecialchars($r['user_name']) ?> (<?= ucfirst($r['user_role']) ?>)
                    </div>
                    <div style="line-height:1.5; font-size:0.9rem;"><?= nl2br(htmlspecialchars($r['message'])) ?></div>
                    <div style="font-size:0.65rem; color:var(--text-muted); text-align:right; margin-top:5px;">
                        <?= date('d M, H:i', strtotime($r['created_at'])) ?>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
      </div>

      <?php if ($ticket['status'] !== 'closed'): ?>
      <div class="card">
        <div class="card-header border-bottom"><h3 class="card-title">Kirim Balasan</h3></div>
        <div style="padding:20px;">
            <form method="POST">
                <div class="form-group" style="margin-bottom:15px;">
                    <textarea name="message" class="form-control" rows="4" placeholder="Ketik balasan Anda..." required style="width:100%;"></textarea>
                </div>
                <div style="display:flex; justify-content:space-between; align-items:center;">
                    <label style="font-size:0.85rem; display:flex; align-items:center; gap:8px;">
                        <input type="checkbox" name="close_ticket"> 
                        Tandai Tiket Selesai (Selesai/Solved)
                    </label>
                    <button type="submit" class="btn btn-primary"><i data-lucide="send" style="width:16px;"></i> Balas Tiket</button>
                </div>
            </form>
        </div>
      </div>
      <?php else: ?>
        <div style="text-align:center; padding:20px; color:var(--text-muted); background:var(--bg-card); border-radius:var(--radius-lg); border:1px dashed var(--border-color);">
            Tiket ini telah ditutup. Jika butuh bantuan lain, silakan buka tiket baru.
        </div>
      <?php endif; ?>
    </main>
  </div>
</div>

<?php include 'includes/footer.php'; ?>
