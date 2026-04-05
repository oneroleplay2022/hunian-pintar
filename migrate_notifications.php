<?php
require_once 'classes/Database.php';
$db = Database::getInstance();
$pdo = $db->getConnection();

$sql = "CREATE TABLE IF NOT EXISTS notifications (
    id INT AUTO_INCREMENT PRIMARY KEY,
    tenant_id INT NOT NULL,
    user_id INT NOT NULL,
    title VARCHAR(100) NOT NULL,
    message TEXT,
    link VARCHAR(255),
    is_read TINYINT(1) DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (tenant_id) REFERENCES tenants(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB";

try {
    $pdo->exec($sql);
    echo "SUCCESS: Table notifications created/exists.\n";
} catch (Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
}
