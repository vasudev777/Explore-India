<?php
include('db.php');
include('check.php');
$msg = '';
$msg_type = '';

$edit_id = 0;
$edit_name = '';
$edit_code = '';
$edit_airport = '';
$edit_lat = '';
$edit_lng = '';

// ===== ADD TRANSPORT CITY =====
if (isset($_POST['add_t_city'])) {
    $city_name    = mysqli_real_escape_string($conn, trim($_POST['city_name']));
    $city_code    = mysqli_real_escape_string($conn, strtoupper(trim($_POST['city_code'])));
    $airport_code = mysqli_real_escape_string($conn, strtoupper(trim($_POST['airport_code'])));
    $lat          = mysqli_real_escape_string($conn, trim($_POST['lat']));
    $lng          = mysqli_real_escape_string($conn, trim($_POST['lng']));

    if (!empty($city_name) && !empty($city_code)) {
        // Duplication Check
        $check = mysqli_query($conn, "SELECT * FROM transport_cities WHERE city_name='$city_name' OR city_code='$city_code'");
        if (mysqli_num_rows($check) > 0) {
            $msg = "City Name or City Code already exists!";
            $msg_type = "error";
        } else {
            $sql = "INSERT INTO transport_cities (city_name, city_code, airport_code, lat, lng) 
                    VALUES ('$city_name', '$city_code', '$airport_code', '$lat', '$lng')";
            if (mysqli_query($conn, $sql)) {
                $msg = "Transport city added successfully!";
                $msg_type = "success";
            } else {
                $msg = "Error adding transport city.";
                $msg_type = "error";
            }
        }
    } else {
        $msg = "Please enter City Name and City Code.";
        $msg_type = "error";
    }
}

// ===== UPDATE TRANSPORT CITY =====
if (isset($_POST['update_t_city'])) {
    $id           = intval($_POST['edit_id']);
    $city_name    = mysqli_real_escape_string($conn, trim($_POST['city_name']));
    $city_code    = mysqli_real_escape_string($conn, strtoupper(trim($_POST['city_code'])));
    $airport_code = mysqli_real_escape_string($conn, strtoupper(trim($_POST['airport_code'])));
    $lat          = mysqli_real_escape_string($conn, trim($_POST['lat']));
    $lng          = mysqli_real_escape_string($conn, trim($_POST['lng']));

    if (!empty($city_name) && !empty($city_code)) {
        $sql = "UPDATE transport_cities SET 
                city_name='$city_name', city_code='$city_code', airport_code='$airport_code', lat='$lat', lng='$lng' 
                WHERE city_id='$id'";
        if (mysqli_query($conn, $sql)) {
            $msg = "Transport city updated successfully!";
            $msg_type = "success";
        } else {
            $msg = "Error updating transport city.";
            $msg_type = "error";
        }
    } else {
        $msg = "Please enter City Name and City Code.";
        $msg_type = "error";
    }
}

// ===== LOAD EDIT DATA =====
if (isset($_GET['edit'])) {
    $edit_id = intval($_GET['edit']);
    $res = mysqli_query($conn, "SELECT * FROM transport_cities WHERE city_id='$edit_id'");
    if ($row = mysqli_fetch_assoc($res)) {
        $edit_name    = $row['city_name'];
        $edit_code    = $row['city_code'];
        $edit_airport = $row['airport_code'];
        $edit_lat     = $row['lat'];
        $edit_lng     = $row['lng'];
    }
}

// ===== DELETE TRANSPORT CITY =====
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    if (mysqli_query($conn, "DELETE FROM transport_cities WHERE city_id='$id'")) {
        $msg = "Transport city deleted successfully!";
        $msg_type = "success";
    } else {
        $msg = "Error deleting transport city.";
        $msg_type = "error";
    }
}

// Fetch all transport cities
$t_cities = mysqli_query($conn, "SELECT * FROM transport_cities ORDER BY city_id DESC");

