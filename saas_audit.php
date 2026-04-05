<?php
require_once 'classes/Auth.php';
require_once 'classes/Database.php';

// Initialize Database & Auth
$db = Database::getInstance();
Auth::requireRole('superadmin');

$pageTitle = 'Audit Log Sistem';

// Filter parameters
$filterAction = trim($_GET['action_filter'] ?? '');
$filterUser = trim($_GET['user_filter'] ?? '');
$filterDateFrom = trim($_GET['date_from'] ?? '');
$filterDateTo = trim($_GET['date_to'] ?? '');

// Build query with filters
$where = [];
$params = [];

if ($filterAction) {
    $where[] = "a.action LIKE ?";
    $params[] = "%$filterAction%";
}
if ($filterUser) {
    $where[] = "(u.name LIKE ? OR a.user_id = ?)";
    $params[] = "%$filterUser%";
    $params[] = $filterUser;
}
if ($filterDateFrom) {
    $where[] = "a.created_at >= ?";
    $params[] = $filterDateFrom . ' 00:00:00';
}
if ($filterDateTo) {
    $where[] = "a.created_at <= ?";
    $params[] = $filterDateTo . ' 23:59:59';
}

$whereClause = !empty($where) ? 'WHERE ' . implode(' AND ', $where) : '';

$query = "
    SELECT a.*, 
           t.name as tenant_name, 
           u.name as user_name 
    FROM audit_logs a 
    LEFT JOIN tenants t ON a.tenant_id = t.id 
    LEFT JOIN users u ON a.user_id = u.id 
    $whereClause
    ORDER BY a.created_at DESC 
    LIMIT 500
";
$logs = $db->fetchAll($query, $params);

// Distinct actions for filter dropdown
$actions = $db->fetchAll("SELECT DISTINCT action FROM audit_logs ORDER BY action ASC");

// Fungsi pembantu untuk memproses value JSON
function formatJsonOutput($jsonString) {
    if (!$jsonString) return '<em style="color:var(--text-muted);">Tidak ada data</em>';
    $decoded = json_decode($jsonString, true);
    if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
        $html = '<table style="width:100%;border-collapse:collapse;font-size:0.8rem;">';
        foreach ($decoded as $key => $value) {
            $valStr = is_array($value) ? json_encode($value, JSON_PRETTY_PRINT) : htmlspecialchars((string)$value);
            $html .= '<tr style="border-bottom:1px solid rgba(128,128,128,0.1);">';
            $html .= '<td style="width:35%;padding:6px 8px;font-weight:600;vertical-align:top;">' . htmlspecialchars($key) . '</td>';
            $html .= '<td style="padding:6px 8px;word-break:break-word;">' . $valStr . '</td>';
            $html .= '</tr>';
        }
        $html .= '</table>';
        return $html;
    }
    return '<pre style="margin:0;white-space:pre-wrap;word-break:break-word;font-size:0.8rem;">' . htmlspecialchars($jsonString) . '</pre>';
}

// Fungsi badge warna per aksi
function actionBadgeClass($action) {
    $action = strtolower($action ?? '');
    if (str_contains($action, 'delete') || str_contains($action, 'hapus')) return 'badge-danger';
    if (str_contains($action, 'add') || str_contains($action, 'create') || str_contains($action, 'tambah')) return 'badge-success';
    if (str_contains($action, 'edit') || str_contains($action, 'update')) return 'badge-warning';
    if (str_contains($action, 'login')) return 'badge-info';
    if (str_contains($action, 'toggle')) return 'badge-warning';
    return 'badge-info';
}
?>
<?php include 'includes/header.php'; ?>

