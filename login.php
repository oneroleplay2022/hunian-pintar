<?php
/**
 * WargaKu - Login Page
 */
require_once __DIR__ . '/classes/Auth.php';
Auth::init();

// Load Settings
$appSettings = ['app_name' => 'WargaKu'];
$settingsFile = __DIR__ . '/config/app_settings.json';
if (file_exists($settingsFile)) {
    $loaded = json_decode(file_get_contents($settingsFile), true);
    if ($loaded) $appSettings = array_merge($appSettings, $loaded);
}

// Already logged in? Redirect to dashboard
if (Auth::check()) {
    if (Auth::isSuperadmin()) {
        header('Location: saas_dashboard.php');
    } else {
        header('Location: index.php');
    }
    exit;
}

// Handle login POST
$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    
    if (Auth::login($email, $password)) {
        if (Auth::isSuperadmin()) {
            header('Location: saas_dashboard.php');
        } else {
            header('Location: index.php');
        }
        exit;
    } else {
        $error = 'Email atau kata sandi salah.';
    }
}

// Handle errors from Google Auth callback
$gError = $_GET['error'] ?? '';
if ($gError) {
    switch ($gError) {
        case 'auth_failed': $error = 'Gagal melakukan otentikasi dengan Google.'; break;
        case 'no_token': $error = 'Token Google tidak ditemukan.'; break;
        case 'invalid_token': $error = 'Token Google tidak valid.'; break;
        case 'mismatched_audience': $error = 'Kesalahan konfigurasi Client ID.'; break;
    }
}

$pageTitle = 'Login';
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <script>
    (function() {
      const theme = localStorage.getItem('theme') || 'light';
      document.documentElement.setAttribute('data-theme', theme);
    })();
  </script>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="description" content="Login - Aplikasi Transparansi & Manajemen Warga">
  <title>Login - <?= htmlspecialchars($appSettings['app_name']) ?></title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="css/style.css">
  <script src="https://accounts.google.com/gsi/client" async defer></script>
</head>
<body>
  <div class="login-page">
    <!-- Animated Background -->
    <div class="login-bg">
      <div class="orb orb-1"></div>
      <div class="orb orb-2"></div>
      <div class="orb orb-3"></div>
    </div>

    <!-- Login Card -->
    <div class="login-card">
      <div class="login-logo">
        <div class="logo-icon">🏘️</div>
        <h2 class="text-gradient"><?= htmlspecialchars($appSettings['app_name']) ?></h2>
        <p>Transparansi & Manajemen Warga</p>
      </div>

      <?php if ($error): ?>
      <div style="background:rgba(239,68,68,0.1);border:1px solid rgba(239,68,68,0.3);color:#f87171;padding:12px 16px;border-radius:var(--radius-md);margin-bottom:20px;font-size:0.88rem;text-align:center;">
        ⚠️ <?= htmlspecialchars($error) ?>
      </div>
      <?php endif; ?>

      <!-- Google Sign In Button -->
      <div id="g_id_onload"
           data-client_id="<?= htmlspecialchars($appSettings['google_client_id'] ?? '') ?>"
           data-context="signin"
           data-ux_mode="popup"
           data-login_uri="<?= (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]" . dirname($_SERVER['PHP_SELF']) ?>/google_callback.php"
           data-auto_prompt="false">
      </div>

      <div class="g_id_signin"
           data-type="standard"
           data-shape="rectangular"
           data-theme="outline"
           data-text="signin_with"
           data-size="large"
           data-logo_alignment="left"
           style="margin-bottom: 20px; display: flex; justify-content: center;">
      </div>

      <div class="login-divider">atau masuk dengan email</div>

      <form class="login-form" action="login.php" method="POST">
        <div class="form-group">
          <label class="form-label" for="email">Email</label>
          <input type="email" class="form-control" id="email" name="email" placeholder="masukkan@email.com" value="<?= htmlspecialchars($_POST['email'] ?? '') ?>" required>
        </div>

        <div class="form-group">
          <label class="form-label" for="password">Kata Sandi</label>
          <input type="password" class="form-control" id="password" name="password" placeholder="••••••••" required>
        </div>

        <div class="login-extras">
          <label class="checkbox-label">
            <input type="checkbox" name="remember"> Ingat saya
          </label>
          <a href="#" style="color: var(--primary-light); font-size: 0.85rem;">Lupa kata sandi?</a>
        </div>

        <button type="submit" class="btn btn-primary login-btn">
          Masuk
        </button>
      </form>

      <div class="login-divider">atau</div>

      <div style="background:rgba(99,102,241,0.06);border:1px solid var(--border-color);border-radius:var(--radius-md);padding:12px;font-size:0.82rem;color:var(--text-secondary);text-align:center;">
        <strong>Demo Account:</strong><br>
        Developer (SaaS): <code>superadmin@wargaku.id</code> / <code>password</code><br>
        Admin Perumahan: <code>admin@wargaku.id</code> / <code>password</code><br>
        Warga: <code>siti@wargaku.id</code> / <code>warga123</code>
      </div>

      <div class="login-footer">
        Belum punya akun? <a href="register.php">Daftar Sekarang</a>
      </div>
    </div>
  </div>

  <script src="js/app.js"></script>
</body>
</html>
