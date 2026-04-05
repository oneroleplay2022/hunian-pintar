<?php
require_once 'classes/Database.php';
$db = Database::getInstance();

echo "1. Creating saas_communications table...\n";
try {
    $db->query("CREATE TABLE IF NOT EXISTS saas_communications (
        id INT AUTO_INCREMENT PRIMARY KEY,
        sender_id INT NOT NULL,
        channels TEXT NOT NULL, -- JSON array: ['system', 'wa', 'email']
        target_type VARCHAR(50) NOT NULL, -- all, active, tenant_id
        target_name VARCHAR(255) DEFAULT NULL, -- 'Semua Admin', 'Tenant: Antigravity', etc.
        subject VARCHAR(255) NOT NULL,
        message TEXT NOT NULL,
        recipient_count INT DEFAULT 0,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )");
    echo "✓ Table saas_communications created.\n";
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
}

echo "\nDone!\n";
