<?php
/**
 * WargaKu - Authentication Class
 */

require_once __DIR__ . '/Database.php';

class Auth {
    private static $user = null;

    /**
     * Start session if not already started
     */
    public static function init() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    /**
     * Attempt login
     */
    public static function login($email, $password) {
        self::init();
        $db = Database::getInstance();

        $user = $db->fetch("SELECT * FROM users WHERE email = ? AND is_active = 1", [$email]);

        if (!$user || !password_verify($password, $user['password'])) {
            return false;
        }

        // Set session
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['tenant_id'] = $user['tenant_id'];
        $_SESSION['user_role'] = $user['role'];
        $_SESSION['user_name'] = $user['name'];
        $_SESSION['login_time'] = time();

        // Update last login
        $db->update('users', ['last_login' => date('Y-m-d H:i:s')], 'id = ?', [$user['id']]);

        self::$user = $user;
        return true;
    }

    /**
     * Register new user
     */
    public static function register($data) {
        $db = Database::getInstance();

        // Check if email already exists
        $exists = $db->fetch("SELECT id FROM users WHERE email = ?", [$data['email']]);
        if ($exists) {
            return ['success' => false, 'message' => 'Email sudah terdaftar'];
        }

        // Hash password
        $userData = [
            'name'     => $data['name'],
            'email'    => $data['email'],
            'phone'    => $data['phone'] ?? null,
            'password' => password_hash($data['password'], PASSWORD_BCRYPT),
            'role'     => $data['role'] ?? 'warga',
        ];

        $id = $db->insert('users', $userData);

        return ['success' => true, 'user_id' => $id];
    }

    /**
     * Logout
     */
    public static function logout() {
        self::init();
        
        // If impersonating, stop instead of full logout
        if (isset($_SESSION['original_user_id'])) {
            self::stopImpersonating();
            return;
        }

        session_unset();
        session_destroy();
        self::$user = null;
    }

    /**
     * Impersonate another user (Login As)
     * @param int $userId Target user ID
     */
    public static function impersonate($userId) {
        self::init();
        if (!self::isSuperadmin() && !isset($_SESSION['original_user_id'])) {
            die("Akses ditolak: Hanya Superadmin yang dapat melakukan impersonasi.");
        }

        $db = Database::getInstance();
        $targetUser = $db->fetch("SELECT * FROM users WHERE id = ? AND is_active = 1", [$userId]);
        if (!$targetUser) return false;

        // Save original ID if first time impersonating
        if (!isset($_SESSION['original_user_id'])) {
            $_SESSION['original_user_id'] = $_SESSION['user_id'];
            $_SESSION['original_user_name'] = $_SESSION['user_name'];
        }

        // Switch session
        $_SESSION['user_id'] = $targetUser['id'];
        $_SESSION['tenant_id'] = $targetUser['tenant_id'];
        $_SESSION['user_role'] = $targetUser['role'];
        $_SESSION['user_name'] = $targetUser['name'];
        
        self::$user = $targetUser;
        return true;
    }

    /**
     * Stop impersonating and return to original superadmin account
     */
    public static function stopImpersonating() {
        self::init();
        if (!isset($_SESSION['original_user_id'])) return false;

        $originalId = $_SESSION['original_user_id'];
        unset($_SESSION['original_user_id']);
        unset($_SESSION['original_user_name']);

        $db = Database::getInstance();
        $originalUser = $db->fetch("SELECT * FROM users WHERE id = ?", [$originalId]);
        if (!$originalUser) {
            session_destroy();
            return false;
        }

        $_SESSION['user_id'] = $originalUser['id'];
        $_SESSION['tenant_id'] = $originalUser['tenant_id'];
        $_SESSION['user_role'] = $originalUser['role'];
        $_SESSION['user_name'] = $originalUser['name'];

        self::$user = $originalUser;
        return true;
    }

    /**
     * Check if currently impersonating
     */
    public static function isImpersonating() {
        self::init();
        return isset($_SESSION['original_user_id']);
    }

    /**
     * Check if user is logged in
     */
    public static function check() {
        self::init();
        return isset($_SESSION['user_id']);
    }

