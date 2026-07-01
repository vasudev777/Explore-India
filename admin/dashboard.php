<?php 
include('check.php');
include('db.php');

// DB counts
$customers   = mysqli_num_rows(mysqli_query($conn, "SELECT * FROM customer_details"));
$guides      = mysqli_num_rows(mysqli_query($conn, "SELECT * FROM local_guide"));
$hotels      = mysqli_num_rows(mysqli_query($conn, "SELECT * FROM hotel"));
$packages    = mysqli_num_rows(mysqli_query($conn, "SELECT * FROM package"));
$states      = mysqli_num_rows(mysqli_query($conn, "SELECT * FROM state"));
$cities      = mysqli_num_rows(mysqli_query($conn, "SELECT * FROM city"));
$trains      = mysqli_num_rows(mysqli_query($conn, "SELECT * FROM trains"));
$tickets     = mysqli_num_rows(mysqli_query($conn, "SELECT * FROM ticket"));
$predef_book = mysqli_num_rows(mysqli_query($conn, "SELECT * FROM predefine_booking"));
$cust_book   = mysqli_num_rows(mysqli_query($conn, "SELECT * FROM customize_booking"));
$trans_book  = mysqli_num_rows(mysqli_query($conn, "SELECT * FROM transport_bookings"));

// Revenue
$rev_r = mysqli_query($conn, "SELECT SUM(fare) as total FROM transport_bookings WHERE status='Confirmed'");
$rev   = mysqli_fetch_assoc($rev_r)['total'] ?? 0;
$rev2_r = mysqli_query($conn, "SELECT SUM(amount) as total FROM predefine_booking WHERE status='Confirmed'");
$rev  += mysqli_fetch_assoc($rev2_r)['total'] ?? 0;

