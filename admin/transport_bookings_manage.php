<?php
include('check.php');
include('db.php');

// ===== FILTERS & SEARCH =====
$search = $_GET['search'] ?? '';
$type_filter = $_GET['type'] ?? 'all';
$status_filter = $_GET['status'] ?? 'all';

$where = "WHERE 1=1";

if ($type_filter !== 'all') {
    $t = mysqli_real_escape_string($conn, $type_filter);
    $where .= " AND tb.type = '$t'";
}

if ($status_filter !== 'all') {
    $st = mysqli_real_escape_string($conn, $status_filter);
    $where .= " AND tb.status = '$st'";
}

if ($search) {
    $s = mysqli_real_escape_string($conn, $search);
    $where .= " AND (tb.booking_ref LIKE '%$s%' OR tb.payment_id LIKE '%$s%' OR tb.from_city LIKE '%$s%' OR tb.to_city LIKE '%$s%' OR cd.cust_fname LIKE '%$s%' OR cd.cust_lname LIKE '%$s%')";
}

// SQL Query with LEFT JOIN to fetch Customer Details
$query = "SELECT tb.*, cd.cust_fname, cd.cust_lname, cd.cust_email, cd.cust_mobile
          FROM transport_bookings tb
          LEFT JOIN customer_details cd ON tb.cust_id = cd.cust_id
          $where
          ORDER BY tb.booking_id DESC";

$result = mysqli_query($conn, $query);
$total_rows = $result ? mysqli_num_rows($result) : 0;

// Stats calculations
$stats_total = mysqli_num_rows(mysqli_query($conn, "SELECT * FROM transport_bookings"));
$stats_pending = mysqli_num_rows(mysqli_query($conn, "SELECT * FROM transport_bookings WHERE status='Pending'"));

$rev_res = mysqli_query($conn, "SELECT SUM(fare) as total FROM transport_bookings WHERE status='Confirmed'");
$total_revenue = mysqli_fetch_assoc($rev_res)['total'] ?? 0;

