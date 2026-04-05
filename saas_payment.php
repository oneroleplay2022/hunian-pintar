<?php
$pageTitle = 'Pembayaran Langganan SaaS';
require_once 'classes/Auth.php';
require_once 'classes/Database.php';
require_once 'classes/PlanHelper.php';

// Auth checks and header routing middleware handles everything
?>
<?php include 'includes/header.php'; ?>
<?php
$db = Database::getInstance();

// currentUser was defined in header.php
if (!isset($currentUser)) {
    $currentUser = Auth::user();
}

// Protect this page from non-admins
if ($currentUser['role'] !== 'admin' && $currentUser['role'] !== 'superadmin') {
    die("Akses ditolak. Layanan pembayaran tunggakan hanya diperuntukkan bagi Pengurus Cluster.");
}

$tenant_id = (int)$currentUser['tenant_id'];

// Get specific tenant Info to display status
$tenant = $db->fetch("SELECT subscription_status, expired_at FROM tenants WHERE id = ?", [$tenant_id]);

// If tenant not found (e.g. Superadmin without a tenant), default to active to avoid blocked screen
$sub_status = $tenant ? $tenant['subscription_status'] : 'active';

// Handle payment confirmation POST with file upload
$success_msg = '';
$error_msg = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'notify_payment') {
    $sub_id = (int)($_POST['sub_id'] ?? 0);
    
    if ($sub_id) {
        // Verify this subscription belongs to this tenant
        $sub = $db->fetch("SELECT id FROM subscriptions WHERE id = ? AND tenant_id = ? AND payment_status = 'pending'", [$sub_id, $tenant_id]);
        
        if (!$sub) {
            $error_msg = "Tagihan tidak ditemukan atau sudah diproses.";
        } else {
            $proofPath = null;
            
            // Handle file upload
            if (isset($_FILES['payment_proof']) && $_FILES['payment_proof']['error'] === UPLOAD_ERR_OK) {
                $file = $_FILES['payment_proof'];
                $allowedTypes = ['image/jpeg', 'image/png', 'image/webp'];
                $maxSize = 2 * 1024 * 1024; // 2MB
                
                if (!in_array($file['type'], $allowedTypes)) {
                    $error_msg = "Format file tidak didukung. Gunakan JPG, PNG, atau WEBP.";
                } elseif ($file['size'] > $maxSize) {
                    $error_msg = "Ukuran file melebihi batas 2MB.";
                } else {
                    // Create upload directory if not exists
                    $uploadDir = 'uploads/payment_proofs/';
                    if (!is_dir($uploadDir)) {
                        mkdir($uploadDir, 0755, true);
                    }
                    
                    // Generate unique filename
                    $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
                    $filename = 'proof_' . $tenant_id . '_' . $sub_id . '_' . time() . '.' . $ext;
                    $proofPath = $uploadDir . $filename;
                    
                    if (!move_uploaded_file($file['tmp_name'], $proofPath)) {
                        $error_msg = "Gagal menyimpan file. Silakan coba lagi.";
                        $proofPath = null;
                    }
                }
            }
            
            if (!$error_msg) {
                // Update subscription with proof
                $updateData = ['payment_proof' => $proofPath];
                $db->update('subscriptions', $updateData, 'id = ?', [$sub_id]);
                
                $success_msg = "Bukti transfer berhasil dikirim! Tim Admin " . ($appSettings['app_name'] ?? 'Pusat') . " akan memverifikasi dalam 1x24 Jam Kerja.";
            }
        }
    } else {
        $error_msg = "Pilih tagihan yang akan dikonfirmasi.";
    }
}

// Get invoices
$invoices = $db->fetchAll("SELECT * FROM subscriptions WHERE tenant_id = ? ORDER BY created_at DESC", [$tenant_id]);
$hasPending = false;
$pendingInvoice = null;
foreach($invoices as $inv) {
    if ($inv['payment_status'] === 'pending') {
        $hasPending = true;
        if (!$pendingInvoice) $pendingInvoice = $inv; // First pending invoice
    }
}

