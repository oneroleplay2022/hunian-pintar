<?php
require_once 'classes/Database.php';
$db = Database::getInstance();
echo "--- PLANS ---\n";
$plans = $db->fetchAll('SELECT * FROM subscription_plans');
foreach ($plans as $p) {
    echo $p['id'] . ": " . $p['name'] . " - Rp " . number_format($p['price'], 0) . " (Max: " . $p['max_houses'] . ")\n";
}
echo "\n--- RECENT SUBSCRIPTIONS ---\n";
$subs = $db->fetchAll('SELECT s.*, t.name as tenant_name FROM subscriptions s JOIN tenants t ON s.tenant_id = t.id ORDER BY s.created_at DESC LIMIT 10');
foreach ($subs as $s) {
    echo "ID: " . $s['id'] . " | Tenant: " . $s['tenant_name'] . " | Amount: Rp " . number_format($s['amount'], 0) . " | Status: " . $s['payment_status'] . " | Created: " . $s['created_at'] . "\n";
}
