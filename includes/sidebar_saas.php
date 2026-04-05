<?php
/**
 * Centralized SaaS Sidebar for Superadmin/Developer
 */
$current_page = basename($_SERVER['PHP_SELF']);
$GLOBAL_SETTINGS = $GLOBAL_SETTINGS ?? ['app_name' => 'WargaKu', 'logo_path' => ''];
?>
<aside class="sidebar" id="sidebar">
    <div class="sidebar-brand" style="justify-content:center; padding: 15px 0;">
        <div class="brand-icon" style="width: auto; height: auto;">
            <?php if (!empty($GLOBAL_SETTINGS['logo_path'])): ?>
                <img src="<?= htmlspecialchars($GLOBAL_SETTINGS['logo_path']) ?>" alt="Logo" style="max-height:60px; max-width:80%; object-fit:contain;">
            <?php else: ?>
                <div style="font-size:1.5rem;">💻</div>
            <?php endif; ?>
        </div>
    </div>
    <nav class="sidebar-menu">
        <div class="menu-label">Menu App Pusat</div>
        
        <?php if (Auth::canAccess('saas_dashboard.php')): ?>
        <a class="menu-item <?= $current_page == 'saas_dashboard.php' ? 'active' : '' ?>" href="saas_dashboard.php">
            <span class="menu-icon"><i data-lucide="layout-dashboard"></i></span>
            <span>Dashboard</span>
        </a>
        <?php endif; ?>

        <?php if (Auth::canAccess('saas_tenants.php')): ?>
        <a class="menu-item <?= ($current_page == 'saas_tenants.php' || $current_page == 'saas_tenant_detail.php') ? 'active' : '' ?>" href="saas_tenants.php">
            <span class="menu-icon"><i data-lucide="building"></i></span>
            <span>Daftar Klien</span>
        </a>
        <?php endif; ?>
        
        <?php if (Auth::canAccess('saas_tickets.php')): ?>
        <a class="menu-item <?= ($current_page == 'saas_tickets.php' || $current_page == 'ticket_detail.php') ? 'active' : '' ?>" href="saas_tickets.php">
            <span class="menu-icon"><i data-lucide="help-circle"></i></span>
            <span>Tiket Support</span>
        </a>
        <?php endif; ?>
        
        <?php if (Auth::canAccess('saas_comms.php')): ?>
        <a class="menu-item <?= ($current_page == 'saas_comms.php' || $current_page == 'saas_broadcast.php' || $current_page == 'saas_announcements.php') ? 'active' : '' ?>" href="saas_comms.php">
            <span class="menu-icon"><i data-lucide="megaphone"></i></span>
            <span>Pusat Komunikasi</span>
        </a>
        <?php endif; ?>
        
        <?php if (Auth::canAccess('saas_billing.php')): ?>
        <a class="menu-item <?= $current_page == 'saas_billing.php' ? 'active' : '' ?>" href="saas_billing.php">
            <span class="menu-icon"><i data-lucide="credit-card"></i></span>
            <span>Tagihan SaaS</span>
        </a>
        <?php endif; ?>

        <?php if (Auth::canAccess('saas_pricing.php')): ?>
        <a class="menu-item <?= $current_page == 'saas_pricing.php' ? 'active' : '' ?>" href="saas_pricing.php">
            <span class="menu-icon"><i data-lucide="tag"></i></span>
            <span>Harga & Paket</span>
        </a>
        <?php endif; ?>
        
        <?php if (Auth::canAccess('saas_audit.php')): ?>
        <a class="menu-item <?= $current_page == 'saas_audit.php' ? 'active' : '' ?>" href="saas_audit.php">
            <span class="menu-icon"><i data-lucide="shield-alert"></i></span>
            <span>Audit Log Sistem</span>
        </a>
        <?php endif; ?>

        <?php if (Auth::canAccess('saas_users.php')): ?>
        <a class="menu-item <?= $current_page == 'saas_users.php' ? 'active' : '' ?>" href="saas_users.php">
            <span class="menu-icon"><i data-lucide="user-plus"></i></span>
            <span>Admin Pusat</span>
        </a>
        <?php endif; ?>

        <?php if (Auth::canAccess('saas_roles.php')): ?>
        <a class="menu-item <?= $current_page == 'saas_roles.php' ? 'active' : '' ?>" href="saas_roles.php">
            <span class="menu-icon"><i data-lucide="lock"></i></span>
            <span>Pengaturan Role</span>
        </a>
        <?php endif; ?>
        
        <?php if (Auth::canAccess('saas_settings.php')): ?>
        <a class="menu-item <?= ($current_page == 'saas_settings.php' || $current_page == 'saas_backup.php') ? 'active' : '' ?>" href="saas_settings.php">
            <span class="menu-icon"><i data-lucide="settings"></i></span>
            <span>Pengaturan Global</span>
        </a>
        <?php endif; ?>
    </nav>
</aside>
