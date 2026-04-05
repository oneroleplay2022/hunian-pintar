<?php 
require_once 'classes/Auth.php';
require_once 'classes/Database.php';
require_once 'classes/Helpers.php';

Auth::requireLogin();
$tenant_id = Auth::tenantId();
$db = Database::getInstance();

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$mode = $id ? 'Edit' : 'Tambah';
$pageTitle = $mode . ' Data Warga';

$warga = [];
if ($id) {
    $warga = $db->fetch("SELECT * FROM residents WHERE id = ? AND tenant_id = ?", [$id, $tenant_id]);
    if (!$warga) {
        Helpers::redirect('warga.php', 'error', 'Data warga tidak ditemukan.');
    }
}

// Handle POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validation
    $nik = Helpers::sanitize($_POST['nik']);
    if (strlen($nik) !== 16) {
        $error = "NIK harus berjumlah 16 digit.";
    } else {
        // Check Uniqueness (Tenant Scope)
        $uniqueWhere = "nik = ? AND tenant_id = ? AND deleted_at IS NULL";
        $uniqueParams = [$nik, $tenant_id];
        if ($id) {
            $uniqueWhere .= " AND id != ?";
            $uniqueParams[] = $id;
        }
        $isExists = $db->fetchColumn("SELECT COUNT(*) FROM residents WHERE $uniqueWhere", $uniqueParams);
        if ($isExists) {
            $error = "NIK sudah terdaftar di perumahan ini.";
        }
    }

    if (!isset($error)) {
        $data = [
            'tenant_id' => $tenant_id,
            'full_name' => Helpers::sanitize($_POST['full_name']),
            'nik' => $nik,
            'gender' => $_POST['gender'],
            'birth_place' => Helpers::sanitize($_POST['birth_place']),
            'birth_date' => $_POST['birth_date'],
            'religion' => $_POST['religion'],
            'marital_status' => $_POST['marital_status'],
            'blood_type' => $_POST['blood_type'],
            'education' => $_POST['education'],
            'profession' => Helpers::sanitize($_POST['profession']),
            'phone' => Helpers::sanitize($_POST['phone']),
            'house_id' => (int)$_POST['house_id'],
            'domicile_status' => $_POST['domicile_status'],
            'family_status' => $_POST['family_status']
        ];

        // Handle Uploads
        if (!empty($_FILES['photo']['name'])) {
            $upload = Helpers::uploadFile($_FILES['photo'], 'residents/photos');
            if ($upload['success']) $data['photo'] = $upload['path'];
        }

        try {
            if ($id) {
                $db->update('residents', $data, 'id = ? AND tenant_id = ?', [$id, $tenant_id]);
                Helpers::flash('success', 'Data warga berhasil diperbarui.');
            } else {
                $db->insert('residents', $data);
                Helpers::flash('success', 'Data warga berhasil ditambahkan.');
            }
            header("Location: warga.php");
            exit;
        } catch (Exception $e) {
            $error = "Terjadi kesalahan: " . $e->getMessage();
        }
    }
}

