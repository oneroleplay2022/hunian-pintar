<?php
require_once 'classes/Database.php';
$db = Database::getInstance();
$table = 'houses';
$res = $db->fetchAll("DESCRIBE $table");
foreach($res as $r) {
    file_put_contents('php://stderr', $r['Field'] . "\n");
}