$page_title = "Dashboard"; // Dynamic title variable for header.php
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Admin Dashboard – Explore India</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.1.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/font-awesome@4.7.0/css/font-awesome.min.css">
    <link href="https://fonts.googleapis.com/css?family=Montserrat:400,600,700,800,900&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css?family=Open+Sans:400,600&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/chart.js@2.7.3/dist/Chart.bundle.min.css" rel="stylesheet">
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        :root {
            --sidebar-w: 250px;
            --bg: #f5f6fa;
            --card: #ffffff;
            --dark: #0f172a;
            --orange: #ff782c; /* Modern Glowing Saffron Accent */
            --green: #10b981;  /* Emerald Green */
            --blue: #3b82f6;   /* Royal Blue */
            --purple: #8b5cf6; /* Violet Purple */
            --red: #ef4444;    /* Crimson Red */
            --border: #e2e8f0;
            --text: #475569;
            --muted: #94a3b8;
            --header-h: 64px;
        }

        body { background: var(--bg); font-family: 'Open Sans', sans-serif; color: var(--text); overflow-x: hidden; }

        /* ══ SIDEBAR STYLES ══ */
        .sidebar {
            position: fixed; top: 0; left: 0;
            width: var(--sidebar-w); height: 100vh;
            background: #09090b;
            display: flex; flex-direction: column;
            z-index: 1000; transition: transform 0.3s;
        }
        .sidebar-logo {
            padding: 24px 20px 20px;
            border-bottom: 1px solid rgba(255,255,255,0.06);
            display: flex; align-items: center; gap: 12px;
        }
        .sidebar-logo .logo-icon { font-size: 24px; }
        .sidebar-logo h2 { font-family: 'Montserrat', sans-serif; font-size: 17px; font-weight: 800; color: #fff; margin: 0; }
        .sidebar-logo h2 span { color: var(--orange); }
        .sidebar-logo .badge-admin { font-size: 9px; font-weight: 700; letter-spacing: 1px; background: rgba(255,120,44,0.12); border: 1px solid rgba(255,120,44,0.25); color: var(--orange); padding: 2px 8px; border-radius: 10px; margin-top: 4px; display: inline-block; }

        .sidebar-nav { flex: 1; padding: 16px 0; overflow-y: auto; }
        .nav-label { font-size: 9px; font-weight: 800; letter-spacing: 2px; text-transform: uppercase; color: rgba(255,255,255,0.2); padding: 12px 20px 6px; }
        .nav-item { display: flex; align-items: center; gap: 12px; padding: 11px 20px; font-size: 13px; font-weight: 600; color: rgba(255,255,255,0.5); text-decoration: none; transition: all 0.2s; cursor: pointer; border-left: 3px solid transparent; }
        .nav-item:hover { background: rgba(255,255,255,0.03); color: #fff; text-decoration: none; }
        .nav-item.active { background: rgba(255,120,44,0.08); color: var(--orange); border-left-color: var(--orange); }
        .nav-item .nav-icon { width: 18px; text-align: center; font-size: 14px; }
        .nav-item .nav-badge { margin-left: auto; font-size: 10px; font-weight: 700; padding: 2px 7px; border-radius: 10px; }

        .sidebar-footer { padding: 16px 20px; border-top: 1px solid rgba(255,255,255,0.06); }
        .logout-btn { display: flex; align-items: center; gap: 10px; color: rgba(255,255,255,0.4); font-size: 13px; font-weight: 600; text-decoration: none; padding: 8px 0; transition: color 0.2s; }
        .logout-btn:hover { color: var(--red); text-decoration: none; }

        /* ══ TOPBAR STYLES ══ */
        .topbar {
            position: fixed; top: 0; left: var(--sidebar-w); right: 0;
            height: var(--header-h); background: #fff;
            border-bottom: 1px solid var(--border);
            display: flex; align-items: center;
            padding: 0 28px; gap: 16px; z-index: 999;
            box-shadow: 0 4px 20px rgba(15, 23, 42, 0.02);
        }
        .topbar-title { font-family: 'Montserrat', sans-serif; font-size: 18px; font-weight: 800; color: var(--dark); flex: 1; }
        .topbar-search { display: flex; align-items: center; gap: 8px; background: #f1f5f9; border: 1.5px solid var(--border); border-radius: 50px; padding: 6px 14px; }
        .topbar-search input { border: none; background: transparent; font-size: 13px; color: var(--dark); outline: none; width: 180px; }
        .topbar-search .fa { color: var(--muted); font-size: 13px; }
        .topbar-right { display: flex; align-items: center; gap: 14px; }
        .topbar-btn { width: 38px; height: 38px; border-radius: 50%; border: 1.5px solid var(--border); background: #f8fafc; display: flex; align-items: center; justify-content: center; cursor: pointer; position: relative; color: var(--text); font-size: 14px; transition: all 0.2s; }
        .topbar-btn:hover { border-color: var(--orange); color: var(--orange); }
        .notif-dot { position: absolute; top: 4px; right: 4px; width: 8px; height: 8px; border-radius: 50%; background: var(--red); border: 2px solid #fff; }
        .admin-avatar { width: 38px; height: 38px; border-radius: 50%; background: linear-gradient(135deg, var(--orange), #d48a1a); display: flex; align-items: center; justify-content: center; font-family: 'Montserrat', sans-serif; font-size: 14px; font-weight: 800; color: #fff; cursor: pointer; }

        /* ══ MAIN LAYOUT ══ */
        .main-content { margin-left: var(--sidebar-w); padding-top: var(--header-h); min-height: 100vh; }
        .content-wrap { padding: 28px; }

        /* Page header */
        .page-header { margin-bottom: 24px; }
        .page-header h1 { font-family: 'Montserrat', sans-serif; font-size: 24px; font-weight: 800; color: var(--dark); margin-bottom: 4px; }
        .page-header p { font-size: 13px; color: var(--muted); margin: 0; }
        .breadcrumb-custom { font-size: 12px; color: var(--muted); display: flex; align-items: center; gap: 6px; margin-bottom: 6px; }
        .breadcrumb-custom .fa { font-size: 10px; }

        /* ══ STAT CARDS ══ */
        .stat-grid { display: grid; grid-template-columns: repeat(4, 1fr); gap: 20px; margin-bottom: 24px; }
        .stat-card {
            background: #fff; border: 1.5px solid var(--border);
            border-radius: 16px; padding: 20px 22px;
            display: flex; align-items: center; gap: 16px;
            transition: transform 0.2s, box-shadow 0.2s;
        }
        .stat-card:hover { transform: translateY(-3px); box-shadow: 0 10px 30px rgba(15, 23, 42, 0.05); }
        .stat-icon { width: 52px; height: 52px; border-radius: 14px; display: flex; align-items: center; justify-content: center; font-size: 22px; flex-shrink: 0; }
        .stat-info .stat-num { font-family: 'Montserrat', sans-serif; font-size: 26px; font-weight: 900; margin-bottom: 3px; }
        .stat-info .stat-label { font-size: 11px; color: var(--muted); font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px; }
        .stat-info .stat-change { font-size: 11px; margin-top: 4px; font-weight: 600; display: flex; align-items: center; gap: 4px; }

        /* ══ DATA CARDS ══ */
        .data-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 24px; }
        .data-card { background: #fff; border: 1.5px solid var(--border); border-radius: 16px; overflow: hidden; }
        .data-card-header { padding: 18px 22px 14px; border-bottom: 1px solid var(--border); display: flex; align-items: center; justify-content: space-between; }
        .data-card-header h3 { font-family: 'Montserrat', sans-serif; font-size: 14px; font-weight: 800; color: var(--dark); margin: 0; }
        .data-card-header .view-all { font-size: 12px; color: var(--orange); font-weight: 600; text-decoration: none; }
        .data-card-header .view-all:hover { text-decoration: underline; }
        .data-card-body { padding: 20px 22px; }

        /* Progress items */
        .progress-item { margin-bottom: 16px; }
        .progress-item:last-child { margin-bottom: 0; }
        .progress-top { display: flex; justify-content: space-between; margin-bottom: 6px; }
        .progress-label { font-size: 13px; font-weight: 600; color: var(--text); }
        .progress-value { font-size: 13px; font-weight: 700; font-family: 'Montserrat', sans-serif; }
        .progress-bar-wrap { height: 6px; background: #e2e8f0; border-radius: 3px; overflow: hidden; }
        .progress-bar-fill { height: 100%; border-radius: 3px; transition: width 1s ease; }

        /* Chart */
        .chart-wrap { padding: 16px 20px; }

        /* ══ QUICK ACTIONS ══ */
        .actions-grid { display: grid; grid-template-columns: repeat(4, 1fr); gap: 14px; margin-bottom: 24px; }
        .action-card { background: #fff; border: 1.5px solid var(--border); border-radius: 14px; padding: 18px 16px; text-align: center; text-decoration: none; transition: all 0.2s; }
        .action-card:hover { border-color: var(--orange); box-shadow: 0 10px 20px rgba(15, 23, 42, 0.05); transform: translateY(-2px); text-decoration: none; }
        .action-icon { width: 44px; height: 44px; border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 18px; margin: 0 auto 10px; }
        .action-label { font-size: 12px; font-weight: 700; color: var(--text); }

        /* ══ FOOTER ══ */
        .admin-footer { background: #fff; border-top: 1px solid var(--border); padding: 18px 28px; font-size: 12px; color: var(--muted); display: flex; justify-content: space-between; align-items: center; }

        /* Responsive */
        @media (max-width: 992px) {
            .stat-grid { grid-template-columns: repeat(2, 1fr); }
            .data-grid { grid-template-columns: 1fr; }
            .actions-grid { grid-template-columns: repeat(2, 1fr); }
        }
        @media (max-width: 768px) {
            .sidebar { transform: translateX(-100%); }
            .sidebar.open { transform: translateX(0); }
            .main-content { margin-left: 0; }
            .topbar { left: 0; }
            .stat-grid { grid-template-columns: 1fr 1fr; }
            .actions-grid { grid-template-columns: repeat(2, 1fr); }
            .topbar-search { display: none; }
        }
    </style>
</head>
<body>

<!-- SIDEBAR INCLUDE -->
<?php include('sidebar.php'); ?>

<!-- TOPBAR INCLUDE -->
<?php include('header.php'); ?>

<!-- MAIN CONTENT -->
<div class="main-content">
    <div class="content-wrap">

        <!-- Page Header -->
        <div class="page-header">
            <div class="breadcrumb-custom">
                <span class="fa fa-home"></span> Home <span class="fa fa-chevron-right"></span> Dashboard
            </div>
            <h1>Welcome Back, Admin 👋</h1>
            <p>Here's what's happening with Explore India today.</p>
        </div>

        <!-- STAT CARDS -->
        <div class="stat-grid">
            <div class="stat-card">
                <div class="stat-icon" style="background:rgba(94,160,255,0.1); border:1px solid rgba(94,160,255,0.2);">👥</div>
                <div class="stat-info">
                    <div class="stat-num" style="color:var(--blue);"><?= $customers ?></div>
                    <div class="stat-label">Customers</div>
                    <div class="stat-change" style="color:var(--green);"><span class="fa fa-arrow-up"></span> Total Registered</div>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon" style="background:rgba(245,166,35,0.1); border:1px solid rgba(245,166,35,0.2);">🏨</div>
                <div class="stat-info">
                    <div class="stat-num" style="color:var(--orange);"><?= $hotels ?></div>
                    <div class="stat-label">Hotels</div>
                    <div class="stat-change" style="color:var(--green);"><span class="fa fa-arrow-up"></span> Across India</div>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon" style="background:rgba(16,185,129,0.1); border:1px solid rgba(16,185,129,0.2);">📦</div>
                <div class="stat-info">
                    <div class="stat-num" style="color:var(--green);"><?= $packages ?></div>
                    <div class="stat-label">Packages</div>
                    <div class="stat-change" style="color:var(--green);"><span class="fa fa-check"></span> Active</div>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon" style="background:rgba(139,92,246,0.1); border:1px solid rgba(139,92,246,0.2);">💰</div>
                <div class="stat-info">
                    <div class="stat-num" style="color:var(--purple);">₹<?= number_format($rev/1000, 1) ?>k</div>
                    <div class="stat-label">Revenue</div>
                    <div class="stat-change" style="color:var(--green);"><span class="fa fa-arrow-up"></span> Total Confirmed</div>
                </div>
            </div>
        </div>

        <!-- QUICK ACTIONS -->
        <div class="actions-grid">
            <a href="customer.php" class="action-card">
                <div class="action-icon" style="background:rgba(94,160,255,0.1);">👥</div>
                <div class="action-label" style="color:var(--blue);">Customers</div>
            </a>
            <a href="package.php" class="action-card">
                <div class="action-icon" style="background:rgba(245,166,35,0.1);">📦</div>
                <div class="action-label" style="color:#d48a1a;">Packages</div>
            </a>
            <a href="hotel.php" class="action-card">
                <div class="action-icon" style="background:rgba(16,185,129,0.1);">🏨</div>
                <div class="action-label" style="color:var(--green);">Hotels</div>
            </a>
            <a href="local_guide.php" class="action-card">
                <div class="action-icon" style="background:rgba(139,92,246,0.1);">👤</div>
                <div class="action-label" style="color:var(--purple);">Local Guides</div>
            </a>
        </div>

        <!-- DATA CARDS -->
        <div class="data-grid">

            <!-- Left: Stats -->
            <div class="data-card">
                <div class="data-card-header">
                    <h3>📊 Platform Overview</h3>
                </div>
                <div class="data-card-body">
                    <div class="progress-item">
                        <div class="progress-top">
                            <span class="progress-label">States</span>
                            <span class="progress-value" style="color:var(--blue);"><?= $states ?></span>
                        </div>
                        <div class="progress-bar-wrap"><div class="progress-bar-fill" style="width:<?= min($states*5,100) ?>%; background:var(--blue);"></div></div>
                    </div>
                    <div class="progress-item">
                        <div class="progress-top">
                            <span class="progress-label">Cities</span>
                            <span class="progress-value" style="color:var(--orange);"><?= $cities ?></span>
                        </div>
                        <div class="progress-bar-wrap"><div class="progress-bar-fill" style="width:<?= min($cities*2,100) ?>%; background:var(--orange);"></div></div>
                    </div>
                    <div class="progress-item">
                        <div class="progress-top">
                            <span class="progress-label">Train Routes</span>
                            <span class="progress-value" style="color:var(--green);"><?= $trains ?></span>
                        </div>
                        <div class="progress-bar-wrap"><div class="progress-bar-fill" style="width:<?= min($trains,100) ?>%; background:var(--green);"></div></div>
                    </div>
                    <div class="progress-item">
                        <div class="progress-top">
                            <span class="progress-label">Local Guides</span>
                            <span class="progress-value" style="color:var(--purple);"><?= $guides ?></span>
                        </div>
                        <div class="progress-bar-wrap"><div class="progress-bar-fill" style="width:<?= min($guides*10,100) ?>%; background:var(--purple);"></div></div>
                    </div>
                    <div class="progress-item">
                        <div class="progress-top">
                            <span class="progress-label">Train Tickets</span>
                            <span class="progress-value" style="color:var(--red);"><?= $tickets ?></span>
                        </div>
                        <div class="progress-bar-wrap"><div class="progress-bar-fill" style="width:<?= min($tickets*5,100) ?>%; background:var(--red);"></div></div>
                    </div>
                </div>
            </div>

            <!-- Right: Bookings Chart -->
            <div class="data-card">
                <div class="data-card-header">
                    <h3>📋 Booking Summary</h3>
                </div>
                <div class="chart-wrap">
                    <canvas id="bookingChart" height="200"></canvas>
                </div>
                <div class="data-card-body" style="padding-top:0;">
                    <div style="display:flex; gap:12px; flex-wrap:wrap;">
                        <div style="flex:1; background:#f0fff8; border:1px solid #c3f0df; border-radius:10px; padding:12px 14px; text-align:center;">
                            <div style="font-family:'Montserrat',sans-serif; font-size:20px; font-weight:800; color:#2ecc9a;"><?= $predef_book ?></div>
                            <div style="font-size:11px; color:var(--muted); margin-top:3px;">Special Pkg</div>
                        </div>
                        <div style="flex:1; background:#fffdf5; border:1px solid #fde8b0; border-radius:10px; padding:12px 14px; text-align:center;">
                            <div style="font-family:'Montserrat',sans-serif; font-size:20px; font-weight:800; color:#d48a1a;"><?= $cust_book ?></div>
                            <div style="font-size:11px; color:var(--muted); margin-top:3px;">Custom Pkg</div>
                        </div>
                        <div style="flex:1; background:#f0f6ff; border:1px solid #c0d8ff; border-radius:10px; padding:12px 14px; text-align:center;">
                            <div style="font-family:'Montserrat',sans-serif; font-size:20px; font-weight:800; color:#5ea0ff;"><?= $trans_book ?></div>
                            <div style="font-size:11px; color:var(--muted); margin-top:3px;">Transport</div>
                        </div>
                    </div>
                </div>
            </div>

        </div>

    </div>

    <!-- FOOTER -->
    <div class="admin-footer">
        <span>© 2024 Explore India — Admin Panel</span>
        <span>Made with ❤️ in India</span>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/jquery@2.2.4/dist/jquery.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.1.3/dist/js/bootstrap.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js@2.7.3/dist/Chart.bundle.min.js"></script>
<script>
// Booking Chart
var ctx = document.getElementById('bookingChart').getContext('2d');
new Chart(ctx, {
    type: 'doughnut',
    data: {
        labels: ['Special Packages', 'Custom Packages', 'Transport'],
        datasets: [{
            data: [<?= $predef_book ?>, <?= $cust_book ?>, <?= $trans_book ?>],
            backgroundColor: ['#2ecc9a', '#f5a623', '#5ea0ff'],
            borderWidth: 0,
            hoverOffset: 6
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: true,
        legend: { position: 'bottom', labels: { fontSize: 12, padding: 16 } },
        cutoutPercentage: 65
    }
});

// Mobile sidebar toggle
function toggleSidebar() {
    document.getElementById('sidebar').classList.toggle('open');
}

// Show menu toggle on mobile
if (window.innerWidth <= 768) {
    document.getElementById('menuToggle').style.display = 'block';
}
window.addEventListener('resize', function() {
    document.getElementById('menuToggle').style.display = window.innerWidth <= 768 ? 'block' : 'none';
});
</script>
</body>
</html>