    /**
     * Get current user data
     */
    public static function user() {
        self::init();
        if (!self::check()) return null;

        if (self::$user === null) {
            $db = Database::getInstance();
            self::$user = $db->fetch("SELECT id, tenant_id, name, email, phone, role, avatar, resident_id, last_login, created_at, password FROM users WHERE id = ?", [$_SESSION['user_id']]);
        }
        return self::$user;
    }

    /**
     * Get user ID
     */
    public static function id() {
        self::init();
        return $_SESSION['user_id'] ?? null;
    }

    /**
     * Get tenant ID
     */
    public static function tenantId() {
        self::init();
        return $_SESSION['tenant_id'] ?? null;
    }

    /**
     * Get user role
     */
    public static function role() {
        self::init();
        return $_SESSION['user_role'] ?? null;
    }

    /**
     * Check if user has specific role
     */
    public static function hasRole($role) {
        if (is_array($role)) {
            return in_array(self::role(), $role);
        }
        return self::role() === $role;
    }

    /**
     * Check if user is superadmin
     */
    public static function isSuperadmin() {
        // Traditionally superadmin role, but check saas_roles too
        $role = self::role();
        if ($role === 'superadmin') return true;

        // Check if role exists in saas_roles
        $db = Database::getInstance();
        $roleData = $db->fetch("SELECT id FROM saas_roles WHERE role_key = ?", [$role]);
        return ($roleData !== false);
    }

    /**
     * Check Access for Superadmin Pages (Dynamic Role)
     */
    public static function canAccess($page) {
        if (!self::isSuperadmin()) return false;
        
        $roleKey = self::role();
        if ($roleKey === 'superadmin') return true;

        $db = Database::getInstance();
        $role = $db->fetch("SELECT permissions FROM saas_roles WHERE role_key = ?", [$roleKey]);
        if (!$role) return false;

        $perms = json_decode($role['permissions'], true) ?: [];
        if (in_array('all', $perms)) return true;
        
        return in_array($page, $perms);
    }

    /**
     * Login or Register with Google data
     */
    public static function loginWithGoogle($data) {
        self::init();
        $db = Database::getInstance();

        // 1. Try to find user by google_id
        $user = $db->fetch("SELECT * FROM users WHERE google_id = ?", [$data['sub']]);

        // 2. If not found by google_id, try by email
        if (!$user) {
            $user = $db->fetch("SELECT * FROM users WHERE email = ?", [$data['email']]);
            if ($user && empty($user['google_id'])) {
                // Link Google Auth to existing email account
                $db->update('users', [
                    'google_id' => $data['sub'],
                    'email_verified' => 1
                ], 'id = ?', [$user['id']]);
            }
        }

        // 3. If still not found, create new user
        if (!$user) {
            $userData = [
                'name'           => $data['name'],
                'email'          => $data['email'],
                'google_id'      => $data['sub'],
                'email_verified' => 1,
                'role'           => 'warga',
                'avatar'         => $data['picture'] ?? null,
                'password'       => password_hash(bin2hex(random_bytes(16)), PASSWORD_BCRYPT), // Random password for safety
                'is_active'      => 1
            ];
            
            $userId = $db->insert('users', $userData);
            $user = $db->fetch("SELECT * FROM users WHERE id = ?", [$userId]);
        }

        if (!$user || $user['is_active'] == 0) {
            return false;
        }

        // 4. Set session
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['tenant_id'] = $user['tenant_id'];
        $_SESSION['user_role'] = $user['role'];
        $_SESSION['user_name'] = $user['name'];
        $_SESSION['login_time'] = time();

        // Update last login
        $db->update('users', ['last_login' => date('Y-m-d H:i:s')], 'id = ?', [$user['id']]);

        self::$user = $user;
        return true;
    }

    /**
     * Require login - redirect if not logged in
     */
    public static function requireLogin() {
        if (!self::check()) {
            header('Location: login.php');
            exit;
        }
    }

    /**
     * Require specific role
     */
    public static function requireRole($role) {
        self::requireLogin();
        if (!self::hasRole($role)) {
            http_response_code(403);
            die('Akses ditolak: Anda tidak memiliki izin untuk halaman ini.');
        }
    }
}
