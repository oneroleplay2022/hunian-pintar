<?php
require_once 'classes/Database.php';
$db = Database::getInstance();
try {
    $db->query("ALTER TABLE houses ADD COLUMN monthly_fee DECIMAL(15,2) DEFAULT 0.00");
    echo "Kolom monthly_fee berhasil ditambah.\n";
} catch(Exception $e) { echo "monthly_fee: " . $e->getMessage() . "\n"; }

try {
    $db->query("ALTER TABLE houses ADD COLUMN description TEXT");
    echo "Kolom description berhasil ditambah.\n";
} catch(Exception $e) { echo "description: " . $e->getMessage() . "\n"; }