// Fallback: Jika admin dibekukan tapi Superadmin belum/lupa membuatkan tagihan, buatkan otomatis
if (in_array($sub_status, ['expired', 'suspended']) && !$hasPending) {
    $db->insert('subscriptions', [
        'tenant_id' => $tenant_id,
        'amount' => getTenantPlanPrice($tenant_id),
        'payment_status' => 'pending',
        'expired_at' => date('Y-m-d', strtotime('+30 days'))
    ]);
    // Refresh agar tagihan barusan muncul di layar
    header("Refresh:0");
    exit;
}
?>

<div class="app-layout">
  <?php if (in_array($sub_status, ['expired', 'suspended'])): ?>
    <!-- Blocked Mode: No sidebar, full screen payment panel -->
    <div style="width:100%; max-width: 1000px; margin: 40px auto; padding: 20px;">
  <?php else: ?>
    <!-- Active Mode: Normal app flow -->
    <?php include 'includes/sidebar.php'; ?>
    <div class="main-wrapper">
      <?php include 'includes/topbar.php'; ?>
      <main class="main-content">
  <?php endif; ?>

      <?php if (in_array($sub_status, ['expired', 'suspended'])): ?>
      <div style="background:var(--danger); color:white; padding: 25px; border-radius: var(--radius-lg); margin-bottom: 30px; text-align:center; box-shadow:var(--shadow-lg);">
        <h2 style="margin-bottom: 15px; font-size:1.7rem;">⚠️ AKSES FASILITAS DIBEKUKAN SEMENTARA</h2>
        <p style="font-size:1rem; line-height:1.6;">Akses Dasbor Admin dan seluruh fungsi Warga untuk komunitas Anda sedang ditangguhkan pusat.<br>Silakan selesaikan kewajiban tagihan berlangganan yang tertunda untuk mengaktifkan kembali layanan secara instan.</p>
        <div style="margin-top:25px;">
            <a href="logout.php" class="btn" style="background:white; color:var(--danger); font-weight:800; border:none; padding:10px 25px;">Keluar (Logout System)</a>
        </div>
      </div>
      <?php else: ?>
      <div class="page-header">
        <div>
          <h1>Tagihan & Langganan Situs</h1>
          <div class="breadcrumb">
            <span class="separator">/</span>
            <span>Riwayat Invoices <?= htmlspecialchars($appSettings['app_name'] ?? 'Sistem') ?> Perumahan Anda</span>
          </div>
        </div>
      </div>
      <?php endif; ?>

      <?php if ($success_msg): ?>
      <div style="background:rgba(16,185,129,0.1);border:1px solid rgba(16,185,129,0.3);color:var(--success);padding:15px 20px;border-radius:var(--radius-md);margin-bottom:25px;font-size:0.95rem;">
        ✅ <strong>Tertampung:</strong> <?= htmlspecialchars($success_msg) ?>
      </div>
      <?php endif; ?>
      <?php if ($error_msg): ?>
      <div style="background:rgba(239,68,68,0.1);border:1px solid rgba(239,68,68,0.3);color:var(--danger);padding:15px 20px;border-radius:var(--radius-md);margin-bottom:25px;font-size:0.95rem;">
        ⚠️ <?= htmlspecialchars($error_msg) ?>
      </div>
      <?php endif; ?>

      <div class="grid-2">
        <!-- LEFT: Payment Instructions + Upload -->
        <div class="card" style="align-self: flex-start;">
            <div class="card-header border-bottom">
                <h3 class="card-title">Instruksi Pembayaran</h3>
            </div>
            <div style="padding:25px;">
                <p style="margin-bottom:20px; color:var(--text-muted); line-height:1.6;">Silakan lakukan mekanisme transfer senilai <b>Nominal Tagihan</b> yang tertera (Status Pending) pada layar Riwayat di sebelah kanan Anda.</p>
                
                <div style="background:var(--bg-main); padding:20px; border-radius:var(--radius-md); border:1px solid var(--border-color); margin-bottom:20px;">
                    <h4 style="font-size:0.9rem; color:var(--text-secondary); margin-bottom:10px;">Bank Tujuan Pembayaran Aplikasi</h4>
                    <?php 
                    $banks = $GLOBAL_SETTINGS['bank_accounts'] ?? [];
                    if (!empty($banks)): 
                        foreach($banks as $index => $bank):
                    ?>
                        <div style="<?= $index > 0 ? 'margin-top:20px; padding-top:15px; border-top:1px dashed var(--border-color);' : '' ?>">
                            <p style="font-size:1.6rem; font-weight:800; color:var(--primary); margin-bottom:5px; font-family:monospace; letter-spacing:1px;"><?= htmlspecialchars($bank['name']) ?> <?= htmlspecialchars($bank['number']) ?></p>
                            <p style="color:var(--text-muted); font-size:0.95rem;">a.n <?= htmlspecialchars($bank['holder']) ?></p>
                        </div>
                    <?php 
                        endforeach;
                    else: 
                    ?>
                        <p style="font-size:1.6rem; font-weight:800; color:var(--primary); margin-bottom:5px; font-family:monospace; letter-spacing:1px;">BCA 837 000 1234</p>
                        <p style="color:var(--text-muted); font-size:0.95rem;">a.n PT Teknologi Inovasi Perumahan</p>
                    <?php endif; ?>
                    
                    <?php if (!empty($GLOBAL_SETTINGS['payment_instructions'])): ?>
                        <div style="margin-top:15px; padding-top:10px; border-top:1px solid var(--border-color); font-size:0.85rem; color:var(--text-secondary); line-height:1.5;">
                            <?= nl2br(htmlspecialchars($GLOBAL_SETTINGS['payment_instructions'])) ?>
                        </div>
                    <?php endif; ?>
                </div>
                
                <?php if ($hasPending && $pendingInvoice): ?>
                <!-- Pending Invoice Summary -->
                <div style="background:rgba(217,119,6,0.08); border:1px solid rgba(217,119,6,0.25); border-radius:var(--radius-md); padding:15px; margin-bottom:20px;">
                    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:8px;">
                        <span style="font-size:0.8rem; color:var(--text-muted); font-weight:600;">TAGIHAN AKTIF</span>
                        <span class="badge badge-warning" style="font-size:0.7rem;">PENDING</span>
                    </div>
                    <div style="font-size:1.3rem; font-weight:800; color:var(--text-main);">
                        Rp <?= number_format($pendingInvoice['amount'], 0, ',', '.') ?>
                    </div>
                    <div style="font-size:0.8rem; color:var(--text-muted); margin-top:4px;">
                        #INV-<?= str_pad($pendingInvoice['id'], 4, '0', STR_PAD_LEFT) ?> · Jatuh tempo: <?= date('d M Y', strtotime($pendingInvoice['expired_at'])) ?>
                    </div>
                </div>

                <!-- Upload Form -->
                <form method="POST" enctype="multipart/form-data" id="paymentForm">
                    <input type="hidden" name="action" value="notify_payment">
                    <input type="hidden" name="sub_id" value="<?= $pendingInvoice['id'] ?>">
                    
                    <div style="margin-bottom:20px;">
                        <label style="display:block; font-size:0.85rem; font-weight:600; color:var(--text-main); margin-bottom:8px;">
                            <i data-lucide="upload-cloud" style="width:14px;height:14px;display:inline-block;vertical-align:middle;margin-right:4px;"></i>
                            Upload Bukti Transfer *
                        </label>
                        
                        <!-- Drop zone -->
                        <div id="dropZone" style="border:2px dashed var(--border-color); border-radius:var(--radius-md); padding:30px 20px; text-align:center; cursor:pointer; transition:all 0.3s ease; background:var(--bg-main);" onclick="document.getElementById('fileInput').click()">
                            <div id="dropContent">
                                <i data-lucide="image-plus" style="width:40px;height:40px;color:var(--text-muted);margin-bottom:10px;"></i>
                                <p style="color:var(--text-muted); font-size:0.9rem; margin-bottom:5px;">Klik atau seret file kesini</p>
                                <p style="color:var(--text-muted); font-size:0.75rem;">JPG, PNG, WEBP · Maks 2MB</p>
                            </div>
                            <div id="previewContainer" style="display:none;">
                                <img id="previewImg" src="" alt="Preview" style="max-width:100%; max-height:200px; border-radius:var(--radius-sm); margin-bottom:10px; box-shadow:var(--shadow-sm);">
                                <p id="fileName" style="font-size:0.8rem; color:var(--text-secondary); font-weight:600;"></p>
                                <button type="button" onclick="event.stopPropagation(); removeFile()" style="background:rgba(239,68,68,0.1); color:var(--danger); border:1px solid rgba(239,68,68,0.2); padding:4px 12px; border-radius:var(--radius-sm); font-size:0.75rem; cursor:pointer; margin-top:8px;">
                                    <i data-lucide="x" style="width:12px;height:12px;display:inline-block;vertical-align:middle;"></i> Hapus
                                </button>
                            </div>
                        </div>
                        <input type="file" name="payment_proof" id="fileInput" accept="image/jpeg,image/png,image/webp" style="display:none;" required>
                    </div>

                    <?php
                    // Check if proof already uploaded for this invoice
                    $existingProof = $pendingInvoice['payment_proof'] ?? null;
                    ?>
                    <?php if ($existingProof): ?>
                    <div style="background:rgba(16,185,129,0.08); border:1px solid rgba(16,185,129,0.2); border-radius:var(--radius-md); padding:12px 15px; margin-bottom:20px; display:flex; align-items:center; gap:12px;">
                        <i data-lucide="check-circle" style="width:20px;height:20px;color:var(--success);flex-shrink:0;"></i>
                        <div>
                            <p style="font-size:0.85rem; font-weight:600; color:var(--success); margin-bottom:2px;">Bukti Transfer Sudah Diupload</p>
                            <p style="font-size:0.75rem; color:var(--text-muted);">Sedang menunggu verifikasi admin pusat. Anda dapat upload ulang jika perlu.</p>
                        </div>
                        <a href="<?= htmlspecialchars($existingProof) ?>" target="_blank" style="margin-left:auto; flex-shrink:0;">
                            <img src="<?= htmlspecialchars($existingProof) ?>" alt="Bukti" style="width:50px; height:50px; object-fit:cover; border-radius:var(--radius-sm); border:2px solid var(--border-color);">
                        </a>
                    </div>
                    <?php endif; ?>

                    <button type="submit" id="submitBtn" class="btn btn-primary" style="width:100%; padding: 12px; font-weight:bold; font-size:1.05rem;">
                        <i data-lucide="send" style="width:16px;height:16px;display:inline-block;vertical-align:middle;margin-right:6px;"></i>
                        <?= $existingProof ? 'Upload Ulang Bukti Transfer' : 'Kirim Bukti Transfer' ?>
                    </button>
                </form>
                <?php else: ?>
                <button class="btn btn-secondary" style="width:100%; padding:12px; opacity:0.6; cursor:not-allowed;" disabled>Tidak Ada Tagihan Aktif (Sudah Bebas)</button>
                <?php endif; ?>
            </div>
        </div>

        <!-- RIGHT: Invoice History -->
        <div class="card" style="align-self: flex-start;">
            <div class="card-header border-bottom">
                <h3 class="card-title">Riwayat Berlangganan (Invoices)</h3>
            </div>
            <div class="table-container" style="max-height:500px; overflow-y:auto;">
                <table class="table" style="font-size:0.95rem;">
                    <thead>
                        <tr>
                            <th>Invoice ID</th>
                            <th>Nominal</th>
                            <th>Batas Terakhir</th>
                            <th>Bukti</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if(empty($invoices)): ?>
                        <tr><td colspan="5" style="text-align:center;padding:30px;color:var(--text-muted);">Tidak menemukan catatan tagihan biaya berlangganan apapun.</td></tr>
                        <?php endif; ?>

                        <?php foreach($invoices as $inv): 
                            $badge = 'badge-warning';
                            if ($inv['payment_status'] === 'paid') $badge = 'badge-success';
                            elseif ($inv['payment_status'] === 'failed') $badge = 'badge-danger';
                        ?>
                        <tr>
                            <td><strong>#INV-<?= str_pad($inv['id'], 4, '0', STR_PAD_LEFT) ?></strong></td>
                            <td style="font-weight:700; color:var(--text-main);">Rp <?= number_format($inv['amount'], 0, ',', '.') ?></td>
                            <td><?= date('d M Y', strtotime($inv['expired_at'])) ?></td>
                            <td>
                                <?php if (!empty($inv['payment_proof'])): ?>
                                <a href="<?= htmlspecialchars($inv['payment_proof']) ?>" target="_blank" title="Lihat Bukti Transfer" style="display:inline-flex; align-items:center; gap:4px; color:var(--primary); font-size:0.8rem; text-decoration:none; font-weight:600;">
                                    <i data-lucide="file-image" style="width:14px;height:14px;"></i> Lihat
                                </a>
                                <?php else: ?>
                                <span style="color:var(--text-muted); font-size:0.75rem;">-</span>
                                <?php endif; ?>
                            </td>
                            <td><span class="badge <?= $badge ?>"><?= strtoupper($inv['payment_status']) ?></span></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
      </div>

  <?php if (in_array($sub_status, ['expired', 'suspended'])): ?>
    </div>
  <?php else: ?>
      </main>
    </div>
  <?php endif; ?>
