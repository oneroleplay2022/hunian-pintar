<?php
require_once 'classes/Database.php';
require_once 'classes/PlanHelper.php';

$db = Database::getInstance();

echo "1. Re-assessing all tenant plans based on house counts...\n";
$reassign = reassignAllTenantPlans();
echo "   - Total Tenants: {$reassign['total']}\n";
echo "   - Plans Changed: {$reassign['changed']}\n";

echo "\n2. Syncing all PENDING invoices with current plan prices...\n";
$pending = $db->fetchAll("SELECT id, tenant_id, amount FROM subscriptions WHERE payment_status = 'pending'");
foreach ($pending as $s) {
    $correctPrice = getTenantPlanPrice($s['tenant_id']);
    if ((float)$s['amount'] !== (float)$correctPrice) {
        $db->update('subscriptions', ['amount' => $correctPrice], 'id = ?', [$s['id']]);
        echo "   - Updated Inv #{$s['id']} (Tenant ID: {$s['tenant_id']}) from Rp " . number_format($s['amount']) . " to Rp " . number_format($correctPrice) . "\n";
    }
}

echo "\nDone!\n";
