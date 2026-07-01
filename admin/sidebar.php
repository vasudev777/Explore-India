<?php
// Ensure Database connection is active
if (isset($conn)) {
    // If counts are not calculated globally, fetch them dynamically for the sidebar
    if (!isset($customers)) $customers = mysqli_num_rows(mysqli_query($conn, "SELECT * FROM customer_details"));
    if (!isset($packages))   $packages  = mysqli_num_rows(mysqli_query($conn, "SELECT * FROM package"));
    if (!isset($hotels))     $hotels    = mysqli_num_rows(mysqli_query($conn, "SELECT * FROM hotel"));
    if (!isset($guides))     $guides    = mysqli_num_rows(mysqli_query($conn, "SELECT * FROM local_guide"));
    if (!isset($predef_book)) $predef_book = mysqli_num_rows(mysqli_query($conn, "SELECT * FROM predefine_booking"));
    if (!isset($cust_book))   $cust_book   = mysqli_num_rows(mysqli_query($conn, "SELECT * FROM customize_booking"));
    if (!isset($trans_book))  $trans_book  = mysqli_num_rows(mysqli_query($conn, "SELECT * FROM transport_bookings"));
}
?>
<!-- SIDEBAR -->
<div class="sidebar" id="sidebar">
    <div class="sidebar-logo">
        <div class="logo-icon">🇮🇳</div>
        <div>
            <h2>Explore <span>India</span></h2>
            <span class="badge-admin">Admin Panel</span>
        </div>
    </div>
    <nav class="sidebar-nav">
        <div class="nav-label">Main</div>
        <a href="index.php" class="nav-item <?= basename($_SERVER['PHP_SELF']) == 'index.php' ? 'active' : '' ?>">
            <span class="nav-icon fa fa-th-large"></span> Dashboard
        </a>
        <a href="customer_manage.php" class="nav-item <?= basename($_SERVER['PHP_SELF']) == 'customer_manage.php' ? 'active' : '' ?>">
            <span class="nav-icon fa fa-users"></span> Customers
            <span class="nav-badge" style="background:rgba(94,160,255,0.12); color:#5ea0ff;"><?= $customers ?? 0 ?></span>
        </a>
        <div class="nav-label">Packages</div>
        <a href="package_manage.php" class="nav-item <?= basename($_SERVER['PHP_SELF']) == 'package_manage.php' ? 'active' : '' ?>">
            <span class="nav-icon fa fa-suitcase"></span> Packages
            <span class="nav-badge" style="background:rgba(245,166,35,0.12); color:#f5a623;"><?= $packages ?? 0 ?></span>
        </a>
        <a href="hotel_manage.php" class="nav-item <?= basename($_SERVER['PHP_SELF']) == 'hotel_manage.php' ? 'active' : '' ?>">
            <span class="nav-icon fa fa-building"></span> Hotels
            <span class="nav-badge" style="background:rgba(46,204,113,0.12); color:#2ecc71;"><?= $hotels ?? 0 ?></span>
        </a>
        <a href="localguidemanage.php" class="nav-item <?= basename($_SERVER['PHP_SELF']) == 'localguidemanage.php' ? 'active' : '' ?>">
            <span class="nav-icon fa fa-user-circle"></span> Local Guides
            <span class="nav-badge" style="background:rgba(180,124,255,0.12); color:#b47cff;"><?= $guides ?? 0 ?></span>
        </a>
        <div class="nav-label">Bookings</div>
        <a href="predefine_bookings_manage.php" class="nav-item <?= basename($_SERVER['PHP_SELF']) == 'predefine_bookings_manage.php' ? 'active' : '' ?>">
            <span class="nav-icon fa fa-star"></span> Special Bookings
            <span class="nav-badge" style="background:rgba(46,204,113,0.12); color:#2ecc71;"><?= $predef_book ?? 0 ?></span>
        </a>
        <a href="customize_bookings_manage.php" class="nav-item <?= basename($_SERVER['PHP_SELF']) == 'customize_bookings_manage.php' ? 'active' : '' ?>">
            <span class="nav-icon fa fa-map-signs"></span> Custom Bookings
            <span class="nav-badge" style="background:rgba(245,166,35,0.12); color:#f5a623;"><?= $cust_book ?? 0 ?></span>
        </a>
        <a href="transport_bookings_manage.php" class="nav-item <?= basename($_SERVER['PHP_SELF']) == 'transport_bookings_manage.php' ? 'active' : '' ?>">
            <span class="nav-icon fa fa-plane"></span> Transport Bookings
            <span class="nav-badge" style="background:rgba(94,160,255,0.12); color:#5ea0ff;"><?= $trans_book ?? 0 ?></span>
        </a>
        <div class="nav-label">Data</div>
        <a href="state_manage.php" class="nav-item <?= basename($_SERVER['PHP_SELF']) == 'state_manage.php' ? 'active' : '' ?>">
            <span class="nav-icon fa fa-map"></span> States
        </a>
        <a href="city_manage.php" class="nav-item <?= basename($_SERVER['PHP_SELF']) == 'city_manage.php' ? 'active' : '' ?>">
            <span class="nav-icon fa fa-map-marker"></span> Cities
        </a>
        <a href="transport_city.php" class="nav-item <?= basename($_SERVER['PHP_SELF']) == 'transport_city.php' ? 'active' : '' ?>">
            <span class="nav-icon fa fa-train"></span> Transport
        </a>
        <a href="feedback_manage.php" class="nav-item <?= basename($_SERVER['PHP_SELF']) == 'feedback_manage.php' ? 'active' : '' ?>">
            <span class="nav-icon fa fa-comment"></span> Feedback
        </a>
    </nav>
    <div class="sidebar-footer">
        <a href="logout.php" class="logout-btn">
            <span class="fa fa-sign-out"></span> Logout
        </a>
    </div>
</div>