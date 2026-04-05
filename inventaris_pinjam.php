<?php $pageTitle = 'Form Peminjaman'; ?>
<?php include 'includes/header.php'; ?>
<div class="app-layout">
  <?php include 'includes/sidebar.php'; ?>
  <div class="main-wrapper">
    <?php include 'includes/topbar.php'; ?>
    <main class="main-content">
      <div class="page-header">
        <div>
          <h1>Form Peminjaman Barang</h1>
          <div class="breadcrumb">
            <a href="index.php">Dashboard</a><span class="separator">/</span>
            <a href="inventaris.php">Inventaris</a><span class="separator">/</span>
            <span>Peminjaman</span>
          </div>
        </div>
        <a href="inventaris.php" class="btn btn-secondary btn-sm">← Kembali</a>
      </div>

      <div class="card" style="padding:24px;">
        <h3 style="margin-bottom:20px;">📋 Data Peminjaman</h3>
        <div class="form-group">
          <label class="form-label">Barang yang Dipinjam</label>
          <select class="form-control">
            <option>Tenda Kerucut 3x3m (tersedia: 3)</option>
            <option>Kursi Lipat Plastik (tersedia: 180)</option>
            <option>Meja Lipat Panjang (tersedia: 18)</option>
            <option>Sound System + Mic (tersedia: 1)</option>
            <option>Genset Portable 5000W (tersedia: 1)</option>
          </select>
        </div>
        <div class="form-group">
          <label class="form-label">Jumlah Pinjam</label>
          <input type="number" class="form-control" min="1" value="1">
        </div>
        <div class="form-group">
          <label class="form-label">Nama Peminjam</label>
          <input type="text" class="form-control" placeholder="Nama lengkap">
        </div>
        <div class="form-group">
          <label class="form-label">Rumah</label>
          <input type="text" class="form-control" placeholder="Contoh: A/12">
        </div>
        <div class="form-row">
          <div class="form-group">
            <label class="form-label">Tanggal Pinjam</label>
            <input type="date" class="form-control">
          </div>
          <div class="form-group">
            <label class="form-label">Tanggal Kembali</label>
            <input type="date" class="form-control">
          </div>
        </div>
        <div class="form-group">
          <label class="form-label">Keperluan</label>
          <textarea class="form-control" rows="3" placeholder="Jelaskan keperluan peminjaman..."></textarea>
        </div>

        <div style="padding:16px;background:rgba(99,102,241,0.06);border-radius:var(--radius-md);border:1px solid rgba(99,102,241,0.15);margin-bottom:20px;">
          <h4 style="font-size:0.9rem;margin-bottom:8px;">📌 Ketentuan Peminjaman</h4>
          <ul style="font-size:0.82rem;color:var(--text-secondary);padding-left:18px;line-height:1.8;">
            <li>Barang harus dikembalikan dalam kondisi baik</li>
            <li>Keterlambatan pengembalian dikenakan denda Rp 10.000/hari</li>
            <li>Kerusakan ditanggung oleh peminjam</li>
            <li>Maksimal peminjaman 7 hari kalender</li>
          </ul>
        </div>

        <div class="form-group">
          <label class="checkbox-label">
            <input type="checkbox"> Saya menyetujui ketentuan peminjaman di atas
          </label>
        </div>

        <div style="display:flex;justify-content:flex-end;gap:12px;padding-top:16px;border-top:1px solid var(--border-color);">
          <a href="inventaris.php" class="btn btn-secondary">Batal</a>
          <button class="btn btn-primary" onclick="showToast('Peminjaman berhasil diajukan!','success')">📋 Ajukan Peminjaman</button>
        </div>
      </div>
    </main>
  </div>
</div>
<?php include 'includes/footer.php'; ?>
