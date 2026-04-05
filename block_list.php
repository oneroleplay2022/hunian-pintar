<?php 
require_once 'classes/Auth.php';
require_once 'classes/Database.php';
require_once 'classes/Helpers.php';

Auth::requireLogin();
$tenant_id = Auth::tenantId();
$db = Database::getInstance();

$pageTitle = 'Manajemen Blok';

// 1. Handle Form Submission
if (isset($_POST['action'])) {
    if ($_POST['action'] === 'add_block') {
        $name = trim($_POST['block_name']);
        if ($name) {
            $db->insert('blocks', [
                'tenant_id' => $tenant_id,
                'block_name' => $name,
                'description' => $_POST['description'] ?? null
            ]);
            Helpers::flash('success', 'Blok baru berhasil ditambahkan.');
        }
    } elseif ($_POST['action'] === 'delete_block') {
        $id = (int)$_POST['id'];
        // Pastikan tidak ada rumah di blok ini sebelum hapus
        $houses = $db->count('houses', 'block_id = ?', [$id]);
        if ($houses == 0) {
            $db->query("DELETE FROM blocks WHERE id = ? AND tenant_id = ?", [$id, $tenant_id]);
            Helpers::flash('success', 'Blok berhasil dihapus.');
        } else {
            Helpers::flash('danger', 'Gagal hapus: Blok masih memiliki rumah di dalamnya.');
        }
    }
    header("Location: block_list.php");
    exit;
}

// 2. Fetch Data
$blocks = $db->fetchAll("
    SELECT b.*, (SELECT COUNT(*) FROM houses WHERE block_id = b.id) as house_count 
    FROM blocks b 
    WHERE b.tenant_id = ? 
    ORDER BY b.block_name
", [$tenant_id]);
?>
<?php include 'includes/header.php'; ?>

<div class="app-layout">
  <?php include 'includes/sidebar.php'; ?>
  <div class="main-wrapper">
    <?php include 'includes/topbar.php'; ?>
    <main class="main-content">
      <div class="page-header">
        <div>
          <h1>Manajemen Blok</h1>
          <div class="breadcrumb">
            <a href="index.php">Dashboard</a><span class="separator">/</span>
            <a href="rumah.php">Data Rumah</a><span class="separator">/</span>
            <span>Blok</span>
          </div>
        </div>
        <a href="rumah.php" class="btn btn-secondary btn-sm">← Kembali</a>
      </div>

      <div style="display:grid; grid-template-columns: 1fr 2fr; gap:24px;">
        <!-- Form Add -->
        <div class="card" style="padding:24px; height:fit-content;">
            <h3 style="margin-bottom:20px;">Tambah Blok Baru</h3>
            <form method="POST">
                <input type="hidden" name="action" value="add_block">
                <div class="form-group">
                    <label class="form-label">Nama Blok</label>
                    <input type="text" name="block_name" class="form-control" placeholder="Contoh: Blok A" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Deskripsi (Opsional)</label>
                    <textarea name="description" class="form-control" placeholder="Catatan singkat..."></textarea>
                </div>
                <button type="submit" class="btn btn-primary w-full" style="margin-top:10px;">💾 Simpan Blok</button>
            </form>
        </div>

        <!-- List Blocks -->
        <div class="card">
            <div class="table-container">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Nama Blok</th>
                            <th>Jumlah Unit</th>
                            <th>Deskripsi</th>
                            <th style="width:100px;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($blocks)): ?>
                            <tr><td colspan="4" style="text-align:center; padding:40px; color:var(--text-muted);">Belum ada blok yang dibuat.</td></tr>
                        <?php endif; ?>
                        <?php foreach($blocks as $b): ?>
                        <tr>
                            <td><strong><?= htmlspecialchars($b['block_name']) ?></strong></td>
                            <td><?= $b['house_count'] ?> Rumah</td>
                            <td class="text-muted"><?= htmlspecialchars($b['description'] ?: '-') ?></td>
                            <td>
                                <form method="POST" onsubmit="return confirm('Hapus blok ini?')">
                                    <input type="hidden" name="action" value="delete_block">
                                    <input type="hidden" name="id" value="<?= $b['id'] ?>">
                                    <button type="submit" class="btn btn-icon btn-sm" style="color:var(--danger);"><i data-lucide="trash-2"></i></button>
                                </form>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
      </div>
    </main>
  </div>
</div>
<?php include 'includes/footer.php'; ?>
