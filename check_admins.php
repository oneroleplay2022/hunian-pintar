<?php
require_once 'classes/Database.php';
$db = Database::getInstance();
$admins = $db->fetchAll("SELECT id, name, email, role, is_active FROM users WHERE role = 'superadmin'");
foreach ($admins as $a) {
    echo "ID: {$a['id']} | Name: {$a['name']} | Email: {$a['email']} | Role: {$a['role']} | Active: {$a['is_active']}\n";
}
echo "\nTotal Superadmins: " . count($admins) . "\n";
