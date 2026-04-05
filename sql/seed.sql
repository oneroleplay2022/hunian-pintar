-- ============================================
-- WargaKu - Seed Data (Demo SaaS)
-- ============================================

USE wargaku;

-- ============================================
-- 0. Tenants & Subscriptions
-- ============================================

INSERT INTO tenants (id, name, address, total_houses, subscription_status, expired_at) VALUES 
(1, 'Perumahan Graha Indah', 'Jl. Raya Graha Indah No. 1, Kota Bandung', 120, 'active', '2027-01-01');

INSERT INTO subscriptions (tenant_id, amount, payment_status, payment_method, expired_at) VALUES 
(1, 1500000, 'paid', 'transfer', '2027-01-01');

-- ============================================
-- 1. Users (password: admin123 / warga123 / password)
-- ============================================

INSERT INTO users (tenant_id, name, email, phone, password, role) VALUES
(NULL, 'Dev WargaKu (SaaS)', 'superadmin@wargaku.id', '08000000000', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'superadmin'),
(1, 'Admin Satu', 'admin@wargaku.id', '081234567890', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin'),
(1, 'Budi Santoso', 'budi@wargaku.id', '081234567891', '$2y$10$LKaJMz7JG5ZKtVL6bSODdu3O5GPjEH1SvFQ0NP.PXjUBDaHVaO6Gy', 'pengurus'),
(1, 'Siti Rahma', 'siti@wargaku.id', '081234567892', '$2y$10$LKaJMz7JG5ZKtVL6bSODdu3O5GPjEH1SvFQ0NP.PXjUBDaHVaO6Gy', 'warga'),
(1, 'Ahmad Hidayat', 'ahmad@wargaku.id', '081234567893', '$2y$10$LKaJMz7JG5ZKtVL6bSODdu3O5GPjEH1SvFQ0NP.PXjUBDaHVaO6Gy', 'warga'),
(1, 'Dewi Lestari', 'dewi@wargaku.id', '081234567894', '$2y$10$LKaJMz7JG5ZKtVL6bSODdu3O5GPjEH1SvFQ0NP.PXjUBDaHVaO6Gy', 'warga'),
(1, 'Irwan Hakim', 'irwan@wargaku.id', '081234567895', '$2y$10$LKaJMz7JG5ZKtVL6bSODdu3O5GPjEH1SvFQ0NP.PXjUBDaHVaO6Gy', 'kolektor'),
(1, 'Pak Jaga', 'jaga@wargaku.id', '081234567896', '$2y$10$LKaJMz7JG5ZKtVL6bSODdu3O5GPjEH1SvFQ0NP.PXjUBDaHVaO6Gy', 'security');

-- ============================================
-- 2. Settings
-- ============================================

INSERT INTO settings (tenant_id, setting_key, setting_value) VALUES
(1, 'nama_perumahan', 'Perumahan Graha Indah'),
(1, 'alamat', 'Jl. Raya Graha Indah No. 1, Kota Bandung'),
(1, 'ketua_rt', 'Admin Satu'),
(1, 'no_rt', 'RT 005 / RW 012'),
(1, 'telepon', '022-1234567'),
(1, 'email_pengurus', 'admin@wargaku.id'),
(1, 'total_rumah', '120'),
(1, 'total_warga', '450'),
(1, 'tata_tertib', 'Setiap tamu wajib melapor ke pos keamanan|Dilarang membuat keributan setelah pukul 22.00 WIB|Setiap rumah wajib memasang stiker kendaraan resmi|Portal gerbang ditutup pukul 23.00 - 05.00 WIB'),
(1, 'visi_misi', 'Mewujudkan lingkungan yang aman, nyaman, dan transparan');

-- ============================================
-- 3. Blocks & Houses
-- ============================================

INSERT INTO blocks (tenant_id, block_name, description, total_houses) VALUES
(1, 'Blok A', 'Blok utama dekat gerbang', 30),
(1, 'Blok B', 'Blok tengah', 30),
(1, 'Blok C', 'Blok belakang dekat taman', 30),
(1, 'Blok D', 'Blok baru (extension)', 30);

INSERT INTO houses (tenant_id, block_id, house_number, status, owner_name, owner_phone, house_type, area_m2) VALUES
(1, 1, 'A/01', 'berpenghuni', 'Budi Santoso', '081234567891', 'Type 45', 90),
(1, 1, 'A/02', 'berpenghuni', 'Siti Rahma', '081234567892', 'Type 36', 72),
(1, 1, 'A/03', 'berpenghuni', 'Ahmad Hidayat', '081234567893', 'Type 45', 90),
(1, 1, 'A/04', 'berpenghuni', 'Dewi Lestari', '081234567894', 'Type 54', 108),
(1, 1, 'A/05', 'kosong', NULL, NULL, 'Type 36', 72),
(1, 1, 'A/06', 'berpenghuni', 'Tono Sukmono', '081234567896', 'Type 36', 72),
(1, 1, 'A/07', 'dikontrakkan', 'Hendra Pratama', '081234567897', 'Type 45', 90),
(1, 1, 'A/08', 'berpenghuni', 'Rina Wati', '081234567898', 'Type 54', 108),
(1, 1, 'A/09', 'berpenghuni', 'Joko Widodo', '081234567899', 'Type 36', 72),
(1, 1, 'A/10', 'berpenghuni', 'Maria Santos', '081234567800', 'Type 45', 90),
(1, 1, 'A/11', 'berpenghuni', 'Dian Agung', '081234567801', 'Type 36', 72),
(1, 1, 'A/12', 'berpenghuni', 'Fajar Nugroho', '081234567802', 'Type 54', 108),
(1, 2, 'B/01', 'berpenghuni', 'Irwan Hakim', '081234567895', 'Type 45', 90),
(1, 2, 'B/02', 'berpenghuni', 'Sari Indah', '081234567810', 'Type 36', 72),
(1, 2, 'B/03', 'dikontrakkan', 'Andi Wijaya', '081234567811', 'Type 45', 90),
(1, 2, 'B/04', 'berpenghuni', 'Nina Safitri', '081234567812', 'Type 36', 72),
(1, 2, 'B/05', 'kosong', NULL, NULL, 'Type 54', 108),
(1, 2, 'B/06', 'berpenghuni', 'Rudi Hartono', '081234567813', 'Type 45', 90);

-- ============================================
-- 4. Families
-- ============================================

INSERT INTO families (tenant_id, house_id, kk_number, head_name, address_on_kk) VALUES
(1, 1, '3201xxxxxxxxxxxx', 'Budi Santoso', 'Jl. Graha Indah Blok A/01'),
(1, 2, '3201xxxxxxxxxxxx', 'Siti Rahma', 'Jl. Graha Indah Blok A/02'),
(1, 3, '3201xxxxxxxxxxxx', 'Ahmad Hidayat', 'Jl. Graha Indah Blok A/03'),
(1, 4, '3201xxxxxxxxxxxx', 'Dewi Lestari', 'Jl. Graha Indah Blok A/04'),
(1, 6, '3201xxxxxxxxxxxx', 'Tono Sukmono', 'Jl. Graha Indah Blok A/06'),
(1, 8, '3201xxxxxxxxxxxx', 'Rina Wati', 'Jl. Graha Indah Blok A/08'),
(1, 9, '3201xxxxxxxxxxxx', 'Joko Widodo', 'Jl. Graha Indah Blok A/09'),
(1, 10, '3201xxxxxxxxxxxx', 'Maria Santos', 'Jl. Graha Indah Blok A/10'),
(1, 13, '3201xxxxxxxxxxxx', 'Irwan Hakim', 'Jl. Graha Indah Blok B/01'),
(1, 14, '3201xxxxxxxxxxxx', 'Sari Indah', 'Jl. Graha Indah Blok B/02');

-- ============================================
-- 5. Residents
-- ============================================

INSERT INTO residents (tenant_id, family_id, house_id, nik, full_name, gender, birth_place, birth_date, religion, marital_status, blood_type, education, profession, phone, domicile_status, family_status) VALUES
(1, 1, 1, '3201010101900001', 'Budi Santoso', 'L', 'Bandung', '1990-05-15', 'Islam', 'Menikah', 'A', 'S1', 'PNS', '081234567891', 'domisili', 'Kepala Keluarga'),
(1, 1, 1, '3201010101920002', 'Ani Santoso', 'P', 'Jakarta', '1992-08-20', 'Islam', 'Menikah', 'B', 'S1', 'Guru', '081234567891', 'domisili', 'Istri'),
(1, 1, 1, '3201010101150003', 'Rafa Santoso', 'L', 'Bandung', '2015-03-10', 'Islam', 'Belum Menikah', 'A', 'SD', 'Pelajar', NULL, 'domisili', 'Anak'),
(1, 2, 2, '3201010101880004', 'Siti Rahma', 'P', 'Surabaya', '1988-12-01', 'Islam', 'Janda', 'O', 'S2', 'Dosen', '081234567892', 'domisili', 'Kepala Keluarga'),
(1, 2, 2, '3201010101100005', 'Zahra Rahma', 'P', 'Bandung', '2010-06-25', 'Islam', 'Belum Menikah', 'O', 'SMP', 'Pelajar', NULL, 'domisili', 'Anak'),
(1, 3, 3, '3201010101850006', 'Ahmad Hidayat', 'L', 'Semarang', '1985-01-30', 'Islam', 'Menikah', 'AB', 'S1', 'Wiraswasta', '081234567893', 'domisili', 'Kepala Keluarga'),
(1, 3, 3, '3201010101870007', 'Lisa Hidayat', 'P', 'Bandung', '1987-07-12', 'Islam', 'Menikah', 'B', 'D3', 'Ibu Rumah Tangga', '081234567893', 'domisili', 'Istri'),
(1, 3, 3, '3201010101120008', 'Dimas Hidayat', 'L', 'Bandung', '2012-09-05', 'Islam', 'Belum Menikah', 'AB', 'SD', 'Pelajar', NULL, 'domisili', 'Anak'),
(1, 3, 3, '3201010101180009', 'Nadia Hidayat', 'P', 'Bandung', '2018-04-22', 'Islam', 'Belum Menikah', '-', 'Belum Sekolah', '-', NULL, 'domisili', 'Anak'),
(1, 4, 4, '3201010101910010', 'Dewi Lestari', 'P', 'Yogyakarta', '1991-11-08', 'Kristen', 'Menikah', 'A', 'S1', 'Desainer', '081234567894', 'domisili', 'Istri'),
(1, 4, 4, '3201010101890011', 'Roni Setiawan', 'L', 'Bandung', '1989-02-14', 'Kristen', 'Menikah', 'B', 'S1', 'Programmer', '081234567894', 'domisili', 'Kepala Keluarga'),
(1, 5, 6, '3201010101780012', 'Tono Sukmono', 'L', 'Medan', '1978-06-30', 'Islam', 'Menikah', 'O', 'SMA', 'Pedagang', '081234567896', 'domisili', 'Kepala Keluarga'),
(1, 6, 8, '3201010101950013', 'Rina Wati', 'P', 'Bandung', '1995-10-15', 'Islam', 'Menikah', 'A', 'S1', 'Apoteker', '081234567898', 'domisili', 'Istri'),
(1, 7, 9, '3201010101800014', 'Joko Widodo', 'L', 'Solo', '1980-03-25', 'Islam', 'Menikah', 'B', 'S2', 'Konsultan', '081234567899', 'domisili', 'Kepala Keluarga'),
(1, 8, 10, '3201010101830015', 'Maria Santos', 'P', 'Manado', '1983-09-18', 'Katolik', 'Menikah', 'AB', 'S1', 'Dokter', '081234567800', 'domisili', 'Istri'),
(1, 9, 13, '3201010101820016', 'Irwan Hakim', 'L', 'Bandung', '1982-04-10', 'Islam', 'Menikah', 'O', 'S1', 'Kolektor', '081234567895', 'domisili', 'Kepala Keluarga'),
(1, 9, 13, '3201010101850017', 'Yuni Hakim', 'P', 'Bandung', '1985-12-28', 'Islam', 'Menikah', 'A', 'D3', 'Akuntan', '081234567895', 'domisili', 'Istri'),
(1, 9, 13, '3201010101100018', 'Rizky Hakim', 'L', 'Bandung', '2010-07-15', 'Islam', 'Belum Menikah', 'O', 'SMP', 'Pelajar', NULL, 'domisili', 'Anak'),
(1, 9, 13, '3201010101130019', 'Alya Hakim', 'P', 'Bandung', '2013-01-09', 'Islam', 'Belum Menikah', 'A', 'SD', 'Pelajar', NULL, 'domisili', 'Anak'),
(1, 9, 13, '3201010101170020', 'Kevin Hakim', 'L', 'Bandung', '2017-05-20', 'Islam', 'Belum Menikah', '-', 'TK', 'Pelajar', NULL, 'domisili', 'Anak'),
(1, 10, 14, '3201010101900021', 'Sari Indah', 'P', 'Bandung', '1990-08-05', 'Islam', 'Menikah', 'B', 'S1', 'Perawat', '081234567810', 'domisili', 'Istri');

-- ============================================
-- 6. Vehicles
-- ============================================

INSERT INTO vehicles (tenant_id, house_id, resident_id, plate_number, type, brand, model, color, sticker_number, sticker_status) VALUES
(1, 1, 1, 'D 1234 AB', 'mobil', 'Toyota', 'Avanza', 'Hitam', 'STK-001', 'aktif'),
(1, 1, 2, 'D 5678 CD', 'motor', 'Honda', 'Beat', 'Putih', 'STK-002', 'aktif'),
(1, 3, 6, 'D 9012 EF', 'mobil', 'Daihatsu', 'Xenia', 'Silver', 'STK-003', 'aktif'),
(1, 4, 11, 'D 3456 GH', 'mobil', 'Honda', 'Brio', 'Merah', 'STK-004', 'aktif'),
(1, 4, 10, 'D 7890 IJ', 'motor', 'Yamaha', 'NMAX', 'Biru', 'STK-005', 'aktif'),
(1, 13, 16, 'D 2345 KL', 'motor', 'Honda', 'Vario', 'Hitam', 'STK-006', 'aktif');

-- ============================================
-- 7. Invoice Types & Invoices
-- ============================================

INSERT INTO invoice_types (tenant_id, name, amount, is_mandatory, description) VALUES
(1, 'IPL Bulanan', 350000, 1, 'Iuran Pengelolaan Lingkungan bulanan'),
(1, 'Iuran Sampah', 50000, 1, 'Biaya pengelolaan sampah bulanan'),
(1, 'Iuran Keamanan', 100000, 1, 'Biaya keamanan dan patroli');

-- Invoices for March 2026
INSERT INTO invoices (tenant_id, invoice_number, house_id, invoice_type_id, period, amount, due_date, status, created_by) VALUES
(1, 'INV-2026-03-001', 1, 1, '2026-03', 350000, '2026-03-15', 'lunas', 1),
(1, 'INV-2026-03-002', 2, 1, '2026-03', 350000, '2026-03-15', 'belum_bayar', 1),
(1, 'INV-2026-03-003', 3, 1, '2026-03', 350000, '2026-03-15', 'lunas', 1),
(1, 'INV-2026-03-004', 4, 1, '2026-03', 350000, '2026-03-15', 'belum_bayar', 1),
(1, 'INV-2026-03-005', 6, 1, '2026-03', 350000, '2026-03-15', 'belum_bayar', 1),
(1, 'INV-2026-03-006', 8, 1, '2026-03', 350000, '2026-03-15', 'lunas', 1),
(1, 'INV-2026-03-007', 9, 1, '2026-03', 350000, '2026-03-15', 'belum_bayar', 1),
(1, 'INV-2026-03-008', 10, 1, '2026-03', 350000, '2026-03-15', 'lunas', 1),
(1, 'INV-2026-03-009', 13, 1, '2026-03', 350000, '2026-03-15', 'belum_bayar', 1),
(1, 'INV-2026-03-010', 14, 1, '2026-03', 350000, '2026-03-15', 'lunas', 1);

-- ============================================
-- 8. Payments
-- ============================================

INSERT INTO payments (tenant_id, invoice_id, transaction_id, method, amount, paid_at, verified_by) VALUES
(1, 1, 'TRX-20260301-001', 'va', 350000, '2026-03-05 10:30:00', 1),
(1, 3, 'TRX-20260303-001', 'qris', 350000, '2026-03-07 14:15:00', 1),
(1, 6, 'TRX-20260306-001', 'tunai', 350000, '2026-03-10 09:00:00', 6),
(1, 8, 'TRX-20260308-001', 'transfer', 350000, '2026-03-12 16:45:00', 1),
(1, 10, 'TRX-20260310-001', 'va', 350000, '2026-03-14 11:20:00', 1);

-- ============================================
-- 9. Cashflows
-- ============================================

INSERT INTO cashflows (tenant_id, type, category, amount, description, transaction_date, created_by) VALUES
(1, 'masuk', 'IPL Bulanan', 1750000, 'Penerimaan IPL Maret 2026 (5 rumah)', '2026-03-14', 1),
(1, 'keluar', 'Gaji Security', 2500000, 'Gaji Pak Jaga bulan Maret', '2026-03-01', 1),
(1, 'keluar', 'Perawatan Taman', 750000, 'Pemotongan rumput dan perawatan taman', '2026-03-05', 1),
(1, 'keluar', 'Listrik PJU', 450000, 'Pembayaran listrik lampu jalan', '2026-03-10', 1),
(1, 'masuk', 'Sewa Aula', 500000, 'Sewa aula RT untuk acara warga', '2026-03-08', 1);

-- ============================================
-- 10. News
-- ============================================

INSERT INTO news (tenant_id, title, content, category, target_block, published_at, author_id) VALUES
(1, 'Jadwal Kerja Bakti Bulanan', 'Assalamualaikum warga Graha Indah.\n\nKami mengundang seluruh warga untuk berpartisipasi dalam kerja bakti bulanan yang akan dilaksanakan pada:\n\n📅 Hari: Minggu, 30 Maret 2026\n⏰ Waktu: 07:00 - 11:00 WIB\n📍 Lokasi: Seluruh area perumahan\n\nKegiatan meliputi:\n1. Pembersihan selokan\n2. Pemotongan rumput area umum\n3. Pengecatan speed bump\n\nMari bersama-sama menjaga kebersihan lingkungan kita!', 'kegiatan', 'semua', '2026-03-25 08:00:00', 1),
(1, 'Pengumuman: Perbaikan Pipa Air Blok A', 'Diberitahukan kepada warga Blok A bahwa akan dilakukan perbaikan pipa air utama pada:\n\n📅 Selasa, 1 April 2026\n⏰ Pukul 09:00 - 15:00 WIB\n\nSelama perbaikan, aliran air di Blok A akan terhenti sementara. Mohon menyiapkan cadangan air.\n\nTerima kasih atas pengertiannya.', 'pengumuman', 'Blok A', '2026-03-26 10:00:00', 1),
(1, 'Laporan Keuangan Februari 2026', 'Berikut ringkasan keuangan RT bulan Februari 2026:\n\n💰 Total Pemasukan: Rp 8.500.000\n💸 Total Pengeluaran: Rp 5.200.000\n📊 Saldo Akhir: Rp 12.800.000\n\nLaporan lengkap dapat diakses melalui menu Kas & Transparansi.', 'keuangan', 'semua', '2026-03-01 09:00:00', 1),
(1, 'Waspada Cuaca Ekstrem!', '⚠️ BMKG mengeluarkan peringatan cuaca ekstrem untuk wilayah Bandung dan sekitarnya mulai 28-31 Maret 2026.\n\nHarap warga melakukan langkah antisipasi:\n1. Pastikan saluran air rumah tidak tersumbat\n2. Amankan barang-barang di luar rumah\n3. Siapkan nomor darurat RT/Security\n\nJika terjadi banjir, segera hubungi pos keamanan atau tekan Panic Button di aplikasi.', 'darurat', 'semua', '2026-03-27 15:00:00', 1),
(1, 'Lomba HUT RI ke-81', 'Dalam rangka memeriahkan HUT RI ke-81, pengurus RT mengadakan berbagai lomba:\n\n🏃 Lomba lari karung\n🥚 Lomba balap kelereng\n🎨 Lomba mewarnai (anak-anak)\n🍽️ Lomba masak\n\nPendaftaran dibuka sampai 10 Agustus 2026. Hubungi sekretaris RT untuk mendaftar.', 'kegiatan', 'semua', '2026-03-20 12:00:00', 1),
(1, 'Tips Hemat Energi di Rumah', 'Menjelang musim kemarau, berikut tips menghemat energi:\n\n1. Matikan lampu saat tidak digunakan\n2. Gunakan AC pada suhu 24-26°C\n3. Cabut charger jika tidak dipakai\n4. Gunakan lampu LED\n5. Manfaatkan cahaya alami siang hari', 'umum', 'semua', '2026-03-22 14:00:00', 1);

-- ============================================
-- 11. Galleries
-- ============================================

INSERT INTO galleries (tenant_id, title, description, category, event_date, created_by) VALUES
(1, 'Kerja Bakti Februari 2026', 'Dokumentasi kegiatan kerja bakti warga bulan Februari', 'Kerja Bakti', '2026-02-23', 1),
(1, 'Perayaan Tahun Baru 2026', 'Momen kebersamaan warga di malam tahun baru', 'Perayaan', '2025-12-31', 1),
(1, 'Posyandu Maret 2026', 'Kegiatan posyandu balita dan lansia', 'Kesehatan', '2026-03-15', 1);

-- ============================================
-- 12. Inventory
-- ============================================

INSERT INTO inventory_items (tenant_id, item_name, total_qty, available_qty, item_condition, description) VALUES
(1, 'Tenda 4x6m', 5, 3, 'baik', 'Tenda ukuran 4x6 meter untuk acara'),
(1, 'Kursi Lipat', 100, 85, 'baik', 'Kursi lipat plastik'),
(1, 'Meja Panjang', 20, 18, 'baik', 'Meja lipat panjang 120cm'),
(1, 'Sound System', 2, 2, 'baik', 'Paket sound system lengkap'),
(1, 'Gerobak Sampah', 4, 4, 'rusak_ringan', 'Gerobak sampah komunal'),
(1, 'Tangga Aluminium', 2, 2, 'baik', 'Tangga lipat aluminium 3m'),
(1, 'Generator Set', 1, 1, 'baik', 'Generator 5000 Watt');

-- ============================================
-- 13. Service Requests (Surat)
-- ============================================

INSERT INTO service_requests (tenant_id, request_number, resident_id, type, purpose, status, created_at) VALUES
(1, 'SRT-20260315-001', 1, 'SK Domisili', 'Untuk keperluan administrasi bank', 'selesai', '2026-03-15 10:00:00'),
(1, 'SRT-20260318-002', 4, 'Pengantar RT', 'Pengantar untuk pembuatan KTP baru', 'diproses', '2026-03-18 09:30:00'),
(1, 'SRT-20260320-003', 6, 'SK Usaha', 'Surat keterangan usaha untuk izin dagang', 'diajukan', '2026-03-20 14:00:00'),
(1, 'SRT-20260322-004', 10, 'SK Domisili', 'Untuk pendaftaran sekolah anak', 'diajukan', '2026-03-22 11:00:00');

-- ============================================
-- 14. Permits
-- ============================================

INSERT INTO permits (tenant_id, permit_number, house_id, resident_id, type, description, start_date, end_date, status) VALUES
(1, 'IZN-20260301-001', 3, 6, 'Renovasi Bangunan', 'Renovasi dapur dan penambahan canopy', '2026-03-01', '2026-04-30', 'disetujui'),
(1, 'IZN-20260315-002', 13, 16, 'Acara / Keramaian', 'Acara hajatan pernikahan', '2026-04-10', '2026-04-11', 'diajukan'),
(1, 'IZN-20260320-003', 8, 13, 'Pemasangan Instalasi', 'Pemasangan panel surya di atap', '2026-04-01', '2026-04-05', 'diajukan');

-- ============================================
-- 15. Complaints (Pengaduan)
-- ============================================

INSERT INTO complaints (tenant_id, ticket_number, resident_id, category, subject, content, priority, status) VALUES
(1, 'TKT-20260310-001', 4, 'Infrastruktur', 'Lampu jalan Blok A mati', 'Lampu jalan di depan rumah A/04 sudah mati selama 3 hari. Mohon segera diperbaiki karena sangat gelap di malam hari.', 'sedang', 'ditanggapi'),
(1, 'TKT-20260315-002', 14, 'Kebersihan', 'Sampah menumpuk di pojok Blok B', 'Sampah di TPS pojok Blok B sudah menumpuk dan berbau. Pengangkutan terakhir sudah 4 hari lalu.', 'tinggi', 'baru'),
(1, 'TKT-20260320-003', 1, 'Keamanan', 'CCTV Gerbang Utama error', 'CCTV di gerbang utama nampak offline dari dashboard monitor. Mohon segera dicek.', 'tinggi', 'proses');

-- ============================================
-- 16. Bansos
-- ============================================

INSERT INTO bansos_programs (tenant_id, name, source, description) VALUES
(1, 'PKH', 'Pemerintah Pusat', 'Program Keluarga Harapan'),
(1, 'BPNT', 'Pemerintah Pusat', 'Bantuan Pangan Non-Tunai'),
(1, 'Zakat RT', 'Internal RT', 'Program zakat fitrah dari warga'),
(1, 'Bantuan Hari Raya', 'Internal RT', 'Bantuan menjelang hari raya besar');

INSERT INTO bansos_recipients (tenant_id, resident_id, program_id, status) VALUES
(1, 12, 1, 'aktif'),
(1, 4, 2, 'aktif'),
(1, 12, 3, 'aktif');

INSERT INTO bansos_distributions (tenant_id, recipient_id, program_id, distribution_date, amount_or_item, distributed_by, status) VALUES
(1, 1, 1, '2026-01-15', 'Rp 500.000', 1, 'tersalurkan'),
(1, 2, 2, '2026-02-10', 'Paket Sembako', 1, 'tersalurkan');

-- ============================================
-- 17. Visitors
-- ============================================

INSERT INTO visitors (tenant_id, house_id, guest_name, guest_id_number, guest_phone, purpose, check_in, check_out, checked_by) VALUES
(1, 1, 'Eko Prasetyo', '3201010101900099', '081987654321', 'Silaturahmi', '2026-03-25 09:00:00', '2026-03-25 12:00:00', 7),
(1, 3, 'Sinta Dewi', '3201010101920088', '081987654322', 'Delivery paket', '2026-03-26 14:30:00', '2026-03-26 14:45:00', 7),
(1, 13, 'Pak Tarjo', NULL, '081987654323', 'Tukang service AC', '2026-03-27 10:00:00', NULL, 7);

-- ============================================
-- 18. Mutations
-- ============================================

INSERT INTO mutations (tenant_id, resident_id, resident_name, house_id, type, family_count, mutation_date, origin_destination, description, created_by) VALUES
(1, NULL, 'Hendra Pratama', 7, 'pindah', 3, '2026-02-28', 'Pindah ke Jakarta', 'Pindah karena pekerjaan', 1),
(1, NULL, 'Agus Setiawan', 5, 'masuk', 4, '2026-03-15', 'Dari Surabaya', 'Pendatang baru kontrak rumah A/07', 1);

-- ============================================
-- 19. Businesses
-- ============================================

INSERT INTO businesses (tenant_id, resident_id, house_id, business_name, category, description, whatsapp, open_days, open_hours, status) VALUES
(1, 12, 6, 'Warung Makan Bu Tono', 'Kuliner', 'Nasi Padang dan masakan rumahan', '081234567896', 'Sen-Sab', '08:00 - 20:00', 'aktif'),
(1, 6, 3, 'Toko Kelontong Hidayat', 'Toko', 'Kebutuhan harian dan ATK', '081234567893', 'Setiap Hari', '06:00 - 22:00', 'aktif'),
(1, 15, 10, 'Klinik dr. Maria', 'Kesehatan', 'Praktek dokter umum', '081234567800', 'Sen-Jum', '16:00 - 20:00', 'aktif');

-- ============================================
-- 20. Program Kerja
-- ============================================

INSERT INTO program_kerja (tenant_id, title, quarter, year, progress, status, pic, budget) VALUES
(1, 'Perbaikan Jalan Blok A-B', 'Q1', 2026, 75, 'berjalan', 'Budi Santoso', 15000000),
(1, 'Pengadaan CCTV Tambahan', 'Q1', 2026, 100, 'selesai', 'Admin Satu', 8000000),
(1, 'Penghijauan Taman RT', 'Q2', 2026, 0, 'belum', 'Dewi Lestari', 5000000),
(1, 'Pelatihan UMKM Warga', 'Q2', 2026, 30, 'berjalan', 'Irwan Hakim', 3000000),
(1, 'Renovasi Pos Keamanan', 'Q3', 2026, 0, 'belum', 'Ahmad Hidayat', 10000000);