<style>
.audit-filter-bar {
    display: flex;
    gap: 12px;
    align-items: flex-end;
    flex-wrap: wrap;
    padding: 16px 20px;
    background: var(--bg-card);
    border-bottom: 1px solid var(--border-color);
}
.audit-filter-bar .filter-group {
    display: flex;
    flex-direction: column;
    gap: 4px;
}
.audit-filter-bar .filter-group label {
    font-size: 0.75rem;
    font-weight: 600;
    color: var(--text-muted);
    text-transform: uppercase;
    letter-spacing: 0.5px;
}
.audit-filter-bar .filter-group input,
.audit-filter-bar .filter-group select {
    font-size: 0.85rem;
    padding: 7px 12px;
    border-radius: var(--radius-md);
    border: 1px solid var(--border-color);
    background: var(--bg-main);
    color: var(--text-main);
    min-width: 150px;
}
.badge-danger { background: rgba(239,68,68,0.15); color: #ef4444; }
.badge-warning { background: rgba(245,158,11,0.15); color: #f59e0b; }
.audit-row:hover { background: rgba(99,102,241,0.03) !important; }
.detail-panel { transition: all 0.2s ease; }
</style>

<div class="app-layout">
  <?php include 'includes/sidebar_saas.php'; ?>

  <div class="main-wrapper">
    <?php include 'includes/topbar.php'; ?>

    <main class="main-content">
      <div class="page-header">
        <div>
          <h1>Audit Log Sistem</h1>
          <div class="breadcrumb">
            <span class="separator">/</span>
            <span>Pengawasan Aktivitas & Keamanan Data</span>
          </div>
        </div>
        <div style="display:flex;gap:8px;align-items:center;">
            <span style="font-size:0.85rem;color:var(--text-muted);"><?= count($logs) ?> entri ditemukan</span>
        </div>
      </div>

      <div class="card" style="margin-top:20px;">
        <!-- Filter Bar -->
        <form method="GET" class="audit-filter-bar">
          <div class="filter-group">
            <label>Jenis Aksi</label>
            <select name="action_filter">
              <option value="">-- Semua --</option>
              <?php foreach ($actions as $a): ?>
              <option value="<?= htmlspecialchars($a['action']) ?>" <?= $filterAction === $a['action'] ? 'selected' : '' ?>>
                <?= htmlspecialchars($a['action']) ?>
              </option>
              <?php endforeach; ?>
            </select>
          </div>
          <div class="filter-group">
            <label>Pengguna</label>
            <input type="text" name="user_filter" value="<?= htmlspecialchars($filterUser) ?>" placeholder="Cari nama...">
          </div>
          <div class="filter-group">
            <label>Dari Tanggal</label>
            <input type="date" name="date_from" value="<?= htmlspecialchars($filterDateFrom) ?>">
          </div>
          <div class="filter-group">
            <label>Sampai Tanggal</label>
            <input type="date" name="date_to" value="<?= htmlspecialchars($filterDateTo) ?>">
          </div>
          <div class="filter-group" style="flex-direction:row;gap:6px;">
            <button type="submit" class="btn btn-primary btn-sm" style="padding:7px 16px;">
              <i data-lucide="search" style="width:14px;height:14px;"></i> Filter
            </button>
            <a href="saas_audit.php" class="btn btn-secondary btn-sm" style="padding:7px 12px;">Reset</a>
          </div>
        </form>

        <div class="card-header border-bottom" style="display:flex;justify-content:space-between;align-items:center;">
          <h3 class="card-title">Riwayat Aktivitas</h3>
        </div>
        <div class="table-container">
          <table class="table" id="auditTable">
            <thead>
              <tr>
                <th style="width:160px;">Waktu</th>
                <th>Aksi</th>
                <th>Pengguna</th>
                <th>Klien/Tenant</th>
                <th>Target Tabel</th>
                <th>IP Address</th>
                <th style="width:60px;">Detail</th>
              </tr>
            </thead>
            <tbody>
              <?php if (empty($logs)): ?>
              <tr>
                <td colspan="7" style="text-align:center;padding:40px;color:var(--text-muted);">
                    <i data-lucide="inbox" style="width:40px;height:40px;display:block;margin:0 auto 10px;opacity:0.3;"></i>
                    Belum ada log aktivitas yang cocok dengan filter.
                </td>
              </tr>
              <?php endif; ?>
              
              <?php foreach ($logs as $idx => $l): ?>
              <tr class="audit-row">
                <td style="font-size:0.82rem;white-space:nowrap;"><?= date('d M Y, H:i', strtotime($l['created_at'])) ?></td>
                <td><span class="badge <?= actionBadgeClass($l['action']) ?>"><?= htmlspecialchars($l['action'] ?? 'Unknown') ?></span></td>
                <td>
                    <strong><?= htmlspecialchars($l['user_name'] ?? 'System') ?></strong>
                </td>
                <td>
                    <?= $l['tenant_name'] ? htmlspecialchars($l['tenant_name']) : '<em style="color:var(--text-muted);">Pusat</em>' ?>
                </td>
                <td>
                    <?php if ($l['table_name']): ?>
                        <code style="font-size:0.8rem;background:rgba(99,102,241,0.08);padding:2px 8px;border-radius:4px;"><?= htmlspecialchars($l['table_name']) ?></code>
                        <?php if ($l['record_id']): ?>
                            <span style="font-size:0.75rem;color:var(--text-muted);"> #<?= $l['record_id'] ?></span>
                        <?php endif; ?>
                    <?php else: ?>
                        <span style="color:var(--text-muted);">-</span>
                    <?php endif; ?>
                </td>
                <td><code style="font-size:0.8rem;"><?= htmlspecialchars($l['ip_address'] ?? '-') ?></code></td>
                <td>
                    <button type="button" class="btn btn-icon btn-sm btn-secondary" title="Lihat Detail"
                        data-audit-idx="<?= $idx ?>">
                        <i data-lucide="eye" style="width:14px;height:14px;"></i>
                    </button>
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

<!-- Modal Detail Audit -->
<div class="modal-overlay" id="auditDetailModal">
  <div class="modal card" style="width:100%;max-width:750px;background:var(--bg-card);border-radius:var(--radius-lg);padding:0;overflow:hidden;box-shadow:var(--shadow-lg);">
      <div class="modal-header border-bottom" style="display:flex;justify-content:space-between;align-items:center;padding:15px 20px;">
        <div>
            <h3 style="margin:0;font-size:1.1rem;color:var(--text-main);" id="detailTitle">Detail Log Aktivitas</h3>
            <div style="font-size:0.8rem;color:var(--text-muted);margin-top:4px;" id="detailSubtitle"></div>
        </div>
        <button type="button" onclick="document.getElementById('auditDetailModal').classList.remove('active')" style="background:none;border:none;cursor:pointer;font-size:1.4rem;color:var(--text-muted);line-height:1;">&times;</button>
      </div>
      
      <div class="modal-body" style="padding:20px;text-align:left;">
        <!-- Info ringkasan -->
        <div style="display:grid;grid-template-columns:1fr 1fr;gap:10px;margin-bottom:16px;">
            <div style="padding:10px 14px;border-radius:8px;background:var(--bg-main);border:1px solid var(--border-color);">
                <div style="font-size:0.7rem;text-transform:uppercase;color:var(--text-muted);font-weight:600;">Target Tabel</div>
                <div style="font-weight:600;margin-top:2px;" id="detailTable">-</div>
            </div>
            <div style="padding:10px 14px;border-radius:8px;background:var(--bg-main);border:1px solid var(--border-color);">
                <div style="font-size:0.7rem;text-transform:uppercase;color:var(--text-muted);font-weight:600;">IP Address</div>
                <div style="font-weight:600;margin-top:2px;font-family:monospace;" id="detailIP">-</div>
            </div>
        </div>

        <!-- Old / New values -->
        <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;">
            <div class="detail-panel" style="background:rgba(239,68,68,0.04);border:1px solid rgba(239,68,68,0.12);border-radius:8px;padding:15px;max-height:350px;overflow-y:auto;">
                <h4 style="margin:0 0 10px 0;font-size:0.85rem;color:var(--danger);display:flex;align-items:center;gap:6px;">
                    <i data-lucide="minus-circle" style="width:14px;height:14px;"></i> Nilai Sebelum (Old)
                </h4>
                <div id="detailOldValues"></div>
            </div>
            
            <div class="detail-panel" style="background:rgba(16,185,129,0.04);border:1px solid rgba(16,185,129,0.12);border-radius:8px;padding:15px;max-height:350px;overflow-y:auto;">
                <h4 style="margin:0 0 10px 0;font-size:0.85rem;color:var(--success);display:flex;align-items:center;gap:6px;">
                    <i data-lucide="plus-circle" style="width:14px;height:14px;"></i> Nilai Sesudah (New)
                </h4>
                <div id="detailNewValues"></div>
            </div>
        </div>
      </div>
      
      <div class="modal-footer border-top" style="display:flex;justify-content:flex-end;padding:15px 20px;">
        <button type="button" class="btn btn-secondary" onclick="document.getElementById('auditDetailModal').classList.remove('active')">Tutup</button>
      </div>
  </div>
</div>

<script>
// Data audit log di-embed sebagai JSON agar aman dari masalah quote/encoding
const auditData = <?= json_encode(array_map(function($l) {
    return [
        'action'     => $l['action'] ?? 'Unknown',
        'user'       => $l['user_name'] ?? 'System',
        'time'       => $l['created_at'],
        'table'      => $l['table_name'] ?? '-',
        'record_id'  => $l['record_id'] ?? '-',
        'ip'         => $l['ip_address'] ?? '-',
        'old_html'   => formatJsonOutput($l['old_values']),
        'new_html'   => formatJsonOutput($l['new_values']),
    ];
}, $logs), JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT) ?>;

document.querySelectorAll('[data-audit-idx]').forEach(btn => {
    btn.addEventListener('click', function() {
        const idx = parseInt(this.getAttribute('data-audit-idx'));
        const d = auditData[idx];
        if (!d) return;
        
        document.getElementById('detailTitle').textContent = 'Aksi: ' + d.action;
        document.getElementById('detailSubtitle').textContent = 'Oleh: ' + d.user + '  •  ' + d.time;
        document.getElementById('detailTable').textContent = d.table + (d.record_id !== '-' ? ' #' + d.record_id : '');
        document.getElementById('detailIP').textContent = d.ip;
        document.getElementById('detailOldValues').innerHTML = d.old_html;
        document.getElementById('detailNewValues').innerHTML = d.new_html;
        
        document.getElementById('auditDetailModal').classList.add('active');
        
        // Re-init lucide icons inside modal
        if (typeof lucide !== 'undefined') lucide.createIcons();
    });
});
</script>

<?php include 'includes/footer.php'; ?>
