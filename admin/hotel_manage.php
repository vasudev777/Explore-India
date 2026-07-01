<?php
include('db.php');
include('check.php');

$msg = '';
$msg_type = '';

$edit_id = 0;
$edit_name = '';
$edit_price = '';
$edit_s_id = 0;
$edit_c_id = 0;
$edit_phone = '';
$edit_rate = 5;

// ===== ADD HOTEL =====
if (isset($_POST['add_hotel'])) {
    $h_name  = mysqli_real_escape_string($conn, trim($_POST['h_name']));
    $h_price = floatval($_POST['h_price']);
    $s_id    = intval($_POST['s_id']);
    $c_id    = intval($_POST['c_id']);
    $h_phone = mysqli_real_escape_string($conn, trim($_POST['h_phone']));
    $h_rate  = intval($_POST['h_rate']);

    if (!empty($h_name) && $s_id > 0 && $c_id > 0) {
        $sql = "INSERT INTO hotel (h_name, h_price, s_id, c_id, h_phone, h_rate) 
                VALUES ('$h_name', '$h_price', '$s_id', '$c_id', '$h_phone', '$h_rate')";
        if (mysqli_query($conn, $sql)) {
            $msg = "Hotel added successfully!";
            $msg_type = "success";
        } else {
            $msg = "Error adding hotel profile.";
            $msg_type = "error";
        }
    } else {
        $msg = "Please enter Hotel Name and select location.";
        $msg_type = "error";
    }
}

// ===== UPDATE HOTEL =====
if (isset($_POST['update_hotel'])) {
    $id      = intval($_POST['edit_id']);
    $h_name  = mysqli_real_escape_string($conn, trim($_POST['h_name']));
    $h_price = floatval($_POST['h_price']);
    $s_id    = intval($_POST['s_id']);
    $c_id    = intval($_POST['c_id']);
    $h_phone = mysqli_real_escape_string($conn, trim($_POST['h_phone']));
    $h_rate  = intval($_POST['h_rate']);

    if (!empty($h_name) && $s_id > 0 && $c_id > 0) {
        $sql = "UPDATE hotel SET 
                h_name='$h_name', h_price='$h_price', s_id='$s_id', c_id='$c_id', h_phone='$h_phone', h_rate='$h_rate' 
                WHERE h_id='$id'";
        if (mysqli_query($conn, $sql)) {
            $msg = "Hotel details updated successfully!";
            $msg_type = "success";
        } else {
            $msg = "Error updating hotel profile.";
            $msg_type = "error";
        }
    } else {
        $msg = "Please enter Hotel Name and select location.";
        $msg_type = "error";
    }
}

// ===== LOAD EDIT DATA =====
if (isset($_GET['edit'])) {
    $edit_id = intval($_GET['edit']);
    $res = mysqli_query($conn, "SELECT * FROM hotel WHERE h_id='$edit_id'");
    if ($row = mysqli_fetch_assoc($res)) {
        $edit_name  = $row['h_name'];
        $edit_price = $row['h_price'];
        $edit_s_id  = $row['s_id'];
        $edit_c_id  = $row['c_id'];
        $edit_phone = $row['h_phone'];
        $edit_rate  = $row['h_rate'];
    }
}

// ===== DELETE HOTEL =====
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    if (mysqli_query($conn, "DELETE FROM hotel WHERE h_id='$id'")) {
        $msg = "Hotel profile deleted successfully!";
        $msg_type = "success";
    } else {
        $msg = "Error deleting hotel profile.";
        $msg_type = "error";
    }
}

