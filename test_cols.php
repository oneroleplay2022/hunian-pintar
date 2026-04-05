<?php
require 'classes/Database.php';
$cols = Database::getInstance()->fetchAll('SHOW COLUMNS FROM audit_logs');
file_put_contents('cols.txt', implode(", ", array_column($cols, 'Field')));
