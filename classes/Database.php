<?php
/**
 * WargaKu - Database Class (PDO Singleton)
 */

require_once __DIR__ . '/../config/database.php';

class Database {
    private static $instance = null;
    private $pdo;

    private function __construct() {
        $dsn = "mysql:host=" . DB_HOST . ";port=" . DB_PORT . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET;
        $options = [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
        ];
        try {
            $this->pdo = new PDO($dsn, DB_USER, DB_PASS, $options);
        } catch (PDOException $e) {
            die('Database connection failed: ' . $e->getMessage());
        }
    }

    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function getConnection() {
        return $this->pdo;
    }

    /**
     * Execute a query with optional params
     */
    public function query($sql, $params = []) {
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt;
    }

    /**
     * Fetch single row
     */
    public function fetch($sql, $params = []) {
        return $this->query($sql, $params)->fetch();
    }

    /**
     * Fetch all rows
     */
    public function fetchAll($sql, $params = []) {
        return $this->query($sql, $params)->fetchAll();
    }

    /**
     * Fetch single value
     */
    public function fetchColumn($sql, $params = []) {
        return $this->query($sql, $params)->fetchColumn();
    }

    /**
     * Insert a row and return last insert ID
     */
    public function insert($table, $data) {
        $columns = implode(', ', array_keys($data));
        $placeholders = implode(', ', array_fill(0, count($data), '?'));
        $sql = "INSERT INTO {$table} ({$columns}) VALUES ({$placeholders})";
        $this->query($sql, array_values($data));
        return $this->pdo->lastInsertId();
    }

    /**
     * Update rows with Audit Trail
     */
    public function update($table, $data, $where, $whereParams = [], $auditAction = 'Update') {
        // Prepare Audit Log (exclude audit_logs itself)
        $oldData = null;
        if ($table !== 'audit_logs') {
            $oldData = $this->fetchAll("SELECT * FROM {$table} WHERE {$where}", $whereParams);
        }

        $set = implode(', ', array_map(fn($col) => "{$col} = ?", array_keys($data)));
        $sql = "UPDATE {$table} SET {$set} WHERE {$where}";
        $params = array_merge(array_values($data), $whereParams);
        $rowCount = $this->query($sql, $params)->rowCount();

        // Record Audit if data changed
        if ($rowCount > 0 && $oldData !== null) {
            foreach ($oldData as $oldRow) {
                $this->recordAudit($table, $oldRow['id'] ?? 0, $auditAction, $oldRow, array_merge($oldRow, $data));
            }
        }

        return $rowCount;
    }

    /**
     * Delete rows with Audit Trail
     */
    public function delete($table, $where, $whereParams = [], $auditAction = 'Delete') {
        // Prepare Audit Log
        $oldData = null;
        if ($table !== 'audit_logs') {
            $oldData = $this->fetchAll("SELECT * FROM {$table} WHERE {$where}", $whereParams);
        }

        $sql = "DELETE FROM {$table} WHERE {$where}";
        $rowCount = $this->query($sql, $whereParams)->rowCount();

        // Record Audit
        if ($rowCount > 0 && $oldData !== null) {
            foreach ($oldData as $oldRow) {
                $this->recordAudit($table, $oldRow['id'] ?? 0, $auditAction, $oldRow, null);
            }
        }

        return $rowCount;
    }

    /**
     * Helper to record audit log
     */
    private function recordAudit($table, $recordId, $action, $oldValues, $newValues) {
        // Avoid infinite loop
        if ($table === 'audit_logs') return;

        $userId = $_SESSION['user_id'] ?? null;
        $tenantId = $_SESSION['tenant_id'] ?? null;
        
        $sql = "INSERT INTO audit_logs (tenant_id, user_id, action, table_name, record_id, old_values, new_values, ip_address) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
        
        $params = [
            $tenantId, 
            $userId, 
            $action, 
            $table, 
            $recordId, 
            $oldValues ? json_encode($oldValues) : null, 
            $newValues ? json_encode($newValues) : null,
            $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1'
        ];

        try {
            // Use PDO directly to avoid recordAudit calling itself if we used $this->query
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute($params);
        } catch (Exception $e) {
            // Silently fail to avoid breaking main transaction
        }
    }

    /**
     * Count rows
     */
    public function count($table, $where = '1=1', $params = []) {
        return (int) $this->fetchColumn("SELECT COUNT(*) FROM {$table} WHERE {$where}", $params);
    }

    /**
     * Begin transaction
     */
    public function beginTransaction() {
        return $this->pdo->beginTransaction();
    }

    public function commit() {
        return $this->pdo->commit();
    }

    public function rollback() {
        return $this->pdo->rollBack();
    }
}
