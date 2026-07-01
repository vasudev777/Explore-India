<?php
include('db.php');
include('check.php');

// ===== Helper to fetch Hotel Names linked to the custom package =====
function getHotelNames($conn, $h_ids) {
    if (empty(trim($h_ids))) return '<span class="text-muted">No hotels selected</span>';
    $cleaned = preg_replace('/[^0-9,]/', '', $h_ids);
    if (empty($cleaned)) return '<span class="text-muted">No hotels selected</span>';
    
    $res = mysqli_query($conn, "SELECT h_name FROM hotel WHERE h_id IN ($cleaned)");
    $names = [];
    if ($res) {
        while ($row = mysqli_fetch_assoc($res)) {
            $names[] = htmlspecialchars($row['h_name']);
        }
    }
    return count($names) > 0 ? implode(', ', $names) : '<span class="text-muted">Unknown Hotels</span>';
}

// ===== FILTERS & SEARCH =====
$search = $_GET['search'] ?? '';
$status_filter = $_GET['status'] ?? 'all';

$where = "WHERE 1=1";

if ($status_filter !== 'all') {
    $st = mysqli_real_escape_string($conn, $status_filter);
    $where .= " AND cb.status = '$st'";
}

if ($search) {
    $s = mysqli_real_escape_string($conn, $search);
    $where .= " AND (cb.booking_ref LIKE '%$s%' OR cb.payment_id LIKE '%$s%' OR cd.cust_fname LIKE '%$s%' OR cd.cust_lname LIKE '%$s%' OR lg.localg_name LIKE '%$s%')";
}

// SQL Query with LEFT JOINS to fetch Customer & Local Guide details
$query = "SELECT cb.*, cd.cust_fname, cd.cust_lname, cd.cust_email, cd.cust_mobile, lg.localg_name, lg.localg_mobile
          FROM customize_booking cb
          LEFT JOIN customer_details cd ON cb.cust_id = cd.cust_id
          LEFT JOIN local_guide lg ON cb.localg_id = lg.localg_id
          $where
          ORDER BY cb.id DESC";

$result = mysqli_query($conn, $query);
$total_rows = $result ? mysqli_num_rows($result) : 0;

// Stats calculations
$stats_total = mysqli_num_rows(mysqli_query($conn, "SELECT * FROM customize_booking"));
$stats_pending = mysqli_num_rows(mysqli_query($conn, "SELECT * FROM customize_booking WHERE status='Pending'"));

$rev_res = mysqli_query($conn, "SELECT SUM(amount) as total FROM customize_booking WHERE status='Confirmed'");
$total_revenue = mysqli_fetch_assoc($rev_res)['total'] ?? 0;

