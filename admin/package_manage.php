<?php
include('db.php');
include('check.php');

$msg = '';
$msg_type = '';

$edit_id = 0;
$edit_name = '';
$edit_s_id = 0;
$edit_h_ids = [];
$edit_price = '';

// ===== Helper function to decode comma-separated hotel IDs to actual names =====
function getHotelNames($conn, $h_ids) {
    if (empty(trim($h_ids))) return '<span class="text-muted">None Selected</span>';
    
    // Clean string to keep only digits and commas
    $cleaned = preg_replace('/[^0-9,]/', '', $h_ids);
    if (empty($cleaned)) return '<span class="text-muted">None Selected</span>';
    
    $res = mysqli_query($conn, "SELECT h_name FROM hotel WHERE h_id IN ($cleaned)");
    $names = [];
    if ($res) {
        while ($row = mysqli_fetch_assoc($res)) {
            $names[] = htmlspecialchars($row['h_name']);
        }
    }
    return count($names) > 0 ? implode(', ', $names) : '<span class="text-muted">Unknown Hotels</span>';
}

// ===== ADD PACKAGE =====
if (isset($_POST['add_package'])) {
    $pa_name = mysqli_real_escape_string($conn, trim($_POST['pa_name']));
    $s_id    = intval($_POST['s_id']);
    $price   = floatval($_POST['price']);
    
    // Combine multiple selected hotels to comma-separated string
    $h_ids_arr = $_POST['h_ids'] ?? [];
    $h_ids_str = implode(',', array_map('intval', $h_ids_arr));

    if (!empty($pa_name) && $s_id > 0) {
        $sql = "INSERT INTO package (pa_name, s_id, h_id, price) 
                VALUES ('$pa_name', '$s_id', '$h_ids_str', '$price')";
        if (mysqli_query($conn, $sql)) {
            $msg = "Special Package added successfully!";
            $msg_type = "success";
        } else {
            $msg = "Error adding package.";
            $msg_type = "error";
        }
    } else {
        $msg = "Please enter Package Name and select State.";
        $msg_type = "error";
    }
}

// ===== UPDATE PACKAGE =====
if (isset($_POST['update_package'])) {
    $id      = intval($_POST['edit_id']);
    $pa_name = mysqli_real_escape_string($conn, trim($_POST['pa_name']));
    $s_id    = intval($_POST['s_id']);
    $price   = floatval($_POST['price']);
    
    // Combine multiple selected hotels to comma-separated string
    $h_ids_arr = $_POST['h_ids'] ?? [];
    $h_ids_str = implode(',', array_map('intval', $h_ids_arr));

    if (!empty($pa_name) && $s_id > 0) {
        $sql = "UPDATE package SET 
                pa_name='$pa_name', s_id='$s_id', h_id='$h_ids_str', price='$price' 
                WHERE pa_id='$id'";
        if (mysqli_query($conn, $sql)) {
            $msg = "Special Package details updated successfully!";
            $msg_type = "success";
        } else {
            $msg = "Error updating package details.";
            $msg_type = "error";
        }
    } else {
        $msg = "Please enter Package Name and select State.";
        $msg_type = "error";
    }
}

// ===== LOAD EDIT DATA =====
if (isset($_GET['edit'])) {
    $edit_id = intval($_GET['edit']);
    $res = mysqli_query($conn, "SELECT * FROM package WHERE pa_id='$edit_id'");
    if ($row = mysqli_fetch_assoc($res)) {
        $edit_name  = $row['pa_name'];
        $edit_s_id  = $row['s_id'];
        $edit_price = $row['price'];
        
        // Convert comma-separated string to array
        $edit_h_ids = !empty($row['h_id']) ? explode(',', $row['h_id']) : [];
    }
}

// ===== DELETE PACKAGE =====
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    if (mysqli_query($conn, "DELETE FROM package WHERE pa_id='$id'")) {
        $msg = "Special Package deleted successfully!";
        $msg_type = "success";
    } else {
        $msg = "Error deleting package.";
        $msg_type = "error";
    }
}

