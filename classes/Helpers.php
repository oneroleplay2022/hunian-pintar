<?php
/**
 * WargaKu - Helper Functions
 */

class Helpers {

    /**
     * Format number as Rupiah
     */
    public static function formatRupiah($amount, $prefix = 'Rp ') {
        return $prefix . number_format((float)$amount, 0, ',', '.');
    }

    /**
     * Format date to Indonesian format
     */
    public static function formatDate($date, $format = 'long') {
        if (!$date) return '-';
        $timestamp = strtotime($date);
        
        $months = ['', 'Januari','Februari','Maret','April','Mei','Juni',
                    'Juli','Agustus','September','Oktober','November','Desember'];
        
        $d = date('j', $timestamp);
        $m = (int)date('n', $timestamp);
        $y = date('Y', $timestamp);

        if ($format === 'short') {
            $shortMonths = ['','Jan','Feb','Mar','Apr','Mei','Jun','Jul','Agu','Sep','Okt','Nov','Des'];
            return "$d {$shortMonths[$m]} $y";
        }
        if ($format === 'relative') {
            return self::timeAgo($timestamp);
        }
        return "$d {$months[$m]} $y";
    }

    /**
     * Relative time ago
     */
    public static function timeAgo($timestamp) {
        $diff = time() - $timestamp;
        if ($diff < 60) return 'Baru saja';
        if ($diff < 3600) return floor($diff / 60) . ' menit lalu';
        if ($diff < 86400) return floor($diff / 3600) . ' jam lalu';
        if ($diff < 604800) return floor($diff / 86400) . ' hari lalu';
        return date('d M Y', $timestamp);
    }

    /**
     * Sanitize input for XSS protection
     */
    public static function sanitize($input) {
        if (is_array($input)) {
            return array_map([self::class, 'sanitize'], $input);
        }
        return htmlspecialchars(trim($input), ENT_QUOTES, 'UTF-8');
    }

    /**
     * Handle file upload
     */
    public static function uploadFile($file, $subdir = '') {
        $uploadDir = rtrim(UPLOAD_DIR, '/') . '/' . ltrim($subdir, '/');
        
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }

        $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        $allowed = ['jpg','jpeg','png','gif','pdf','doc','docx','webp'];
        
        if (!in_array($ext, $allowed)) {
            return ['success' => false, 'message' => 'Format file tidak diizinkan'];
        }

        if ($file['size'] > 5 * 1024 * 1024) { // 5MB max
            return ['success' => false, 'message' => 'Ukuran file maksimal 5MB'];
        }

        $filename = uniqid() . '_' . time() . '.' . $ext;
        $filepath = $uploadDir . '/' . $filename;

        if (move_uploaded_file($file['tmp_name'], $filepath)) {
            $relativePath = 'uploads/' . ltrim($subdir, '/') . '/' . $filename;
            return ['success' => true, 'path' => $relativePath, 'filename' => $filename];
        }

        return ['success' => false, 'message' => 'Gagal upload file'];
    }

    /**
     * Generate invoice number
     */
    public static function generateInvoiceNumber($prefix = 'INV') {
        return $prefix . '-' . date('Y-m') . '-' . str_pad(mt_rand(1, 9999), 4, '0', STR_PAD_LEFT);
    }

    /**
     * Generate ticket/request number
     */
    public static function generateTicketNumber($prefix = 'TKT') {
        return $prefix . '-' . date('Ymd') . '-' . str_pad(mt_rand(1, 999), 3, '0', STR_PAD_LEFT);
    }

    /**
     * Get badge class by status
     */
    public static function statusBadge($status) {
        $map = [
            'lunas' => 'success', 'selesai' => 'success', 'aktif' => 'success',
            'berpenghuni' => 'success', 'tersalurkan' => 'success', 'disetujui' => 'success',
            'dikembalikan' => 'success', 'baik' => 'success', 'domisili' => 'success',
            'belum_bayar' => 'warning', 'menunggu' => 'warning', 'diajukan' => 'warning',
            'baru' => 'warning', 'belum' => 'warning', 'dipinjam' => 'warning',
            'kosong' => 'warning', 'sebagian' => 'warning',
            'ditolak' => 'danger', 'terlambat' => 'danger', 'darurat' => 'danger',
            'rusak_berat' => 'danger', 'false_alarm' => 'danger',
            'diproses' => 'info', 'berjalan' => 'info', 'ditanggapi' => 'info',
            'diverifikasi' => 'info', 'dikontrakkan' => 'info', 'proses' => 'info',
            'ditangani' => 'info',
            'rusak_ringan' => 'warning',
        ];
        return $map[strtolower($status)] ?? 'neutral';
    }

    /**
     * Format status label (human readable)
     */
    public static function statusLabel($status) {
        $map = [
            'belum_bayar' => 'Belum Bayar', 'lunas' => 'Lunas', 'sebagian' => 'Sebagian',
            'berpenghuni' => 'Berpenghuni', 'kosong' => 'Kosong', 'dikontrakkan' => 'Dikontrakkan',
            'domisili' => 'Domisili', 'domisili_luar' => 'Luar Domisili', 'kk_luar' => 'KK Luar',
            'masuk' => 'Masuk', 'keluar' => 'Keluar', 'pindah' => 'Pindah', 'meninggal' => 'Meninggal',
            'kepala_keluarga' => 'Kepala Keluarga', 'istri' => 'Istri', 'anak' => 'Anak', 'lainnya' => 'Lainnya',
            'diajukan' => 'Diajukan', 'diverifikasi' => 'Diverifikasi', 'diproses' => 'Diproses',
            'selesai' => 'Selesai', 'ditolak' => 'Ditolak', 'disetujui' => 'Disetujui',
            'baru' => 'Baru', 'ditanggapi' => 'Ditanggapi', 'proses' => 'Proses',
            'aktif' => 'Aktif', 'nonaktif' => 'Nonaktif',
            'dipinjam' => 'Dipinjam', 'dikembalikan' => 'Dikembalikan', 'terlambat' => 'Terlambat',
            'tersalurkan' => 'Tersalurkan', 'gagal' => 'Gagal',
            'baik' => 'Baik', 'rusak_ringan' => 'Rusak Ringan', 'rusak_berat' => 'Rusak Berat',
            'belum' => 'Belum', 'berjalan' => 'Berjalan',
            'darurat' => 'Darurat', 'ditangani' => 'Ditangani', 'false_alarm' => 'False Alarm',
        ];
        return $map[strtolower($status)] ?? ucfirst(str_replace('_', ' ', $status));
    }

    /**
     * Flash message (set)
     */
    public static function flash($key, $message) {
        if (session_status() === PHP_SESSION_NONE) session_start();
        $_SESSION['flash'][$key] = $message;
    }

    /**
     * Flash message (get + clear)
     */
    public static function getFlash($key) {
        if (session_status() === PHP_SESSION_NONE) session_start();
        $msg = $_SESSION['flash'][$key] ?? null;
        unset($_SESSION['flash'][$key]);
        return $msg;
    }

    /**
     * Redirect with optional flash
     */
    public static function redirect($url, $flashKey = null, $flashMsg = null) {
        if ($flashKey) self::flash($flashKey, $flashMsg);
        header("Location: $url");
        exit;
    }
}
