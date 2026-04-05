<?php
$db = new PDO('mysql:host=localhost;dbname=wargaku', 'root', '');
$res = $db->query('DESCRIBE residents');
while($row = $res->fetch(PDO::FETCH_ASSOC)) {
    echo $row['Field'] . " ";
}
