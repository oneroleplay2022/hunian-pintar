<?php $pageTitle = 'Pengaduan Warga'; ?>
<?php include 'includes/header.php'; ?>

<div class="app-layout">
  <?php include 'includes/sidebar.php'; ?>

  <div class="main-wrapper">
    <?php include 'includes/topbar.php'; ?>

    <main class="main-content">
      <div class="page-header">
        <div>
          <h1>Pengaduan & Saran Warga</h1>
          <div class="breadcrumb">
            <a href="index.php">Dashboard</a>
            <span class="separator">/</span>
            <span>Pelayanan</span>
            <span class="separator">/</span>
            <span>Pengaduan</span>
          </div>
        </div>
        <a href="pengaduan_form.php" class="btn btn-primary btn-sm"><i data-lucide="message-square-plus" style="width:16px;height:16px;"></i> Buat Pengaduan</a>
      </div>

      <!-- Stats -->
      <div class="stats-grid" style="grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));">
        <div class="stat-card">
          <div class="stat-icon blue"><i data-lucide="message-square"></i></div>
          <div class="stat-info">
            <div class="stat-label">Total Tiket</div>
            <div class="stat-value">89</div>
          </div>
        </div>
        <div class="stat-card">
          <div class="stat-icon yellow"><i data-lucide="clock"></i></div>
          <div class="stat-info">
            <div class="stat-label">Menunggu</div>
            <div class="stat-value">3</div>
          </div>
        </div>
        <div class="stat-card">
          <div class="stat-icon green"><i data-lucide="check-circle"></i></div>
          <div class="stat-info">
            <div class="stat-label">Terselesaikan</div>
            <div class="stat-value">82</div>
          </div>
        </div>
        <div class="stat-card">
          <div class="stat-icon purple"><i data-lucide="star"></i></div>
          <div class="stat-info">
            <div class="stat-label">Rata-rata Rating</div>
            <div class="stat-value">4.6 ⭐</div>
          </div>
        </div>
      </div>

      <!-- Tabs -->
      <div class="tabs">
        <button class="tab-btn active">Semua</button>
        <button class="tab-btn">Keluhan</button>
        <button class="tab-btn">Saran</button>
        <button class="tab-btn">Menunggu</button>
        <button class="tab-btn">Selesai</button>
      </div>

      <!-- Tickets -->
      <div style="display:flex;flex-direction:column;gap:12px;">
        <?php
        $tickets = [
          ['TK-089', 'Keluhan', 'Lampu jalan Blok C mati sudah 3 hari', 'Budi Santoso', 'A/12', '27 Mar 2026', 'Baru', 'warning',
            'Lampu jalan di dekat rumah C/10 sudah mati 3 hari. Mohon segera diperbaiki karena sangat gelap dan rawan pencurian.', null],
          ['TK-088', 'Saran', 'Tambah tempat sampah di area taman', 'Siti Rahayu', 'B/05', '26 Mar 2026', 'Diproses', 'info',
            'Di area taman blok B hanya ada 1 tempat sampah. Mohon ditambah minimal 2 buah lagi.', 'Terima kasih sarannya Bu. Kami akan tambah 2 tempat sampah minggu depan.'],
          ['TK-087', 'Keluhan', 'Air PDAM sering mati sore hari', 'Ahmad Fauzi', 'C/08', '25 Mar 2026', 'Baru', 'warning',
            'Sudah 1 minggu air PDAM mati setiap sore pukul 16-20. Mohon ditindaklanjuti ke pihak PDAM.', null],
          ['TK-086', 'Keluhan', 'Portal gerbang belakang rusak', 'Dewi Lestari', 'A/22', '24 Mar 2026', 'Selesai', 'success',
            'Portal gerbang belakang sudah tidak bisa ditutup sempurna.', 'Portal sudah diperbaiki pada 25 Maret 2026. Silakan dicek kembali.'],
          ['TK-085', 'Saran', 'Adakan senam pagi rutin tiap minggu', 'Nur Hidayah', 'B/11', '23 Mar 2026', 'Selesai', 'success',
            'Mohon diadakan senam pagi tiap hari Minggu untuk warga.', 'Ide bagus! Senam pagi akan diadakan mulai Minggu, 30 Maret 2026 pukul 06.30 di taman blok B.'],
          ['TK-084', 'Keluhan', 'Sampah menumpuk di pojok Blok D', 'Riko Pratama', 'D/15', '22 Mar 2026', 'Selesai', 'success',
            'Pojok Blok D dekat saluran air banyak sampah menumpuk.', 'Sudah dibersihkan oleh petugas kebersihan. Kami juga akan pasang papan larangan buang sampah.'],
        ];
        foreach ($tickets as $t): ?>
        <div class="ticket-card">
          <div class="ticket-header">
            <div style="display:flex;align-items:center;gap:10px;">
              <span class="badge badge-<?= $t[1] == 'Keluhan' ? 'danger' : 'info' ?>"><?= $t[1] ?></span>
              <span class="ticket-title"><?= $t[2] ?></span>
            </div>
            <span class="badge badge-<?= $t[7] ?>"><?= $t[6] ?></span>
          </div>
          <div class="ticket-body"><?= $t[8] ?></div>

          <?php if ($t[9]): ?>
          <div style="background:rgba(16,185,129,0.06);border:1px solid rgba(16,185,129,0.15);border-radius:var(--radius-md);padding:12px;margin-bottom:12px;">
            <div style="font-size:0.78rem;font-weight:600;color:var(--success);margin-bottom:4px;">💬 Respon Admin:</div>
            <div style="font-size:0.85rem;color:var(--text-secondary);"><?= $t[9] ?></div>
          </div>
          <?php endif; ?>

          <div class="ticket-footer">
            <div style="display:flex;align-items:center;gap:12px;">
              <span><i data-lucide="user" style="width:12px;height:12px;display:inline;vertical-align:middle;"></i> <?= $t[3] ?></span>
              <span><i data-lucide="home" style="width:12px;height:12px;display:inline;vertical-align:middle;"></i> <?= $t[4] ?></span>
              <span><?= $t[0] ?></span>
            </div>
            <div style="display:flex;align-items:center;gap:8px;">
              <span><?= $t[5] ?></span>
              <?php if ($t[6] == 'Baru'): ?>
                <a href="pengaduan_detail.php" class="btn btn-sm btn-primary" style="padding:4px 12px;">Tanggapi</a>
              <?php endif; ?>
            </div>
          </div>
        </div>
        <?php endforeach; ?>
      </div>

      <div class="pagination">
        <button class="page-btn">←</button>
        <button class="page-btn active">1</button>
        <button class="page-btn">2</button>
        <button class="page-btn">→</button>
      </div>
    </main>
  </div>
