<?php
/**
 * WargaKu - Database Configuration
 */

define('DB_HOST', '127.0.0.1');
define('DB_PORT', '3307');
define('DB_NAME', 'wargaku');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_CHARSET', 'utf8mb4');

// App config
define('APP_NAME', 'WargaKu');
define('APP_VERSION', '1.0.0');
define('APP_URL', 'http://localhost/warga');
define('UPLOAD_DIR', __DIR__ . '/../uploads/');

// Session config
define('SESSION_LIFETIME', 86400); // 24 hours