// Get Houses for dropdown
$houses = $db->fetchAll("
    SELECT h.id, h.house_number, b.block_name 
    FROM houses h 
    JOIN blocks b ON h.block_id = b.id 
    WHERE h.tenant_id = ? 
    ORDER BY b.block_name, h.house_number
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
          <h1><?= $mode ?> Data Warga</h1>
          <div class="breadcrumb">
            <a href="index.php">Dashboard</a><span class="separator">/</span>
            <a href="warga.php">Data Warga</a><span class="separator">/</span>
            <span><?= $mode ?></span>
          </div>
        </div>
        <a href="warga.php" class="btn btn-secondary btn-sm">← Kembali</a>
      </div>

      <?php if (isset($error)): ?>
        <div style="background:rgba(239,68,68,0.1); border:1px solid rgba(239,68,68,0.3); color:var(--danger); padding:12px 16px; border-radius:var(--radius-md); margin-bottom:20px;">
            ⚠️ <?= $error ?>
        </div>
      <?php endif; ?>

      <form method="POST" enctype="multipart/form-data" class="card" style="padding:24px;">
        <h3 style="margin-bottom:20px;">📋 Data Diri</h3>
        <div class="form-row">
            <div class="form-group">
              <label class="form-label">NIK (16 digit) *</label>
              <input type="text" name="nik" class="form-control" placeholder="3201xxxxxxxxxxxx" maxlength="16" value="<?= $warga['nik'] ?? '' ?>" required>
            </div>
            <div class="form-group">
              <label class="form-label">Nama Lengkap *</label>
              <input type="text" name="full_name" class="form-control" placeholder="Nama sesuai KTP" value="<?= $warga['full_name'] ?? '' ?>" required>
            </div>
        </div>
        
        <div class="form-row">
          <div class="form-group">
            <label class="form-label">Tempat Lahir</label>
            <input type="text" name="birth_place" class="form-control" placeholder="Contoh: Jakarta" value="<?= $warga['birth_place'] ?? '' ?>">
          </div>
          <div class="form-group">
            <label class="form-label">Tanggal Lahir</label>
            <input type="date" name="birth_date" class="form-control" value="<?= $warga['birth_date'] ?? '' ?>">
          </div>
        </div>

        <div class="form-row">
          <div class="form-group">
            <label class="form-label">Jenis Kelamin</label>
            <select name="gender" class="form-control">
                <option value="L" <?= ($warga['gender'] ?? '') == 'L' ? 'selected' : '' ?>>Laki-laki</option>
                <option value="P" <?= ($warga['gender'] ?? '') == 'P' ? 'selected' : '' ?>>Perempuan</option>
            </select>
          </div>
          <div class="form-group">
            <label class="form-label">Agama</label>
            <select name="religion" class="form-control">
                <?php foreach(['Islam','Kristen','Katolik','Hindu','Buddha','Konghucu'] as $r): ?>
                <option value="<?= $r ?>" <?= ($warga['religion'] ?? '') == $r ? 'selected' : '' ?>><?= $r ?></option>
                <?php endforeach; ?>
            </select>
          </div>
        </div>

        <div class="form-row">
          <div class="form-group">
            <label class="form-label">Status Pernikahan</label>
            <select name="marital_status" class="form-control">
                <option value="single" <?= ($warga['marital_status'] ?? '') == 'single' ? 'selected' : '' ?>>Belum Menikah</option>
                <option value="married" <?= ($warga['marital_status'] ?? '') == 'married' ? 'selected' : '' ?>>Menikah</option>
                <option value="divorced" <?= ($warga['marital_status'] ?? '') == 'divorced' ? 'selected' : '' ?>>Cerai</option>
            </select>
          </div>
          <div class="form-group">
            <label class="form-label">Golongan Darah</label>
            <select name="blood_type" class="form-control">
                <?php foreach(['-','A','B','AB','O'] as $bt): ?>
                <option value="<?= $bt ?>" <?= ($warga['blood_type'] ?? '') == $bt ? 'selected' : '' ?>><?= $bt ?></option>
                <?php endforeach; ?>
            </select>
          </div>
        </div>

        <div class="form-row">
          <div class="form-group">
            <label class="form-label">Pendidikan Terakhir</label>
            <select name="education" class="form-control">
                <?php foreach(['SD','SMP','SMA/SMK','D3','S1','S2','S3','Lainnya'] as $ed): ?>
                <option value="<?= $ed ?>" <?= ($warga['education'] ?? '') == $ed ? 'selected' : '' ?>><?= $ed ?></option>
                <?php endforeach; ?>
            </select>
          </div>
          <div class="form-group">
            <label class="form-label">Pekerjaan</label>
            <input type="text" name="profession" class="form-control" placeholder="Pekerjaan" value="<?= $warga['profession'] ?? '' ?>">
          </div>
        </div>

        <h3 style="margin:24px 0 20px;padding-top:20px;border-top:1px solid var(--border-color);">🏠 Data Tinggal</h3>
        <div class="form-row">
          <div class="form-group">
            <label class="form-label">Alamat Rumah (Pilih Unit) *</label>
            <select name="house_id" class="form-control" required>
                <option value="">-- Pilih Rumah --</option>
                <?php foreach($houses as $h): ?>
                <option value="<?= $h['id'] ?>" <?= ($warga['house_id'] ?? '') == $h['id'] ? 'selected' : '' ?>>
                    <?= htmlspecialchars($h['block_name'] . ' / No. ' . $h['house_number']) ?>
                </option>
                <?php endforeach; ?>
            </select>
          </div>
          <div class="form-group">
            <label class="form-label">Status Domisili</label>
            <select name="domicile_status" class="form-control">
                <option value="domisili" <?= ($warga['domicile_status'] ?? '') == 'domisili' ? 'selected' : '' ?>>Tetap (KTP Setempat)</option>
                <option value="domisili_luar" <?= ($warga['domicile_status'] ?? '') == 'domisili_luar' ? 'selected' : '' ?>>Domisili Luar (KTP Luar)</option>
            </select>
          </div>
        </div>

        <div class="form-row">
          <div class="form-group">
            <label class="form-label">Status dalam Keluarga</label>
            <select name="family_status" class="form-control">
                <option value="kepala_keluarga" <?= ($warga['family_status'] ?? '') == 'kepala_keluarga' ? 'selected' : '' ?>>Kepala Keluarga</option>
                <option value="istri" <?= ($warga['family_status'] ?? '') == 'istri' ? 'selected' : '' ?>>Istri</option>
                <option value="anak" <?= ($warga['family_status'] ?? '') == 'anak' ? 'selected' : '' ?>>Anak</option>
                <option value="lainnya" <?= ($warga['family_status'] ?? '') == 'lainnya' ? 'selected' : '' ?>>Lainnya</option>
            </select>
          </div>
          <div class="form-group">
            <label class="form-label">No. HP / WhatsApp</label>
            <input type="tel" name="phone" class="form-control" placeholder="08xxxxxxxxxx" value="<?= $warga['phone'] ?? '' ?>">
          </div>
        </div>

        <h3 style="margin:24px 0 20px;padding-top:20px;border-top:1px solid var(--border-color);">📷 Dokumen</h3>
        <div class="form-group">
          <label class="form-label">Pas Foto Warga</label>
          <?php if(!empty($warga['photo'])): ?>
            <div style="margin-bottom:10px;"><img src="<?= $warga['photo'] ?>" style="width:100px; height:100px; object-fit:cover; border-radius:8px;"></div>
          <?php endif; ?>
          <input type="file" name="photo" class="form-control" accept="image/*">
        </div>

        <div style="display:flex;justify-content:flex-end;gap:12px;margin-top:24px;padding-top:20px;border-top:1px solid var(--border-color);">
          <a href="warga.php" class="btn btn-secondary">Batal</a>
          <button type="submit" class="btn btn-primary">💾 SIMPAN DATA WARGA</button>
        </div>
      </form>
    </main>
  </div>
</div>
<?php include 'includes/footer.php'; ?>
