<?php
require_once 'classes/Database.php';
$db = Database::getInstance();
$cols = $db->fetchAll("DESCRIBE users");
print_r($cols);
