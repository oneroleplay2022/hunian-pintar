-- ============================================
-- WargaKu - Database Schema (SaaS / Multi-Tenant)
-- Aplikasi Transparansi & Manajemen Warga
-- ============================================

CREATE DATABASE IF NOT EXISTS wargaku CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE wargaku;

-- WARNING: Forcing a drop to reset schema for SaaS migration
DROP DATABASE IF EXISTS wargaku;
CREATE DATABASE wargaku CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE wargaku;

-- ============================================
-- 0. SAAS LEVEL: Tenants & Subscriptions
-- ============================================

CREATE TABLE tenants (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(150) NOT NULL,
    address TEXT,
    total_houses INT DEFAULT 0,
    subscription_status ENUM('trial','active','expired','suspended') DEFAULT 'trial',
    expired_at DATE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB;

CREATE TABLE subscriptions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    tenant_id INT NOT NULL,
    amount DECIMAL(12,2) NOT NULL,
    payment_status ENUM('pending','paid','failed') DEFAULT 'pending',
    payment_method VARCHAR(50),
    payment_proof VARCHAR(255),
    expired_at DATE NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (tenant_id) REFERENCES tenants(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- ============================================
-- 1. CORE: Users & Settings
-- ============================================

CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    tenant_id INT,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    phone VARCHAR(20),
    password VARCHAR(255) NOT NULL,
    role ENUM('superadmin','admin','pengurus','warga','kolektor','security') NOT NULL DEFAULT 'warga',
    avatar VARCHAR(255),
    resident_id INT DEFAULT NULL,
    is_active TINYINT(1) DEFAULT 1,
    last_login DATETIME,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (tenant_id) REFERENCES tenants(id) ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE settings (
    id INT AUTO_INCREMENT PRIMARY KEY,
    tenant_id INT NOT NULL,
    setting_key VARCHAR(100) NOT NULL,
    setting_value TEXT,
    updated_by INT,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (tenant_id) REFERENCES tenants(id) ON DELETE CASCADE,
    FOREIGN KEY (updated_by) REFERENCES users(id) ON DELETE SET NULL,
    UNIQUE KEY unique_setting_tenant (tenant_id, setting_key)
) ENGINE=InnoDB;

-- ============================================
-- 2. KEPENDUDUKAN: Blocks, Houses, Residents
-- ============================================

CREATE TABLE blocks (
    id INT AUTO_INCREMENT PRIMARY KEY,
    tenant_id INT NOT NULL,
    block_name VARCHAR(50) NOT NULL,
    description TEXT,
    total_houses INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (tenant_id) REFERENCES tenants(id) ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE houses (
    id INT AUTO_INCREMENT PRIMARY KEY,
    tenant_id INT NOT NULL,
    block_id INT NOT NULL,
    house_number VARCHAR(20) NOT NULL,
    status ENUM('berpenghuni','kosong','dikontrakkan') DEFAULT 'berpenghuni',
    owner_name VARCHAR(100),
    owner_phone VARCHAR(20),
    house_type VARCHAR(50),
    area_m2 DECIMAL(8,2),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (tenant_id) REFERENCES tenants(id) ON DELETE CASCADE,
    FOREIGN KEY (block_id) REFERENCES blocks(id) ON DELETE CASCADE,
    UNIQUE KEY unique_house_tenant (tenant_id, block_id, house_number)
) ENGINE=InnoDB;

CREATE TABLE families (
    id INT AUTO_INCREMENT PRIMARY KEY,
    tenant_id INT NOT NULL,
    house_id INT NOT NULL,
    kk_number VARCHAR(20),
    head_name VARCHAR(100),
    address_on_kk TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (tenant_id) REFERENCES tenants(id) ON DELETE CASCADE,
    FOREIGN KEY (house_id) REFERENCES houses(id) ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE residents (
    id INT AUTO_INCREMENT PRIMARY KEY,
    tenant_id INT NOT NULL,
    family_id INT,
    house_id INT NOT NULL,
    nik VARCHAR(20),
    full_name VARCHAR(100) NOT NULL,
    gender ENUM('L','P') NOT NULL,
    birth_place VARCHAR(100),
    birth_date DATE,
    religion VARCHAR(30),
    marital_status VARCHAR(30),
    blood_type VARCHAR(5),
    education VARCHAR(50),
    profession VARCHAR(100),
    phone VARCHAR(20),
    photo VARCHAR(255),
    domicile_status ENUM('domisili','domisili_luar','kk_luar') DEFAULT 'domisili',
    family_status VARCHAR(30) DEFAULT 'Anggota',
    is_active TINYINT(1) DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    deleted_at DATETIME DEFAULT NULL,
    FOREIGN KEY (tenant_id) REFERENCES tenants(id) ON DELETE CASCADE,
    FOREIGN KEY (family_id) REFERENCES families(id) ON DELETE SET NULL,
    FOREIGN KEY (house_id) REFERENCES houses(id) ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE mutations (
    id INT AUTO_INCREMENT PRIMARY KEY,
    tenant_id INT NOT NULL,
    resident_id INT,
    resident_name VARCHAR(100) NOT NULL,
    house_id INT,
    type ENUM('masuk','pindah','meninggal') NOT NULL,
    family_count INT DEFAULT 1,
    mutation_date DATE NOT NULL,
    origin_destination VARCHAR(255),
    description TEXT,
    document_url VARCHAR(255),
    created_by INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (tenant_id) REFERENCES tenants(id) ON DELETE CASCADE,
    FOREIGN KEY (resident_id) REFERENCES residents(id) ON DELETE SET NULL,
    FOREIGN KEY (house_id) REFERENCES houses(id) ON DELETE SET NULL,
    FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB;

CREATE TABLE vehicles (
    id INT AUTO_INCREMENT PRIMARY KEY,
    tenant_id INT NOT NULL,
    house_id INT NOT NULL,
    resident_id INT,
    plate_number VARCHAR(20) NOT NULL,
    type ENUM('mobil','motor') NOT NULL,
    brand VARCHAR(50),
    model VARCHAR(50),
    color VARCHAR(30),
    sticker_number VARCHAR(30),
    sticker_status ENUM('aktif','expired','belum') DEFAULT 'belum',
    photo VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (tenant_id) REFERENCES tenants(id) ON DELETE CASCADE,
    FOREIGN KEY (house_id) REFERENCES houses(id) ON DELETE CASCADE,
    FOREIGN KEY (resident_id) REFERENCES residents(id) ON DELETE SET NULL
) ENGINE=InnoDB;

CREATE TABLE visitors (
    id INT AUTO_INCREMENT PRIMARY KEY,
    tenant_id INT NOT NULL,
    house_id INT NOT NULL,
    guest_name VARCHAR(100) NOT NULL,
    guest_id_number VARCHAR(30),
    guest_phone VARCHAR(20),
    purpose VARCHAR(255),
    vehicle_plate VARCHAR(20),
    id_photo VARCHAR(255),
    check_in DATETIME NOT NULL,
    check_out DATETIME,
    checked_by INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (tenant_id) REFERENCES tenants(id) ON DELETE CASCADE,
    FOREIGN KEY (house_id) REFERENCES houses(id) ON DELETE CASCADE,
    FOREIGN KEY (checked_by) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB;

CREATE TABLE businesses (
    id INT AUTO_INCREMENT PRIMARY KEY,
    tenant_id INT NOT NULL,
    resident_id INT NOT NULL,
    house_id INT,
    business_name VARCHAR(100) NOT NULL,
    category VARCHAR(50),
    description TEXT,
    whatsapp VARCHAR(20),
    open_days VARCHAR(100),
    open_hours VARCHAR(50),
    photo VARCHAR(255),
    status ENUM('aktif','tutup') DEFAULT 'aktif',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (tenant_id) REFERENCES tenants(id) ON DELETE CASCADE,
    FOREIGN KEY (resident_id) REFERENCES residents(id) ON DELETE CASCADE,
    FOREIGN KEY (house_id) REFERENCES houses(id) ON DELETE SET NULL
) ENGINE=InnoDB;

-- ============================================
-- 3. KEUANGAN: Invoices, Payments, Cashflow
-- ============================================

CREATE TABLE invoice_types (
    id INT AUTO_INCREMENT PRIMARY KEY,
    tenant_id INT NOT NULL,
    name VARCHAR(100) NOT NULL,
    amount DECIMAL(12,2) NOT NULL,
    is_mandatory TINYINT(1) DEFAULT 1,
    description TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (tenant_id) REFERENCES tenants(id) ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE invoices (
    id INT AUTO_INCREMENT PRIMARY KEY,
    tenant_id INT NOT NULL,
    invoice_number VARCHAR(50) NOT NULL,
    house_id INT NOT NULL,
    invoice_type_id INT NOT NULL,
    period VARCHAR(7) NOT NULL COMMENT 'Format: YYYY-MM',
    amount DECIMAL(12,2) NOT NULL,
    due_date DATE,
    status ENUM('belum_bayar','lunas','sebagian') DEFAULT 'belum_bayar',
    notes TEXT,
    created_by INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (tenant_id) REFERENCES tenants(id) ON DELETE CASCADE,
    FOREIGN KEY (house_id) REFERENCES houses(id) ON DELETE CASCADE,
    FOREIGN KEY (invoice_type_id) REFERENCES invoice_types(id) ON DELETE CASCADE,
    FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE SET NULL,
    UNIQUE KEY unique_invoice_tenant (tenant_id, invoice_number)
) ENGINE=InnoDB;

CREATE TABLE payments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    tenant_id INT NOT NULL,
    invoice_id INT NOT NULL,
    transaction_id VARCHAR(50),
    method ENUM('va','qris','tunai','transfer') NOT NULL,
    amount DECIMAL(12,2) NOT NULL,
    admin_fee DECIMAL(10,2) DEFAULT 0,
    payment_proof VARCHAR(255),
    paid_at DATETIME NOT NULL,
    verified_by INT,
    notes TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (tenant_id) REFERENCES tenants(id) ON DELETE CASCADE,
    FOREIGN KEY (invoice_id) REFERENCES invoices(id) ON DELETE CASCADE,
    FOREIGN KEY (verified_by) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB;

CREATE TABLE cashflows (
    id INT AUTO_INCREMENT PRIMARY KEY,
    tenant_id INT NOT NULL,
    type ENUM('masuk','keluar') NOT NULL,
    category VARCHAR(100) NOT NULL,
    amount DECIMAL(12,2) NOT NULL,
    description TEXT,
    transaction_date DATE NOT NULL,
    receipt_photo VARCHAR(255),
    created_by INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (tenant_id) REFERENCES tenants(id) ON DELETE CASCADE,
    FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB;

-- ============================================
-- 4. PELAYANAN: Surat, Perizinan, Pengaduan
-- ============================================

CREATE TABLE service_requests (
    id INT AUTO_INCREMENT PRIMARY KEY,
    tenant_id INT NOT NULL,
    request_number VARCHAR(50) NOT NULL,
    resident_id INT NOT NULL,
    type VARCHAR(50) NOT NULL COMMENT 'SK Domisili, SK Usaha, Pengantar, dll',
    purpose TEXT,
    requirements_url VARCHAR(255),
    status ENUM('diajukan','diverifikasi','diproses','selesai','ditolak') DEFAULT 'diajukan',
    rejection_reason TEXT,
    document_url VARCHAR(255),
    processed_by INT,
    processed_at DATETIME,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (tenant_id) REFERENCES tenants(id) ON DELETE CASCADE,
    FOREIGN KEY (resident_id) REFERENCES residents(id) ON DELETE CASCADE,
    FOREIGN KEY (processed_by) REFERENCES users(id) ON DELETE SET NULL,
    UNIQUE KEY unique_req_number_tenant (tenant_id, request_number)
) ENGINE=InnoDB;

CREATE TABLE permits (
    id INT AUTO_INCREMENT PRIMARY KEY,
    tenant_id INT NOT NULL,
    permit_number VARCHAR(50) NOT NULL,
    house_id INT NOT NULL,
    resident_id INT NOT NULL,
    type VARCHAR(50) NOT NULL COMMENT 'Renovasi, Acara/Keramaian, Instalasi, dll',
    description TEXT,
    start_date DATE,
    end_date DATE,
    attachment_url VARCHAR(255),
    status ENUM('diajukan','disetujui','ditolak','selesai') DEFAULT 'diajukan',
    reviewed_by INT,
    review_notes TEXT,
    reviewed_at DATETIME,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (tenant_id) REFERENCES tenants(id) ON DELETE CASCADE,
    FOREIGN KEY (house_id) REFERENCES houses(id) ON DELETE CASCADE,
    FOREIGN KEY (resident_id) REFERENCES residents(id) ON DELETE CASCADE,
    FOREIGN KEY (reviewed_by) REFERENCES users(id) ON DELETE SET NULL,
    UNIQUE KEY unique_permit_number_tenant (tenant_id, permit_number)
) ENGINE=InnoDB;

CREATE TABLE complaints (
    id INT AUTO_INCREMENT PRIMARY KEY,
    tenant_id INT NOT NULL,
    ticket_number VARCHAR(50) NOT NULL,
    resident_id INT,
    category VARCHAR(50) NOT NULL,
    subject VARCHAR(200) NOT NULL,
    content TEXT NOT NULL,
    priority ENUM('rendah','sedang','tinggi','darurat') DEFAULT 'sedang',
    is_anonymous TINYINT(1) DEFAULT 0,
    photo_url VARCHAR(255),
    status ENUM('baru','ditanggapi','proses','selesai') DEFAULT 'baru',
    assigned_to INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (tenant_id) REFERENCES tenants(id) ON DELETE CASCADE,
    FOREIGN KEY (resident_id) REFERENCES residents(id) ON DELETE SET NULL,
    FOREIGN KEY (assigned_to) REFERENCES users(id) ON DELETE SET NULL,
    UNIQUE KEY unique_complaint_ticket_tenant (tenant_id, ticket_number)
) ENGINE=InnoDB;

CREATE TABLE complaint_replies (
    id INT AUTO_INCREMENT PRIMARY KEY,
    tenant_id INT NOT NULL,
    complaint_id INT NOT NULL,
    user_id INT,
    message TEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (tenant_id) REFERENCES tenants(id) ON DELETE CASCADE,
    FOREIGN KEY (complaint_id) REFERENCES complaints(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB;

-- ============================================
-- 5. BANSOS
-- ============================================

CREATE TABLE bansos_programs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    tenant_id INT NOT NULL,
    name VARCHAR(100) NOT NULL,
    source VARCHAR(100),
    description TEXT,
    is_active TINYINT(1) DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (tenant_id) REFERENCES tenants(id) ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE bansos_recipients (
    id INT AUTO_INCREMENT PRIMARY KEY,
    tenant_id INT NOT NULL,
    resident_id INT NOT NULL,
    program_id INT NOT NULL,
    status ENUM('aktif','nonaktif','selesai') DEFAULT 'aktif',
    registered_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (tenant_id) REFERENCES tenants(id) ON DELETE CASCADE,
    FOREIGN KEY (resident_id) REFERENCES residents(id) ON DELETE CASCADE,
    FOREIGN KEY (program_id) REFERENCES bansos_programs(id) ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE bansos_distributions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    tenant_id INT NOT NULL,
    recipient_id INT NOT NULL,
    program_id INT NOT NULL,
    distribution_date DATE NOT NULL,
    amount_or_item VARCHAR(100),
    photo_proof VARCHAR(255),
    distributed_by INT,
    status ENUM('tersalurkan','gagal') DEFAULT 'tersalurkan',
    notes TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (tenant_id) REFERENCES tenants(id) ON DELETE CASCADE,
    FOREIGN KEY (recipient_id) REFERENCES bansos_recipients(id) ON DELETE CASCADE,
    FOREIGN KEY (program_id) REFERENCES bansos_programs(id) ON DELETE CASCADE,
    FOREIGN KEY (distributed_by) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB;

-- ============================================
-- 6. INVENTARIS
-- ============================================

CREATE TABLE inventory_items (
    id INT AUTO_INCREMENT PRIMARY KEY,
    tenant_id INT NOT NULL,
    item_name VARCHAR(100) NOT NULL,
    total_qty INT NOT NULL DEFAULT 0,
    available_qty INT NOT NULL DEFAULT 0,
    item_condition ENUM('baik','rusak_ringan','rusak_berat') DEFAULT 'baik',
    photo VARCHAR(255),
    description TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (tenant_id) REFERENCES tenants(id) ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE inventory_loans (
    id INT AUTO_INCREMENT PRIMARY KEY,
    tenant_id INT NOT NULL,
    item_id INT NOT NULL,
    resident_id INT NOT NULL,
    house_label VARCHAR(20),
    qty INT NOT NULL DEFAULT 1,
    purpose TEXT,
    loan_date DATE NOT NULL,
    expected_return DATE,
    actual_return DATE,
    status ENUM('dipinjam','dikembalikan','terlambat') DEFAULT 'dipinjam',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (tenant_id) REFERENCES tenants(id) ON DELETE CASCADE,
    FOREIGN KEY (item_id) REFERENCES inventory_items(id) ON DELETE CASCADE,
    FOREIGN KEY (resident_id) REFERENCES residents(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- ============================================
-- 7. KONTEN: Berita, Galeri
-- ============================================

CREATE TABLE news (
    id INT AUTO_INCREMENT PRIMARY KEY,
    tenant_id INT NOT NULL,
    title VARCHAR(200) NOT NULL,
    content TEXT NOT NULL,
    category ENUM('pengumuman','kegiatan','darurat','keuangan','umum') DEFAULT 'umum',
    image_url VARCHAR(255),
    target_block VARCHAR(50) DEFAULT 'semua',
    is_published TINYINT(1) DEFAULT 1,
    published_at DATETIME,
    author_id INT,
    views INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (tenant_id) REFERENCES tenants(id) ON DELETE CASCADE,
    FOREIGN KEY (author_id) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB;

CREATE TABLE news_comments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    tenant_id INT NOT NULL,
    news_id INT NOT NULL,
    user_id INT,
    comment TEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (tenant_id) REFERENCES tenants(id) ON DELETE CASCADE,
    FOREIGN KEY (news_id) REFERENCES news(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB;

CREATE TABLE galleries (
    id INT AUTO_INCREMENT PRIMARY KEY,
    tenant_id INT NOT NULL,
    title VARCHAR(200) NOT NULL,
    description TEXT,
    category VARCHAR(50),
    event_date DATE,
    created_by INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (tenant_id) REFERENCES tenants(id) ON DELETE CASCADE,
    FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB;

CREATE TABLE gallery_photos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    tenant_id INT NOT NULL,
    gallery_id INT NOT NULL,
    photo_url VARCHAR(255) NOT NULL,
    caption VARCHAR(200),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (tenant_id) REFERENCES tenants(id) ON DELETE CASCADE,
    FOREIGN KEY (gallery_id) REFERENCES galleries(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- ============================================
-- 8. KEAMANAN & PROGRAM KERJA
-- ============================================

CREATE TABLE panic_alerts (
    id INT AUTO_INCREMENT PRIMARY KEY,
    tenant_id INT NOT NULL,
    house_id INT,
    resident_id INT,
    alert_type ENUM('darurat','kebakaran','banjir','pencurian','medis','lainnya') DEFAULT 'darurat',
    description TEXT,
    latitude DECIMAL(10,7),
    longitude DECIMAL(10,7),
    status ENUM('aktif','ditangani','selesai','false_alarm') DEFAULT 'aktif',
    handled_by INT,
    handled_at DATETIME,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (tenant_id) REFERENCES tenants(id) ON DELETE CASCADE,
    FOREIGN KEY (house_id) REFERENCES houses(id) ON DELETE SET NULL,
    FOREIGN KEY (resident_id) REFERENCES residents(id) ON DELETE SET NULL,
    FOREIGN KEY (handled_by) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB;

CREATE TABLE program_kerja (
    id INT AUTO_INCREMENT PRIMARY KEY,
    tenant_id INT NOT NULL,
    title VARCHAR(200) NOT NULL,
    quarter VARCHAR(20),
    year INT NOT NULL,
    progress INT DEFAULT 0,
    status ENUM('belum','berjalan','selesai') DEFAULT 'belum',
    pic VARCHAR(100),
    budget DECIMAL(12,2),
    description TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (tenant_id) REFERENCES tenants(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- ============================================
-- 9. AUDIT LOG
-- ============================================

CREATE TABLE audit_logs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    tenant_id INT NOT NULL,
    user_id INT,
    action VARCHAR(50) NOT NULL,
    table_name VARCHAR(50),
    record_id INT,
    old_values JSON,
    new_values JSON,
    ip_address VARCHAR(45),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (tenant_id) REFERENCES tenants(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB;

-- ============================================
-- INDEXES for Performance
-- ============================================

CREATE INDEX idx_all_tenant ON tenants(id);
CREATE INDEX idx_residents_house ON residents(tenant_id, house_id);
CREATE INDEX idx_residents_family ON residents(tenant_id, family_id);
CREATE INDEX idx_residents_nik ON residents(tenant_id, nik);
CREATE INDEX idx_houses_block ON houses(tenant_id, block_id);
CREATE INDEX idx_invoices_house ON invoices(tenant_id, house_id);
CREATE INDEX idx_invoices_period ON invoices(tenant_id, period);
CREATE INDEX idx_invoices_status ON invoices(tenant_id, status);
CREATE INDEX idx_payments_invoice ON payments(tenant_id, invoice_id);
CREATE INDEX idx_cashflows_date ON cashflows(tenant_id, transaction_date);
CREATE INDEX idx_news_published ON news(tenant_id, is_published, published_at);
CREATE INDEX idx_complaints_status ON complaints(tenant_id, status);
CREATE INDEX idx_visitors_checkin ON visitors(tenant_id, check_in);
CREATE INDEX idx_audit_user ON audit_logs(tenant_id, user_id, created_at);
