<?php
include('db.php');
include('check.php');

$msg = '';
$msg_type = '';

// ===== TOGGLE BLOCK STATUS =====
if (isset($_GET['toggle_block'])) {
    $id = intval($_GET['toggle_block']);
    $res = mysqli_query($conn, "SELECT is_blocked FROM customer_details WHERE cust_id='$id'");
    if ($row = mysqli_fetch_assoc($res)) {
        $new_status = ($row['is_blocked'] == 1) ? 0 : 1;
        mysqli_query($conn, "UPDATE customer_details SET is_blocked='$new_status' WHERE cust_id='$id'");
        $msg = "Customer block status updated successfully!";
        $msg_type = "success";
    }
}

// ===== DELETE PROFILE =====
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    if (mysqli_query($conn, "DELETE FROM customer_details WHERE cust_id='$id'")) {
        $msg = "Customer profile deleted permanently!";
        $msg_type = "success";
    } else {
        $msg = "Error deleting customer profile.";
        $msg_type = "error";
    }
}

// ===== FILTERS & SEARCH =====
$filter = $_GET['filter'] ?? 'all';
$search = $_GET['search'] ?? '';

$where = "WHERE 1=1";

if ($filter === 'active') {
    $where .= " AND cd.is_blocked = 0";
} elseif ($filter === 'blocked') {
    $where .= " AND cd.is_blocked = 1";
} elseif ($filter === 'verified') {
    $where .= " AND cd.is_verified = 1";
} elseif ($filter === 'unverified') {
    $where .= " AND cd.is_verified = 0";
}

if ($search) {
    $s = mysqli_real_escape_string($conn, $search);
    // Updated to search by actual city name (c_name) and state name (s_name) too!
    $where .= " AND (cd.cust_fname LIKE '%$s%' OR cd.cust_lname LIKE '%$s%' OR cd.cust_email LIKE '%$s%' OR cd.cust_mobile LIKE '%$s%' OR c.c_name LIKE '%$s%' OR s.s_name LIKE '%$s%')";
}

// LEFT JOIN to get actual State & City Names
$query = "SELECT cd.*, s.s_name, c.c_name 
          FROM customer_details cd
          LEFT JOIN state s ON cd.cust_state = s.s_id
          LEFT JOIN city c ON cd.cust_city = c.c_id
          $where 
          ORDER BY cd.cust_id DESC";
          
$result = mysqli_query($conn, $query);
$total = $result ? mysqli_num_rows($result) : 0;

// Calculate stats for mini cards
$count_all = mysqli_num_rows(mysqli_query($conn, "SELECT * FROM customer_details"));
$count_active = mysqli_num_rows(mysqli_query($conn, "SELECT * FROM customer_details WHERE is_blocked=0"));
$count_blocked = mysqli_num_rows(mysqli_query($conn, "SELECT * FROM customer_details WHERE is_blocked=1"));
$count_verified = mysqli_num_rows(mysqli_query($conn, "SELECT * FROM customer_details WHERE is_verified=1"));

