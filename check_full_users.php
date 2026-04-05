<?php
require_once 'classes/Database.php';
$db = Database::getInstance();
$cols = $db->fetchAll("DESCRIBE users");
foreach ($cols as $c) {
    echo "Field: " . $c['Field'] . " | Null: " . $c['Null'] . " | Key: " . $c['Key'] . " | Default: " . $c['Default'] . "\n";
}
