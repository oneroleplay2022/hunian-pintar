<?php
require_once 'classes/Database.php';
$db = Database::getInstance();
$table = 'residents';
try {
    $res = $db->fetchAll("DESCRIBE $table");
    foreach ($res as $row) {
        echo $row['Field'] . "\n";
    }
} catch (Exception $e) { echo "Error: " . $e->getMessage(); }
