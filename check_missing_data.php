<?php
require_once 'classes/Database.php';
$db = Database::getInstance();
$tenant_id = 9; // Sesuaikan dengan ID tenant anda di pengujian tadi

echo "AUDIT UNTUK TENANT ID: $tenant_id\n\n";

// 1. Cek isi tabel residents
$residents = $db->fetchAll("SELECT id, full_name, domicile_status, family_status, deleted_at FROM residents WHERE tenant_id = ? LIMIT 5", [$tenant_id]);
echo "Isi tabel 'residents':\n";
foreach($residents as $r) {
    echo "- ID: {$r['id']}, Name: {$r['full_name']}, Domisili: '{$r['domicile_status']}', Status: '{$r['family_status']}', Deleted: '{$r['deleted_at']}'\n";
}

// 2. Cek apakah ada unit rumah
$houses = $db->count('houses', 'tenant_id = ?', [$tenant_id]);
echo "\nTotal unit rumah untuk tenant ini: $houses\n";

// 3. Cek blok
$blocks = $db->fetchAll("SELECT * FROM blocks WHERE tenant_id = ?", [$tenant_id]);
echo "Daftar Blok:\n";
foreach($blocks as $b) { echo "- {$b['block_name']} (ID: {$b['id']})\n"; }
