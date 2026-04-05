<?php
require_once 'classes/Database.php';
$db = Database::getInstance();

echo "1. Checking if roles table exists...\n";
try {
    $db->query("CREATE TABLE IF NOT EXISTS saas_roles (
        id INT AUTO_INCREMENT PRIMARY KEY,
        role_name VARCHAR(50) NOT NULL,
        role_key VARCHAR(50) NOT NULL UNIQUE,
        description TEXT,
        permissions TEXT, -- JSON array of file names or keys
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )");
    
    // Seed default if empty
    $count = (int)$db->fetchColumn("SELECT COUNT(*) FROM saas_roles");
    if ($count === 0) {
        $db->insert('saas_roles', [
            'role_name' => 'Superadmin (Full Access)',
            'role_key' => 'superadmin',
            'description' => 'Akses penuh ke seluruh sistem tanpa batasan.',
            'permissions' => json_encode(['all'])
        ]);
        echo "✓ Default 'superadmin' role seeded.\n";
    }
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
}

echo "\nDone!\n";
