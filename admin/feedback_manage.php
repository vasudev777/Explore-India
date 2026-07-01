<?php
include('db.php');
include('check.php');

// ===== SEARCH & TYPE FILTERS =====
$search = $_GET['search'] ?? '';
$type_filter = $_GET['type'] ?? 'all';

$where = "WHERE 1=1";

if ($type_filter !== 'all') {
    $t = mysqli_real_escape_string($conn, $type_filter);
    $where .= " AND f.type = '$t'";
}

if ($search) {
    $s = mysqli_real_escape_string($conn, $search);
    $where .= " AND (f.message LIKE '%$s%' OR cd.cust_fname LIKE '%$s%' OR cd.cust_lname LIKE '%$s%' OR cd.cust_email LIKE '%$s%')";
}

// Fetch Feedback joined with Customer details
$query = "SELECT f.*, cd.cust_fname, cd.cust_lname, cd.cust_email, cd.cust_mobile 
          FROM feedback f 
          LEFT JOIN customer_details cd ON f.cust_id = cd.cust_id 
          $where 
          ORDER BY f.f_id DESC";

$result = mysqli_query($conn, $query);
$total_rows = $result ? mysqli_num_rows($result) : 0;

// Stats Calculation
$total_feedbacks = mysqli_num_rows(mysqli_query($conn, "SELECT * FROM feedback"));
$avg_res = mysqli_query($conn, "SELECT AVG(rating) as average FROM feedback");
$avg_rating = mysqli_fetch_assoc($avg_res)['average'] ?? 0;

// Fetch distinct types for filter buttons
$types_res = mysqli_query($conn, "SELECT DISTINCT type FROM feedback WHERE type IS NOT NULL AND type != ''");

// Helper function to render star rating HTML
function getStarHtml($rating) {
    $stars = '';
    for ($i = 1; $i <= 5; $i++) {
        if ($i <= $rating) {
            $stars .= '<i class="fa fa-star" style="color:#FF9F43; margin-right:2px;"></i>';
        } else {
            $stars .= '<i class="fa fa-star-o" style="color:#cbd5e1; margin-right:2px;"></i>';
        }
    }
    return $stars;
}

