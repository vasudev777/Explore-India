<?php
// Session check in case it's not started yet (failsafe)
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
// Get admin name initial (Default 'A' if not logged in)
$admin_initial = isset($_SESSION['admin_uname']) ? strtoupper(substr($_SESSION['admin_uname'], 0, 1)) : 'A';
$admin_display_name = isset($_SESSION['admin_uname']) ? htmlspecialchars($_SESSION['admin_uname']) : 'Admin';
?>
<div class="topbar">
    <!-- Mobile Menu Toggle Button -->
    <button class="topbar-btn d-md-none" id="menuToggle" style="border: none; background: transparent; margin-right: 10px;" onclick="toggleSidebar()">
        <i class="fa fa-bars"></i>
    </button>

    <!-- Dynamic Page Title based on $page_title variable set on each page -->
    <div class="topbar-title">
        <?= isset($page_title) ? htmlspecialchars($page_title) : 'Explore India Console' ?>
    </div>

    <!-- Search Input (Non-functional quick-search helper) -->
    <div class="topbar-search">
        <i class="fa fa-search"></i>
        <input type="text" placeholder="Global search index...">
    </div>

    <!-- Right Side Actions & Dynamic Profile -->
    <div class="topbar-right">
        <!-- Quick Notification Bell -->
        <div class="topbar-btn">
            <i class="fa fa-bell-o"></i>
            <span class="notif-dot"></span>
        </div>
        
        <!-- Interactive Admin Profile Profile Bubble -->
        <div class="topbar-right" title="Logged in as: <?= $admin_display_name ?>">
            <div class="admin-avatar-wrap">
                <!-- Renders first letter of active admin dynamic username -->
                <div class="admin-avatar">
                    <?= $admin_initial ?>
                </div>
                <div class="admin-status"></div>
            </div>
        </div>
    </div>
</div>

<!-- ══ GLOBAL MOBILE RESPONSIVENESS OVERRIDES ══ -->
<style>
@media (max-width: 992px) {
    .stat-grid { grid-template-columns: repeat(2, 1fr) !important; }
    .data-grid { grid-template-columns: 1fr !important; }
    .actions-grid { grid-template-columns: repeat(2, 1fr) !important; }
}

@media (max-width: 768px) {
    /* Layout */
    .main-content { margin-left: 0 !important; }
    .topbar { left: 0 !important; padding: 0 16px !important; }
    .content-wrap { padding: 16px !important; }
    
    /* Sidebar Drawer */
    .sidebar { transform: translateX(-100%); transition: transform 0.3s ease-in-out; }
    .sidebar.open { transform: translateX(0) !important; }
    
    /* Enable horizontal scrolling on all tables to prevent breakage */
    table {
        display: block !important;
        width: 100% !important;
        overflow-x: auto !important;
        -webkit-overflow-scrolling: touch !important;
    }
}

@media (max-width: 576px) {
    /* Stat Cards */
    .stat-grid { grid-template-columns: 1fr !important; gap: 12px !important; }
    .stat-card { padding: 14px 18px !important; gap: 12px !important; }
    .stat-icon { width: 44px !important; height: 44px !important; font-size: 18px !important; }
    .stat-info .stat-num { font-size: 22px !important; }
    .stat-info .stat-label { font-size: 10px !important; }
    
    /* Quick Actions */
    .actions-grid { grid-template-columns: repeat(2, 1fr) !important; gap: 10px !important; }
    .action-card { padding: 12px 10px !important; }
    .action-icon { width: 36px !important; height: 36px !important; font-size: 16px !important; margin: 0 auto 6px !important; }
    .action-label { font-size: 11px !important; }
    
    /* Content scale */
    .page-header h1 { font-size: 20px !important; }
    .data-card-body { padding: 14px !important; }
    .chart-wrap { padding: 10px !important; }
    .admin-footer { padding: 16px !important; flex-direction: column; gap: 8px; text-align: center; }
}
</style>