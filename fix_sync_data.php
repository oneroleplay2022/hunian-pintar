<?php
require_once 'classes/Database.php';
$db = Database::getInstance();

// 1. Audit Tenant Aktif
echo "Daftar Tenant:\n";
$tenants = $db->fetchAll("SELECT id, name FROM tenants");
foreach($tenants as $t) echo "- ID: {$t['id']}, Nama: {$t['name']}\n";

// 2. Audit Blok & Rumah Tanpa Tenant
echo "\nAudit Data Blok & Rumah:\n";
$blocksCount = $db->count('blocks', 'tenant_id IS NULL OR tenant_id = 0');
$housesCount = $db->count('houses', 'tenant_id IS NULL OR tenant_id = 0');
$residentsCount = $db->count('residents', 'tenant_id IS NULL OR tenant_id = 0');

echo "- Blok tanpa tenant: $blocksCount\n";
echo "- Rumah tanpa tenant: $housesCount\n";
echo "- Warga tanpa tenant: $residentsCount\n";

// 3. Sinkronisasi (Opsional: Jika ada data yg 'tercecer', hubungkan ke tenant pertama atau yg aktif)
if ($tenants && ($blocksCount > 0 || $housesCount > 0 || $residentsCount > 0)) {
    $firstTenantId = $tenants[0]['id'];
    echo "\nSINKRONISASI massal ke Tenant ID: $firstTenantId...\n";
    
    $db->update('blocks', ['tenant_id' => $firstTenantId], 'tenant_id IS NULL OR tenant_id = 0');
    $db->update('houses', ['tenant_id' => $firstTenantId], 'tenant_id IS NULL OR tenant_id = 0');
    $db->update('residents', ['tenant_id' => $firstTenantId], 'tenant_id IS NULL OR tenant_id = 0');
    
    echo "Selesa!\n";
} else {
    echo "\nTidak ada data yang perlu disinkronkan.\n";
}