</div>

<script>
// File upload handling with drag & drop + preview
const dropZone = document.getElementById('dropZone');
const fileInput = document.getElementById('fileInput');
const dropContent = document.getElementById('dropContent');
const previewContainer = document.getElementById('previewContainer');
const previewImg = document.getElementById('previewImg');
const fileNameEl = document.getElementById('fileName');

if (dropZone) {
    // Drag events
    ['dragenter', 'dragover'].forEach(evt => {
        dropZone.addEventListener(evt, (e) => {
            e.preventDefault();
            dropZone.style.borderColor = 'var(--primary)';
            dropZone.style.background = 'rgba(59,130,246,0.05)';
        });
    });
    ['dragleave', 'drop'].forEach(evt => {
        dropZone.addEventListener(evt, (e) => {
            e.preventDefault();
            dropZone.style.borderColor = 'var(--border-color)';
            dropZone.style.background = 'var(--bg-main)';
        });
    });
    dropZone.addEventListener('drop', (e) => {
        const files = e.dataTransfer.files;
        if (files.length > 0) {
            fileInput.files = files;
            showPreview(files[0]);
        }
    });
}

if (fileInput) {
    fileInput.addEventListener('change', function() {
        if (this.files.length > 0) {
            showPreview(this.files[0]);
        }
    });
}

