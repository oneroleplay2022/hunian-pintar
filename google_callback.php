<?php
/**
 * WargaKu - Google Auth Callback Handler
 */
require_once __DIR__ . '/classes/Auth.php';
require_once __DIR__ . '/classes/Database.php';

Auth::init();

// 1. Get ID Token from POST
$idToken = $_POST['credential'] ?? '';

if (empty($idToken)) {
    header('Location: login.php?error=no_token');
    exit;
}

// 2. Verify Token with Google API
// In a production environment with Composer, you'd use Google_Client.
// Here we use the tokeninfo endpoint as a lightweight alternative.
$url = "https://oauth2.googleapis.com/tokeninfo?id_token=" . $idToken;
$response = file_get_contents($url);
$data = json_decode($response, true);

if (!$data || isset($data['error'])) {
    header('Location: login.php?error=invalid_token');
    exit;
}

// 3. Check Audience (Security Best Practice)
$appSettings = ['google_client_id' => ''];
$settingsFile = __DIR__ . '/config/app_settings.json';
if (file_exists($settingsFile)) {
    $loaded = json_decode(file_get_contents($settingsFile), true);
    if ($loaded) $appSettings = array_merge($appSettings, $loaded);
}

if ($data['aud'] !== $appSettings['google_client_id']) {
    header('Location: login.php?error=mismatched_audience');
    exit;
}

// 4. Authenticate User
if (Auth::loginWithGoogle($data)) {
    // Redirect based on role
    if (Auth::isSuperadmin()) {
        header('Location: saas_dashboard.php');
    } else {
        header('Location: index.php');
    }
} else {
    header('Location: login.php?error=auth_failed');
}
exit;
