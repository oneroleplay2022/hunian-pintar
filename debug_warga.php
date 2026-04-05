<?php
require_once 'classes/Auth.php';
require_once 'classes/Database.php';

Auth::requireLogin();
$tenant_id = Auth::tenantId();
$db = Database::getInstance();

echo "AUDIT SEDANG BERJALAN UNTUK LOGIN USER\n";
echo "Tenant ID Anda: " . $tenant_id . "\n\n";

// 1. Cek isi tabel residents
$residents = $db->fetchAll("SELECT id, full_name, tenant_id, deleted_at FROM residents WHERE tenant_id = ?", [$tenant_id]);
echo "Jumlah warga untuk Tenant ID $tenant_id: " . count($residents) . "\n";
foreach($residents as $r) {
    echo "- ID: {$r['id']}, Name: {$r['full_name']}, Deleted: " . ($r['deleted_at'] ?? 'Aktif') . "\n";
}

// 2. Cek apakah ada data tanpa tenant_id sama sekali
$orphans = $db->count('residents', 'tenant_id IS NULL OR tenant_id = 0');
echo "\nWarga tanpa Tenant ID (Orphans): $orphans\n";

// 3. Cek seluruh tenant yang ada
$tenants = $db->fetchAll("SELECT id, name FROM tenants");
echo "\nDaftar Tenant Seluruhnya:\n";
foreach($tenants as $t) {
    $c = $db->count('residents', 'tenant_id = ?', [$t['id']]);
    echo "- ID: {$t['id']}, Nama: {$t['name']} (Jumlah Warga di DB: $c)\n";
}
