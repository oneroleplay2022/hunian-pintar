<?php
require_once 'classes/Database.php';
$db = Database::getInstance();
try {
    $db->query("ALTER TABLE houses ADD COLUMN owner_address TEXT AFTER owner_phone");
    echo "Kolom owner_address berhasil ditambah.\n";
} catch(Exception $e) { echo "Error: " . $e->getMessage() . "\n"; }
