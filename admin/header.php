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