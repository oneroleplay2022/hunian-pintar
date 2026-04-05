<?php
require_once 'classes/Auth.php';
require_once 'classes/Database.php';
require_once 'classes/PlanHelper.php';

Auth::requireRole('superadmin');
$db = Database::getInstance();

$pageTitle = 'Tagihan SaaS';

$success_msg = '';
$error_msg = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    
    if ($action === 'approve_payment') {
        $sub_id = (int)($_POST['sub_id'] ?? 0);
        $method = $_POST['payment_method'] ?? 'Transfer';
        
        if ($sub_id) {
            try {
                $db->beginTransaction();
                
                // 1. Catat pembayaran di tabel subscriptions
                $db->update('subscriptions', [
                    'payment_status' => 'paid',
                    'payment_method' => $method
                ], 'id = ?', [$sub_id]);
                
                // 2. Ambil informasi tagihan untuk memperbarui tenant
                $sub = $db->fetch("SELECT tenant_id, expired_at FROM subscriptions WHERE id = ?", [$sub_id]);
                
                if ($sub) {
                    // 3. Ubah status tenant jadi 'active' dan perpanjang masa aktif (expired_at)
                    $db->update('tenants', [
                        'subscription_status' => 'active',
                        'expired_at' => $sub['expired_at']
                    ], 'id = ?', [$sub['tenant_id']]);
                }
                
                $db->commit();
                $success_msg = "Pembayaran dikonfirmasi, masa aktif/status tenant telah diperbarui.";
            } catch (Exception $e) {
                $db->rollback();
                $error_msg = "Gagal memproses pembayaran: " . $e->getMessage();
            }
        }
    } elseif ($action === 'create_invoice') {
        $tenant_id = (int)($_POST['tenant_id'] ?? 0);
        $amount = (float)($_POST['amount'] ?? 0);
        $expired_at = $_POST['expired_at'] ?? '';
        
        if ($tenant_id && $amount && $expired_at) {
            $db->insert('subscriptions', [
                'tenant_id' => $tenant_id,
                'amount' => $amount,
                'payment_status' => 'pending',
                'expired_at' => $expired_at
            ]);
            $success_msg = "Tagihan langganan baru berhasil dibuat.";
        } else {
            $error_msg = "Semua field tagihan wajib diisi.";
        }
    }
}

