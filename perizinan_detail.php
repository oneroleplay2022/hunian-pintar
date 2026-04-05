<?php $pageTitle = 'Detail Perizinan'; ?>
<?php include 'includes/header.php'; ?>
<div class="app-layout">
  <?php include 'includes/sidebar.php'; ?>
  <div class="main-wrapper">
    <?php include 'includes/topbar.php'; ?>
    <main class="main-content">
      <div class="page-header">
        <div><h1>Detail Pengajuan Izin</h1>
          <div class="breadcrumb"><a href="index.php">Dashboard</a><span class="separator">/</span><a href="perizinan.php">Perizinan</a><span class="separator">/</span><span>Detail</span></div>
        </div>
        <a href="perizinan.php" class="btn btn-secondary btn-sm">← Kembali</a>
      </div>

      <div class="grid-2" style="gap:24px;">
        <div>
          <div class="card" style="padding:24px;">
            <div style="display:flex;align-items:center;gap:16px;margin-bottom:24px;">
              <div style="width:56px;height:56px;border-radius:var(--radius-lg);background:rgba(245,158,11,0.1);display:flex;align-items:center;justify-content:center;font-size:2rem;">🏗️</div>
              <div>
                <h2 style="font-size:1.2rem;">Izin Renovasi Fasad Rumah</h2>
                <span class="badge badge-warning">Menunggu Persetujuan</span>
              </div>
            </div>
            <div style="display:flex;flex-direction:column;gap:12px;font-size:0.88rem;">
              <div style="display:flex;justify-content:space-between;"><span class="text-muted">No. Pengajuan</span><strong>IZN-2026-03-008</strong></div>
              <div style="display:flex;justify-content:space-between;"><span class="text-muted">Pemohon</span><strong>Budi Santoso (A/12)</strong></div>
              <div style="display:flex;justify-content:space-between;"><span class="text-muted">Jenis Izin</span><strong>Renovasi Bangunan</strong></div>
              <div style="display:flex;justify-content:space-between;"><span class="text-muted">Tgl Pengajuan</span><strong>27 Maret 2026</strong></div>
              <div style="display:flex;justify-content:space-between;"><span class="text-muted">Rencana Mulai</span><strong>1 April 2026</strong></div>
              <div style="display:flex;justify-content:space-between;"><span class="text-muted">Estimasi Selesai</span><strong>30 April 2026</strong></div>
            </div>
            <div style="margin-top:20px;padding-top:16px;border-top:1px solid var(--border-color);">
              <h4 style="margin-bottom:8px;">Deskripsi Pekerjaan</h4>
              <p style="font-size:0.88rem;color:var(--text-secondary);line-height:1.7;">Renovasi fasad depan rumah meliputi: pengecatan ulang dinding luar, penggantian atap kanopi, dan pemasangan pagar baru. Tidak ada perubahan struktur utama. Kontraktor: CV Maju Jaya.</p>
            </div>
            <div style="margin-top:16px;">
              <h4 style="margin-bottom:8px;">📎 Lampiran</h4>
              <div style="display:flex;flex-direction:column;gap:6px;">
                <div style="padding:10px;background:var(--bg-input);border-radius:var(--radius-sm);font-size:0.85rem;display:flex;justify-content:space-between;">
                  <span>📐 denah_renovasi.pdf</span><button class="btn btn-sm btn-outline">Unduh</button>
                </div>
                <div style="padding:10px;background:var(--bg-input);border-radius:var(--radius-sm);font-size:0.85rem;display:flex;justify-content:space-between;">
                  <span>📸 foto_kondisi_awal.jpg</span><button class="btn btn-sm btn-outline">Unduh</button>
                </div>
              </div>
            </div>
          </div>
        </div>

        <div>
          <!-- Review Panel -->
          <div class="card" style="padding:20px;margin-bottom:16px;">
            <h4 style="margin-bottom:16px;">⚖️ Review & Keputusan</h4>
            <div class="form-group">
              <label class="form-label">Keputusan</label>
              <div style="display:flex;gap:12px;">
                <button class="btn btn-sm" style="flex:1;background:rgba(16,185,129,0.1);color:var(--success);border:1px solid var(--success);" onclick="showToast('Izin DISETUJUI!','success')">✅ Setujui</button>
                <button class="btn btn-sm" style="flex:1;background:rgba(245,158,11,0.1);color:var(--warning);border:1px solid var(--warning);" onclick="showToast('Perlu revisi dikirim','warning')">🔄 Revisi</button>
                <button class="btn btn-sm" style="flex:1;background:rgba(239,68,68,0.1);color:var(--danger);border:1px solid var(--danger);" onclick="showToast('Izin DITOLAK','error')">❌ Tolak</button>
              </div>
            </div>
            <div class="form-group"><label class="form-label">Catatan Review</label><textarea class="form-control" rows="4" placeholder="Catatan persetujuan / alasan penolakan..."></textarea></div>
            <div class="form-group">
              <label class="form-label">Syarat Tambahan (opsional)</label>
              <textarea class="form-control" rows="2" placeholder="Syarat khusus yang harus dipenuhi pemohon..."></textarea>
            </div>
          </div>

          <!-- Timeline -->
          <div class="card" style="padding:20px;">
            <h4 style="margin-bottom:12px;">📊 Riwayat</h4>
            <div style="display:flex;flex-direction:column;gap:8px;font-size:0.82rem;">
              <div style="padding:10px 14px;background:var(--bg-input);border-radius:var(--radius-sm);">
                <div style="display:flex;justify-content:space-between;"><strong>Pengajuan dikirim</strong><span class="text-muted">27 Mar, 10:15</span></div>
                <div class="text-muted">Oleh: Budi Santoso via aplikasi</div>
              </div>
              <div style="padding:10px 14px;background:var(--bg-input);border-radius:var(--radius-sm);">
                <div style="display:flex;justify-content:space-between;"><strong>Dokumen diverifikasi</strong><span class="text-muted">27 Mar, 14:00</span></div>
                <div class="text-muted">Oleh: Admin — Dokumen lengkap</div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </main>
  </div>
</div>
<?php include 'includes/footer.php'; ?>
