<?php $pageTitle = 'Tambah Program Kerja'; ?>
<?php include 'includes/header.php'; ?>
<div class="app-layout">
  <?php include 'includes/sidebar.php'; ?>
  <div class="main-wrapper">
    <?php include 'includes/topbar.php'; ?>
    <main class="main-content">
      <div class="page-header">
        <div>
          <h1>Tambah Program Kerja</h1>
          <div class="breadcrumb">
            <a href="index.php">Dashboard</a><span class="separator">/</span>
            <a href="profil.php">Profil</a><span class="separator">/</span>
            <span>Tambah Proker</span>
          </div>
        </div>
        <a href="profil.php" class="btn btn-secondary btn-sm">← Kembali</a>
      </div>

      <div class="card">
        <div class="card-header"><h3 class="card-title">📋 Detail Program Kerja</h3></div>
        <div style="padding:24px;">
          <div class="form-group">
            <label class="form-label">Nama Program</label>
            <input type="text" class="form-control" placeholder="Contoh: Program Penghijauan Taman RT">
          </div>
          <div class="form-row">
            <div class="form-group">
              <label class="form-label">Kuartal</label>
              <select class="form-control">
                <option>Q1 (Jan-Mar)</option>
                <option>Q2 (Apr-Jun)</option>
                <option selected>Q3 (Jul-Sep)</option>
                <option>Q4 (Okt-Des)</option>
              </select>
            </div>
            <div class="form-group">
              <label class="form-label">Tahun</label>
              <select class="form-control">
                <option selected>2026</option>
                <option>2027</option>
              </select>
            </div>
          </div>
          <div class="form-row">
            <div class="form-group">
              <label class="form-label">Tanggal Mulai</label>
              <input type="date" class="form-control">
            </div>
            <div class="form-group">
              <label class="form-label">Target Selesai</label>
              <input type="date" class="form-control">
            </div>
          </div>
          <div class="form-group">
            <label class="form-label">Penanggung Jawab</label>
            <input type="text" class="form-control" placeholder="Nama pengurus / seksi">
          </div>
          <div class="form-group">
            <label class="form-label">Anggaran (Rp)</label>
            <input type="number" class="form-control" placeholder="0">
          </div>
          <div class="form-group">
            <label class="form-label">Deskripsi & Rincian Kegiatan</label>
            <textarea class="form-control" rows="5" placeholder="Jelaskan detail program kerja, langkah-langkah, dan target yang ingin dicapai..."></textarea>
          </div>
          <div class="form-group">
            <label class="form-label">Progres (%)</label>
            <div style="display:flex;align-items:center;gap:12px;">
              <input type="range" min="0" max="100" value="0" style="flex:1;" id="progressRange" oninput="document.getElementById('progressValue').textContent=this.value+'%'">
              <span id="progressValue" style="font-weight:700;min-width:40px;">0%</span>
            </div>
          </div>
          <div class="form-group">
            <label class="form-label">Status</label>
            <select class="form-control">
              <option>Belum Dimulai</option>
              <option>Berjalan</option>
              <option>Selesai</option>
              <option>Ditunda</option>
            </select>
          </div>
          <div style="display:flex;justify-content:flex-end;gap:12px;margin-top:24px;padding-top:20px;border-top:1px solid var(--border-color);">
            <a href="profil.php" class="btn btn-secondary">Batal</a>
            <button class="btn btn-primary" onclick="showToast('Program kerja berhasil disimpan!','success')">💾 Simpan Program</button>
          </div>
        </div>
      </div>
    </main>
  </div>
</div>
<?php include 'includes/footer.php'; ?>
