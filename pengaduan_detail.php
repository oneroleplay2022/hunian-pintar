<?php $pageTitle = 'Detail Pengaduan'; ?>
<?php include 'includes/header.php'; ?>
<div class="app-layout">
  <?php include 'includes/sidebar.php'; ?>
  <div class="main-wrapper">
    <?php include 'includes/topbar.php'; ?>
    <main class="main-content">
      <div class="page-header">
        <div><h1>Detail Pengaduan</h1>
          <div class="breadcrumb"><a href="index.php">Dashboard</a><span class="separator">/</span><a href="pengaduan.php">Pengaduan</a><span class="separator">/</span><span>Detail</span></div>
        </div>
        <a href="pengaduan.php" class="btn btn-secondary btn-sm">← Kembali</a>
      </div>

      <div class="grid-2" style="gap:24px;">
        <div>
          <div class="card" style="padding:24px;">
            <div style="display:flex;align-items:center;gap:16px;margin-bottom:20px;">
              <div style="width:56px;height:56px;border-radius:var(--radius-lg);background:rgba(239,68,68,0.1);display:flex;align-items:center;justify-content:center;font-size:2rem;">🚨</div>
              <div>
                <h2 style="font-size:1.2rem;">Lampu Jalan Blok C Mati</h2>
                <div style="display:flex;gap:6px;margin-top:4px;">
                  <span class="badge badge-info">Keluhan</span>
                  <span class="badge badge-warning">Prioritas: Sedang</span>
                  <span class="badge badge-warning">Diproses</span>
                </div>
              </div>
            </div>
            <div style="display:flex;flex-direction:column;gap:12px;font-size:0.88rem;margin-bottom:20px;">
              <div style="display:flex;justify-content:space-between;"><span class="text-muted">No. Tiket</span><strong>ADU-2026-03-042</strong></div>
              <div style="display:flex;justify-content:space-between;"><span class="text-muted">Pelapor</span><strong>Maya Sari (C/19)</strong></div>
              <div style="display:flex;justify-content:space-between;"><span class="text-muted">Tgl Lapor</span><strong>20 Maret 2026, 19:45</strong></div>
              <div style="display:flex;justify-content:space-between;"><span class="text-muted">Lokasi</span><strong>Jalan Blok C, depan rumah No. 18-20</strong></div>
            </div>
            <div style="padding-top:16px;border-top:1px solid var(--border-color);">
              <h4 style="margin-bottom:8px;">Deskripsi</h4>
              <p style="font-size:0.88rem;color:var(--text-secondary);line-height:1.7;">Lampu jalan di depan rumah C/18 sampai C/20 sudah mati sejak 3 hari yang lalu. Sangat gelap di malam hari dan berbahaya untuk warga yang lewat. Mohon segera diperbaiki.</p>
            </div>
            <div style="margin-top:16px;">
              <h4 style="margin-bottom:8px;">📸 Foto Bukti</h4>
              <div style="display:grid;grid-template-columns:repeat(2,1fr);gap:8px;">
                <div style="aspect-ratio:4/3;background:linear-gradient(135deg,rgba(15,23,42,0.3),rgba(15,23,42,0.2));border-radius:var(--radius-md);display:flex;align-items:center;justify-content:center;font-size:2rem;">🌑</div>
                <div style="aspect-ratio:4/3;background:linear-gradient(135deg,rgba(15,23,42,0.3),rgba(15,23,42,0.2));border-radius:var(--radius-md);display:flex;align-items:center;justify-content:center;font-size:2rem;">💡</div>
              </div>
            </div>
          </div>
        </div>

        <div>
          <!-- Response Thread -->
          <div class="card" style="margin-bottom:16px;">
            <div class="card-header"><h3 class="card-title">💬 Thread Tanggapan</h3></div>
            <div style="padding:20px;display:flex;flex-direction:column;gap:16px;">
              <div style="display:flex;gap:12px;">
                <div style="width:36px;height:36px;border-radius:50%;background:rgba(99,102,241,0.2);display:flex;align-items:center;justify-content:center;font-size:0.8rem;flex-shrink:0;">👤</div>
                <div style="flex:1;padding:12px;background:var(--bg-input);border-radius:var(--radius-md);">
                  <div style="font-size:0.82rem;margin-bottom:4px;"><strong>Admin Satu</strong> <span class="text-muted">— 21 Mar, 08:30</span></div>
                  <p style="font-size:0.85rem;margin:0;">Terima kasih laporannya Bu Maya. Kami sudah koordinasi dengan seksi pembangunan untuk mengecek lampu jalan tersebut hari ini.</p>
                </div>
              </div>
              <div style="display:flex;gap:12px;">
                <div style="width:36px;height:36px;border-radius:50%;background:rgba(16,185,129,0.2);display:flex;align-items:center;justify-content:center;font-size:0.8rem;flex-shrink:0;">🔧</div>
                <div style="flex:1;padding:12px;background:var(--bg-input);border-radius:var(--radius-md);">
                  <div style="font-size:0.82rem;margin-bottom:4px;"><strong>Riko Pratama (Seksi Pembangunan)</strong> <span class="text-muted">— 21 Mar, 14:00</span></div>
                  <p style="font-size:0.85rem;margin:0;">Sudah dicek. Kerusakannya di panel MCB tiang. Kami sudah pesan sparepart, estimasi diperbaiki 2-3 hari.</p>
                </div>
              </div>
            </div>

            <div style="padding:16px 20px;border-top:1px solid var(--border-color);">
              <textarea class="form-control" rows="3" placeholder="Tambahkan tanggapan..."></textarea>
              <div style="display:flex;justify-content:flex-end;gap:8px;margin-top:8px;">
                <input type="file" id="attachReply" accept="image/*" style="display:none;">
                <button class="btn btn-outline btn-sm" onclick="document.getElementById('attachReply').click()">📎 Lampirkan</button>
                <button class="btn btn-primary btn-sm" onclick="showToast('Tanggapan terkirim!','success')">Kirim</button>
              </div>
            </div>
          </div>

          <!-- Admin Panel -->
          <div class="card" style="padding:20px;">
            <h4 style="margin-bottom:12px;">⚙️ Update Status</h4>
            <div class="form-group">
              <select class="form-control">
                <option>Diterima</option><option selected>Diproses</option><option>Selesai</option><option>Ditutup</option>
              </select>
            </div>
            <div class="form-group">
              <label class="form-label">Assign ke</label>
              <select class="form-control"><option>Seksi Pembangunan</option><option>Seksi Kebersihan</option><option>Seksi Keamanan</option><option>Ketua RT</option></select>
            </div>
            <button class="btn btn-primary btn-sm w-full" onclick="showToast('Status diperbarui!','success')">✅ Update</button>
          </div>
        </div>
      </div>
    </main>
  </div>
</div>
<?php include 'includes/footer.php'; ?>
