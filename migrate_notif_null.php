<?php
require_once 'classes/Database.php';
$db = Database::getInstance();
$pdo = $db->getConnection();

try {
    $pdo->exec("ALTER TABLE notifications MODIFY tenant_id INT NULL");
    echo "SUCCESS: notifications.tenant_id is now NULLABLE.\n";
} catch (Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
}
