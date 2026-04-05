<?php $pageTitle = 'Buat Tagihan'; ?>
<?php include 'includes/header.php'; ?>
<div class="app-layout">
  <?php include 'includes/sidebar.php'; ?>
  <div class="main-wrapper">
    <?php include 'includes/topbar.php'; ?>
    <main class="main-content">
      <div class="page-header">
        <div><h1>Buat Tagihan Baru</h1>
          <div class="breadcrumb"><a href="index.php">Dashboard</a><span class="separator">/</span><a href="iuran.php">Iuran</a><span class="separator">/</span><span>Buat Tagihan</span></div>
        </div>
        <a href="iuran.php" class="btn btn-secondary btn-sm">← Kembali</a>
      </div>

      <div class="card" style="padding:24px;">
        <div class="form-group">
          <label class="form-label">Jenis Tagihan</label>
          <select class="form-control"><option>IPL Bulanan</option><option>Iuran Khusus</option><option>Sumbangan Sukarela</option><option>Denda</option></select>
        </div>
        <div class="form-row">
          <div class="form-group"><label class="form-label">Periode / Bulan</label><input type="month" class="form-control" value="2026-04"></div>
          <div class="form-group"><label class="form-label">Jatuh Tempo</label><input type="date" class="form-control"></div>
        </div>
        <div class="form-group">
          <label class="form-label">Target Tagihan</label>
          <div style="display:flex;gap:12px;">
            <label class="checkbox-label" style="flex:1;padding:14px;border:1px solid var(--primary);border-radius:var(--radius-md);background:rgba(99,102,241,0.05);">
              <input type="radio" name="target" value="all" checked> Semua Rumah
            </label>
            <label class="checkbox-label" style="flex:1;padding:14px;border:1px solid var(--border-color);border-radius:var(--radius-md);">
              <input type="radio" name="target" value="block"> Per Blok
            </label>
            <label class="checkbox-label" style="flex:1;padding:14px;border:1px solid var(--border-color);border-radius:var(--radius-md);">
              <input type="radio" name="target" value="custom"> Rumah Tertentu
            </label>
          </div>
        </div>

        <h4 style="margin:24px 0 16px;padding-top:16px;border-top:1px solid var(--border-color);">Rincian Komponen Tagihan</h4>
        <div style="display:flex;flex-direction:column;gap:8px;margin-bottom:16px;" id="lineItems">
          <div style="display:flex;gap:8px;align-items:center;">
            <input type="text" class="form-control" value="Iuran IPL Bulanan" style="flex:2;">
            <input type="number" class="form-control" value="300000" style="flex:1;">
            <button class="btn btn-icon btn-sm btn-secondary" style="color:var(--danger);"><i data-lucide="trash-2" style="width:14px;height:14px;"></i></button>
          </div>
          <div style="display:flex;gap:8px;align-items:center;">
            <input type="text" class="form-control" value="Iuran Keamanan" style="flex:2;">
            <input type="number" class="form-control" value="30000" style="flex:1;">
            <button class="btn btn-icon btn-sm btn-secondary" style="color:var(--danger);"><i data-lucide="trash-2" style="width:14px;height:14px;"></i></button>
          </div>
          <div style="display:flex;gap:8px;align-items:center;">
            <input type="text" class="form-control" value="Iuran Kebersihan" style="flex:2;">
            <input type="number" class="form-control" value="20000" style="flex:1;">
            <button class="btn btn-icon btn-sm btn-secondary" style="color:var(--danger);"><i data-lucide="trash-2" style="width:14px;height:14px;"></i></button>
          </div>
        </div>
        <button class="btn btn-outline btn-sm" onclick="showToast('Tambah item','info')"><i data-lucide="plus" style="width:14px;height:14px;"></i> Tambah Komponen</button>

        <div style="display:flex;justify-content:space-between;margin-top:20px;padding:16px;background:var(--bg-input);border-radius:var(--radius-md);font-size:1.1rem;font-weight:700;">
          <span>Total per Rumah</span>
          <span style="color:var(--primary-light);">Rp 350.000</span>
        </div>

        <div class="form-group" style="margin-top:20px;">
          <label class="checkbox-label"><input type="checkbox" checked> Kirim notifikasi ke warga via WhatsApp / Push</label>
        </div>

        <div style="display:flex;justify-content:flex-end;gap:12px;margin-top:24px;padding-top:20px;border-top:1px solid var(--border-color);">
          <a href="iuran.php" class="btn btn-secondary">Batal</a>
          <button class="btn btn-primary" onclick="showToast('Tagihan berhasil dibuat untuk semua rumah!','success')">📤 Buat & Kirim Tagihan</button>
        </div>
      </div>
    </main>
  </div>
</div>
<?php include 'includes/footer.php'; ?>
