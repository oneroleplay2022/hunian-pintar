<?php
require_once 'classes/Auth.php';
require_once 'classes/Database.php';
require_once 'classes/Helpers.php';

Auth::requireRole('superadmin');
$db = Database::getInstance();

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$invoice = $db->fetch("
    SELECT s.*, t.name as tenant_name, t.address as tenant_address, p.name as plan_name
    FROM subscriptions s
    JOIN tenants t ON s.tenant_id = t.id
    LEFT JOIN subscription_plans p ON t.plan_id = p.id
    WHERE s.id = ?
", [$id]);

if (!$invoice) {
    die("Invoice tidak ditemukan.");
}

// App Settings for Logo/Name
$appSettings = ['app_name' => 'WargaKu', 'company_address' => 'Jl. Teknologi Digital No. 123, Jakarta'];
$settingsFile = 'config/app_settings.json';
if (file_exists($settingsFile)) {
    $loaded = json_decode(file_get_contents($settingsFile), true);
    if ($loaded) $appSettings = array_merge($appSettings, $loaded);
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Invoice #INV-<?= str_pad($invoice['id'], 4, '0', STR_PAD_LEFT) ?></title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary: #2563eb;
            --secondary: #64748b;
            --text-main: #1e293b;
            --text-muted: #64748b;
            --border: #e2e8f0;
            --bg-light: #f8fafc;
            --success: #10b981;
            --warning: #f59e0b;
        }

        * { box-sizing: border-box; }
        body { font-family: 'Inter', sans-serif; background: #eaeff5; margin: 0; padding: 40px; color: var(--text-main); -webkit-print-color-adjust: exact; }

        /* UI Toolbar */
        .toolbar { 
            max-width: 850px; margin: 0 auto 30px auto; display: flex; justify-content: space-between; align-items: center; 
            background: white; padding: 15px 25px; border-radius: 12px; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
        }
        .btn-back { color: var(--text-muted); text-decoration: none; font-size: 0.9rem; display: flex; align-items: center; gap: 8px; font-weight: 500; }
        .btn-back:hover { color: var(--primary); }
        .btn-print { background: var(--primary); color: white; border: none; padding: 10px 24px; border-radius: 8px; cursor: pointer; font-weight: 600; font-size: 0.9rem; transition: all 0.2s; box-shadow: 0 2px 10px rgba(37, 99, 235, 0.2); }
        .btn-print:hover { transform: translateY(-1px); box-shadow: 0 4px 12px rgba(37, 99, 235, 0.3); }

        /* Invoice Container */
        .invoice-card { 
            max-width: 850px; margin: 0 auto; background: white; padding: 60px; border-radius: 1px; box-shadow: 0 10px 25px rgba(0,0,0,0.05); position: relative; overflow: hidden;
        }

        /* Branding & Header */
        .header { display: flex; justify-content: space-between; margin-bottom: 60px; }
        .logo-area h1 { margin: 0; font-size: 2rem; font-weight: 800; letter-spacing: -1px; color: var(--primary); }
        .logo-area p { margin: 5px 0 0 0; font-size: 0.9rem; color: var(--text-muted); max-width: 300px; line-height: 1.5; }
        
        .meta-area { text-align: right; }
        .meta-area h2 { margin: 0; font-size: 2.2rem; font-weight: 300; text-transform: uppercase; color: #cbd5e1; margin-bottom: 15px; }
        .inv-number { font-size: 1.1rem; font-weight: 700; color: var(--text-main); margin-bottom: 5px; }
        .inv-date { font-size: 0.85rem; color: var(--text-muted); display: block; }

        /* Billing Details */
        .details-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 40px; margin-bottom: 50px; padding: 30px; background: var(--bg-light); border-radius: 12px; }
        .label { font-size: 0.75rem; font-weight: 700; text-transform: uppercase; color: var(--text-muted); letter-spacing: 1px; display: block; margin-bottom: 10px; }
        .value-bold { font-size: 1.1rem; font-weight: 700; color: var(--text-main); display: block; }
        .value-text { font-size: 0.95rem; color: var(--text-main); line-height: 1.6; margin-top: 5px; display: block; }

        /* Status Stamp */
        .stamp { 
            position: absolute; top: 120px; right: 80px; width: 180px; height: 180px; border-radius: 50%; display: flex; align-items: center; justify-content: center; 
            border: 4px dashed; opacity: 0.15; transform: rotate(15deg); pointer-events: none;
        }
        .stamp-txt { font-weight: 800; font-size: 1.8rem; text-transform: uppercase; text-align: center; line-height: 1; }
        .status-paid { color: var(--success); border-color: var(--success); }
        .status-pending { color: var(--warning); border-color: var(--warning); }

        /* Table Styling */
        .table { width: 100%; border-collapse: collapse; margin-bottom: 40px; }
        .table th { padding: 15px 20px; text-align: left; background: var(--text-main); color: white; font-size: 0.8rem; text-transform: uppercase; font-weight: 600; letter-spacing: 1px; }
        .table td { padding: 25px 20px; border-bottom: 1px solid var(--border); vertical-align: top; }
        .table td b { font-size: 1.05rem; display: block; margin-bottom: 5px; }
        .table td small { color: var(--text-muted); }
        .price-col { text-align: right; width: 200px; font-weight: 700; font-size: 1.1rem; }

        /* Summary */
        .summary-area { display: flex; justify-content: flex-end; }
        .summary-box { width: 350px; }
        .summary-row { display: flex; justify-content: space-between; padding: 12px 20px; border-bottom: 1px solid var(--border); font-size: 0.95rem; }
        .summary-row.grand-total { background: var(--primary); color: white; padding: 20px; border-radius: 8px; margin-top: 10px; font-size: 1.3rem; font-weight: 800; }

        /* Footer */
        .footer { margin-top: 80px; padding-top: 30px; border-top: 1px solid var(--border); }
        .footer-grid { display: grid; grid-template-columns: 1.5fr 1fr; gap: 40px; }
        .terms h4 { margin: 0 0 10px 0; font-size: 0.9rem; }
        .terms p { font-size: 0.8rem; color: var(--text-muted); line-height: 1.6; margin: 0; }
        .sign-area { text-align: center; }
        .sign-area p { margin-top: 80px; font-weight: 700; font-size: 0.95rem; }
        .sign-copy { font-size: 0.75rem; color: var(--text-muted); margin-top: 5px; display: block; border-top: 1px solid #eee; padding-top: 10px; }

        @media print {
            body { background: white; padding: 0; }
            .toolbar { display: none !important; }
            .invoice-card { box-shadow: none; border: none; padding: 0; max-width: 100%; }
            .details-grid { background: #f1f5f9 !important; border: 1px solid #e2e8f0; }
        }
    </style>
</head>
<body>

    <div class="toolbar">
        <a href="saas_billing.php" class="btn-back">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m15 18-6-6 6-6"/></svg>
            Kembali ke Billing
        </a>
        <button onclick="window.print()" class="btn-print">Cetak Dokumen</button>
    </div>

    <div class="invoice-card">
        <!-- Status Stamp -->
        <div class="stamp status-<?= $invoice['payment_status'] === 'paid' ? 'paid' : 'pending' ?>">
            <div class="stamp-txt">
                <?= $invoice['payment_status'] === 'paid' ? 'PAID<br>LUNAS' : 'DUE<br>PENDING' ?>
            </div>
        </div>

        <div class="header">
            <div class="logo-area">
                <h1><?= htmlspecialchars($appSettings['app_name']) ?></h1>
                <p><?= htmlspecialchars($appSettings['company_address']) ?></p>
            </div>
            <div class="meta-area">
                <h2>INVOICE</h2>
                <div class="inv-number">#INV-<?= str_pad($invoice['id'], 4, '0', STR_PAD_LEFT) ?></div>
                <span class="inv-date">Tgl Terbit: <b><?= date('d M Y', strtotime($invoice['created_at'])) ?></b></span>
                <span class="inv-date">Jatuh Tempo: <b><?= date('d M Y', strtotime($invoice['expired_at'])) ?></b></span>
            </div>
        </div>

        <div class="details-grid">
            <div class="billing-to">
                <span class="label">Invoice Untuk:</span>
                <span class="value-bold"><?= htmlspecialchars($invoice['tenant_name']) ?></span>
                <span class="value-text"><?= nl2br(htmlspecialchars($invoice['tenant_address'] ?? 'Alamat klien belum terdaftar di sistem.')) ?></span>
            </div>
            <div class="payment-info">
                <span class="label">Keterangan Bayar:</span>
                <span class="value-bold"><?= $invoice['payment_status'] === 'paid' ? 'Telah Dilunasi' : 'Menunggu Pembayaran' ?></span>
                <span class="value-text" style="font-size: 0.85rem;">
                    <?php if($invoice['payment_status'] === 'paid'): ?>
                        Metode: <?= $invoice['payment_method'] ?> / Bank Transfer<br>
                        Sesuai mutasi yang terekam pada sistem.
                    <?php else: ?>
                        Silakan selesaikan pembayaran sebelum tanggal jatuh tempo di atas untuk menghindari penangguhan layanan otomatis.
                    <?php endif; ?>
                </span>
            </div>
        </div>

        <table class="table">
            <thead>
                <tr>
                    <th>Deskripsi Layanan & Deskripsi Sistem</th>
                    <th style="text-align: right;">Total Biaya (IDR)</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>
                        <b>Paket Langganan Layanan <?= htmlspecialchars($appSettings['app_name']) ?>: <?= htmlspecialchars($invoice['plan_name'] ?? 'Premium Core') ?></b>
                        <small>Akses infrastruktur cloud & manajemen lingkungan untuk 1 (satu) bulan kedepan.<br>Status: Otomatis diproses setiap akhir periode aktif.</small>
                    </td>
                    <td class="price-col">Rp <?= number_format($invoice['amount'], 0, ',', '.') ?></td>
                </tr>
            </tbody>
        </table>

        <div class="summary-area">
            <div class="summary-box">
                <div class="summary-row">
                    <span style="color:var(--text-muted);">Biaya Konsultasi & Setup</span>
                    <span>Rp 0</span>
                </div>
                <div class="summary-row">
                    <span style="color:var(--text-muted);">Pajak (PPN 0%)</span>
                    <span>Rp 0</span>
                </div>
                <div class="summary-row grand-total">
                    <span>Grand Total</span>
                    <span>Rp <?= number_format($invoice['amount'], 0, ',', '.') ?></span>
                </div>
            </div>
        </div>

        <div class="footer">
            <div class="footer-grid">
                <div class="terms">
                    <h4>Catatan Pembayaran:</h4>
                    <p>Pembayaran dilakukan maksimal 3 hari setelah tanggal jatuh tempo. Jika melebihi batas tersebut, sistem secara otomatis akan menangguhkan akses dasbor admin bagi pengurus perumahan. Mohon lakukan konfirmasi upload bukti transfer di halaman Pembayaran pada dasbor masing-masing.</p>
                </div>
                <div class="sign-area">
                    <p>Finance <?= htmlspecialchars($appSettings['app_name']) ?></p>
                    <span class="sign-copy">Generated officially via <?= htmlspecialchars($appSettings['app_name']) ?> Central</span>
                </div>
            </div>
        </div>
    </div>

</body>
</html>