// Fetch all hotels joined with State & City Names
$hotels_res = mysqli_query($conn, "SELECT h.*, s.s_name, c.c_name 
                                   FROM hotel h 
                                   LEFT JOIN state s ON h.s_id = s.s_id 
                                   LEFT JOIN city c ON h.c_id = c.c_id 
                                   ORDER BY h.h_id DESC");

// Fetch states & cities list for dropdowns
$states_list = mysqli_query($conn, "SELECT * FROM state ORDER BY s_name ASC");
$cities_list = mysqli_query($conn, "SELECT * FROM city ORDER BY c_name ASC");

function getStarHtml($rating) {
    $stars = '';
    for ($i = 1; $i <= 5; $i++) {
        if ($i <= $rating) {
            $stars .= '<i class="fa fa-star" style="color:#FF9F43; margin-right:1px;"></i>';
        } else {
            $stars .= '<i class="fa fa-star-o" style="color:#cbd5e1; margin-right:1px;"></i>';
        }
    }
    return $stars;
}

$page_title = "Hotel Directory"; // Sets Topbar Title dynamically
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Hotels – Explore India Admin</title>
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

        /* ══ TWO COLUMN GRID ══ */
        .split-grid { display: grid; grid-template-columns: 360px 1fr; gap: 24px; align-items: start; }

        .za-card { background: #fff; border: 1.5px solid var(--border); border-radius: 20px; overflow: hidden; box-shadow: 0 4px 20px rgba(0,0,0,0.01); }
        .za-card-header { padding: 20px 24px; border-bottom: 1px solid var(--border); display: flex; align-items: center; justify-content: space-between; }
        .za-card-title { font-family: 'Montserrat', sans-serif; font-size: 15px; font-weight: 800; color: var(--dark); }
        .za-card-body { padding: 24px; }

        /* FORM CONTROLS */
        .form-group { margin-bottom: 16px; }
        .form-group label { font-size: 11px; font-weight: 800; text-transform: uppercase; color: var(--muted); display: block; margin-bottom: 6px; letter-spacing: 0.5px; }
        .form-control { border: 1.5px solid var(--border); border-radius: 12px; padding: 10px 14px; font-size: 13.5px; color: var(--dark); background: #fff; outline: none; transition: border-color 0.2s; width: 100%; font-family: 'Open Sans', sans-serif; }
        .form-control:focus { border-color: var(--orange); }

        select.form-control {
            appearance: none; cursor: pointer;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='8' viewBox='0 0 12 8'%3E%3Cpath d='M1 1l5 5 5-5' stroke='%23888' stroke-width='1.5' fill='none'/%3E%3C/svg%3E");
            background-repeat: no-repeat; background-position: right 14px center; padding-right: 32px;
        }

        .za-btn { display: inline-flex; align-items: center; justify-content: center; gap: 6px; padding: 10px 20px; border-radius: 12px; font-size: 13.5px; font-weight: 700; cursor: pointer; transition: all 0.2s; border: none; text-decoration: none; font-family: 'Open Sans', sans-serif; width: 100%; text-align: center; }
        .za-btn:hover { transform: translateY(-1px); text-decoration: none; }
        .za-btn-orange { background: var(--orange-grad); color: #fff; box-shadow: 0 4px 12px rgba(255,120,44,0.2); }
        .za-btn-orange:hover { color: #fff; }
        .za-btn-outline { background: transparent; color: var(--text); border: 1.5px solid var(--border); }
        .za-btn-outline:hover { border-color: var(--dark); color: var(--dark); }

        /* TABLE */
        .za-table { width: 100%; border-collapse: collapse; font-size: 13.5px; }
        .za-table th { font-size: 11px; font-weight: 700; letter-spacing: 1px; text-transform: uppercase; color: var(--muted); padding: 14px 18px; text-align: left; border-bottom: 1px solid var(--border); background: #FCFDFE; }
        .za-table td { padding: 14px 18px; border-bottom: 1px solid #F1F5F9; vertical-align: middle; }
        .za-table tr:hover td { background: #F8FAFC; }

        /* BADGES */
        .za-badge { display: inline-flex; align-items: center; gap: 5px; padding: 4px 10px; border-radius: 50px; font-size: 11px; font-weight: 700; }
        .za-badge.blue { background: rgba(59,130,246,0.1); color: var(--blue); }
        .za-badge.orange { background: rgba(255,120,44,0.1); color: var(--orange); }

        .action-links { display: flex; gap: 10px; }
        .action-links a { font-size: 12.5px; font-weight: 700; text-decoration: none; transition: color 0.2s; display: inline-flex; align-items: center; gap: 4px; }
        .edit-link { color: var(--blue); }
        .edit-link:hover { color: #1d4ed8; }
        .delete-link { color: var(--red); }
        .delete-link:hover { color: #dc2626; }

        /* ALERTS */
        .za-alert { padding: 12px 16px; border-radius: 10px; font-size: 13.5px; font-weight: 500; margin-bottom: 20px; display: flex; align-items: center; gap: 10px; }
        .za-alert.success { background: rgba(16,185,129,0.08); color: #065f46; border-left: 4px solid var(--green); }
        .za-alert.error   { background: rgba(239,68,68,0.08); color: #991b1b; border-left: 4px solid var(--red); }

        .za-empty { text-align: center; padding: 40px 20px; color: var(--muted); }
        .za-empty i { font-size: 32px; margin-bottom: 8px; display: block; opacity: 0.3; }

        @media (max-width: 1200px) {
            .split-grid { grid-template-columns: 1fr; }
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
            <h1>Hotel Directory</h1>
            <p>Manage list of hotels, set ratings, room pricing, and map location parameters dynamically.</p>
        </div>

        <?php if ($msg): ?>
            <div class="za-alert <?= $msg_type ?>">
                <i class="fa <?= $msg_type == 'success' ? 'fa-check-circle' : 'fa-exclamation-triangle' ?>"></i>
                <?= htmlspecialchars($msg) ?>
            </div>
        <?php endif; ?>

        <!-- Split Grid Layout -->
        <div class="split-grid">
            
            <!-- Left: Add/Edit Form -->
            <div class="za-card">
                <div class="za-card-header">
                    <div class="za-card-title"><?= $edit_id > 0 ? "✏️ Edit Hotel" : "➕ Add New Hotel" ?></div>
                </div>
                <div class="za-card-body">
                    <form method="POST">
                        <?php if ($edit_id > 0): ?>
                            <input type="hidden" name="edit_id" value="<?= $edit_id ?>">
                        <?php endif; ?>

                        <!-- Hotel Name -->
                        <div class="form-group">
                            <label>Hotel Name</label>
                            <input type="text" name="h_name" class="form-control" placeholder="Enter hotel name" value="<?= htmlspecialchars($edit_name) ?>" required>
                        </div>

                        <!-- Price and Phone (Grid) -->
                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 12px;">
                            <div class="form-group">
                                <label>Price per Night (₹)</label>
                                <input type="number" step="0.01" name="h_price" class="form-control" placeholder="e.g. 1500" value="<?= htmlspecialchars($edit_price) ?>" required>
                            </div>
                            <div class="form-group">
                                <label>Contact Phone</label>
                                <input type="text" name="h_phone" class="form-control" placeholder="e.g. 98345xxxxx" value="<?= htmlspecialchars($edit_phone) ?>">
                            </div>
                        </div>

                        <!-- State Dropdown Selector -->
                        <div class="form-group">
                            <label>State Location</label>
                            <select name="s_id" id="stateSelect" class="form-control" required>
                                <option value="">-- Select State --</option>
                                <?php if (mysqli_num_rows($states_list) > 0): ?>
                                    <?php while ($st = mysqli_fetch_assoc($states_list)): ?>
                                        <option value="<?= $st['s_id'] ?>" <?= ($edit_s_id == $st['s_id']) ? 'selected' : '' ?>>
                                            <?= htmlspecialchars($st['s_name']) ?>
                                        </option>
                                    <?php endwhile; ?>
                                <?php endif; ?>
                            </select>
                        </div>

                        <!-- City Dropdown Selector (Dependent on State) -->
                        <div class="form-group">
                            <label>City Location</label>
                            <select name="c_id" id="citySelect" class="form-control" required>
                                <option value="">-- Select City --</option>
                                <!-- JavaScript will load cities matching selected State -->
                            </select>
                        </div>

                        <!-- Hotel Rating (Stars) -->
                        <div class="form-group">
                            <label>Hotel Rating</label>
                            <select name="h_rate" class="form-control" required>
                                <option value="5" <?= ($edit_rate == 5) ? 'selected' : '' ?>>⭐⭐⭐⭐⭐ (5 Star)</option>
                                <option value="4" <?= ($edit_rate == 4) ? 'selected' : '' ?>>⭐⭐⭐⭐ (4 Star)</option>
                                <option value="3" <?= ($edit_rate == 3) ? 'selected' : '' ?>>⭐⭐⭐ (3 Star)</option>
                                <option value="2" <?= ($edit_rate == 2) ? 'selected' : '' ?>>⭐⭐ (2 Star)</option>
                                <option value="1" <?= ($edit_rate == 1) ? 'selected' : '' ?>>⭐ (1 Star)</option>
                            </select>
                        </div>

                        <?php if ($edit_id > 0): ?>
                            <button type="submit" name="update_hotel" class="za-btn za-btn-orange">Update Hotel Details</button>
                            <a href="hotel_manage.php" class="za-btn za-btn-outline" style="margin-top: 10px;">Cancel</a>
                        <?php else: ?>
                            <button type="submit" name="add_hotel" class="za-btn za-btn-orange">Add Hotel</button>
                        <?php endif; ?>
                    </form>
                </div>
            </div>

            <!-- Right: Hotels Directory Table -->
            <div class="za-card">
                <div class="za-card-header">
                    <div class="za-card-title">All Hotels (<?= mysqli_num_rows($hotels_res) ?>)</div>
                </div>
                <div style="overflow-x:auto;">
                    <?php if (mysqli_num_rows($hotels_res) > 0): ?>
                        <table class="za-table">
                            <thead>
                                <tr>
                                    <th>#ID</th>
                                    <th>Hotel Details</th>
                                    <th>Price/Night</th>
                                    <th>Location</th>
                                    <th>Rating</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php while ($row = mysqli_fetch_assoc($hotels_res)): ?>
                                    <tr>
                                        <td style="font-weight:700; color:var(--muted);">#<?= $row['h_id'] ?></td>
                                        <td>
                                            <div style="font-weight:700; color:var(--dark); font-size:14.5px;"><?= htmlspecialchars($row['h_name']) ?></div>
                                            <div style="font-size:12px; color:var(--muted); margin-top:2px;">📞 <?= htmlspecialchars($row['h_phone'] ? $row['h_phone'] : 'No Contact Phone') ?></div>
                                        </td>
                                        <td style="font-weight:700; color:var(--green);">₹<?= number_format($row['h_price'], 2) ?></td>
                                        <td>
                                            <span class="za-badge blue">
                                                <i class="fa fa-map-marker"></i> <?= htmlspecialchars($row['c_name'] ?? 'N/A') ?>, <?= htmlspecialchars($row['s_name'] ?? 'N/A') ?>
                                            </span>
                                        </td>
                                        <td><span style="white-space:nowrap;"><?= getStarHtml($row['h_rate']) ?></span></td>
                                        <td>
                                            <div class="action-links">
                                                <a href="hotel_manage.php?edit=<?= $row['h_id'] ?>" class="edit-link">
                                                    <i class="fa fa-pencil"></i> Edit
                                                </a>
                                                <a href="hotel_manage.php?delete=<?= $row['h_id'] ?>" class="delete-link" onclick="return confirm('Are you sure you want to delete this hotel profile?')">
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
                            <i class="fa fa-building-o"></i>
                            <p>No hotels added to the database directory yet.</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

        </div>

    </div>
    
    <!-- FOOTER -->
    <div class="admin-footer">
        <span>© 2026 Explore India — Admin Panel</span>
        <span>Made with ❤️ in India</span>
    </div>
</div>

<!-- Raw Cities Data array for JS dependent dropdown filter -->
<script>
const allCities = [
    <?php 
    if (mysqli_num_rows($cities_list) > 0) {
        mysqli_data_seek($cities_list, 0); // Reset pointer
        while ($ct = mysqli_fetch_assoc($cities_list)) {
            echo "{ c_id: " . $ct['c_id'] . ", s_id: " . $ct['s_id'] . ", c_name: '" . addslashes($ct['c_name']) . "' },";
        }
    }
    ?>
];
const editCityId = <?= $edit_c_id > 0 ? $edit_c_id : 0 ?>;
</script>

<script src="https://cdn.jsdelivr.net/npm/jquery@2.2.4/dist/jquery.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.1.3/dist/js/bootstrap.min.js"></script>
<script>
// State-City Filter Logic
const stateSelect = document.getElementById('stateSelect');
const citySelect = document.getElementById('citySelect');

function filterCities() {
    const selectedStateId = parseInt(stateSelect.value);
    
    // Clear City options, leave default
    citySelect.innerHTML = '<option value="">-- Select City --</option>';
    
    if (!selectedStateId) return;

    // Filter matching cities
    const filtered = allCities.filter(c => c.s_id === selectedStateId);
    
    // Populate city dropdown
    filtered.forEach(city => {
        const option = document.createElement('option');
        option.value = city.c_id;
        option.textContent = city.c_name;
        
        // Auto-select in edit mode
        if (editCityId === city.c_id) {
            option.selected = true;
        }
        citySelect.appendChild(option);
    });
}

// Trigger state filter logic on change
stateSelect.addEventListener('change', filterCities);

// Initialize on page load (essential for edit mode)
document.addEventListener('DOMContentLoaded', () => {
    if (stateSelect.value) {
        filterCities();
    }
});

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