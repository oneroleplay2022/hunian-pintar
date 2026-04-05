<?php
/**
 * Notification Class
 * Menangani pengiriman Email (SMTP) dan WhatsApp (WHACenter)
 * Data konfigurasi diambil otomatis dari config/app_settings.json
 */

class Notification {
    private static $settings = null;

    /**
     * Load settings from JSON
     */
    private static function loadSettings() {
        if (self::$settings === null) {
            $path = __DIR__ . '/../config/app_settings.json';
            if (file_exists($path)) {
                self::$settings = json_decode(file_get_contents($path), true);
            } else {
                self::$settings = [];
            }
        }
    }

    /**
     * Kirim WhatsApp via WHACenter
     * @param string $to Nomor tujuan (format 628...)
     * @param string $message Isi pesan
     * @return array Status pengiriman
     */
    public static function sendWA($to, $message) {
        self::loadSettings();
        
        $deviceId = self::$settings['whacenter_device_id'] ?? '';
        $apiKey = self::$settings['whacenter_api_key'] ?? '';

        if (empty($deviceId) || empty($apiKey)) {
            return ['status' => false, 'message' => 'Konfigurasi WHACenter belum diset.'];
        }

        // Clean number: hapus +, -, spasi, dan pastikan diawali 62
        $to = preg_replace('/[^0-9]/', '', $to);
        if (substr($to, 0, 1) === '0') {
            $to = '62' . substr($to, 1);
        } elseif (substr($to, 0, 2) !== '62') {
            $to = '62' . $to;
        }

        $url = "https://cloud.whacenter.com/api/send";
        $data = [
            'device_id' => $deviceId,
            'number'    => $to,
            'message'   => $message
        ];

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
        curl_setopt($ch, CURLOPT_TIMEOUT, 10);
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        $result = json_decode($response, true);
        if ($httpCode == 200 && isset($result['status']) && $result['status'] == true) {
            return ['status' => true, 'message' => 'WA Terkirim.'];
        }

        return ['status' => false, 'message' => 'Gagal kirim WA: ' . ($result['message'] ?? 'Unknown Error')];
    }

    /**
     * Kirim Email via mail() bawaan PHP
     * @param string $to Alamat email tujuan
     * @param string $subject Subjek email
     * @param string $message Isi pesan (HTML)
     * @return array Status pengiriman
     */
    public static function sendEmail($to, $subject, $message) {
        self::loadSettings();
        
        $fromEmail = self::$settings['smtp_user'] ?? 'no-reply@wargaku.id';
        $appName = self::$settings['app_name'] ?? 'WargaKu';

        $headers = "MIME-Version: 1.0" . "\r\n";
        $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
        $headers .= "From: $appName <$fromEmail>" . "\r\n";

        if (mail($to, $subject, $message, $headers)) {
            return ['status' => true, 'message' => 'Email Terkirim.'];
        }

        return ['status' => false, 'message' => 'Gagal kirim Email.'];
    }

    /**
     * Kirim Notifikasi Selamat Datang ke Admin Tenant Baru
     */
    public static function welcomeTenant($toEmail, $toPhone, $tenantName, $adminName, $password) {
        // 1. Persiapkan Pesan WhatsApp
        $waMsg = "📢 *Selamat Datang di WargaKu!*\n\n";
        $waMsg .= "Halo Bapak/Ibu *$adminName*,\n";
        $waMsg .= "Akun Dashboard Perumahan *$tenantName* Anda telah aktif.\n\n";
        $waMsg .= "📍 *Detail Login:*\n";
        $waMsg .= "Email: $toEmail\n";
        $waMsg .= "Password: $password\n\n";
        $waMsg .= "Silakan login di: " . (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]/login.php\n\n";
        $waMsg .= "Terima kasih telah menggunakan layanan kami.";

        // 2. Persiapkan Pesan Email
        $emailSubject = "Selamat Datang di $tenantName - WargaKu";
        $emailMsg = "
            <html>
            <body style='font-family: Arial, sans-serif; line-height: 1.6; color: #333;'>
                <div style='max-width: 600px; margin: 0 auto; padding: 20px; border: 1px solid #ddd; border-top: 5px solid #10b981; border-radius: 8px;'>
                    <h2 style='color: #10b981;'>Selamat Datang di WargaKu!</h2>
                    <p>Halo <strong>$adminName</strong>,</p>
                    <p>Akun Dashboard Perumahan <strong>$tenantName</strong> Anda telah berhasil diaktifkan. Anda sekarang dapat mulai mengelola data warga dan keuangan perumahan Anda.</p>
                    <div style='background: #f9f9f9; padding: 15px; border-radius: 5px; margin: 20px 0;'>
                        <p style='margin: 0;'><strong>Detail Login:</strong></p>
                        <p style='margin: 5px 0;'>Email: $toEmail</p>
                        <p style='margin: 5px 0;'>Password: $password</p>
                    </div>
                    <p>Silakan login melalui tautan berikut:</p>
                    <p><a href='http://$_SERVER[HTTP_HOST]/login.php' style='display: inline-block; background: #10b981; color: #fff; padding: 10px 20px; text-decoration: none; border-radius: 5px;'>Login ke Dashboard</a></p>
                    <hr style='border: 0; border-top: 1px solid #ddd; margin: 20px 0;'>
                    <p style='font-size: 0.85rem; color: #777;'>Jika Anda mengalami kendala, silakan hubungi tim support kami.</p>
                </div>
            </body>
            </html>
        ";

        // Kirim keduanya
        $waResult = self::sendWA($toPhone, $waMsg);
        $emailResult = self::sendEmail($toEmail, $emailSubject, $emailMsg);

        return [
            'status' => $waResult['status'] || $emailResult['status'],
            'message' => "WA: {$waResult['message']} | Email: {$emailResult['message']}"
        ];
    }

    /**
     * Simpan Notifikasi ke Database (Sistem)
     * @param int $tenantId ID Tenant
     * @param int $userId ID User Penerima
     * @param string $title Judul Notifikasi
     * @param string $message Isi Notifikasi
     * @param string $link Tautan (Opsional)
     * @return bool
     */
    public static function addSystemNotification($tenantId, $userId, $title, $message, $link = '') {
        require_once __DIR__ . '/Database.php';
        $db = Database::getInstance();
        return $db->insert('notifications', [
            'tenant_id' => $tenantId,
            'user_id' => $userId,
            'title' => $title,
            'message' => $message,
            'link' => $link
        ]);
    }
}