$page_title = "Transport Bookings"; // Sets Topbar Title dynamically
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Transport Bookings – Explore India Admin</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.1.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/font-awesome@4.7.0/css/font-awesome.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;600;700;800;900&family=Open+Sans:wght@400;600;700&display=swap" rel="stylesheet">
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        :root {
            --sidebar-w: 260px;
            --bg: #F4F6FA;
            --card: #ffffff;
            --dark: #0F172A;
            --orange: #FF782C;
            --orange-grad: linear-gradient(135deg, #FF782C, #F39C12);
            --green: #10B981;  
            --green-grad: linear-gradient(135deg, #10B981, #059669);
            --blue: #3B82F6;   
            --blue-grad: linear-gradient(135deg, #3B82F6, #1D4ED8);
            --red: #EF4444;    
            --red-grad: linear-gradient(135deg, #EF4444, #DC2626);
            --border: #E2E8F0;
            --text: #475569;
            --muted: #94A3B8;
            --header-h: 64px;
        }

        body { background: var(--bg); font-family: 'Open Sans', sans-serif; color: var(--text); overflow-x: hidden; }

        /* ══ SIDEBAR STYLES ══ */
        .sidebar {
            position: fixed; top: 0; left: 0;
            width: var(--sidebar-w); height: 100vh;
            background: #09090B;
            display: flex; flex-direction: column;
            z-index: 1000; transition: transform 0.3s;
            box-shadow: 4px 0 20px rgba(0,0,0,0.15);
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
        .nav-item .nav-badge { margin-left: auto; font-size: 10px; font-weight: 700; padding: 2px 7px; border-radius: 10px; }

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
        .topbar-search { display: flex; align-items: center; gap: 8px; background: #f1f5f9; border: 1.5px solid var(--border); border-radius: 50px; padding: 6px 16px; }
        .topbar-search input { border: none; background: transparent; font-size: 13px; color: var(--dark); outline: none; width: 180px; }
        .topbar-search .fa { color: var(--muted); font-size: 13px; }
        .topbar-right { display: flex; align-items: center; gap: 14px; }
        .topbar-btn { width: 38px; height: 38px; border-radius: 50%; border: 1.5px solid var(--border); background: #f8fafc; display: flex; align-items: center; justify-content: center; cursor: pointer; position: relative; color: var(--text); font-size: 14px; transition: all 0.2s; }
        .topbar-btn:hover { border-color: var(--orange); color: var(--orange); }
        .notif-dot { position: absolute; top: 4px; right: 4px; width: 8px; height: 8px; border-radius: 50%; background: var(--red); border: 2px solid #fff; }
        .admin-avatar { width: 38px; height: 38px; border-radius: 50%; background: var(--orange-grad); display: flex; align-items: center; justify-content: center; font-family: 'Montserrat', sans-serif; font-size: 14px; font-weight: 800; color: #fff; cursor: pointer; }

        /* ══ MAIN LAYOUT ══ */
        .main-content { margin-left: var(--sidebar-w); padding-top: var(--header-h); min-height: 100vh; }
        .content-wrap { padding: 28px; }

        /* Page header */
        .page-header { margin-bottom: 24px; }
        .page-header h1 { font-family: 'Montserrat', sans-serif; font-size: 24px; font-weight: 800; color: var(--dark); margin-bottom: 4px; }
        .page-header p { font-size: 13px; color: var(--muted); margin: 0; }

        /* ══ STAT CARDS ══ */
        .stat-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 20px; margin-bottom: 24px; }
        .stat-card {
            background: #fff; border: 1.5px solid var(--border);
            border-radius: 20px; padding: 22px 24px;
            display: flex; align-items: center; gap: 16px;
            transition: transform 0.2s, box-shadow 0.2s;
        }
        .stat-card:hover { transform: translateY(-3px); box-shadow: 0 10px 30px rgba(15, 23, 42, 0.05); }
        .stat-icon { width: 52px; height: 52px; border-radius: 14px; display: flex; align-items: center; justify-content: center; font-size: 22px; flex-shrink: 0; color: #fff; }
        .stat-info .stat-num { font-family: 'Montserrat', sans-serif; font-size: 26px; font-weight: 900; margin-bottom: 2px; color: var(--dark); }
        .stat-info .stat-label { font-size: 11px; color: var(--muted); font-weight: 800; text-transform: uppercase; letter-spacing: 0.5px; }

        /* ══ TABLE CARD ══ */
        .za-card { background: #fff; border: 1.5px solid var(--border); border-radius: 20px; overflow: hidden; box-shadow: 0 4px 20px rgba(0,0,0,0.01); }
        .za-card-header { padding: 20px 24px; border-bottom: 1px solid var(--border); display: flex; align-items: center; justify-content: space-between; gap: 12px; flex-wrap: wrap; }
        .za-card-title { font-family: 'Montserrat', sans-serif; font-size: 16px; font-weight: 800; color: var(--dark); }

        .za-table { width: 100%; border-collapse: collapse; font-size: 13.5px; }
        .za-table th { font-size: 11px; font-weight: 700; letter-spacing: 1px; text-transform: uppercase; color: var(--muted); padding: 14px 18px; text-align: left; border-bottom: 1px solid var(--border); background: #FCFDFE; white-space: nowrap; }
        .za-table td { padding: 14px 18px; border-bottom: 1px solid #F1F5F9; vertical-align: middle; }
        .za-table tr:hover td { background: #F8FAFC; }

        /* BADGES */
        .za-badge { display: inline-flex; align-items: center; gap: 5px; padding: 4px 10px; border-radius: 50px; font-size: 11px; font-weight: 700; text-transform: uppercase; }
        .za-badge.green  { background: rgba(16,185,129,0.1); color: var(--green); }
        .za-badge.red    { background: rgba(239,68,68,0.1); color: var(--red); }
        .za-badge.orange { background: rgba(255,120,44,0.1); color: var(--orange); }
        .za-badge.blue   { background: rgba(59,130,246,0.1); color: var(--blue); }

        /* BUTTONS */
        .za-btn { display: inline-flex; align-items: center; gap: 6px; padding: 8px 16px; border-radius: 10px; font-size: 13px; font-weight: 600; cursor: pointer; transition: all 0.2s; border: none; text-decoration: none; font-family: 'Open Sans', sans-serif; white-space: nowrap; }
        .za-btn:hover { transform: translateY(-1px); text-decoration: none; }
        .za-btn-outline { background: transparent; color: var(--text); border: 1.5px solid var(--border); }
        .za-btn-outline:hover { border-color: var(--dark); color: var(--dark); }
        .za-btn-sm { padding: 6px 12px; font-size: 12px; border-radius: 8px; }

        .za-filter-bar { display: flex; align-items: center; gap: 10px; flex-wrap: wrap; }
        .za-filter-pill { padding: 7px 16px; border-radius: 50px; border: 1.5px solid var(--border); background: #fff; font-size: 13px; font-weight: 600; color: var(--text); cursor: pointer; text-decoration: none; transition: all 0.2s; }
        .za-filter-pill:hover { border-color: var(--orange); color: var(--orange); text-decoration: none; }
        .za-filter-pill.active { background: var(--orange); border-color: var(--orange); color: #fff; }

        .za-search-wrap { position: relative; }
        .za-search-wrap i { position: absolute; left: 14px; top: 50%; transform: translateY(-50%); color: var(--muted); font-size: 13px; }
        .za-search-input { padding: 8px 14px 8px 36px; border: 1.5px solid var(--border); border-radius: 50px; font-size: 13px; color: var(--dark); background: #fff; outline: none; width: 220px; transition: border-color 0.2s; }
        .za-search-input:focus { border-color: var(--orange); }

        /* CUSTOMER AVATAR */
        .za-cust-avatar { width: 38px; height: 38px; border-radius: 50%; display: flex; align-items: center; justify-content: center; color: #fff; font-family: 'Montserrat', sans-serif; font-size: 14px; font-weight: 800; text-transform: uppercase; background: var(--orange-grad); }

        /* MODAL */
        .za-modal-overlay { position: fixed; inset: 0; background: rgba(15, 23, 42, 0.4); z-index: 9999; display: none; align-items: center; justify-content: center; backdrop-filter: blur(4px); }
        .za-modal-overlay.open { display: flex; }
        .za-modal { background: #fff; border-radius: 24px; width: 90%; max-width: 560px; max-height: 90vh; overflow-y: auto; box-shadow: 0 20px 50px rgba(15, 23, 42, 0.15); border: 1px solid var(--border); }
        .za-modal-header { padding: 22px 24px; border-bottom: 1px solid var(--border); display: flex; align-items: center; justify-content: space-between; position: sticky; top: 0; background: #fff; z-index: 1; }
        .za-modal-title { font-family: 'Montserrat', sans-serif; font-size: 17px; font-weight: 800; color: var(--dark); }
        .za-modal-close { width: 32px; height: 32px; border-radius: 50%; background: #F1F5F9; border: none; cursor: pointer; display: flex; align-items: center; justify-content: center; font-size: 14px; color: var(--text); transition: all 0.2s; }
        .za-modal-close:hover { background: var(--border); color: var(--dark); }
        .za-modal-body { padding: 24px; }
        .za-modal-footer { padding: 16px 24px; border-top: 1px solid var(--border); display: flex; gap: 10px; justify-content: flex-end; position: sticky; bottom: 0; background: #fff; }
        .za-label { display: block; font-size: 10px; font-weight: 800; color: var(--muted); letter-spacing: 0.5px; margin-bottom: 6px; text-transform: uppercase; }

        .za-empty { text-align: center; padding: 60px 20px; color: var(--muted); }
        .za-empty i { font-size: 40px; margin-bottom: 12px; display: block; opacity: 0.3; }

        @media (max-width: 992px) {
            .stat-grid { grid-template-columns: repeat(2, 1fr); }
        }
        @media (max-width: 768px) {
            .sidebar { transform: translateX(-100%); }
            .sidebar.open { transform: translateX(0); }
            .main-content { margin-left: 0; }
            .topbar { left: 0; }
            .stat-grid { grid-template-columns: 1fr; }
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
            <h1>Transport Bookings</h1>
            <p>Monitor flights, trains, and cabs reservations, check travel logs, and track transactions.</p>
        </div>

        <!-- Stats Grid -->
        <div class="stat-grid">
            <div class="stat-card">
                <div class="stat-icon" style="background: var(--blue-grad);">✈️</div>
                <div class="stat-info">
                    <div class="stat-num"><?= $stats_total ?></div>
                    <div class="stat-label">Total Transport Bookings</div>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon" style="background: var(--green-grad);">💰</div>
                <div class="stat-info">
                    <div class="stat-num">₹<?= number_format($total_revenue, 2) ?></div>
                    <div class="stat-label">Total Revenue Collected</div>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon" style="background: var(--orange-grad);">⏳</div>
                <div class="stat-info">
                    <div class="stat-num"><?= $stats_pending ?></div>
                    <div class="stat-label">Pending Gateways</div>
                </div>
            </div>
        </div>

        <!-- Table Card -->
        <div class="za-card">
            <div class="za-card-header">
                <div class="za-card-title">Transport Booking Logs (<?= $total_rows ?>)</div>
                
                <div style="display:flex; gap:12px; align-items:center; flex-wrap:wrap;">
                    <!-- Type and Status Filter -->
                    <div class="za-filter-bar">
                        <a href="transport_bookings_manage.php?type=all&status=<?= urlencode($status_filter) ?>&search=<?= urlencode($search) ?>" class="za-filter-pill <?= $type_filter=='all'?'active':'' ?>">All</a>
                        <a href="transport_bookings_manage.php?type=flight&status=<?= urlencode($status_filter) ?>&search=<?= urlencode($search) ?>" class="za-filter-pill <?= $type_filter=='flight'?'active':'' ?>">Flights</a>
                        <a href="transport_bookings_manage.php?type=train&status=<?= urlencode($status_filter) ?>&search=<?= urlencode($search) ?>" class="za-filter-pill <?= $type_filter=='train'?'active':'' ?>">Trains</a>
                        <a href="transport_bookings_manage.php?type=cab&status=<?= urlencode($status_filter) ?>&search=<?= urlencode($search) ?>" class="za-filter-pill <?= $type_filter=='cab'?'active':'' ?>">Cabs</a>
                    </div>
                    
                    <!-- Search Input -->
                    <form method="GET" class="za-search-wrap">
                        <input type="hidden" name="type" value="<?= htmlspecialchars($type_filter) ?>">
                        <input type="hidden" name="status" value="<?= htmlspecialchars($status_filter) ?>">
                        <i class="fa fa-search"></i>
                        <input type="text" name="search" class="za-search-input" placeholder="Search reference..." value="<?= htmlspecialchars($search) ?>">
                    </form>
                </div>
            </div>

            <div style="padding:0; overflow-x:auto;">
                <?php if ($total_rows > 0): ?>
                    <table class="za-table">
                        <thead>
                            <tr>
                                <th>Booking Ref</th>
                                <th>Customer Details</th>
                                <th>Transport Type</th>
                                <th>Travel Route</th>
                                <th>Travel Date</th>
                                <th>Fare Paid</th>
                                <th>Status</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($c = mysqli_fetch_assoc($result)): ?>
                                <tr>
                                    <td style="color:var(--dark); font-weight:700; font-family:monospace; font-size:14px;">
                                        <?= htmlspecialchars($c['booking_ref']) ?>
                                    </td>
                                    <td>
                                        <div style="display:flex; align-items:center; gap:10px;">
                                            <div class="za-cust-avatar" style="width:34px; height:34px; font-size:12px;">
                                                <?= $c['cust_fname'] ? substr($c['cust_fname'], 0, 1) : 'U' ?>
                                            </div>
                                            <div>
                                                <div style="font-weight:700; color:var(--dark);"><?= htmlspecialchars($c['cust_fname'] . ' ' . $c['cust_lname']) ?></div>
                                                <div style="font-size:11.5px; color:var(--muted);"><?= htmlspecialchars($c['cust_email']) ?></div>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <?php if ($c['type'] === 'flight'): ?>
                                            <span class="za-badge blue"><i class="fa fa-plane"></i> Flight</span>
                                        <?php elseif ($c['type'] === 'train'): ?>
                                            <span class="za-badge orange"><i class="fa fa-train"></i> Train</span>
                                        <?php else: ?>
                                            <span class="za-badge green"><i class="fa fa-taxi"></i> Cab</span>
                                        <?php endif; ?>
                                    </td>
                                    <td style="font-weight:700; color:var(--dark);">
                                        <?= htmlspecialchars($c['from_city']) ?> ➔ <?= htmlspecialchars($c['to_city']) ?>
                                    </td>
                                    <td style="font-weight:600; color:var(--text);"><?= date('d M Y', strtotime($c['travel_date'])) ?></td>
                                    <td style="font-weight:700; color:var(--green);">₹<?= number_format($c['fare'], 2) ?></td>
                                    <td>
                                        <?php if ($c['status'] === 'Confirmed'): ?>
                                            <span class="za-badge green"><i class="fa fa-check-circle"></i> Confirmed</span>
                                        <?php elseif ($c['status'] === 'Pending'): ?>
                                            <span class="za-badge orange"><i class="fa fa-clock-o"></i> Pending</span>
                                        <?php else: ?>
                                            <span class="za-badge red"><?= htmlspecialchars($c['status']) ?></span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <button class="za-btn za-btn-outline za-btn-sm" onclick='openBookingModal(<?= json_encode($c, JSON_HEX_APOS | JSON_HEX_QUOT) ?>)'>
                                            <i class="fa fa-eye"></i> Details
                                        </button>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                <?php else: ?>
                    <div class="za-empty">
                        <i class="fa fa-ticket"></i>
                        <p>No transport bookings matching the filter parameters found.</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>

    </div>
    
    <!-- FOOTER -->
    <div class="admin-footer">
        <span>© 2026 Explore India — Admin Panel</span>
        <span>Made with ❤️ in India</span>
    </div>
</div>

<!-- DETAILS MODAL -->
<div class="za-modal-overlay" id="bookingModal">
    <div class="za-modal">
        <div class="za-modal-header">
            <div class="za-modal-title">🎫 Booking Details</div>
            <button class="za-modal-close" onclick="closeModal('bookingModal')">✕</button>
        </div>
        <div class="za-modal-body">
            <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom: 20px; padding-bottom: 15px; border-bottom: 1px solid var(--border);">
                <div>
                    <span style="font-size: 11px; color: var(--muted); text-transform: uppercase; font-weight: 700; letter-spacing: 0.5px;">Booking Ref</span>
                    <h3 id="view_ref" style="font-family:'Montserrat',sans-serif; font-size: 20px; font-weight: 800; color: var(--dark); margin-top: 2px;">FLI000000</h3>
                </div>
                <div id="view_status"></div>
            </div>

            <!-- Route Info Block -->
            <div style="margin-bottom: 20px;">
                <label class="za-label">Journey Route & Schedule</label>
                <div style="background: #F8FAFC; border: 1.5px solid var(--border); border-radius: 12px; padding: 14px 18px; display:flex; align-items:center; justify-content:space-between;">
                    <div>
                        <span style="font-size:11px; color:var(--muted); text-transform:uppercase; font-weight:700;">Departure City</span>
                        <div id="view_from" style="font-weight:800; font-size:16px; color:var(--dark);">Delhi</div>
                    </div>
                    <div style="font-size: 18px; color:var(--orange);"><i class="fa fa-long-arrow-right"></i></div>
                    <div style="text-align:right;">
                        <span style="font-size:11px; color:var(--muted); text-transform:uppercase; font-weight:700;">Destination City</span>
                        <div id="view_to" style="font-weight:800; font-size:16px; color:var(--dark);">Dehradun</div>
                    </div>
                </div>
            </div>

            <!-- Customer Details Block -->
            <div style="margin-bottom: 20px; border-top: 1px solid var(--border); padding-top: 20px;">
                <label class="za-label">Customer Profile</label>
                <div style="display:flex; align-items:center; gap:12px;">
                    <div class="za-cust-avatar" id="view_avatar" style="width:42px; height:42px; font-size:15px;">U</div>
                    <div>
                        <div id="view_cust_name" style="font-weight:700; color:var(--dark); font-size:15px;">Customer Name</div>
                        <div id="view_cust_email" style="font-size:12.5px; color:var(--muted);">email@example.com</div>
                        <div id="view_cust_phone" style="font-size:12px; color:var(--muted);">📞 Phone</div>
                    </div>
                </div>
            </div>

            <!-- Decoded JSON Parameters Details -->
            <div style="margin-bottom: 20px; border-top: 1px solid var(--border); padding-top: 20px;">
                <label class="za-label">Transport Details & Configuration</label>
                <div id="view_specs" style="background:#FFFDF9; border:1px solid #FDE8B0; border-radius:12px; padding:14px 18px; font-size:13.5px; line-height:1.6; color:var(--dark);">
                    <!-- Dynamic properties load here via Javascript -->
                </div>
            </div>

            <!-- Travel and Transaction Grid -->
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px; border-top: 1px solid var(--border); padding-top: 20px; margin-bottom: 20px;">
                <div>
                    <label class="za-label">Travel Date</label>
                    <p id="view_date" style="font-size:14.5px; font-weight:700; color:var(--dark);"></p>
                </div>
                <div>
                    <label class="za-label">Fare Amount</label>
                    <p id="view_fare" style="font-size:15px; font-weight:700; color:var(--green);"></p>
                </div>
            </div>

            <!-- Transaction Gateway Coordinates -->
            <div style="display: grid; grid-template-columns: 1fr; gap: 16px; border-top: 1px solid var(--border); padding-top: 20px;">
                <div>
                    <label class="za-label">Payment ID (Gateway Reference)</label>
                    <p id="view_payment_id" style="font-size:13px; font-weight:700; color:var(--dark); font-family:monospace; word-break:break-all;"></p>
                </div>
                <div>
                    <label class="za-label">Booking Timestamp</label>
                    <p id="view_booked_at" style="font-size:13.5px; font-weight:600; color:var(--text);"></p>
                </div>
            </div>
        </div>
        <div class="za-modal-footer">
            <button type="button" class="za-btn za-btn-outline" onclick="closeModal('bookingModal')">Close</button>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/jquery@2.2.4/dist/jquery.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.1.3/dist/js/bootstrap.min.js"></script>
<script>
// Modal Controls
function openModal(id) { 
    document.getElementById(id).classList.add('open'); 
    document.body.style.overflow = 'hidden'; 
}

function closeModal(id) {
        document.getElementById(id).classList.remove('open'); 
    document.body.style.overflow = ''; 
}

function openBookingModal(b) {
    document.getElementById('view_ref').innerText = b.booking_ref;
    document.getElementById('view_from').innerText = b.from_city;
    document.getElementById('view_to').innerText = b.to_city;
    
    // Customer profile info
    var custName = b.cust_fname ? (b.cust_fname + ' ' + b.cust_lname) : 'Unknown Customer';
    var custEmail = b.cust_email ? b.cust_email : 'No email available';
    var custPhone = b.cust_mobile ? b.cust_mobile : 'N/A';
    var firstLetter = b.cust_fname ? b.cust_fname.charAt(0).toUpperCase() : 'U';

    document.getElementById('view_cust_name').innerText = custName;
    document.getElementById('view_cust_email').innerText = custEmail;
    document.getElementById('view_cust_phone').innerText = '📞 ' + custPhone;
    document.getElementById('view_avatar').innerText = firstLetter;

    // Parse details column JSON dynamically
    var specsHtml = '';
    try {
        var specData = JSON.parse(b.details);
        
        if (b.type === 'flight') {
            specsHtml += '<div style="margin-bottom:6px;"><strong style="color:var(--blue);">Airline:</strong> ' + (specData.airline || 'N/A') + '</div>';
            specsHtml += '<div style="margin-bottom:6px;"><strong style="color:var(--blue);">Flight Number:</strong> ' + (specData.flight_no || 'N/A') + '</div>';
            if (specData.class) specsHtml += '<div style="margin-bottom:6px;"><strong style="color:var(--blue);">Class:</strong> ' + specData.class + '</div>';
            if (specData.duration) specsHtml += '<div><strong style="color:var(--blue);">Duration:</strong> ' + specData.duration + '</div>';
        } else if (b.type === 'train') {
            specsHtml += '<div style="margin-bottom:6px;"><strong style="color:var(--orange);">Train Name:</strong> ' + (specData.train_name || 'N/A') + '</div>';
            specsHtml += '<div style="margin-bottom:6px;"><strong style="color:var(--orange);">Train Number:</strong> ' + (specData.train_no || 'N/A') + '</div>';
            if (specData.class) specsHtml += '<div style="margin-bottom:6px;"><strong style="color:var(--orange);">Class:</strong> ' + specData.class + '</div>';
            if (specData.duration) specsHtml += '<div><strong style="color:var(--orange);">Duration:</strong> ' + specData.duration + '</div>';
        } else if (b.type === 'cab') {
            specsHtml += '<div style="margin-bottom:6px;"><strong style="color:var(--green);">Cab Name:</strong> ' + (specData.cab_name || 'N/A') + '</div>';
            specsHtml += '<div style="margin-bottom:6px;"><strong style="color:var(--green);">Cab Type:</strong> ' + (specData.cab_type || 'N/A') + '</div>';
            if (specData.driver_name) specsHtml += '<div style="margin-bottom:6px;"><strong style="color:var(--green);">Driver Name:</strong> ' + specData.driver_name + '</div>';
            if (specData.driver_phone) specsHtml += '<div><strong style="color:var(--green);">Driver Contact:</strong> ' + specData.driver_phone + '</div>';
        } else {
            // General fallback output of keys if type is not matched
            for (var key in specData) {
                if (specData.hasOwnProperty(key)) {
                    specsHtml += '<div><strong>' + key.toUpperCase() + ':</strong> ' + specData[key] + '</div>';
                }
            }
        }
    } catch (e) {
        specsHtml = '<span class="text-muted"><i class="fa fa-info-circle"></i> Raw Details: ' + b.details + '</span>';
    }
    
    document.getElementById('view_specs').innerHTML = specsHtml;

    // Transaction Details
    document.getElementById('view_date').innerText = b.travel_date;
    document.getElementById('view_fare').innerText = '₹' + parseFloat(b.fare).toFixed(2);
    document.getElementById('view_payment_id').innerText = b.payment_id ? b.payment_id : 'N/A';
    document.getElementById('view_booked_at').innerText = b.booked_at;

    // Status Badge inside Modal
    var statusDiv = document.getElementById('view_status');
    if (b.status === 'Confirmed') {
        statusDiv.innerHTML = '<span class="za-badge green"><i class="fa fa-check-circle"></i> Confirmed</span>';
    } else if (b.status === 'Pending') {
        statusDiv.innerHTML = '<span class="za-badge orange"><i class="fa fa-clock-o"></i> Pending</span>';
    } else {
        statusDiv.innerHTML = '<span class="za-badge red">' + b.status + '</span>';
    }

    openModal('bookingModal');
}

// Mobile sidebar toggle function
function toggleSidebar() {
    document.getElementById('sidebar').classList.toggle('open');
}

// Show menu toggle on mobile screen size
if (window.innerWidth <= 768) {
    var toggleBtn = document.getElementById('menuToggle');
    if (toggleBtn) toggleBtn.style.display = 'block';
}
window.addEventListener('resize', function() {
    var toggleBtn = document.getElementById('menuToggle');
    if (toggleBtn) {
        toggleBtn.style.display = window.innerWidth <= 768 ? 'block' : 'none';
    }
});
</script>
</body>
</html>