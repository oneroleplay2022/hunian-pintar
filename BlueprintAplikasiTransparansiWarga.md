# **Blueprint Aplikasi Transparansi & Manajemen Warga (SaaS)**

Aplikasi ini dirancang sebagai platform *all-in-one* untuk manajemen lingkungan (Cluster, Perumahan, Pemukiman) dengan fokus pada transparansi keuangan dan kemudahan administrasi.

## **1\. Arsitektur Multi-Tenant & Aplikasi (Delivery Method)**

Sistem akan diakses melalui tiga pintu utama sesuai peran pengguna:

1. **APK RT-Online (Warga):** Aplikasi utama untuk warga (Berita, Iuran, Panic Button, Surat).  
2. **APK Kolektor Iuran (Petugas):** Khusus petugas penagih untuk input pembayaran tunai di lapangan (otomatis sinkron ke kas).  
3. **APK Ronda (Security/Warga):** Fitur absensi patroli, laporan kejadian, dan pantau CCTV.  
4. **Web Admin Dashboard:** Untuk pengurus (RT/RW/Developer) mengelola data kependudukan dan keuangan yang kompleks.

## **2\. Pemetaan Fitur (Feature Matrix)**

Saya telah mengelompokkan 50+ fitur permintaan Anda ke dalam 6 modul utama:

### **I. Modul Informasi & Organisasi (Governance)**

* **Profil Lingkungan:** Tata Tertib digital, Struktur Organisasi (interaktif), dan Program Kerja tahunan.  
* **Galeri & Berita:** Foto Gallery kegiatan warga dan portal Berita Online/Pengumuman.  
* **Aset & Inventaris:** Pendataan Barang Inventaris (tenda, kursi, alat pertukangan) beserta sistem peminjamannya.

### **II. Modul Kependudukan & Sensus Digital (Big Data Warga)**

* **Pendataan Dasar:** Data Rumah, Warga, Keluarga, dan Kepala Keluarga.  
* **Log Mutasi:** Otomasi data Warga Pindah, Meninggal, dan Pendatang baru.  
* **Sistem Tamu:** Pendataan Tamu Warga (Check-in/Check-out via aplikasi).  
* **Logistik Warga:** Pendataan Kendaraan (untuk stiker akses) dan Usaha/Lapak warga.  
* **Informasi Kategori Spesifik (Dashboard Statistik):**  
  * Filter warga: Domisili Setempat, Domisili Luar, KK Luar.  
  * Kategori khusus: Warga Berhak Pemilu, Berdasarkan Usia (Balita/Lansia), Kurang Mampu, W.N.A, dan Profesi.  
  * **Statistik Kependudukan:** Grafik otomatis dari seluruh data di atas.

### **III. Modul Keuangan & Pembayaran (Transparansi Total)**

* **Sistem Iuran:** Iuran Bulanan IPL, sumbangan sukarela, dan denda (jika ada).  
* **Payment Gateway:** Pembayaran via **Virtual Account & QRIS** (Nama VA: PT Anda \- Nama Perumahan).  
* **Laporan Real-time:** \* Laporan Pembayaran & Tunggakan (per rumah).  
  * Rekapitulasi Iuran & Saldo Akhir.  
  * **Transparansi Kas:** Input Transaksi Kas, Upload Bukti Pengeluaran (Kuitansi), dan Laporan Cashflow/Bulanan yang bisa di-download warga.

### **IV. Modul Pelayanan Publik (E-Government RT)**

* **E-Surat:** Pengajuan Surat Pengantar / Keterangan secara mandiri.  
* **Legalitas Digital:** Tandatangan Digital pengurus dan sistem Upload Berkas persyaratan.  
* **Perizinan:** Izin Pembangunan/Renovasi dan Izin Keramaian (hajatan, dll).  
* **Layanan Pihak ke-3:** Integrasi kontak/pemesanan Gas, Air Minum, Laundry, dan jasa lingkungan lainnya.  
* **Komunikasi:** Saran & Keluhan warga (Ticketing system) dan E-Mail/WhatsApp Notifikasi.