$page_title = "Transport Cities"; // Sets Topbar Title dynamically
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Transport Cities – Explore India Admin</title>
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
        .split-grid { display: grid; grid-template-columns: 380px 1fr; gap: 24px; align-items: start; }

        .za-card { background: #fff; border: 1.5px solid var(--border); border-radius: 20px; overflow: hidden; box-shadow: 0 4px 20px rgba(0,0,0,0.01); }
        .za-card-header { padding: 20px 24px; border-bottom: 1px solid var(--border); display: flex; align-items: center; justify-content: space-between; }
        .za-card-title { font-family: 'Montserrat', sans-serif; font-size: 15px; font-weight: 800; color: var(--dark); }
        .za-card-body { padding: 24px; }

        /* FORM CONTROLS */
        .form-group { margin-bottom: 16px; }
        .form-group label { font-size: 11px; font-weight: 800; text-transform: uppercase; color: var(--muted); display: block; margin-bottom: 6px; letter-spacing: 0.5px; }
        .form-control { border: 1.5px solid var(--border); border-radius: 12px; padding: 10px 14px; font-size: 13.5px; color: var(--dark); background: #fff; outline: none; transition: border-color 0.2s; width: 100%; font-family: 'Open Sans', sans-serif; }
        .form-control:focus { border-color: var(--orange); }

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
            <h1>Transport Cities</h1>
            <p>Manage cities used for booking flights, trains, and cabs (airport codes, city codes, and coordinates).</p>
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
                    <div class="za-card-title"><?= $edit_id > 0 ? "✏️ Edit Transport City" : "➕ Add Transport City" ?></div>
                </div>
                <div class="za-card-body">
                    <form method="POST">
                        <?php if ($edit_id > 0): ?>
                            <input type="hidden" name="edit_id" value="<?= $edit_id ?>">
                        <?php endif; ?>

                        <!-- City Name -->
                        <div class="form-group">
                            <label>City Name</label>
                            <input type="text" name="city_name" class="form-control" placeholder="e.g. Mumbai" value="<?= htmlspecialchars($edit_name) ?>" required>
                        </div>

                        <!-- City Code & Airport Code (Grid Layout) -->
                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 12px;">
                            <div class="form-group">
                                <label>City Code (Train)</label>
                                <input type="text" name="city_code" class="form-control" placeholder="e.g. MUM" maxlength="10" value="<?= htmlspecialchars($edit_code) ?>" required>
                            </div>
                            <div class="form-group">
                                <label>Airport Code</label>
                                <input type="text" name="airport_code" class="form-control" placeholder="e.g. BOM" maxlength="10" value="<?= htmlspecialchars($edit_airport) ?>">
                            </div>
                        </div>

                        <!-- Lat & Lng (Grid Layout) -->
                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 12px;">
                            <div class="form-group">
                                <label>Latitude</label>
                                <input type="text" name="lat" class="form-control" placeholder="e.g. 19.076090" value="<?= htmlspecialchars($edit_lat) ?>">
                            </div>
                            <div class="form-group">
                                <label>Longitude</label>
                                <input type="text" name="lng" class="form-control" placeholder="e.g. 72.877230" value="<?= htmlspecialchars($edit_lng) ?>">
                            </div>
                        </div>

                        <?php if ($edit_id > 0): ?>
                            <button type="submit" name="update_t_city" class="za-btn za-btn-orange">Update City</button>
                            <a href="transport_city.php" class="za-btn za-btn-outline" style="margin-top: 10px;">Cancel</a>
                        <?php else: ?>
                            <button type="submit" name="add_t_city" class="za-btn za-btn-orange">Add Transport City</button>
                        <?php endif; ?>
                    </form>
                </div>
            </div>

            <!-- Right: Cities List Table -->
            <div class="za-card">
                <div class="za-card-header">
                    <div class="za-card-title">All Transport Cities (<?= mysqli_num_rows($t_cities) ?>)</div>
                </div>
                <div style="overflow-x:auto;">
                    <?php if (mysqli_num_rows($t_cities) > 0): ?>
                        <table class="za-table">
                            <thead>
                                <tr>
                                    <th>#ID</th>
                                    <th>City Name</th>
                                    <th>City Code</th>
                                    <th>Airport Code</th>
                                    <th>Coordinates (Lat/Lng)</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php while ($row = mysqli_fetch_assoc($t_cities)): ?>
                                    <tr>
                                        <td style="font-weight:700; color:var(--muted);">#<?= $row['city_id'] ?></td>
                                        <td style="font-weight:700; color:var(--dark);"><?= htmlspecialchars($row['city_name']) ?></td>
                                        <td><span class="za-badge orange"><?= htmlspecialchars($row['city_code']) ?></span></td>
                                        <td><span class="za-badge blue"><?= htmlspecialchars($row['airport_code'] ? $row['airport_code'] : 'N/A') ?></span></td>
                                        <td style="font-size:12.5px; color:var(--text); font-family: monospace;">
                                            <?= $row['lat'] ? htmlspecialchars($row['lat']) : '0.0000' ?>,<br>
                                            <?= $row['lng'] ? htmlspecialchars($row['lng']) : '0.0000' ?>
                                        </td>
                                        <td>
                                            <div class="action-links">
                                                <a href="transport_city.php?edit=<?= $row['city_id'] ?>" class="edit-link">
                                                    <i class="fa fa-pencil"></i> Edit
                                                </a>
                                                <a href="transport_city.php?delete=<?= $row['city_id'] ?>" class="delete-link" onclick="return confirm('Are you sure you want to delete this transport city?')">
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
                            <i class="fa fa-plane"></i>
                            <p>No transport cities added yet.</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

        </div>

    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/jquery@2.2.4/dist/jquery.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.1.3/dist/js/bootstrap.min.js"></script>
<script>
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