function showPreview(file) {
    // Validate
    const allowedTypes = ['image/jpeg', 'image/png', 'image/webp'];
    if (!allowedTypes.includes(file.type)) {
        alert('Format file tidak didukung. Gunakan JPG, PNG, atau WEBP.');
        fileInput.value = '';
        return;
    }
    if (file.size > 2 * 1024 * 1024) {
        alert('Ukuran file melebihi batas 2MB.');
        fileInput.value = '';
        return;
    }

    const reader = new FileReader();
    reader.onload = function(e) {
        previewImg.src = e.target.result;
        fileNameEl.textContent = file.name + ' (' + (file.size / 1024).toFixed(0) + ' KB)';
        dropContent.style.display = 'none';
        previewContainer.style.display = 'block';
        dropZone.style.borderColor = 'var(--success)';
        dropZone.style.background = 'rgba(16,185,129,0.03)';
    };
    reader.readAsDataURL(file);
}

function removeFile() {
    fileInput.value = '';
    dropContent.style.display = 'block';
    previewContainer.style.display = 'none';
    dropZone.style.borderColor = 'var(--border-color)';
    dropZone.style.background = 'var(--bg-main)';
}

// Form validation
const paymentForm = document.getElementById('paymentForm');
if (paymentForm) {
    paymentForm.addEventListener('submit', function(e) {
        if (!fileInput.files || fileInput.files.length === 0) {
            e.preventDefault();
            alert('Silakan upload bukti transfer terlebih dahulu.');
            return false;
        }
        
        const submitBtn = document.getElementById('submitBtn');
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<i data-lucide="loader" style="width:16px;height:16px;display:inline-block;vertical-align:middle;margin-right:6px;animation:spin 1s linear infinite;"></i> Mengunggah...';
    });
}
</script>

<style>
@keyframes spin {
    from { transform: rotate(0deg); }
    to { transform: rotate(360deg); }
}
#dropZone:hover {
    border-color: var(--primary) !important;
    background: rgba(59,130,246,0.03) !important;
}
</style>

<?php include 'includes/footer.php'; ?>