$page_title = "Custom Bookings"; // Sets Topbar Title dynamically
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Custom Bookings – Explore India Admin</title>
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
            <h1>Custom Bookings Directory</h1>
            <p>Monitor customized itinerary bookings, transaction confirmations, and assigned guides.</p>
        </div>

        <!-- Stats Grid -->
        <div class="stat-grid">
            <div class="stat-card">
                <div class="stat-icon" style="background: var(--blue-grad);">📋</div>
                <div class="stat-info">
                    <div class="stat-num"><?= $stats_total ?></div>
                    <div class="stat-label">Total Bookings</div>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon" style="background: var(--green-grad);">💰</div>
                <div class="stat-info">
                    <div class="stat-num">₹<?= number_format($total_revenue, 2) ?></div>
                    <div class="stat-label">Confirmed Revenue</div>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon" style="background: var(--orange-grad);">⏳</div>
                <div class="stat-info">
                    <div class="stat-num"><?= $stats_pending ?></div>
                    <div class="stat-label">Pending Bookings</div>
                </div>
            </div>
        </div>

        <!-- Table Card -->
        <div class="za-card">
            <div class="za-card-header">
                <div class="za-card-title">Custom Itinerary Bookings Log (<?= $total_rows ?>)</div>
                
                <div style="display:flex; gap:12px; align-items:center; flex-wrap:wrap;">
                    <!-- Filter bar -->
                    <div class="za-filter-bar">
                        <a href="customize_bookings_manage.php?status=all&search=<?= urlencode($search) ?>" class="za-filter-pill <?= $status_filter=='all'?'active':'' ?>">All</a>
                        <a href="customize_bookings_manage.php?status=Confirmed&search=<?= urlencode($search) ?>" class="za-filter-pill <?= $status_filter=='Confirmed'?'active':'' ?>">Confirmed</a>
                        <a href="customize_bookings_manage.php?status=Pending&search=<?= urlencode($search) ?>" class="za-filter-pill <?= $status_filter=='Pending'?'active':'' ?>">Pending</a>
                    </div>
                    
                    <!-- Search Bar -->
                    <form method="GET" class="za-search-wrap">
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
                                <th>Assigned Guide</th>
                                <th>Travel Start</th>
                                <th>Duration</th>
                                <th>Amount Paid</th>
                                <th>Status</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($c = mysqli_fetch_assoc($result)): ?>
                                <?php 
                                // Fetch hotel names dynamically
                                $hotel_names_str = getHotelNames($conn, $c['h_id']);
                                ?>
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
                                    <td style="font-weight:600; color:var(--text);">
                                        <i class="fa fa-user-circle-o" style="color:var(--orange);"></i> 
                                        <?= $c['localg_name'] ? htmlspecialchars($c['localg_name']) : '<span class="text-muted">No Guide Mapped</span>' ?>
                                    </td>
                                    <td style="font-weight:600; color:var(--dark);"><?= date('d M Y', strtotime($c['date'])) ?></td>
                                    <td style="font-weight:700; color:var(--blue);"><?= htmlspecialchars($c['day']) ?> Days</td>
                                    <td style="font-weight:700; color:var(--green);">₹<?= number_format($c['amount'], 2) ?></td>
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
                                        <!-- Open details modal passing data -->
                                        <button class="za-btn za-btn-outline za-btn-sm" 
                                                onclick='openBookingModal(<?= json_encode($c, JSON_HEX_APOS | JSON_HEX_QUOT) ?>, "<?= addslashes($hotel_names_str) ?>")'>
                                            <i class="fa fa-eye"></i> Details
                                        </button>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                <?php else: ?>
                    <div class="za-empty">
                        <i class="fa fa-map-signs"></i>
                        <p>No customized itinerary bookings found matching the parameters.</p>
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
            <div class="za-modal-title">📋 Custom Booking Details</div>
            <button class="za-modal-close" onclick="closeModal('bookingModal')">✕</button>
        </div>
        <div class="za-modal-body">
            <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom: 20px; padding-bottom: 15px; border-bottom: 1px solid var(--border);">
                <div>
                    <span style="font-size: 11px; color: var(--muted); text-transform: uppercase; font-weight: 700; letter-spacing: 0.5px;">Booking Ref</span>
                    <h3 id="view_ref" style="font-family:'Montserrat',sans-serif; font-size: 20px; font-weight: 800; color: var(--dark); margin-top: 2px;">CUS000000</h3>
                </div>
                <div id="view_status"></div>
            </div>

            <!-- Customer Details Block -->
            <div style="margin-bottom: 20px;">
                <label class="za-label">Customer Profile</label>
                <div style="display:flex; align-items:center; gap:12px; background: #F8FAFC; border: 1.5px solid var(--border); border-radius: 12px; padding: 12px 16px;">
                    <div class="za-cust-avatar" id="view_avatar" style="width:42px; height:42px; font-size:15px;">U</div>
                    <div>
                        <div id="view_cust_name" style="font-weight:700; color:var(--dark); font-size:15px;">Customer Name</div>
                        <div id="view_cust_email" style="font-size:12.5px; color:var(--muted);">email@example.com</div>
                        <div id="view_cust_phone" style="font-size:12px; color:var(--muted);">📞 Phone</div>
                    </div>
                </div>
            </div>

            <!-- Custom Hotels List -->
            <div style="margin-bottom: 20px; border-top: 1px solid var(--border); padding-top: 20px;">
                <label class="za-label">Selected Hotels</label>
                <p id="view_hotels" style="font-size:14px; font-weight:700; color:var(--orange); line-height:1.5;"></p>
            </div>

            <!-- Assigned Local Guide -->
            <div style="margin-bottom: 20px; border-top: 1px solid var(--border); padding-top: 20px;">
                <label class="za-label">Assigned Local Guide</label>
                <div style="background: #FDF8F5; border: 1.5px solid rgba(255,120,44,0.15); border-radius: 12px; padding: 12px 16px; display:flex; align-items:center; gap:10px;">
                    <div style="font-size:24px; color:var(--orange);"><i class="fa fa-user-circle"></i></div>
                    <div>
                        <div id="view_guide_name" style="font-weight:700; color:var(--dark); font-size:14.5px;">Guide Name</div>
                        <div id="view_guide_phone" style="font-size:12px; color:var(--muted);">📞 Phone</div>
                    </div>
                </div>
            </div>

            <!-- Travel Start and Duration Grid -->
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px; border-top: 1px solid var(--border); padding-top: 20px; margin-bottom: 20px;">
                <div>
                    <label class="za-label">Travel Date</label>
                    <p id="view_date" style="font-size:14.5px; font-weight:700; color:var(--dark);"></p>
                </div>
                <div>
                    <label class="za-label">Trip Duration</label>
                    <p id="view_duration" style="font-size:14.5px; font-weight:700; color:var(--blue);"></p>
                </div>
            </div>

            <!-- Transaction Info Grid -->
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px; border-top: 1px solid var(--border); padding-top: 20px;">
                <div>
                    <label class="za-label">Amount Paid</label>
                    <p id="view_amount" style="font-size:15px; font-weight:700; color:var(--green);"></p>
                </div>
                <div>
                    <label class="za-label">Payment ID</label>
                    <p id="view_payment_id" style="font-size:13px; font-weight:700; color:var(--dark); font-family:monospace; word-break:break-all;"></p>
                </div>
                <div style="grid-column: span 2; border-top: 1px solid var(--border); padding-top: 16px; margin-top: 8px;">
                    <label class="za-label">Booking Timestamp</label>
                    <p id="view_created_at" style="font-size:13.5px; font-weight:600; color:var(--text);"></p>
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

