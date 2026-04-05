<?php
require_once 'classes/Auth.php';
require_once 'classes/Database.php';

Auth::requireRole('superadmin');
$db = Database::getInstance();
$pdo = $db->getConnection();

if (isset($_GET['download'])) {
    $tables = [];
    $result = $pdo->query("SHOW TABLES");
    while ($row = $result->fetch(PDO::FETCH_NUM)) {
        $tables[] = $row[0];
    }

    $sql = "-- WargaKu SaaS Backup\n";
    $sql .= "-- Generated: " . date('Y-m-d H:i:s') . "\n\n";
    $sql .= "SET FOREIGN_KEY_CHECKS=0;\n\n";

    foreach ($tables as $table) {
        // Drop table if exists
        $sql .= "DROP TABLE IF EXISTS `{$table}`;\n";
        
        // Show create table
        $res = $pdo->query("SHOW CREATE TABLE `{$table}`");
        $showCreate = $res->fetch(PDO::FETCH_ASSOC);
        $sql .= $showCreate['Create Table'] . ";\n\n";

        // Fetch data
        $res = $pdo->query("SELECT * FROM `{$table}`");
        $rows = $res->fetchAll(PDO::FETCH_ASSOC);
        
        foreach ($rows as $row) {
            $cols = array_keys($row);
            $vals = array_map(function($v) use ($pdo) {
                if ($v === null) return "NULL";
                return $pdo->quote($v);
            }, array_values($row));
            
            $sql .= "INSERT INTO `{$table}` (`" . implode("`, `", $cols) . "`) VALUES (" . implode(", ", $vals) . ");\n";
        }
        $sql .= "\n";
    }
    
    $sql .= "SET FOREIGN_KEY_CHECKS=1;\n";

    header('Content-Type: application/octet-stream');
    header('Content-Disposition: attachment; filename="backup_wargaku_' . date('Ymd_His') . '.sql"');
    echo $sql;
    exit;
}

$pageTitle = 'Backup Database';
include 'includes/header.php';
?>

<div class="app-layout">
  <?php include 'includes/sidebar_saas.php'; ?>

  <div class="main-wrapper">
    <?php include 'includes/topbar.php'; ?>
    <main class="main-content">
      <div class="page-header"><h1>Pusat Keamanan Data</h1></div>

      <div class="card" style="max-width:600px;">
        <div class="card-header border-bottom"><h3 class="card-title">Backup Database Sistem</h3></div>
        <div style="padding:40px; text-align:center;">
            <div style="width:80px; height:80px; background:rgba(16,185,129,0.1); border-radius:50%; display:flex; align-items:center; justify-content:center; margin:0 auto 20px;">
                <i data-lucide="database" style="width:40px; height:40px; color:var(--success);"></i>
            </div>
            <h3>Unduh Cadangan SQL</h3>
            <p style="color:var(--text-muted); margin-bottom:30px;">Klik tombol di bawah untuk mengunduh seluruh data aplikasi (termasuk semua tenant) dalam satu file .sql.</p>
            <a href="?download=1" class="btn btn-primary" style="padding:12px 30px;"><i data-lucide="download" style="width:18px; margin-right:8px;"></i> UNDUH BACKUP SEKARANG</a>
        </div>
      </div>
    </main>
  </div>
</div>
<?php include 'includes/footer.php'; ?>
