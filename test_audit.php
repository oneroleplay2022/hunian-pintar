<?php
require_once 'classes/Database.php';
$db = Database::getInstance();

// 1. Update existing tenant address
$id = 1;
$oldVal = $db->fetch("SELECT address FROM tenants WHERE id = ?", [$id]);
$newAddr = "Test Update " . time();
$db->update('tenants', ['address' => $newAddr], 'id = ?', [$id]);

// 2. Check audit_logs
$log = $db->fetch("SELECT * FROM audit_logs ORDER BY id DESC LIMIT 1");
echo "Table: " . $log['table_name'] . "\n";
echo "Action: " . $log['action'] . "\n";
echo "Old Values: " . $log['old_values'] . "\n";
echo "New Values: " . $log['new_values'] . "\n";

if ($log['old_values'] && $log['new_values']) {
    echo "\n>>> SUCCESS: Audit Trail recorded OLD vs NEW values! <<<\n";
} else {
    echo "\n>>> FAILED: Old or New values missing. <<<\n";
}
