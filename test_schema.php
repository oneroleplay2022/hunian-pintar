<?php
$db = new PDO('mysql:host=localhost;dbname=warga', 'root', '');
$res = $db->query('SHOW CREATE TABLE audit_logs')->fetch(PDO::FETCH_ASSOC);
file_put_contents('schema_audit.txt', $res['Create Table']);
