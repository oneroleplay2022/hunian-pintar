<?php 
require_once 'classes/Auth.php';
require_once 'classes/Database.php';
require_once 'classes/Helpers.php';

Auth::requireLogin();
$tenant_id = Auth::tenantId();
$db = Database::getInstance();

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if (!$id) {
    Helpers::redirect('warga.php', 'error', 'ID Warga tidak valid.');
}

// 1. Fetch Resident Data
$warga = $db->fetch("
    SELECT r.*, h.house_number, b.block_name 
    FROM residents r 
    LEFT JOIN houses h ON r.house_id = h.id 
    LEFT JOIN blocks b ON h.block_id = b.id 
    WHERE r.id = ? AND r.tenant_id = ? AND r.deleted_at IS NULL
", [$id, $tenant_id]);

if (!$warga) {
    Helpers::redirect('warga.php', 'error', 'Data warga tidak ditemukan.');
}

$pageTitle = 'Detail: ' . htmlspecialchars($warga['full_name']);

// 2. Fetch Family Members (Same Household)
$family = [];
if ($warga['house_id']) {
    $family = $db->fetchAll("
        SELECT * FROM residents 
        WHERE house_id = ? AND id != ? AND tenant_id = ? AND deleted_at IS NULL
        ORDER BY FIELD(family_status, 'kepala_keluarga', 'istri', 'anak', 'lainnya')
    ", [$warga['house_id'], $id, $tenant_id]);
}

// 3. Fetch Registered Vehicles
$vehicles = $db->fetchAll("SELECT * FROM vehicles WHERE house_id = ? AND tenant_id = ?", [$warga['house_id'], $tenant_id]);

// 4. Fetch Recent Invoices
$invoices = $db->fetchAll("SELECT * FROM invoices WHERE house_id = ? AND tenant_id = ? ORDER BY created_at DESC LIMIT 5", [$warga['house_id'], $tenant_id]);

// 5. Fetch Activity Logs
$logs = $db->fetchAll("
    SELECT * FROM audit_logs 
    WHERE tenant_id = ? 
    AND (
        (table_name = 'residents' AND row_id = ?) 
        OR (table_name = 'invoices' AND row_id IN (SELECT id FROM invoices WHERE house_id = ?))
    )
    ORDER BY created_at DESC LIMIT 6
", [$tenant_id, $id, $warga['house_id']]);

// Helper age calculation
function getAge($birthDate) {
    if (!$birthDate) return '-';
    $birthDate = new DateTime($birthDate);
    $today = new DateTime('today');
    return $birthDate->diff($today)->y . ' Thn';
}
?>
<?php include 'includes/header.php'; ?>
<div class="app-layout">
  <?php include 'includes/sidebar.php'; ?>
  <div class="main-wrapper">
    <?php include 'includes/topbar.php'; ?>
    <main class="main-content">
      <div class="page-header">
        <div>
          <h1>Profil Warga</h1>
          <div class="breadcrumb">
            <a href="index.php">Dashboard</a><span class="separator">/</span>
            <a href="warga.php">Data Warga</a><span class="separator">/</span>
            <span><?= htmlspecialchars($warga['full_name']) ?></span>
          </div>
        </div>
        <div style="display:flex;gap:10px;">
          <a href="warga.php" class="btn btn-secondary btn-sm">← Kembali</a>
          <a href="warga_form.php?id=<?= $id ?>" class="btn btn-primary btn-sm"><i data-lucide="pencil" style="width:14px;height:14px;"></i> Edit</a>
        </div>
      </div>

      <div class="grid-2" style="gap:24px;">
        <!-- Profile Card -->
        <div>
          <div class="card" style="padding:24px;text-align:center;margin-bottom:16px;">
            <?php if ($warga['photo']): ?>
                <img src="<?= htmlspecialchars($warga['photo']) ?>" style="width:120px;height:120px;border-radius:50%;object-fit:cover;margin:0 auto 16px;border:4px solid var(--bg-main);box-shadow:var(--shadow-md);">
            <?php else: ?>
                <div style="width:100px;height:100px;border-radius:50%;background:linear-gradient(135deg,var(--primary),var(--accent));display:flex;align-items:center;justify-content:center;font-size:2.5rem;color:white;margin:0 auto 16px;">👤</div>
            <?php endif; ?>
            <h2 style="font-size:1.3rem;"><?= htmlspecialchars($warga['full_name']) ?></h2>
            <div class="text-muted" style="margin-bottom:12px;"><?= Helpers::statusLabel($warga['family_status'] ?? '-') ?> • <?= htmlspecialchars($warga['block_name'] . '/' . $warga['house_number']) ?></div>
            <div style="display:flex;justify-content:center;gap:8px;">
              <span class="badge badge-success">Sesuai KK</span>
              <span class="badge badge-info"><?= Helpers::statusLabel($warga['domicile_status'] ?? 'domisili') ?></span>
            </div>
          </div>

          <!-- Personal Info -->
          <div class="card" style="padding:20px;">
            <h4 style="margin-bottom:16px;">📋 Data Pribadi</h4>
            <div style="display:flex;flex-direction:column;gap:12px;font-size:0.88rem;">
              <div style="display:flex;justify-content:space-between;"><span class="text-muted">NIK</span><strong style="font-family:monospace;"><?= $warga['nik'] ?></strong></div>
              <div style="display:flex;justify-content:space-between;"><span class="text-muted">Tempat, Tgl Lahir</span><strong><?= htmlspecialchars($warga['birth_place'] ?? '-') ?>, <?= date('d M Y', strtotime($warga['birth_date'] ?? 'now')) ?></strong></div>
              <div style="display:flex;justify-content:space-between;"><span class="text-muted">Usia</span><strong><?= getAge($warga['birth_date']) ?></strong></div>
              <div style="display:flex;justify-content:space-between;"><span class="text-muted">Jenis Kelamin</span><strong><?= $warga['gender'] == 'L' ? 'Laki-laki' : 'Perempuan' ?></strong></div>
              <div style="display:flex;justify-content:space-between;"><span class="text-muted">Agama</span><strong><?= htmlspecialchars($warga['religion'] ?? '-') ?></strong></div>
              <div style="display:flex;justify-content:space-between;"><span class="text-muted">Status Pernikahan</span><strong><?= Helpers::statusLabel($warga['marital_status'] ?? '-') ?></strong></div>
              <div style="display:flex;justify-content:space-between;"><span class="text-muted">Gol Darah</span><strong><?= htmlspecialchars($warga['blood_type'] ?? '-') ?></strong></div>
              <div style="display:flex;justify-content:space-between;"><span class="text-muted">Pendidikan</span><strong><?= htmlspecialchars($warga['education'] ?? '-') ?></strong></div>
              <div style="display:flex;justify-content:space-between;"><span class="text-muted">Pekerjaan</span><strong><?= htmlspecialchars($warga['profession'] ?? '-') ?></strong></div>
              <div style="display:flex;justify-content:space-between;"><span class="text-muted">No. HP</span><strong><?= htmlspecialchars($warga['phone'] ?? '-') ?></strong></div>
            </div>
          </div>
        </div>

        <div>
          <!-- Keluarga -->
          <div class="card" style="margin-bottom:16px;">
            <div class="card-header"><h3 class="card-title">👨‍👩‍👧‍👦 Anggota Keluarga Lain di Rumah</h3></div>
            <?php if (empty($family)): ?>
                <div style="padding:20px;text-align:center;color:var(--text-muted);">Tidak ada anggota keluarga lain terdaftar.</div>
            <?php else: ?>
                <?php foreach ($family as $f): ?>
                <div style="padding:14px 20px;border-bottom:1px solid var(--border-color);display:flex;align-items:center;gap:12px;">
                  <div style="width:40px;height:40px;border-radius:50%;background:rgba(99,102,241,0.1);display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                    <?php if(!empty($f['photo'])): ?><img src="<?= $f['photo'] ?>" style="width:100%;height:100%;border-radius:50%;object-fit:cover;"><?php else: ?>👤<?php endif; ?>
                  </div>
                  <div style="flex:1;">
                    <strong style="font-size:0.9rem;"><?= htmlspecialchars($f['full_name']) ?></strong>
                    <div class="text-muted" style="font-size:0.78rem;"><?= Helpers::statusLabel($f['family_status']) ?> • <?= getAge($f['birth_date']) ?> • <?= htmlspecialchars($f['profession'] ?? '-') ?></div>
                  </div>
                  <a href="warga_detail.php?id=<?= $f['id'] ?>" class="btn btn-sm btn-outline">Profil</a>
                </div>
                <?php endforeach; ?>
            <?php endif; ?>
          </div>

          <!-- Kendaraan -->
          <div class="card" style="margin-bottom:16px;">
            <div class="card-header"><h3 class="card-title">🚗 Kendaraan Terdaftar</h3></div>
            <?php if (empty($vehicles)): ?>
                <div style="padding:20px;text-align:center;color:var(--text-muted);">Belum ada kendaraan terdaftar.</div>
            <?php else: ?>
                <?php foreach ($vehicles as $v): ?>
                <div style="padding:14px 20px;border-bottom:1px solid var(--border-color);display:flex;align-items:center;justify-content:space-between;">
                  <div><strong><?= htmlspecialchars($v['plate_number']) ?></strong> <span class="text-muted">— <?= htmlspecialchars($v['brand'] . ' ' . $v['color']) ?></span></div>
                  <span class="badge badge-<?= $v['sticker_status'] == 'aktif' ? 'success' : 'secondary' ?>">Stiker: <?= strtoupper($v['sticker_status']) ?></span>
                </div>
                <?php endforeach; ?>
            <?php endif; ?>
          </div>

          <!-- Iuran Status -->
          <div class="card" style="margin-bottom:16px;">
            <div class="card-header"><h3 class="card-title">💰 Status Iuran (Tagihan Terakhir)</h3></div>
            <div style="padding:20px;">
              <?php if (empty($invoices)): ?>
                <div style="text-align:center;color:var(--text-muted);">Belum ada riwayat tagihan.</div>
              <?php else: ?>
                  <?php foreach ($invoices as $inv): ?>
                  <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:12px;">
                    <span class="text-muted"><?= htmlspecialchars($inv['title']) ?> (<?= date('M y', strtotime($inv['created_at'])) ?>)</span>
                    <span class="badge badge-<?= Helpers::statusBadge($inv['status']) ?>"><?= Helpers::statusLabel($inv['status']) ?></span>
                  </div>
                  <?php endforeach; ?>
              <?php endif; ?>
            </div>
          </div>

          <!-- Activity Log -->
          <div class="card">
            <div class="card-header"><h3 class="card-title">📝 Log Aktivitas</h3></div>
            <?php if (empty($logs)): ?>
                <div style="padding:20px;text-align:center;color:var(--text-muted);">Belum ada log aktivitas.</div>
            <?php else: ?>
                <?php foreach ($logs as $log): ?>
                <div style="padding:12px 20px;border-bottom:1px solid var(--border-color);font-size:0.85rem;display:flex;justify-content:space-between;">
                  <span><?= htmlspecialchars($log['action'] ?? 'Aktivitas') ?></span>
                  <span class="text-muted"><?= date('d M Y', strtotime($log['created_at'])) ?></span>
                </div>
                <?php endforeach; ?>
            <?php endif; ?>
          </div>
        </div>
      </div>
    </main>
  </div>
</div>
<?php include 'includes/footer.php'; ?>