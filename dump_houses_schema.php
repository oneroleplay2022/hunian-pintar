<?php
require_once 'classes/Database.php';
$db = Database::getInstance();
$table = 'houses';
$res = $db->fetchAll("DESCRIBE $table");
foreach($res as $r) {
    echo $r['Field'] . "\n";
}
