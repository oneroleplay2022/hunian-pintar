<?php
require_once 'classes/Database.php';
$db = Database::getInstance();
$cols = $db->fetchAll("DESCRIBE users");
foreach ($cols as $col) {
    echo $col['Field'] . " - " . $col['Type'] . "\n";
}