function openBookingModal(b, hotels) {
    document.getElementById('view_ref').innerText = b.booking_ref;
    document.getElementById('view_hotels').innerHTML = hotels;
    
    // Customer profile info
    var custName = b.cust_fname ? (b.cust_fname + ' ' + b.cust_lname) : 'Unknown Customer';
    var custEmail = b.cust_email ? b.cust_email : 'No email available';
    var custPhone = b.cust_mobile ? b.cust_mobile : 'N/A';
    var firstLetter = b.cust_fname ? b.cust_fname.charAt(0).toUpperCase() : 'U';

    document.getElementById('view_cust_name').innerText = custName;
    document.getElementById('view_cust_email').innerText = custEmail;
    document.getElementById('view_cust_phone').innerText = '📞 ' + custPhone;
    document.getElementById('view_avatar').innerText = firstLetter;

    // Assigned Local Guide Info
    var guideName = b.localg_name ? b.localg_name : 'No Guide Assigned';
    var guidePhone = b.localg_mobile ? b.localg_mobile : 'N/A';
    document.getElementById('view_guide_name').innerText = guideName;
    document.getElementById('view_guide_phone').innerText = '📞 ' + guidePhone;

    // Travel Details
    document.getElementById('view_date').innerText = b.date;
    document.getElementById('view_duration').innerText = b.day + ' Days';
    
    // Payment Details
    document.getElementById('view_amount').innerText = '₹' + parseFloat(b.amount).toFixed(2);
    document.getElementById('view_payment_id').innerText = b.payment_id ? b.payment_id : 'N/A';
    document.getElementById('view_created_at').innerText = b.created_at;

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