### **V. Modul Keamanan & Kedaruratan (Safety First)**

* **Panic Button:** Tombol darurat yang mengirim notifikasi ke Pos Security dan HP warga sekitar.  
* **Early Warning System:** Notifikasi khusus untuk info **Banjir** atau cuaca ekstrem.  
* **Monitoring:** Pantau CCTV lingkungan langsung dari aplikasi.  
* **Operasional Keamanan:** Log digital Pos Security, Portal (buka-tutup tamu), dan fitur Ronda Malam (Check-point GPS).

### **VI. Modul Bantuan Sosial**

* **Manajemen Bansos:** Pendataan warga penerima Bantuan Sosial agar distribusi tepat sasaran dan transparan.

## **3\. Alur Pembayaran & Bisnis (SaaS Model)**

### **A. Tagihan Perumahan ke Anda (B2B)**

* **Pricing:** Tagihan otomatis flat per rumah (misal: 100 rumah x Rp2.000).  
* **Metode:** VA/QRIS atas nama perusahaan Anda.  
* **Siklus:** Pilihan Bulanan atau Tahunan (diskon untuk bayar tahunan).

### **B. Tagihan Warga ke RT (B2C)**

* **Mekanisme:** Warga bayar ke VA/QRIS yang Anda sediakan (Nama Merchant tetap membawa nama perusahaan Anda untuk legalitas bank, namun sub-label nama perumahan mereka).  
* **Settlement:** Dana terkumpul bisa ditarik (*disburse*) oleh bendahara RT ke rekening bank mereka kapan saja.

## **4\. Tambahan Masukan untuk Pengembangan**

1. **Fitur "Rumah Kosong":** Menambahkan status pada pendataan rumah untuk membedakan rumah berpenghuni, kosong, atau dikontrakkan. Ini penting untuk koordinasi keamanan dan tagihan IPL.  
2. **Log Audit Keuangan:** Mengingat tujuan utama adalah transparansi, tambahkan fitur yang mencatat siapa yang mengedit data keuangan untuk menghindari kecurangan admin.  
3. **Offline Mode untuk APK Ronda:** Petugas ronda seringkali berada di area yang susah sinyal. Pastikan data ronda tersimpan lokal dan sinkron saat ada internet.  
4. **Broadcast Berbasis Lokasi:** Kemampuan admin untuk mengirim pesan hanya ke blok tertentu (misal: "Hanya Blok A: Ada perbaikan pipa air").

## **5\. Rencana Transisi (Home Server ke Produksi)**

* **Tahap 1 (Dev):** Development menggunakan Docker di Home Server.  
* **Tahap 2 (Uji Coba):** Implementasi di 1 perumahan (Pilot Project) menggunakan tunnel (Cloudflare/Ngrok).  
* **Tahap 3 (Scale):** Migrasi ke Cloud VPS (seperti DigitalOcean atau Google Cloud) saat user mulai bertambah untuk menjamin *uptime* 24/7.

# **Skema Database: Aplikasi Transparansi Warga (SaaS)**

Skema ini dirancang menggunakan pendekatan relasional (PostgreSQL/MySQL). Setiap tabel (kecuali tabel sistem) wajib memiliki tenant\_id untuk memisahkan data antar perumahan.

## **1\. Modul Utama & Tenant (SaaS Level)**

| Tabel | Deskripsi | Field Kunci |
| :---- | :---- | :---- |
| **tenants** | Data perumahan/cluster yang berlangganan. | id, name, address, total\_houses, subscription\_status |
| **users** | Akun pengguna (Admin, Pengurus, Warga, Kolektor). | id, tenant\_id, email, password, role, is\_verified |
| **subscriptions** | Log pembayaran tenant ke perusahaan Anda. | id, tenant\_id, amount, payment\_status, expired\_at |

## **2\. Modul Kependudukan (Sensus Digital)**