// Fetch all packages with State Names (LEFT JOIN)
$packages_res = mysqli_query($conn, "SELECT p.*, s.s_name 
                                     FROM package p 
                                     LEFT JOIN state s ON p.s_id = s.s_id 
                                     ORDER BY p.pa_id DESC");

// Fetch states list
$states_list = mysqli_query($conn, "SELECT * FROM state ORDER BY s_name ASC");

// Fetch all hotels to load in multiple select dropdown
$hotels_list = mysqli_query($conn, "SELECT h_id, h_name, s_id FROM hotel ORDER BY h_name ASC");

$page_title = "Special Packages"; // Sets Topbar Title dynamically
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Packages – Explore India Admin</title>
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

        /* Multiple select customize */
        select.form-control[multiple] {
            background-image: none;
            padding: 10px;
            height: 140px;
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
            <h1>Special Packages</h1>
            <p>Create predefined tour packages, map them to states, link multiple available hotels, and set package pricing.</p>
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
                    <div class="za-card-title"><?= $edit_id > 0 ? "✏️ Edit Package" : "➕ Add New Package" ?></div>
                </div>
                <div class="za-card-body">
                    <form method="POST">
                        <?php if ($edit_id > 0): ?>
                            <input type="hidden" name="edit_id" value="<?= $edit_id ?>">
                        <?php endif; ?>

                        <!-- Package Name -->
                        <div class="form-group">
                            <label>Package Name</label>
                            <input type="text" name="pa_name" class="form-control" placeholder="Enter package name (e.g. Kashmir Bliss)" value="<?= htmlspecialchars($edit_name) ?>" required>
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

                        <!-- Included Hotels (Multiple Selection dropdown - Dependent on State) -->
                        <div class="form-group">
                            <label>Included Hotels (Hold Ctrl to select multiple)</label>
                            <select name="h_ids[]" id="hotelSelect" class="form-control" multiple>
                                <!-- JavaScript will load hotels belonging to selected State -->
                            </select>
                        </div>

                        <!-- Package Price -->
                        <div class="form-group">
                            <label>Package Price (₹)</label>
                            <input type="number" step="0.01" name="price" class="form-control" placeholder="e.g. 25000" value="<?= htmlspecialchars($edit_price) ?>" required>
                        </div>

                        <?php if ($edit_id > 0): ?>
                            <button type="submit" name="update_package" class="za-btn za-btn-orange">Update Package</button>
                            <a href="package_manage.php" class="za-btn za-btn-outline" style="margin-top: 10px;">Cancel</a>
                        <?php else: ?>
                            <button type="submit" name="add_package" class="za-btn za-btn-orange">Add Package</button>
                        <?php endif; ?>
                    </form>
                </div>
            </div>

            <!-- Right: Packages List Table -->
            <div class="za-card">
                <div class="za-card-header">
                    <div class="za-card-title">All Special Packages (<?= mysqli_num_rows($packages_res) ?>)</div>
                </div>
                <div style="overflow-x:auto;">
                    <?php if (mysqli_num_rows($packages_res) > 0): ?>
                        <table class="za-table">
                            <thead>
                                <tr>
                                    <th>#ID</th>
                                    <th>Package Name</th>
                                    <th>State Location</th>
                                    <th>Included Hotels</th>
                                    <th>Price</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php while ($row = mysqli_fetch_assoc($packages_res)): ?>
                                    <tr>
                                        <td style="font-weight:700; color:var(--muted);">#<?= $row['pa_id'] ?></td>
                                        <td style="font-weight:700; color:var(--dark);"><?= htmlspecialchars($row['pa_name']) ?></td>
                                        <td>
                                            <span class="za-badge blue">
                                                <i class="fa fa-map-marker"></i> <?= htmlspecialchars($row['s_name'] ?? 'Unknown State') ?>
                                            </span>
                                        </td>
                                        <td style="max-width:280px; font-weight:600; color:var(--text); line-height: 1.4;">
                                            <!-- Dynamically translates IDs to Hotel Names list -->
                                            <?= getHotelNames($conn, $row['h_id']) ?>
                                        </td>
                                        <td style="font-weight:700; color:var(--green);">₹<?= number_format($row['price'], 2) ?></td>
                                        <td>
                                            <div class="action-links">
                                                <a href="package_manage.php?edit=<?= $row['pa_id'] ?>" class="edit-link">
                                                    <i class="fa fa-pencil"></i> Edit
                                                </a>
                                                <a href="package_manage.php?delete=<?= $row['pa_id'] ?>" class="delete-link" onclick="return confirm('Delete this special package profile?')">
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
                            <i class="fa fa-suitcase"></i>
                            <p>No special packages created yet.</p>
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

<!-- Raw Hotels list array for dependent select dropdown filter -->
<script>
const allHotels = [
    <?php 
    if (mysqli_num_rows($hotels_list) > 0) {
        mysqli_data_seek($hotels_list, 0); // Reset pointer
        while ($ht = mysqli_fetch_assoc($hotels_list)) {
            echo "{ h_id: " . $ht['h_id'] . ", s_id: " . $ht['s_id'] . ", h_name: '" . addslashes($ht['h_name']) . "' },";
        }
    }
    ?>
];
const editHotelIds = <?= json_encode(array_map('intval', $edit_h_ids)) ?>;
</script>

<script src="https://cdn.jsdelivr.net/npm/jquery@2.2.4/dist/jquery.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.1.3/dist/js/bootstrap.min.js"></script>
<script>
// State-Hotel Filter Logic
const stateSelect = document.getElementById('stateSelect');
const hotelSelect = document.getElementById('hotelSelect');

function filterHotels() {
    const selectedStateId = parseInt(stateSelect.value);
    
    // Clear Hotel multi-select dropdown
    hotelSelect.innerHTML = '';
    
    if (!selectedStateId) return;

    // Filter hotels belonging to the selected State
    const filtered = allHotels.filter(h => h.s_id === selectedStateId);
    
    if (filtered.length === 0) {
        const option = document.createElement('option');
        option.disabled = true;
        option.textContent = '-- No hotels available in this State --';
        hotelSelect.appendChild(option);
        return;
    }

    // Populate filtered hotels
    filtered.forEach(hotel => {
        const option = document.createElement('option');
        option.value = hotel.h_id;
        option.textContent = hotel.h_name;
        
        // Auto-select in edit mode
        if (editHotelIds.includes(hotel.h_id)) {
            option.selected = true;
        }
        hotelSelect.appendChild(option);
    });
}

// Trigger state filter logic on change
stateSelect.addEventListener('change', filterHotels);

// Initialize on page load (essential for edit mode)
document.addEventListener('DOMContentLoaded', () => {
    if (stateSelect.value) {
        filterHotels();
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