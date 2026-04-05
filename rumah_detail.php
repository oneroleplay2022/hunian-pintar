<?php 
require_once 'classes/Auth.php';
require_once 'classes/Database.php';
require_once 'classes/Helpers.php';

Auth::requireLogin();
$tenant_id = Auth::tenantId();
$db = Database::getInstance();

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$house = $db->fetch("
    SELECT h.*, b.block_name 
    FROM houses h 
    JOIN blocks b ON h.block_id = b.id 
    WHERE h.id = ? AND h.tenant_id = ?
", [$id, $tenant_id]);

if (!$house) {
    Helpers::redirect('rumah.php', 'danger', 'Data rumah tidak ditemukan.');
}

// 1. Fetch Real Residents
$residents = $db->fetchAll("
    SELECT * FROM residents 
    WHERE house_id = ? AND tenant_id = ? AND deleted_at IS NULL
    ORDER BY family_status DESC, full_name ASC
", [$id, $tenant_id]);

// 2. Calculate Real Arrears (Tunggakan)
$tunggakan = $db->fetchColumn("
    SELECT SUM(amount) FROM invoices 
    WHERE house_id = ? AND tenant_id = ? AND status IN ('tagihan', 'menunggak')
", [$id, $tenant_id]) ?: 0;

// 3. Get Default IPL Plan (for display)
$defaultIPL = $db->fetchColumn("SELECT amount FROM invoice_types WHERE tenant_id = ? AND name LIKE '%IPL%' LIMIT 1") ?: 150000;

$pageTitle = 'Detail Rumah — ' . $house['block_name'] . '/' . $house['house_number'];
?>
<?php include 'includes/header.php'; ?>
<div class="app-layout">
  <?php include 'includes/sidebar.php'; ?>
  <div class="main-wrapper">
    <?php include 'includes/topbar.php'; ?>
    <main class="main-content">
      <div class="page-header">
        <div>
          <h1>Detail Rumah — <?= htmlspecialchars($house['block_name'] . '/' . $house['house_number']) ?></h1>
          <div class="breadcrumb">
            <a href="index.php">Dashboard</a><span class="separator">/</span>
            <a href="rumah.php">Data Rumah</a><span class="separator">/</span>
            <span><?= htmlspecialchars($house['block_name'] . '/' . $house['house_number']) ?></span>
          </div>
        </div>
        <div style="display:flex;gap:10px;">
          <a href="rumah.php" class="btn btn-secondary btn-sm">← Kembali</a>
          <a href="rumah_form.php?id=<?= $id ?>" class="btn btn-primary btn-sm"><i data-lucide="pencil" style="width:14px;height:14px;"></i> Edit Rumah</a>
        </div>
      </div>

      <div class="grid-2" style="gap:24px;">
        <div>
          <!-- House Info -->
          <div class="card animate-fadeIn" style="padding:24px;margin-bottom:16px; border-top: 4px solid var(--<?= Helpers::statusBadge($house['status']) ?>);">
            <div style="display:flex;align-items:center;gap:16px;margin-bottom:20px;">
              <div style="width:72px;height:72px;border-radius:var(--radius-lg);background:linear-gradient(135deg,rgba(99,102,241,0.15),rgba(6,182,212,0.1));display:flex;align-items:center;justify-content:center;font-size:2.5rem;">🏠</div>
              <div>
                <h2 style="font-size:1.3rem;">Blok <?= htmlspecialchars($house['block_name']) ?> / No. <?= htmlspecialchars($house['house_number']) ?></h2>
                <span class="badge badge-<?= Helpers::statusBadge($house['status']) ?>"><?= Helpers::statusLabel($house['status']) ?></span>
              </div>
            </div>
            <div style="display:flex;flex-direction:column;gap:12px;font-size:0.88rem;">
              <div style="display:flex;justify-content:space-between;"><span class="text-muted">Pemilik / Atas Nama</span><strong><?= htmlspecialchars($house['owner_name'] ?: '-') ?></strong></div>
              <div style="display:flex;justify-content:space-between;"><span class="text-muted">Tipe Rumah</span><strong><?= htmlspecialchars($house['house_type'] ?: '-') ?></strong></div>
              <div style="display:flex;justify-content:space-between;"><span class="text-muted">Luas Tanah/Bangunan</span><strong><?= $house['area_m2'] ?> m²</strong></div>
              <div style="display:flex;justify-content:space-between;"><span class="text-muted">Kontak Pemilik</span><strong><?= htmlspecialchars($house['owner_phone'] ?: '-') ?></strong></div>
            </div>
          </div>

          <!-- IPL Summary -->
          <div class="card animate-fadeIn stagger-1" style="padding:20px;">
            <h4 style="margin-bottom:16px;">💰 Tagihan & Iuran Bulanan</h4>
            <div style="display:grid;grid-template-columns:repeat(2,1fr);gap:12px;margin-bottom:16px;">
              <div style="text-align:center;padding:14px;background:var(--bg-input);border-radius:var(--radius-md);">
                <div style="font-size:1.4rem;font-weight:800;color:var(--primary);">Rp <?= number_format($defaultIPL, 0, ',', '.') ?></div>
                <div class="text-muted" style="font-size:0.82rem;">Tarif IPL/bln</div>
              </div>
              <div style="text-align:center;padding:14px;background:var(--bg-input);border-radius:var(--radius-md);">
                <div style="font-size:1.4rem;font-weight:800;color:<?= $tunggakan > 0 ? 'var(--danger)' : 'var(--success)' ?>;">Rp <?= number_format($tunggakan, 0, ',', '.') ?></div>
                <div class="text-muted" style="font-size:0.82rem;">Tunggakan</div>
              </div>
            </div>
            <a href="iuran.php?house_id=<?= $id ?>" class="btn btn-primary btn-sm w-full" style="margin-bottom:8px;">Kelola Tagihan</a>
            <a href="pembayaran.php?house_id=<?= $id ?>" class="btn btn-outline btn-sm w-full">Riwayat Pembayaran →</a>
          </div>
        </div>

        <div>
          <!-- Residents -->
          <div class="card animate-fadeIn">
            <div class="card-header" style="display:flex; justify-content:space-between; align-items:center;">
                <h3 class="card-title">👥 Penghuni (<?= count($residents) ?>)</h3>
                <a href="warga_form.php?house_id=<?= $id ?>" class="btn btn-primary btn-sm" style="font-size:0.75rem;"><i data-lucide="plus" style="width:12px;height:12px;"></i> Tambah</a>
            </div>
            
            <?php if (empty($residents)): ?>
                <div style="padding:30px; text-align:center; color:var(--text-muted); font-size:0.85rem;">
                    Belum ada warga yang terdaftar di rumah ini.
                </div>
            <?php endif; ?>

            <?php foreach ($residents as $r): ?>
            <div style="padding:14px 20px;border-bottom:1px solid var(--border-color);display:flex;align-items:center;gap:12px;">
              <div style="width:40px;height:40px;border-radius:50%;background:rgba(99,102,241,0.1);display:flex;align-items:center;justify-content:center; overflow:hidden;">
                <?php if($r['photo']): ?>
                    <img src="<?= htmlspecialchars($r['photo']) ?>" style="width:100%; height:100%; object-fit:cover;">
                <?php else: ?>
                    <i data-lucide="user" style="width:20px; color:var(--primary);"></i>
                <?php endif; ?>
              </div>
              <div style="flex:1;">
                <strong style="font-size:0.9rem;"><?= htmlspecialchars($r['full_name']) ?></strong>
                <div class="text-muted" style="font-size:0.78rem;">
                    <?= Helpers::statusLabel($r['family_status']) ?> • <?= $r['gender'] == 'L' ? 'Laki-laki' : 'Perempuan' ?>
                </div>
              </div>
              <a href="warga_detail.php?id=<?= $r['id'] ?>" class="btn btn-sm btn-outline">Detail</a>
            </div>
            <?php endforeach; ?>
          </div>

          <!-- Notes -->
          <div class="card animate-fadeIn stagger-2" style="margin-top:16px; padding:20px;">
            <h4 style="margin-bottom:12px; font-size:0.9rem;">📝 Catatan Admin</h4>
            <div style="font-size:0.85rem; color:var(--text-secondary); line-height:1.5;">
                <?= htmlspecialchars(($house['description'] ?? '') ?: 'Tidak ada catatan tambahan untuk rumah ini.') ?>
            </div>
          </div>
        </div>
      </div>
    </main>
  </div>
</div>
<?php include 'includes/footer.php'; ?>