| Tabel | Deskripsi | Field Kunci |
| :---- | :---- | :---- |
| **blocks** | Data blok dalam perumahan (Blok A, B, dsb). | id, tenant\_id, block\_name |
| **houses** | Detail setiap unit rumah. | id, block\_id, house\_number, status (Huni/Kosong/Kontrak) |
| **families** | Data Kepala Keluarga. | id, house\_id, kk\_number, address\_on\_kk |
| **residents** | Data individu warga. | id, family\_id, nik, full\_name, gender, birth\_date, profession, is\_wna, domicile\_status, is\_bansos\_receiver, can\_vote |
| **mutations** | Log warga pindah, datang, atau meninggal. | id, resident\_id, type (In/Out/Death), date, note |
| **vehicles** | Data kendaraan warga. | id, house\_id, plate\_number, type (Car/Bike), brand |
| **visitors** | Log tamu yang berkunjung. | id, house\_id, guest\_name, check\_in, check\_out, id\_card\_photo |

## **3\. Modul Keuangan & Payment Gateway**

| Tabel | Deskripsi | Field Kunci |
| :---- | :---- | :---- |
| **invoice\_types** | Jenis iuran (IPL, Sampah, Keamanan). | id, tenant\_id, name, amount, is\_mandatory |
| **invoices** | Tagihan otomatis yang dikirim ke warga. | id, house\_id, invoice\_type\_id, period (MM-YYYY), amount, status (Unpaid/Paid) |
| **payments** | Log transaksi pembayaran (VA/QRIS). | id, invoice\_id, transaction\_id (PG), method (VA/QRIS), paid\_at, admin\_fee |
| **cashflow** | Catatan kas masuk dan keluar RT. | id, tenant\_id, type (In/Out), amount, category, description, receipt\_photo\_url |
| **disbursements** | Log penarikan dana dari sistem ke rekening RT. | id, tenant\_id, amount, bank\_account, status |

## **4\. Modul Pelayanan & Keamanan**

| Tabel | Deskripsi | Field Kunci |
| :---- | :---- | :---- |
| **service\_requests** | Pengajuan surat keterangan/pengantar. | id, resident\_id, type, requirements\_url, status, digital\_signature\_url |
| **permits** | Izin renovasi atau keramaian. | id, house\_id, type, start\_date, end\_date, status |
| **inventory** | Barang milik RT (Tenda, Kursi, dsb). | id, tenant\_id, item\_name, total\_qty, condition |
| **inventory\_loans** | Log peminjaman barang inventaris. | id, inventory\_id, resident\_id, qty, loan\_date, return\_date |
| **ronda\_logs** | Absensi dan laporan patroli keamanan. | id, tenant\_id, officer\_name, checkpoint\_name, timestamp, photo\_url |
| **panic\_alerts** | Log ketika tombol panic button ditekan. | id, house\_id, timestamp, status (Handled/False Alarm) |
| **suggestions** | Fitur saran dan keluhan warga. | id, resident\_id, title, content, category, response\_from\_admin |

## **5\. Modul Konten & Informasi**

| Tabel | Deskripsi | Field Kunci |
| :---- | :---- | :---- |
| **news** | Berita online dan pengumuman lingkungan. | id, tenant\_id, title, content, image\_url, created\_at |
| **galleries** | Dokumentasi foto kegiatan warga. | id, tenant\_id, title, image\_url, event\_date |
| **local\_businesses** | Daftar lapak/usaha warga. | id, resident\_id, business\_name, category, whatsapp\_number, photo\_url |

## **Catatan Penting untuk Developer:**

1. **Indexing:** Selalu berikan index pada tenant\_id dan house\_id karena kolom ini yang paling sering digunakan dalam query filter.  
2. **Soft Deletes:** Gunakan kolom deleted\_at (Soft Delete) untuk data warga atau transaksi keuangan agar data tetap bisa dilacak jika terjadi kesalahan hapus.  
3. **Audit Trail:** Pertimbangkan tabel audit\_logs untuk mencatat aktivitas admin seperti: *"Admin A mengubah saldo Kas RT dari 1jt menjadi 500rb"* demi menjaga transparansi.  
4. **Security:** Data sensitif seperti nik dan password wajib di-enkripsi.

