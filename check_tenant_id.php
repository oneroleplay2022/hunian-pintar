<?php
require_once 'classes/Database.php';
$db = Database::getInstance();
$col = $db->fetch("SHOW COLUMNS FROM users LIKE 'tenant_id'");
print_r($col);