$page_title = "Feedback Audit"; // Sets Topbar Title dynamically
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Feedback Hub – Explore India Admin</title>
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

        /* STAT CARDS */
        .stat-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 24px; }
        .stat-card { background: #fff; border: 1.5px solid var(--border); border-radius: 20px; padding: 20px 24px; display: flex; align-items: center; gap: 18px; }
        .stat-icon { width: 54px; height: 54px; border-radius: 16px; display: flex; align-items: center; justify-content: center; font-size: 22px; color: #fff; }
        .stat-info .stat-num { font-family: 'Montserrat', sans-serif; font-size: 26px; font-weight: 900; color: var(--dark); }
        .stat-info .stat-label { font-size: 11px; color: var(--muted); font-weight: 800; text-transform: uppercase; letter-spacing: 0.5px; }

        /* ══ TABLE CARD ══ */
        .za-card { background: #fff; border: 1.5px solid var(--border); border-radius: 20px; overflow: hidden; box-shadow: 0 4px 20px rgba(0,0,0,0.01); }
        .za-card-header { padding: 20px 24px; border-bottom: 1px solid var(--border); display: flex; align-items: center; justify-content: space-between; gap: 12px; flex-wrap: wrap; }
        .za-card-title { font-family: 'Montserrat', sans-serif; font-size: 15px; font-weight: 800; color: var(--dark); }

        .za-table { width: 100%; border-collapse: collapse; font-size: 13.5px; }
        .za-table th { font-size: 11px; font-weight: 700; letter-spacing: 1px; text-transform: uppercase; color: var(--muted); padding: 14px 18px; text-align: left; border-bottom: 1px solid var(--border); background: #FCFDFE; }
        .za-table td { padding: 14px 18px; border-bottom: 1px solid #F1F5F9; vertical-align: middle; }
        .za-table tr:hover td { background: #F8FAFC; }

        /* BADGES & CHIPS */
        .za-badge { display: inline-flex; align-items: center; padding: 4px 10px; border-radius: 50px; font-size: 11px; font-weight: 700; text-transform: uppercase; }
        .za-badge.orange { background: rgba(255,120,44,0.1); color: var(--orange); }
        .za-badge.blue { background: rgba(59,130,246,0.1); color: var(--blue); }

        /* FILTERS & SEARCH */
        .za-filter-bar { display: flex; align-items: center; gap: 10px; flex-wrap: wrap; }
        .za-filter-pill { padding: 7px 16px; border-radius: 50px; border: 1.5px solid var(--border); background: #fff; font-size: 13px; font-weight: 600; color: var(--text); cursor: pointer; text-decoration: none; transition: all 0.2s; text-transform: capitalize; }
        .za-filter-pill:hover { border-color: var(--orange); color: var(--orange); text-decoration: none; }
        .za-filter-pill.active { background: var(--orange); border-color: var(--orange); color: #fff; }

        .za-search-wrap { position: relative; }
        .za-search-wrap i { position: absolute; left: 14px; top: 50%; transform: translateY(-50%); color: var(--muted); font-size: 13px; }
        .za-search-input { padding: 8px 14px 8px 36px; border: 1.5px solid var(--border); border-radius: 50px; font-size: 13px; color: var(--dark); background: #fff; outline: none; width: 220px; transition: border-color 0.2s; }
        .za-search-input:focus { border-color: var(--orange); }

        .za-btn { display: inline-flex; align-items: center; gap: 6px; padding: 6px 14px; border-radius: 10px; font-size: 12.5px; font-weight: 700; cursor: pointer; border: 1.5px solid var(--border); background: transparent; color: var(--text); transition: all 0.2s; }
        .za-btn:hover { border-color: var(--dark); color: var(--dark); transform: translateY(-1px); }

        /* CUSTOMER AVATAR */
        .za-cust-avatar { width: 38px; height: 38px; border-radius: 50%; display: flex; align-items: center; justify-content: center; color: #fff; font-family: 'Montserrat', sans-serif; font-size: 14px; font-weight: 800; text-transform: uppercase; background: var(--orange-grad); }

        /* MODAL */
        .za-modal-overlay { position: fixed; inset: 0; background: rgba(15, 23, 42, 0.4); z-index: 9999; display: none; align-items: center; justify-content: center; backdrop-filter: blur(4px); }
        .za-modal-overlay.open { display: flex; }
        .za-modal { background: #fff; border-radius: 24px; width: 90%; max-width: 540px; max-height: 90vh; overflow-y: auto; box-shadow: 0 20px 50px rgba(15, 23, 42, 0.15); border: 1px solid var(--border); }
        .za-modal-header { padding: 22px 24px; border-bottom: 1px solid var(--border); display: flex; align-items: center; justify-content: space-between; position: sticky; top: 0; background: #fff; z-index: 1; }
        .za-modal-title { font-family: 'Montserrat', sans-serif; font-size: 17px; font-weight: 800; color: var(--dark); }
        .za-modal-close { width: 32px; height: 32px; border-radius: 50%; background: #F1F5F9; border: none; cursor: pointer; display: flex; align-items: center; justify-content: center; font-size: 14px; color: var(--text); transition: all 0.2s; }
        .za-modal-close:hover { background: var(--border); color: var(--dark); }
        .za-modal-body { padding: 24px; }
        .za-modal-footer { padding: 16px 24px; border-top: 1px solid var(--border); display: flex; gap: 10px; justify-content: flex-end; position: sticky; bottom: 0; background: #fff; }
        .za-label { display: block; font-size: 10px; font-weight: 800; color: var(--muted); letter-spacing: 0.5px; margin-bottom: 6px; text-transform: uppercase; }

        .za-empty { text-align: center; padding: 60px 20px; color: var(--muted); }
        .za-empty i { font-size: 40px; margin-bottom: 12px; display: block; opacity: 0.3; }

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
            <h1>Feedback Hub</h1>
            <p>Monitor ratings, customer messages, and suggestions submitted across categories.</p>
        </div>

        <!-- Stats Grid -->
        <div class="stat-grid">
            <div class="stat-card">
                <div class="stat-icon" style="background: var(--blue-grad);">💬</div>
                <div class="stat-info">
                    <div class="stat-num"><?= $total_feedbacks ?></div>
                    <div class="stat-label">Total Reviews</div>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon" style="background: var(--orange-grad);">⭐️</div>
                <div class="stat-info">
                    <div class="stat-num"><?= number_format($avg_rating, 1) ?> / 5.0</div>
                    <div class="stat-label">Average Platform Score</div>
                </div>
            </div>
        </div>

        <!-- Table Card -->
        <div class="za-card">
            <div class="za-card-header">
                <div class="za-card-title">Customer Feedback Log (<?= $total_rows ?>)</div>
                
                <div style="display:flex; gap:12px; align-items:center; flex-wrap:wrap;">
                    <!-- Type Filters -->
                    <div class="za-filter-bar">
                        <a href="feedback_manage.php?type=all&search=<?= urlencode($search) ?>" class="za-filter-pill <?= $type_filter=='all'?'active':'' ?>">All Categories</a>
                        <?php while ($ty = mysqli_fetch_assoc($types_res)): ?>
                            <a href="feedback_manage.php?type=<?= urlencode($ty['type']) ?>&search=<?= urlencode($search) ?>" class="za-filter-pill <?= $type_filter==$ty['type']?'active':'' ?>">
                                <?= htmlspecialchars($ty['type']) ?>
                            </a>
                        <?php endwhile; ?>
                    </div>
                    
                    <!-- Search Input -->
                    <form method="GET" class="za-search-wrap">
                        <input type="hidden" name="type" value="<?= htmlspecialchars($type_filter) ?>">
                        <i class="fa fa-search"></i>
                        <input type="text" name="search" class="za-search-input" placeholder="Search message or name..." value="<?= htmlspecialchars($search) ?>">
                    </form>
                </div>
            </div>

            <div style="overflow-x:auto;">
                <?php if ($total_rows > 0): ?>
                    <table class="za-table">
                        <thead>
                            <tr>
                                <th>#ID</th>
                                <th>Customer Info</th>
                                <th>Rating Star</th>
                                <th>Feedback Message</th>
                                <th>Category Type</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($row = mysqli_fetch_assoc($result)): ?>
                                <tr>
                                    <td style="color:var(--muted); font-size:12px; font-weight:700;">#<?= $row['f_id'] ?></td>
                                    <td>
                                        <div style="display:flex; align-items:center; gap:10px;">
                                            <div class="za-cust-avatar">
                                                <?= $row['cust_fname'] ? substr($row['cust_fname'], 0, 1) : 'U' ?>
                                            </div>
                                            <div>
                                                <div style="font-weight:700; color:var(--dark);">
                                                    <?= $row['cust_fname'] ? htmlspecialchars($row['cust_fname'] . ' ' . $row['cust_lname']) : 'Unknown Customer' ?>
                                                </div>
                                                <div style="font-size:11.5px; color:var(--muted);"><?= htmlspecialchars($row['cust_email'] ?? 'No email available') ?></div>
                                            </div>
                                        </div>
                                    </td>
                                    <td style="white-space:nowrap;"><?= getStarHtml($row['rating']) ?></td>
                                    <td style="max-width:320px; font-weight:600; color:var(--dark); overflow:hidden; text-overflow:ellipsis; white-space:nowrap;">
                                        <?= htmlspecialchars($row['message']) ?>
                                    </td>
                                    <td>
                                        <span class="za-badge orange">
                                            <?= htmlspecialchars($row['type'] ? $row['type'] : 'General') ?>
                                        </span>
                                    </td>
                                    <td>
                                        <button class="za-btn" onclick='openFeedbackModal(<?= json_encode($row, JSON_HEX_APOS | JSON_HEX_QUOT) ?>)'>
                                            <i class="fa fa-eye"></i> View Feedback
                                        </button>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                <?php else: ?>
                    <div class="za-empty">
                        <i class="fa fa-comment-o"></i>
                        <p>No feedback matching the filter query found.</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>

    </div>
</div>

<!-- VIEW MODAL -->
<div class="za-modal-overlay" id="feedbackModal">
    <div class="za-modal">
        <div class="za-modal-header">
            <div class="za-modal-title">💬 Customer Review & Feedback</div>
            <button class="za-modal-close" onclick="closeModal('feedbackModal')">✕</button>
        </div>
        <div class="za-modal-body">
            <!-- Review Stars and Score -->
            <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:20px; padding-bottom:15px; border-bottom:1px solid var(--border);">
                <div>
                    <span class="za-label">Star Rating</span>
                    <div id="modal_stars" style="font-size:18px; margin-top:2px;"></div>
                </div>
                <div>
                    <span class="za-label">Category Type</span>
                    <span id="modal_type" class="za-badge orange" style="margin-top:2px;"></span>
                </div>
            </div>

            <!-- Customer Details -->
            <div style="margin-bottom:20px;">
                <span class="za-label">Submitted By</span>
                <div style="background:#F8FAFC; border:1.5px solid var(--border); border-radius:12px; padding:14px 18px; display:flex; align-items:center; gap:12px;">
                    <div class="za-cust-avatar" style="width:42px; height:42px; font-size:16px;">U</div>
                    <div>
                        <div id="modal_name" style="font-weight:700; color:var(--dark); font-size:15px;">Customer Name</div>
                        <div id="modal_email" style="font-size:12.5px; color:var(--muted);">email@example.com</div>
                    </div>
                </div>
            </div>

            <!-- Full Message -->
            <div>
                <span class="za-label">Full Message</span>
                <p id="modal_message" style="background:#fffcf7; border:1px solid #fde8b0; border-radius:14px; padding:16px 20px; font-size:14px; font-weight:600; color:var(--dark); line-height:1.6; white-space:pre-wrap;"></p>
            </div>
        </div>
        <div class="za-modal-footer">
            <button class="za-btn" onclick="closeModal('feedbackModal')">Close</button>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/jquery@2.2.4/dist/jquery.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.1.3/dist/js/bootstrap.min.js"></script>
<script>
// Modal Functions
function openModal(id) {
    document.getElementById(id).classList.add('open');
    document.body.style.overflow = 'hidden';
}

function closeModal(id) {
    document.getElementById(id).classList.remove('open');
        document.body.style.overflow = '';
}

function openFeedbackModal(fb) {
    // Dynamic Name Mapping
    var custName = fb.cust_fname ? (fb.cust_fname + ' ' + fb.cust_lname) : 'Unknown Customer';
    var custEmail = fb.cust_email ? fb.cust_email : 'No email available';
    
    document.getElementById('modal_name').innerText = custName;
    document.getElementById('modal_email').innerText = custEmail;
    document.getElementById('modal_type').innerText = fb.type ? fb.type.toUpperCase() : 'GENERAL';
    document.getElementById('modal_message').innerText = fb.message;
    
    // Dynamic Star Mapping inside modal
    var starsHtml = '';
    var rating = parseInt(fb.rating);
    for (var i = 1; i <= 5; i++) {
        if (i <= rating) {
            starsHtml += '<i class="fa fa-star" style="color:#FF9F43; margin-right:3px;"></i>';
        } else {
            starsHtml += '<i class="fa fa-star-o" style="color:#cbd5e1; margin-right:3px;"></i>';
        }
    }
    document.getElementById('modal_stars').innerHTML = starsHtml;

    // Set first letter as avatar icon
    var avatarLetter = fb.cust_fname ? fb.cust_fname.charAt(0).toUpperCase() : 'U';
    var modalAvatar = document.querySelector('#feedbackModal .za-cust-avatar');
    if (modalAvatar) modalAvatar.innerText = avatarLetter;

    openModal('feedbackModal');
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