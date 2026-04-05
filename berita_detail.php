<?php $pageTitle = 'Detail Berita'; ?>
<?php include 'includes/header.php'; ?>
<div class="app-layout">
  <?php include 'includes/sidebar.php'; ?>
  <div class="main-wrapper">
    <?php include 'includes/topbar.php'; ?>
    <main class="main-content">
      <div class="page-header">
        <div>
          <h1>Detail Berita</h1>
          <div class="breadcrumb">
            <a href="index.php">Dashboard</a><span class="separator">/</span>
            <a href="berita.php">Berita</a><span class="separator">/</span>
            <span>Detail</span>
          </div>
        </div>
        <div style="display:flex;gap:10px;">
          <a href="berita.php" class="btn btn-secondary btn-sm">← Kembali</a>
          <a href="berita_form.php" class="btn btn-primary btn-sm"><i data-lucide="pencil" style="width:14px;height:14px;"></i> Edit</a>
        </div>
      </div>

      <div class="grid-2" style="gap:24px;">
        <!-- Content -->
        <div>
          <div class="card" style="overflow:hidden;">
            <div style="height:300px;background:linear-gradient(135deg,rgba(99,102,241,0.2),rgba(6,182,212,0.15));display:flex;align-items:center;justify-content:center;">
              <span style="font-size:4rem;">📰</span>
            </div>
            <div style="padding:24px;">
              <div style="display:flex;align-items:center;gap:12px;margin-bottom:16px;">
                <span class="badge badge-info">Pengumuman</span>
                <span class="text-muted" style="font-size:0.85rem;">📅 22 Maret 2026</span>
                <span class="text-muted" style="font-size:0.85rem;">👁️ 245 views</span>
              </div>
              <h2 style="font-size:1.5rem;margin-bottom:16px;line-height:1.4;">Jadwal Kerja Bakti Bulan Depan</h2>
              <div style="color:var(--text-secondary);line-height:1.8;font-size:0.95rem;">
                <p>Assalamu'alaikum Wr. Wb.</p>
                <p>Dengan hormat, kami informasikan bahwa kerja bakti bulanan akan dilaksanakan di area taman Blok A-C pada hari:</p>
                <div style="padding:16px;background:var(--bg-input);border-radius:var(--radius-md);margin:16px 0;border-left:3px solid var(--primary);">
                  <strong>📅 Hari/Tanggal:</strong> Minggu, 5 April 2026<br>
                  <strong>🕐 Waktu:</strong> 07.00 - 11.00 WIB<br>
                  <strong>📍 Lokasi:</strong> Taman Blok A, B, dan C<br>
                  <strong>🧹 Kegiatan:</strong> Bersih-bersih taman, potong rumput, cat ulang pagar
                </div>
                <p>Dimohon partisipasi seluruh warga. Setiap rumah diharapkan mengirimkan minimal 1 perwakilan.</p>
                <p><strong>Perlengkapan yang perlu dibawa:</strong></p>
                <ul style="padding-left:20px;">
                  <li>Sapu, ember, dan kain lap</li>
                  <li>Sarung tangan kerja</li>
                  <li>Cangkul / sekop (jika ada)</li>
                </ul>
                <p>Konsumsi (snack + minum) disediakan oleh RT.</p>
                <p>Terima kasih atas perhatian dan partisipasinya.</p>
                <p style="margin-top:24px;"><em>Wassalamu'alaikum Wr. Wb.</em></p>
                <p><strong>H. Supriadi, S.H.</strong><br><span class="text-muted">Ketua RT 05</span></p>
              </div>
            </div>
          </div>

          <!-- Comments -->
          <div class="card" style="margin-top:20px;">
            <div class="card-header"><h3 class="card-title">💬 Komentar (5)</h3></div>
            <div style="padding:20px;display:flex;flex-direction:column;gap:16px;">
              <?php
              $comments = [
                ['Budi Santoso', 'A/12', '22 Mar', 'Siap hadir pak! Saya bawa cangkul dari rumah. 💪'],
                ['Siti Rahayu', 'B/05', '22 Mar', 'Apakah anak-anak boleh ikut membantu?'],
                ['Dewi Lestari', 'A/22', '23 Mar', 'Mohon maaf saya berhalangan, tapi saya kirim suami dan anak saya ya pak.'],
                ['Maya Sari', 'C/19', '23 Mar', 'Saya sediakan snack tambahan dari warung saya 😊'],
                ['Riko Pratama', 'D/15', '24 Mar', 'Siap pak, saya bawa mesin potong rumput!'],
              ];
              foreach ($comments as $c): ?>
              <div style="display:flex;gap:12px;">
                <div style="width:40px;height:40px;border-radius:50%;background:linear-gradient(135deg,var(--primary),var(--accent));display:flex;align-items:center;justify-content:center;color:white;font-weight:700;font-size:0.85rem;flex-shrink:0;">
                  <?= mb_substr($c[0], 0, 1) ?>
                </div>
                <div style="flex:1;">
                  <div style="display:flex;align-items:center;gap:8px;margin-bottom:4px;">
                    <strong style="font-size:0.9rem;"><?= $c[0] ?></strong>
                    <span class="text-muted" style="font-size:0.78rem;"><?= $c[1] ?> • <?= $c[2] ?></span>
                  </div>
                  <p style="font-size:0.88rem;color:var(--text-secondary);margin:0;"><?= $c[3] ?></p>
                </div>
              </div>
              <?php endforeach; ?>

              <div style="padding-top:16px;border-top:1px solid var(--border-color);">
                <textarea class="form-control" rows="3" placeholder="Tulis komentar..."></textarea>
                <div style="display:flex;justify-content:flex-end;margin-top:8px;">
                  <button class="btn btn-primary btn-sm">Kirim Komentar</button>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Sidebar Info -->
        <div>
          <div class="card" style="padding:20px;margin-bottom:16px;">
            <h4 style="margin-bottom:16px;">ℹ️ Info Berita</h4>
            <div style="display:flex;flex-direction:column;gap:12px;font-size:0.88rem;">
              <div style="display:flex;justify-content:space-between;"><span class="text-muted">Penulis</span><strong>Admin Satu</strong></div>
              <div style="display:flex;justify-content:space-between;"><span class="text-muted">Kategori</span><span class="badge badge-info">Pengumuman</span></div>
              <div style="display:flex;justify-content:space-between;"><span class="text-muted">Dipublikasikan</span><strong>22 Mar 2026</strong></div>
              <div style="display:flex;justify-content:space-between;"><span class="text-muted">Views</span><strong>245</strong></div>
              <div style="display:flex;justify-content:space-between;"><span class="text-muted">Komentar</span><strong>5</strong></div>
              <div style="display:flex;justify-content:space-between;"><span class="text-muted">Status</span><span class="badge badge-success">Published</span></div>
            </div>
          </div>

          <div class="card" style="padding:20px;">
            <h4 style="margin-bottom:16px;">📰 Berita Lainnya</h4>
            <div style="display:flex;flex-direction:column;gap:12px;">
              <a href="#" style="font-size:0.88rem;color:var(--text-secondary);text-decoration:none;">Perbaikan Jalan di Blok D</a>
              <a href="#" style="font-size:0.88rem;color:var(--text-secondary);text-decoration:none;">Lomba 17 Agustus — Pendaftaran Dibuka</a>
              <a href="#" style="font-size:0.88rem;color:var(--text-secondary);text-decoration:none;">Edaran Iuran IPL Maret 2026</a>
            </div>
          </div>
        </div>
      </div>
    </main>
  </div>
</div>
<?php include 'includes/footer.php'; ?>
