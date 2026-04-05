<?php
require_once 'classes/Auth.php';
require_once 'classes/Database.php';
require_once 'classes/Helpers.php';

Auth::requireRole('superadmin');
$db = Database::getInstance();

$tickets = $db->fetchAll("SELECT t.*, tn.name as tenant_name FROM support_tickets t 
                          JOIN tenants tn ON t.tenant_id = tn.id 
                          ORDER BY t.created_at DESC");

$pageTitle = 'Manajemen Tiket Support';
include 'includes/header.php';
?>

<div class="app-layout">
  <?php include 'includes/sidebar_saas.php'; ?>

  <div class="main-wrapper">
    <?php include 'includes/topbar.php'; ?>
    <main class="main-content">
      <div class="page-header"><h1>Daftar Tiket Bantuan Client</h1></div>

      <div class="card">
        <div class="table-container">
            <table class="table">
                <thead>
                    <tr><th>ID</th><th>Client (Penyewa)</th><th>Subjek</th><th>Status</th><th>Prioritas</th><th>Waktu</th><th>Aksi</th></tr>
                </thead>
                <tbody>
                    <?php if (empty($tickets)): ?>
                        <tr><td colspan="7" style="text-align:center;padding:20px;">Belum ada pendaftaran tiket bantuan.</td></tr>
                    <?php endif; 
                    foreach($tickets as $t): 
                        $badge = 'badge-success';
                        if ($t['status'] === 'open') $badge = 'badge-warning';
                        elseif ($t['status'] === 'replied') $badge = 'badge-info';
                    ?>
                    <tr>
                        <td><strong>#<?= $t['id'] ?></strong></td>
                        <td><b><?= htmlspecialchars($t['tenant_name']) ?></b></td>
                        <td><?= htmlspecialchars($t['subject']) ?></td>
                        <td><span class="badge <?= $badge ?>"><?= strtoupper($t['status']) ?></span></td>
                        <td><?= ucfirst($t['priority']) ?></td>
                        <td><?= date('d/m H:i', strtotime($t['created_at'])) ?></td>
                        <td><a href="ticket_detail.php?id=<?= $t['id'] ?>" class="btn btn-xs btn-primary">Lihat & Balas</a></td>
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
