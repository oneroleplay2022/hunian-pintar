<?php
require_once 'classes/Database.php';
$db = Database::getInstance();
$cols = $db->fetchAll("DESCRIBE notifications");
print_r($cols);
