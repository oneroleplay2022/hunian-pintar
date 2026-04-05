<?php
require_once 'classes/Database.php';
$db = Database::getInstance();
$cols = $db->fetchAll("DESCRIBE users");
file_put_contents('users_structure.txt', print_r($cols, true));
echo "DONE";