</div>

<!-- Add Ticket Modal -->
<div class="modal-overlay" id="addTicketModal">
  <div class="modal">
    <div class="modal-header">
      <h3 class="modal-title">Buat Pengaduan / Saran</h3>
      <button class="modal-close">✕</button>
    </div>
    <div class="modal-body">
      <div class="form-group">
        <label class="form-label">Jenis</label>
        <div style="display:flex;gap:12px;">
          <label class="checkbox-label" style="flex:1;padding:12px;border:1px solid var(--border-color);border-radius:var(--radius-md);cursor:pointer;">
            <input type="radio" name="ticket_type" value="complaint" checked> 😤 Keluhan
          </label>
          <label class="checkbox-label" style="flex:1;padding:12px;border:1px solid var(--border-color);border-radius:var(--radius-md);cursor:pointer;">
            <input type="radio" name="ticket_type" value="suggestion"> 💡 Saran
          </label>
        </div>
      </div>
      <div class="form-group">
        <label class="form-label">Kategori</label>
        <select class="form-control">
          <option>Infrastruktur</option>
          <option>Kebersihan</option>
          <option>Keamanan</option>
          <option>Keuangan</option>
          <option>Kegiatan</option>
          <option>Lainnya</option>
        </select>
      </div>
      <div class="form-group">
        <label class="form-label">Judul</label>
        <input type="text" class="form-control" placeholder="Judul singkat pengaduan / saran">
      </div>
      <div class="form-group">
        <label class="form-label">Deskripsi Lengkap</label>
        <textarea class="form-control" rows="4" placeholder="Jelaskan secara detail..."></textarea>
      </div>
      <div class="form-group">
        <label class="form-label">Lampiran Foto (opsional)</label>
        <input type="file" class="form-control" accept="image/*" multiple>
      </div>
    </div>
    <div class="modal-footer">
      <button class="btn btn-secondary modal-cancel">Batal</button>
      <button class="btn btn-primary">Kirim Pengaduan</button>
    </div>
  </div>
</div>

<?php include 'includes/footer.php'; ?>
