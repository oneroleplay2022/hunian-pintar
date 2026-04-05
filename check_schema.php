<?php
require_once 'classes/Database.php';
$db = Database::getInstance();
$table = 'houses';
$res = $db->fetchAll("DESCRIBE $table");
echo "STRUKTUR TABEL $table:\n";
foreach($res as $r) {
    echo "- Field: {$r['Field']}, Type: {$r['Type']}\n";
}
