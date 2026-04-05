<?php
require_once 'classes/Auth.php';
require_once 'classes/Database.php';

Auth::requireLogin();
$user = Auth::user();
$db = Database::getInstance();

$db->update('notifications', ['is_read' => 1], 'user_id = ?', [$user['id']]);

header("Location: " . ($_SERVER['HTTP_REFERER'] ?? 'index.php'));
exit;
