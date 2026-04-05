<?php
require_once 'classes/Database.php';
require_once 'classes/PlanHelper.php';

$db = Database::getInstance();

echo "--- SYNCING TENANTS & INVOICES ---\n";

// 1. Force reassignment for everyone
$tenants = $db->fetchAll("SELECT id, name FROM tenants");
foreach ($tenants as $t) {
    autoAssignPlan($t['id']);
}
echo "✓ All tenants re-assigned to current plans based on house counts.\n";

// 2. Update Pending Invoices
$pending = $db->fetchAll("SELECT s.id, s.tenant_id, s.amount, t.name as tenant_name FROM subscriptions s JOIN tenants t ON s.tenant_id = t.id WHERE s.payment_status = 'pending'");
foreach ($pending as $s) {
    $correctPrice = getTenantPlanPrice($s['tenant_id']);
    if ((float)$s['amount'] !== (float)$correctPrice) {
        $db->update('subscriptions', ['amount' => $correctPrice], 'id = ?', [$s['id']]);
        echo "✓ Updated Inv #{$s['id']} ({$s['tenant_name']}) from " . $s['amount'] . " to " . $correctPrice . "\n";
    }
}

echo "\n--- FINAL DATA VERIFICATION ---\n";
$report = $db->fetchAll("SELECT t.name, p.name as plan, p.price as plan_price,
                        (SELECT amount FROM subscriptions WHERE tenant_id = t.id AND payment_status = 'pending' ORDER BY created_at DESC LIMIT 1) as active_invoice
                        FROM tenants t 
                        LEFT JOIN subscription_plans p ON t.plan_id = p.id");
foreach ($report as $r) {
    echo "Tenant: {$r['name']} | Plan: {$r['plan']} (Rp {$r['plan_price']}) | Active Inv: Rp " . ($r['active_invoice'] ?? 'None') . "\n";
}