// Comprehensive SaaS Finance Analytics
$analytics = $db->fetch("SELECT 
    COUNT(id) as total_invoices,
    SUM(CASE WHEN payment_status = 'paid' THEN amount ELSE 0 END) as lifetime_revenue,
    SUM(CASE WHEN payment_status = 'pending' THEN amount ELSE 0 END) as total_receivables,
    SUM(CASE WHEN payment_status = 'paid' AND MONTH(created_at) = MONTH(CURRENT_DATE) AND YEAR(created_at) = YEAR(CURRENT_DATE) THEN amount ELSE 0 END) as monthly_revenue,
    COUNT(CASE WHEN payment_status = 'pending' THEN 1 END) as pending_count,
    AVG(CASE WHEN payment_status = 'paid' THEN amount ELSE NULL END) as avg_transaction
    FROM subscriptions");

$lifetimeRevenue = $analytics['lifetime_revenue'] ?? 0;
$receivables = $analytics['total_receivables'] ?? 0;
$monthlyRevenue = $analytics['monthly_revenue'] ?? 0;
$pendingCount = $analytics['pending_count'] ?? 0;
$avgTrx = $analytics['avg_transaction'] ?? 0;

// Revenue by Tenant (Top 5 Customers)
$topTenants = $db->fetchAll("SELECT t.name, SUM(s.amount) as total_spent 
                             FROM subscriptions s 
                             JOIN tenants t ON s.tenant_id = t.id 
                             WHERE s.payment_status = 'paid' 
                             GROUP BY s.tenant_id 
                             ORDER BY total_spent DESC 
                             LIMIT 5");

// Monthly Growth (Last 6 Months)
$growthData = $db->fetchAll("SELECT DATE_FORMAT(created_at, '%b %Y') as month, 
                             SUM(amount) as revenue 
                             FROM subscriptions 
                             WHERE payment_status = 'paid' 
                             GROUP BY month 
                             ORDER BY created_at ASC 
                             LIMIT 6");

// Filter & Search Handling
$searchTenant = (int)($_GET['tenant_id'] ?? 0);
$whereClause = $searchTenant ? "WHERE s.tenant_id = $searchTenant" : "";

// Fetch subscriptions list
$query = "SELECT s.*, t.name as tenant_name 
          FROM subscriptions s 
          JOIN tenants t ON s.tenant_id = t.id 
          $whereClause
          ORDER BY s.created_at DESC";
$subscriptions = $db->fetchAll($query);

// Fetch tenants for dropdown (with plan price)
$tenants = $db->fetchAll("SELECT t.id, t.name, COALESCE(p.price, 0) as plan_price 
                          FROM tenants t 
                          LEFT JOIN subscription_plans p ON t.plan_id = p.id 
                          ORDER BY t.name ASC");

?>
<?php include 'includes/header.php'; ?>

<div class="app-layout">
  <!-- Sidebar Khusus Superadmin -->
  <?php include 'includes/sidebar_saas.php'; ?>

  <div class="main-wrapper">
    <?php include 'includes/topbar.php'; ?>

    <main class="main-content">
      <div class="page-header">
        <div>
          <h1>Keuangan SaaS (Finance Dashboard)</h1>
          <div class="breadcrumb">
            <span class="separator">/</span>
            <span>Analytics & Penagihan Pusat</span>
          </div>
        </div>
        <div style="display:flex; gap:10px;">
            <form method="GET" style="display:flex; gap:8px; margin:0; align-items:center;">
                <select name="tenant_id" class="form-control" style="font-size:0.8rem; padding:6px 10px; width:200px;" onchange="this.form.submit()">
                    <option value="">-- Semua Perumahan --</option>
                    <?php foreach($tenants as $tn): ?>
                    <option value="<?= $tn['id'] ?>" <?= $searchTenant == $tn['id'] ? 'selected' : '' ?>><?= htmlspecialchars($tn['name']) ?></option>
                    <?php endforeach; ?>
                </select>
                <?php if($searchTenant): ?>
                    <a href="saas_billing.php" class="btn btn-secondary btn-sm" title="Clear Filter">&times;</a>
                <?php endif; ?>
            </form>
            <button class="btn btn-primary btn-sm" onclick="document.getElementById('addInvoiceModal').classList.add('active')"><i data-lucide="plus" style="width:16px;height:16px;"></i> Buat Tagihan Baru</button>
        </div>
      </div>

      <!-- Financial Headline Cards -->
      <div class="stats-grid" style="grid-template-columns: repeat(4, 1fr); margin-bottom: 25px;">
        <div class="stat-card" style="border-bottom: 4px solid var(--primary);">
            <div class="stat-icon blue"><i data-lucide="trending-up"></i></div>
            <div class="stat-info">
              <small style="color:var(--text-muted);font-weight:600;font-size:0.75rem;text-transform:uppercase;">Lifetime Revenue</small>
              <div class="stat-value" style="font-size:1.2rem; color:var(--primary); font-weight:800;">Rp <?= number_format($lifetimeRevenue, 0, ',', '.') ?></div>
            </div>
        </div>
        <div class="stat-card" style="border-bottom: 4px solid var(--success);">
            <div class="stat-icon green"><i data-lucide="calendar"></i></div>
            <div class="stat-info">
              <small style="color:var(--text-muted);font-weight:600;font-size:0.75rem;text-transform:uppercase;">Revenue Bulan Ini</small>
              <div class="stat-value" style="font-size:1.2rem; color:var(--success); font-weight:800;">Rp <?= number_format($monthlyRevenue, 0, ',', '.') ?></div>
            </div>
        </div>
        <div class="stat-card" style="border-bottom: 4px solid var(--warning);">
            <div class="stat-icon yellow"><i data-lucide="alert-circle"></i></div>
            <div class="stat-info">
              <small style="color:var(--text-muted);font-weight:600;font-size:0.75rem;text-transform:uppercase;">Total Piutang</small>
              <div class="stat-value" style="font-size:1.2rem; color:var(--warning); font-weight:800;">Rp <?= number_format($receivables, 0, ',', '.') ?></div>
              <small style="color:var(--danger);font-weight:700;"><?= $pendingCount ?> Invoice Pending</small>
            </div>
        </div>
        <div class="stat-card" style="border-bottom: 4px solid #8b5cf6;">
            <div class="stat-icon" style="background:#8b5cf615; color:#8b5cf6;"><i data-lucide="bar-chart"></i></div>
            <div class="stat-info">
              <small style="color:var(--text-muted);font-weight:600;font-size:0.75rem;text-transform:uppercase;">ARPU (Rata-rata Tagihan)</small>
              <div class="stat-value" style="font-size:1.2rem; color:#8b5cf6; font-weight:800;">Rp <?= number_format($avgTrx, 0, ',', '.') ?></div>
            </div>
        </div>
      </div>

      <div class="grid-2-1" style="display:grid; grid-template-columns: 2fr 1fr; gap:20px; margin-bottom:20px;">
        <div class="card">
            <div class="card-header border-bottom d-flex justify-content-between">
                <h3 class="card-title">Riwayat Penagihan & Invoices</h3>
                <?php if($searchTenant): ?> <span class="badge badge-primary">Filter: <?= htmlspecialchars($tenants[array_search($searchTenant, array_column($tenants, 'id'))]['name'] ?? '...') ?></span> <?php endif; ?>
            </div>
            <div class="table-container">
              <table class="table">
                <thead>
                  <tr>
                    <th>Invoice ID</th>
                    <th>Nama Klien</th>
                    <th>Nominal</th>
                    <th>Tgl Jatuh Tempo</th>
                    <th>Bukti Transfer</th>
                    <th>Status</th>
                    <th>Aksi</th>
                  </tr>
                </thead>
                <tbody>
                  <?php if (empty($subscriptions)): ?>
                  <tr><td colspan="7" style="text-align:center;padding:40px;color:var(--text-muted);">Tidak ada transaksi yang ditemukan untuk kriteria ini.</td></tr>
                  <?php endif; ?>
                  <?php foreach ($subscriptions as $s): 
                    $badge = 'badge-warning';
                    if ($s['payment_status'] === 'paid') $badge = 'badge-success';
                    elseif ($s['payment_status'] === 'failed') $badge = 'badge-danger';
                  ?>
                  <tr>
                    <td><a href="saas_invoice_print.php?id=<?= $s['id'] ?>" target="_blank" style="text-decoration:none; color:var(--primary); font-weight:700;">#INV-<?= str_pad($s['id'], 4, '0', STR_PAD_LEFT) ?></a></td>
                    <td><?= htmlspecialchars($s['tenant_name']) ?></td>
                    <td style="font-weight:700;">Rp <?= number_format($s['amount'], 0, ',', '.') ?></td>
                    <td><?= date('d M Y', strtotime($s['expired_at'])) ?></td>
                    <td>
                      <?php if (!empty($s['payment_proof'])): ?>
                      <a href="javascript:void(0)" onclick="viewProof('<?= htmlspecialchars($s['payment_proof']) ?>', '#INV-<?= str_pad($s['id'], 4, '0', STR_PAD_LEFT) ?>')" style="display:inline-flex; align-items:center; gap:4px; text-decoration:none;">
                        <img src="<?= htmlspecialchars($s['payment_proof']) ?>" alt="Bukti" style="width:36px; height:36px; object-fit:cover; border-radius:var(--radius-sm); border:2px solid var(--border-color); transition:transform 0.2s;" onmouseover="this.style.transform='scale(1.15)'" onmouseout="this.style.transform='scale(1)'">
                        <span style="font-size:0.7rem; color:var(--primary); font-weight:600;">Lihat</span>
                      </a>
                      <?php else: ?>
                      <span style="color:var(--text-muted); font-size:0.7rem;">Belum ada</span>
                      <?php endif; ?>
                    </td>
                    <td><span class="badge <?= $badge ?>"><?= strtoupper($s['payment_status']) ?></span></td>
                    <td>
                      <div style="display:flex; gap:8px; align-items:center;">
                        <a href="saas_invoice_print.php?id=<?= $s['id'] ?>" target="_blank" class="btn btn-icon btn-xs btn-secondary" title="Cetak Invoice">
                          <i data-lucide="printer" style="width:14px;height:14px;"></i>
                        </a>
                        <?php if ($s['payment_status'] === 'pending'): ?>
                        <form method="POST" style="margin:0; display:flex; gap:5px; align-items:center;">
                          <input type="hidden" name="action" value="approve_payment">
                          <input type="hidden" name="sub_id" value="<?= $s['id'] ?>">
                          <select name="payment_method" class="form-control" style="font-size:0.75rem; padding:4px 8px; width:100px;" required>
                              <option value="Transfer">Transfer</option>
                              <option value="Tunai">Tunai</option>
                              <option value="Lainnya">Lainnya</option>
                          </select>
                          <button type="submit" class="btn btn-xs btn-success" onclick="return confirm('Sudah menerima dana di mutasi bank/tangan?')">Approve</button>
                        </form>
                        <?php else: ?>
                        <span class="text-muted" style="font-size:0.75rem;">Lunas via <?= $s['payment_method'] ?></span>
                        <?php endif; ?>
                      </div>
                    </td>
                  </tr>
                  <?php endforeach; ?>
                </tbody>
              </table>
            </div>
        </div>

        <div class="card">
            <div class="card-header border-bottom">
                <h3 class="card-title">Top 5 Kontributor Revenue</h3>
            </div>
            <div style="padding:20px;">
                <?php foreach($topTenants as $tt): ?>
                <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:15px; padding-bottom:10px; border-bottom:1px solid var(--border-color);">
                    <div>
                        <div style="font-weight:700; font-size:0.9rem; color:var(--text-main);"><?= htmlspecialchars($tt['name']) ?></div>
                        <small style="color:var(--text-muted);">Total Pembayaran</small>
                    </div>
                    <div style="font-weight:800; color:var(--primary); font-size:0.9rem;">Rp <?= number_format($tt['total_spent'], 0, ',', '.') ?></div>
                </div>
                <?php endforeach; ?>
                <?php if(empty($topTenants)): ?>
                    <p style="text-align:center; color:var(--text-muted); font-size:0.85rem;">Belum ada data pendapatan untuk dianalisis.</p>
                <?php endif; ?>
            </div>
        </div>
      </div>
    </main>
  </div>
</div>

<!-- Modal Add Invoice -->
<div class="modal-overlay" id="addInvoiceModal">
  <div class="modal card" style="width:100%;max-width:500px;background:var(--bg-card);border-radius:var(--radius-lg);padding:0;overflow:hidden;box-shadow:var(--shadow-lg);">
    <form method="POST">
      <input type="hidden" name="action" value="create_invoice">
      <div class="modal-header border-bottom" style="display:flex;justify-content:space-between;align-items:center;padding:15px 20px;">
        <h3 style="margin:0;font-size:1.1rem;color:var(--text-main);">Buat Tagihan (Invoice) Baru</h3>
        <button type="button" onclick="document.getElementById('addInvoiceModal').classList.remove('active')" style="background:none;border:none;cursor:pointer;font-size:1.2rem;color:var(--text-muted);">&times;</button>
      </div>
      <div class="modal-body" style="padding:20px;max-height:60vh;overflow-y:auto;text-align:left;">
        <div class="form-group" style="margin-bottom:15px;">
          <label class="form-label">Perumahan Pelanggan *</label>
          <select name="tenant_id" id="invoiceTenantSelect" class="form-control" required style="width:100%;" onchange="autoFillAmount()">
            <option value="" data-price="0">-- Pilih Perumahan --</option>
            <?php foreach($tenants as $tn): ?>
            <option value="<?= $tn['id'] ?>" data-price="<?= $tn['plan_price'] ?>"><?= htmlspecialchars($tn['name']) ?> (Rp <?= number_format($tn['plan_price'], 0, ',', '.') ?>/bln)</option>
            <?php endforeach; ?>
          </select>
        </div>
        <div class="form-group" style="margin-bottom:15px;">
          <label class="form-label">Nominal Tagihan (Rp) *</label>
          <input type="number" name="amount" id="invoiceAmount" class="form-control" required style="width:100%;" min="0" value="">
          <small class="text-muted">Otomatis terisi sesuai paket aktif tenant. Bisa diubah manual jika perlu.</small>
        </div>
        <div class="form-group" style="margin-bottom:15px;">
          <label class="form-label">Batas Waktu Target/Expired *</label>
          <input type="date" name="expired_at" class="form-control" required style="width:100%;" value="<?= date('Y-m-d', strtotime('+1 month')) ?>">
        </div>
      </div>
      <div class="modal-footer border-top" style="display:flex;justify-content:flex-end;gap:10px;padding:15px 20px;">
        <button type="button" class="btn btn-secondary" onclick="document.getElementById('addInvoiceModal').classList.remove('active')">Batal</button>
        <button type="submit" class="btn btn-primary" style="padding:8px 20px;">Buat Tagihan</button>
      </div>
    </form>
  </div>
</div>

<?php include 'includes/footer.php'; ?>

<!-- Modal View Payment Proof -->
<div class="modal-overlay" id="proofModal">
  <div class="modal card" style="width:100%;max-width:550px;background:var(--bg-card);border-radius:var(--radius-lg);padding:0;overflow:hidden;box-shadow:var(--shadow-lg);">
    <div class="modal-header border-bottom" style="display:flex;justify-content:space-between;align-items:center;padding:15px 20px;">
      <h3 style="margin:0;font-size:1.1rem;color:var(--text-main);">
        <i data-lucide="file-image" style="width:18px;height:18px;display:inline-block;vertical-align:middle;margin-right:6px;"></i>
        Bukti Transfer <span id="proofInvoiceId" style="color:var(--primary);"></span>
      </h3>
      <button type="button" onclick="document.getElementById('proofModal').classList.remove('active')" style="background:none;border:none;cursor:pointer;font-size:1.2rem;color:var(--text-muted);">&times;</button>
    </div>
    <div class="modal-body" style="padding:20px;text-align:center;">
      <img id="proofImage" src="" alt="Bukti Transfer" style="max-width:100%;max-height:400px;border-radius:var(--radius-md);box-shadow:var(--shadow-md);border:1px solid var(--border-color);">
      <div style="margin-top:15px;">
        <a id="proofDownload" href="" target="_blank" class="btn btn-secondary btn-sm" style="font-size:0.8rem;">
          <i data-lucide="external-link" style="width:14px;height:14px;display:inline-block;vertical-align:middle;margin-right:4px;"></i> Buka di Tab Baru
        </a>
      </div>
    </div>
  </div>
</div>

<script>
function viewProof(imageSrc, invoiceId) {
    document.getElementById('proofImage').src = imageSrc;
    document.getElementById('proofDownload').href = imageSrc;
    document.getElementById('proofInvoiceId').textContent = invoiceId;
    document.getElementById('proofModal').classList.add('active');
    // Re-init lucide for new icons
    if (typeof lucide !== 'undefined') lucide.createIcons();
}

function autoFillAmount() {
    const select = document.getElementById('invoiceTenantSelect');
    const amountInput = document.getElementById('invoiceAmount');
    const selected = select.options[select.selectedIndex];
    const price = selected ? selected.getAttribute('data-price') : 0;
    amountInput.value = price || '';
}
</script>