$page_title = "Customer Management"; // Sets dynamic title in header.php
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Customers – Explore India Admin</title>
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
            --purple: #8B5CF6; 
            --purple-grad: linear-gradient(135deg, #8B5CF6, #6D28D9);
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
        .stat-grid { display: grid; grid-template-columns: repeat(4, 1fr); gap: 20px; margin-bottom: 24px; }
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
        .za-table tr:last-child td { border-bottom: none; }
        .za-table tr:hover td { background: #F8FAFC; }

        /* BADGES */
        .za-badge { display: inline-flex; align-items: center; gap: 5px; padding: 4px 10px; border-radius: 50px; font-size: 11px; font-weight: 700; }
        .za-badge.green  { background: rgba(16,185,129,0.1); color: var(--green); }
        .za-badge.red    { background: rgba(239,68,68,0.1); color: var(--red); }
        .za-badge.gray   { background: #E2E8F0; color: var(--text); }
        .za-badge.blue   { background: rgba(59,130,246,0.1); color: var(--blue); }

        /* BUTTONS */
        .za-btn { display: inline-flex; align-items: center; gap: 6px; padding: 8px 16px; border-radius: 10px; font-size: 13px; font-weight: 600; cursor: pointer; transition: all 0.2s; border: none; text-decoration: none; font-family: 'Open Sans', sans-serif; white-space: nowrap; }
        .za-btn:hover { transform: translateY(-1px); text-decoration: none; }
        .za-btn-outline { background: transparent; color: var(--text); border: 1.5px solid var(--border); }
        .za-btn-outline:hover { border-color: var(--dark); color: var(--dark); }
        .za-btn-red { background: var(--red); color: #fff; }
        .za-btn-red:hover { background: #dc2626; color: #fff; }
        .za-btn-sm { padding: 6px 12px; font-size: 12px; border-radius: 8px; }

        /* ALERTS */
        .za-alert { padding: 12px 16px; border-radius: 10px; font-size: 13.5px; font-weight: 500; margin-bottom: 20px; display: flex; align-items: center; gap: 10px; }
        .za-alert.success { background: rgba(16,185,129,0.08); color: #065f46; border-left: 4px solid var(--green); }
        .za-alert.error   { background: rgba(239,68,68,0.08); color: #991b1b; border-left: 4px solid var(--red); }

        .za-filter-bar { display: flex; align-items: center; gap: 10px; flex-wrap: wrap; }
        .za-filter-pill { padding: 7px 16px; border-radius: 50px; border: 1.5px solid var(--border); background: #fff; font-size: 13px; font-weight: 600; color: var(--text); cursor: pointer; text-decoration: none; transition: all 0.2s; }
        .za-filter-pill:hover { border-color: var(--orange); color: var(--orange); text-decoration: none; }
        .za-filter-pill.active { background: var(--orange); border-color: var(--orange); color: #fff; }

        .za-search-wrap { position: relative; }
        .za-search-wrap i { position: absolute; left: 14px; top: 50%; transform: translateY(-50%); color: var(--muted); font-size: 13px; }
        .za-search-input { padding: 8px 14px 8px 36px; border: 1.5px solid var(--border); border-radius: 50px; font-size: 13px; color: var(--dark); background: #fff; outline: none; width: 200px; transition: border-color 0.2s; }
        .za-search-input:focus { border-color: var(--orange); }

        /* CUSTOMER AVATAR */
        .za-cust-avatar { width: 38px; height: 38px; border-radius: 50%; display: flex; align-items: center; justify-content: center; color: #fff; font-family: 'Montserrat', sans-serif; font-size: 14px; font-weight: 800; text-transform: uppercase; }

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
            .stat-grid { grid-template-columns: 1fr 1fr; }
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
            <h1>Customer Management</h1>
            <p>Verify registration states, control active sessions, and search details.</p>
        </div>

        <?php if ($msg): ?>
            <div class="za-alert <?= $msg_type ?>">
                <i class="fa <?= $msg_type == 'success' ? 'fa-check-circle' : 'fa-exclamation-triangle' ?>"></i>
                <?= htmlspecialchars($msg) ?>
            </div>
        <?php endif; ?>

        <!-- Stats Grid -->
        <div class="stat-grid">
            <div class="stat-card">
                <div class="stat-icon" style="background: var(--blue-grad);">👥</div>
                <div class="stat-info">
                    <div class="stat-num"><?= $count_all ?></div>
                    <div class="stat-label">Total Users</div>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon" style="background: var(--green-grad);">✅</div>
                <div class="stat-info">
                    <div class="stat-num"><?= $count_active ?></div>
                    <div class="stat-label">Active Users</div>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon" style="background: var(--red-grad);">🚫</div>
                <div class="stat-info">
                    <div class="stat-num"><?= $count_blocked ?></div>
                    <div class="stat-label">Blocked Users</div>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon" style="background: var(--purple-grad);">💎</div>
                <div class="stat-info">
                    <div class="stat-num"><?= $count_verified ?></div>
                    <div class="stat-label">Verified Users</div>
                </div>
            </div>
        </div>

        <!-- Table Card -->
        <div class="za-card">
            <div class="za-card-header">
                <div class="za-card-title">All Customer Profiles (<?= $total ?>)</div>
                
                <div style="display:flex; gap:12px; align-items:center; flex-wrap:wrap;">
                    <!-- Filter bar -->
                    <div class="za-filter-bar">
                        <a href="customer_manage.php?filter=all&search=<?= urlencode($search) ?>" class="za-filter-pill <?= $filter=='all'?'active':'' ?>">All</a>
                        <a href="customer_manage.php?filter=active&search=<?= urlencode($search) ?>" class="za-filter-pill <?= $filter=='active'?'active':'' ?>">Active</a>
                        <a href="customer_manage.php?filter=blocked&search=<?= urlencode($search) ?>" class="za-filter-pill <?= $filter=='blocked'?'active':'' ?>">Blocked</a>
                        <a href="customer_manage.php?filter=verified&search=<?= urlencode($search) ?>" class="za-filter-pill <?= $filter=='verified'?'active':'' ?>">Verified</a>
                    </div>
                    
                    <!-- Search Bar -->
                    <form method="GET" class="za-search-wrap">
                        <input type="hidden" name="filter" value="<?= htmlspecialchars($filter) ?>">
                        <i class="fa fa-search"></i>
                        <input type="text" name="search" class="za-search-input" placeholder="Search customer..." value="<?= htmlspecialchars($search) ?>">
                    </form>
                </div>
            </div>

            <div style="padding:0; overflow-x:auto;">
                <?php if ($total > 0): ?>
                    <table class="za-table">
                        <thead>
                            <tr>
                                <th>#ID</th>
                                <th>Customer Details</th>
                                <th>Gender</th>
                                <th>Verification</th>
                                <th>Status</th>
                                <th>Location</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($c = mysqli_fetch_assoc($result)): ?>
                                <tr>
                                    <td style="color:var(--muted); font-size:12px; font-weight:700;">#<?= $c['cust_id'] ?></td>
                                    <td>
                                        <div style="display:flex; align-items:center; gap:12px;">
                                            <div class="za-cust-avatar" style="background: <?= $c['is_blocked'] == 1 ? 'var(--red-grad)' : 'var(--orange-grad)' ?>">
                                                <?= substr($c['cust_fname'], 0, 1) ?>
                                            </div>
                                            <div>
                                                <div style="font-weight:700; color:var(--dark);"><?= htmlspecialchars($c['cust_fname'] . ' ' . $c['cust_lname']) ?></div>
                                                <div style="font-size:12px; color:var(--muted);"><?= htmlspecialchars($c['cust_email']) ?></div>
                                                <div style="font-size:12px; color:var(--muted);">📞 <?= htmlspecialchars($c['cust_mobile']) ?></div>
                                            </div>
                                        </div>
                                    </td>
                                    <td style="text-transform: capitalize; font-weight:600;"><?= htmlspecialchars($c['cust_gender']) ?></td>
                                    <td>
                                        <?php if ($c['is_verified'] == 1): ?>
                                            <span class="za-badge green"><i class="fa fa-check-circle"></i> Verified</span>
                                        <?php else: ?>
                                            <span class="za-badge gray"><i class="fa fa-times-circle"></i> Unverified</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php if ($c['is_blocked'] == 1): ?>
                                            <span class="za-badge red"><i class="fa fa-ban"></i> Blocked</span>
                                        <?php else: ?>
                                            <span class="za-badge green"><i class="fa fa-circle"></i> Active</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <!-- Displays actual Joined City & State Name fetched from tables -->
                                        <div style="font-weight:600;"><?= htmlspecialchars($c['c_name'] ?? 'N/A') ?></div>
                                        <div style="font-size:11px; color:var(--muted);"><?= htmlspecialchars($c['s_name'] ?? 'N/A') ?></div>
                                    </td>
                                    <td>
                                        <div style="display:flex; gap:6px;">
                                            <!-- View Details Button -->
                                            <button class="za-btn za-btn-outline za-btn-sm" onclick='openViewModal(<?= json_encode($c, JSON_HEX_APOS | JSON_HEX_QUOT) ?>)'>
                                                <i class="fa fa-eye"></i> View
                                            </button>

                                            <!-- Toggle Status Button -->
                                            <?php if ($c['is_blocked'] == 1): ?>
                                                <a href="customer_manage.php?toggle_block=<?= $c['cust_id'] ?>&filter=<?= $filter ?>&search=<?= urlencode($search) ?>" class="za-btn za-btn-outline za-btn-sm" style="color:var(--green); border-color:var(--green);">
                                                    <i class="fa fa-check"></i> Unblock
                                                </a>
                                            <?php else: ?>
                                                <a href="customer_manage.php?toggle_block=<?= $c['cust_id'] ?>&filter=<?= $filter ?>&search=<?= urlencode($search) ?>" class="za-btn za-btn-outline za-btn-sm" style="color:var(--red); border-color:var(--red);" onclick="return confirm('Block this customer account?')">
                                                    <i class="fa fa-ban"></i> Block
                                                </a>
                                            <?php endif; ?>

                                            <!-- Delete Button -->
                                            <a href="customer_manage.php?delete=<?= $c['cust_id'] ?>&filter=<?= $filter ?>&search=<?= urlencode($search) ?>" class="za-btn za-btn-red za-btn-sm" onclick="return confirm('Delete this customer account permanently?')">
                                                <i class="fa fa-trash"></i> Delete
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                
                
                                <?php else: ?>
                    <div class="za-empty">
                        <i class="fa fa-users"></i>
                        <p>No customers found matching the criteria.</p>
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

<!-- VIEW MODAL -->
<div class="za-modal-overlay" id="viewModal">
    <div class="za-modal">
        <div class="za-modal-header">
            <div class="za-modal-title">👤 Customer Profile Details</div>
            <button class="za-modal-close" onclick="closeModal('viewModal')">✕</button>
        </div>
        <div class="za-modal-body">
            <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom: 20px; padding-bottom: 15px; border-bottom: 1px solid var(--border);">
                <div>
                    <span style="font-size: 11px; color: var(--muted); text-transform: uppercase; font-weight: 700; letter-spacing: 0.5px;">Customer ID</span>
                    <h3 id="view_id" style="font-family:'Montserrat',sans-serif; font-size: 22px; font-weight: 800; color: var(--dark); margin-top: 2px;">#0000</h3>
                </div>
                <div id="view_status"></div>
            </div>

            <!-- Customer Details Block -->
            <div style="margin-bottom: 20px;">
                <label class="za-label">Demographics</label>
                <div style="background: #F8FAFC; border: 1.5px solid var(--border); border-radius: 12px; padding: 14px 18px;">
                    <div style="font-weight: 700; color: var(--dark); font-size: 16px; margin-bottom: 8px;" id="view_name">Customer Name</div>
                    <div style="font-size: 13.5px; color: var(--text); line-height: 1.6;">
                        <i class="fa fa-venus-mars" style="width: 18px; color: var(--muted);"></i> Gender: <strong id="view_gender" style="text-transform:capitalize;"></strong><br>
                        <i class="fa fa-birthday-cake" style="width: 18px; color: var(--muted);"></i> Birthdate: <strong id="view_birthdate"></strong><br>
                        <i class="fa fa-check-circle" style="width: 18px; color: var(--muted);"></i> Verification: <span id="view_verified"></span>
                    </div>
                </div>
            </div>

            <!-- Contact Info Grid -->
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px; margin-bottom: 20px; border-top: 1px solid var(--border); padding-top: 20px;">
                <div>
                    <label class="za-label">Mobile Number</label>
                    <p id="view_mobile" style="font-size:15px; font-weight:700; color:var(--dark);"></p>
                </div>
                <div>
                    <label class="za-label">Email Address</label>
                    <p id="view_email" style="font-size:14px; font-weight:700; color:var(--dark); word-break:break-all;"></p>
                </div>
            </div>

            <!-- Location info -->
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px; border-top: 1px solid var(--border); padding-top: 20px;">
                <div>
                    <label class="za-label">City</label>
                    <p id="view_city" style="font-size:15px; font-weight:700; color:var(--dark);"></p>
                </div>
                <div>
                    <label class="za-label">State</label>
                    <p id="view_state" style="font-size:15px; font-weight:700; color:var(--dark);"></p>
                </div>
                <div style="grid-column: span 2; border-top: 1px solid var(--border); padding-top: 16px; margin-top: 8px;">
                    <label class="za-label">Home Address</label>
                    <p id="view_address" style="font-size:14px; font-weight:600; color:var(--text); line-height: 1.5;"></p>
                </div>
            </div>
        </div>
        <div class="za-modal-footer">
            <button type="button" class="za-btn za-btn-outline" onclick="closeModal('viewModal')">Close</button>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/jquery@2.2.4/dist/jquery.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.1.3/dist/js/bootstrap.min.js"></script>
<script>
// Modal Control Functions
function openModal(id) { 
    document.getElementById(id).classList.add('open'); 
    document.body.style.overflow = 'hidden'; 
}

function closeModal(id)
    { 
    document.getElementById(id).classList.remove('open'); 
    document.body.style.overflow = ''; 
}

function openViewModal(cust) {
    document.getElementById('view_id').innerText = '#' + cust.cust_id;
    document.getElementById('view_name').innerText = cust.cust_fname + ' ' + cust.cust_lname;
    document.getElementById('view_gender').innerText = cust.cust_gender;
    document.getElementById('view_birthdate').innerText = cust.cust_birthdate;
    
    // Verification status mapping
    var verifiedSpan = document.getElementById('view_verified');
    if (cust.is_verified == 1) {
        verifiedSpan.className = 'za-badge green';
        verifiedSpan.innerHTML = '<i class="fa fa-check-circle"></i> Verified';
    } else {
        verifiedSpan.className = 'za-badge gray';
        verifiedSpan.innerHTML = '<i class="fa fa-times-circle"></i> Unverified';
    }

    // Block status mapping
    var statusDiv = document.getElementById('view_status');
    if (cust.is_blocked == 1) {
        statusDiv.innerHTML = '<span class="za-badge red"><i class="fa fa-ban"></i> Blocked</span>';
    } else {
        statusDiv.innerHTML = '<span class="za-badge green"><i class="fa fa-circle"></i> Active</span>';
    }

    document.getElementById('view_mobile').innerText = cust.cust_mobile;
    document.getElementById('view_email').innerText = cust.cust_email;
    
    // Fetches Joined City & State Name dynamically
    document.getElementById('view_city').innerText = cust.c_name ? cust.c_name : 'N/A';
    document.getElementById('view_state').innerText = cust.s_name ? cust.s_name : 'N/A';
    document.getElementById('view_address').innerText = cust.cust_address ? cust.cust_address : 'Address not provided';

    openModal('viewModal');
}

// Mobile sidebar toggle function
function toggleSidebar() {
    document.getElementById('sidebar').classList.toggle('open');
}

// Show menu toggle on mobile
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