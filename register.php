<?php
/**
 * WargaKu - Register Page
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
    header('Location: index.php');
    exit;
}

// Handle register POST
$error = '';
$success = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['full_name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $password = $_POST['password'] ?? '';
    $passwordConfirm = $_POST['password_confirm'] ?? '';
    
    // Validation
    if (strlen($password) < 6) {
        $error = 'Kata sandi minimal 6 karakter.';
    } elseif ($password !== $passwordConfirm) {
        $error = 'Konfirmasi kata sandi tidak cocok.';
    } else {
        $result = Auth::register([
            'name'     => $name,
            'email'    => $email,
            'phone'    => $phone,
            'password' => $password,
            'role'     => 'warga',
        ]);
        
        if ($result['success']) {
            // Auto login after register
            Auth::login($email, $password);
            header('Location: index.php');
            exit;
        } else {
            $error = $result['message'];
        }
    }
}

$pageTitle = 'Registrasi';
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
  <title>Registrasi - <?= htmlspecialchars($appSettings['app_name']) ?></title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="css/style.css">
  <script src="https://accounts.google.com/gsi/client" async defer></script>
</head>
<body>
  <div class="login-page">
    <div class="login-bg">
      <div class="orb orb-1"></div>
      <div class="orb orb-2"></div>
      <div class="orb orb-3"></div>
    </div>

    <div class="login-card" style="max-width: 520px;">
      <div class="login-logo">
        <div class="logo-icon">🏘️</div>
        <h2 class="text-gradient">Daftar Akun</h2>
        <p>Bergabung dengan komunitas <?= htmlspecialchars($appSettings['app_name']) ?></p>
      </div>

      <?php if ($error): ?>
      <div style="background:rgba(239,68,68,0.1);border:1px solid rgba(239,68,68,0.3);color:#f87171;padding:12px 16px;border-radius:var(--radius-md);margin-bottom:20px;font-size:0.88rem;text-align:center;">
        ⚠️ <?= htmlspecialchars($error) ?>
      </div>
      <?php endif; ?>

      <!-- Google Sign In Button -->
      <div id="g_id_onload"
           data-client_id="<?= htmlspecialchars($appSettings['google_client_id'] ?? '') ?>"
           data-context="signup"
           data-ux_mode="popup"
           data-login_uri="<?= (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]" . dirname($_SERVER['PHP_SELF']) ?>/google_callback.php"
           data-auto_prompt="false">
      </div>

      <div class="g_id_signin"
           data-type="standard"
           data-shape="rectangular"
           data-theme="outline"
           data-text="signup_with"
           data-size="large"
           data-logo_alignment="left"
           style="margin-bottom: 24px; display: flex; justify-content: center;">
      </div>

      <div class="login-divider">atau daftar secara manual</div>

      <!-- Step indicators -->
      <div style="display: flex; gap: 8px; margin-bottom: 28px;">
        <div class="step-indicator active" id="step1ind" style="flex:1; height:4px; border-radius:4px; background: var(--primary); transition: background 0.3s;"></div>
        <div class="step-indicator" id="step2ind" style="flex:1; height:4px; border-radius:4px; background: var(--border-color); transition: background 0.3s;"></div>
        <div class="step-indicator" id="step3ind" style="flex:1; height:4px; border-radius:4px; background: var(--border-color); transition: background 0.3s;"></div>
      </div>

      <form class="login-form" action="register.php" method="POST">
        <!-- Step 1: Data Pribadi -->
        <div id="regStep1">
          <div class="form-group">
            <label class="form-label">Nama Lengkap</label>
            <input type="text" class="form-control" name="full_name" placeholder="Nama lengkap sesuai KTP" value="<?= htmlspecialchars($_POST['full_name'] ?? '') ?>" required>
          </div>
          <div class="form-group">
            <label class="form-label">NIK</label>
            <input type="text" class="form-control" name="nik" placeholder="16 digit NIK" maxlength="16" value="<?= htmlspecialchars($_POST['nik'] ?? '') ?>" required>
          </div>
          <div class="form-row">
            <div class="form-group">
              <label class="form-label">No. HP / WhatsApp</label>
              <input type="tel" class="form-control" name="phone" placeholder="08xxxxxxxxxx" value="<?= htmlspecialchars($_POST['phone'] ?? '') ?>" required>
            </div>
            <div class="form-group">
              <label class="form-label">Email</label>
              <input type="email" class="form-control" name="email" placeholder="email@contoh.com" value="<?= htmlspecialchars($_POST['email'] ?? '') ?>" required>
            </div>
          </div>
          <button type="button" class="btn btn-primary login-btn" onclick="nextStep(2)">Lanjutkan →</button>
        </div>

        <!-- Step 2: Alamat -->
        <div id="regStep2" style="display: none;">
          <div class="form-group">
            <label class="form-label">Perumahan / Cluster</label>
            <select class="form-control" name="tenant">
              <option value="1">Perumahan Graha Indah</option>
            </select>
          </div>
          <div class="form-row">
            <div class="form-group">
              <label class="form-label">Blok</label>
              <select class="form-control" name="block">
                <option value="">Pilih blok...</option>
                <option>Blok A</option>
                <option>Blok B</option>
                <option>Blok C</option>
                <option>Blok D</option>
              </select>
            </div>
            <div class="form-group">
              <label class="form-label">No. Rumah</label>
              <input type="text" class="form-control" name="house_number" placeholder="Contoh: 12">
            </div>
          </div>
          <div class="form-group">
            <label class="form-label">Status Hunian</label>
            <select class="form-control" name="status">
              <option value="owner">Pemilik</option>
              <option value="renter">Penyewa / Kontrak</option>
              <option value="family">Keluarga Pemilik</option>
            </select>
          </div>
          <div style="display:flex; gap:12px;">
            <button type="button" class="btn btn-secondary" style="flex:1;" onclick="nextStep(1)">← Kembali</button>
            <button type="button" class="btn btn-primary" style="flex:1;" onclick="nextStep(3)">Lanjutkan →</button>
          </div>
        </div>

        <!-- Step 3: Akun -->
        <div id="regStep3" style="display: none;">
          <div class="form-group">
            <label class="form-label">Kata Sandi</label>
            <input type="password" class="form-control" name="password" placeholder="Minimal 6 karakter" required>
          </div>
          <div class="form-group">
            <label class="form-label">Konfirmasi Kata Sandi</label>
            <input type="password" class="form-control" name="password_confirm" placeholder="Ulangi kata sandi">
          </div>
          <div class="form-group">
            <label class="checkbox-label">
              <input type="checkbox" required> Saya menyetujui <a href="#" style="color:var(--primary-light)">syarat & ketentuan</a>
            </label>
          </div>
          <div style="display:flex; gap:12px;">
            <button type="button" class="btn btn-secondary" style="flex:1;" onclick="nextStep(2)">← Kembali</button>
            <button type="submit" class="btn btn-primary" style="flex:1;">Daftar Sekarang ✓</button>
          </div>
        </div>
      </form>

      <div class="login-footer">
        Sudah punya akun? <a href="login.php">Masuk di sini</a>
      </div>
    </div>
  </div>

  <script>
    function nextStep(step) {
      document.getElementById('regStep1').style.display = step === 1 ? '' : 'none';
      document.getElementById('regStep2').style.display = step === 2 ? '' : 'none';
      document.getElementById('regStep3').style.display = step === 3 ? '' : 'none';

      document.getElementById('step1ind').style.background = step >= 1 ? 'var(--primary)' : 'var(--border-color)';
      document.getElementById('step2ind').style.background = step >= 2 ? 'var(--primary)' : 'var(--border-color)';
      document.getElementById('step3ind').style.background = step >= 3 ? 'var(--primary)' : 'var(--border-color)';
    }
  </script>
  <script src="js/app.js"></script>
</body>
</html>
