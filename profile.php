<?php
include('db.php');
session_start();
if (!isset($_SESSION['uemail'])) { header('Location: login.php'); exit; }

$cust_id = intval($_SESSION['ucust_id']);
$sql = "SELECT cd.*, s.s_name, c.c_name FROM customer_details cd
        LEFT JOIN state s ON s.s_id = cd.cust_state
        LEFT JOIN city c ON c.c_id = cd.cust_city
        WHERE cd.cust_id = $cust_id";
$result = mysqli_query($conn, $sql);
$row = mysqli_fetch_assoc($result);

// Booking counts
$cust_count    = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as c FROM customize_booking WHERE cust_id=$cust_id"))['c'] ?? 0;
$predef_count  = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as c FROM predefine_booking WHERE cust_id=$cust_id"))['c'] ?? 0;
$trans_count   = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as c FROM transport_bookings WHERE cust_id=$cust_id"))['c'] ?? 0;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>My Profile – Explore India</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="css/bootstrap.css" rel='stylesheet' type='text/css'/>
    <link href="css/style.css" rel='stylesheet' type='text/css'/>
    <link href="css/font-awesome.min.css" rel="stylesheet">
    <link href="//fonts.googleapis.com/css?family=Montserrat:400,500,600,700,800" rel="stylesheet">
    <link href="//fonts.googleapis.com/css?family=Open+Sans:300,400,600" rel="stylesheet">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
    <script src="js/disable.js"></script>
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
        body { background: #fff; font-family: 'Open Sans', sans-serif; color: #1a1a1a; overflow-x: hidden; }

        /* DARK HERO */
        .page-hero {
            background: linear-gradient(160deg, #0a0a0a 0%, #12082a 50%, #0a0a0a 100%);
            padding: 100px 20px 60px; text-align: center;
        }
        .avatar {
            width: 80px; height: 80px; border-radius: 50%;
            background: linear-gradient(135deg, #f5a623, #e8920e);
            display: flex; align-items: center; justify-content: center;
            font-size: 32px; font-weight: 800; color: #000;
            margin: 0 auto 16px;
            font-family: 'Montserrat', sans-serif;
            border: 3px solid rgba(245,166,35,0.3);
        }
        .page-hero h1 { font-family: 'Montserrat', sans-serif; font-size: clamp(22px, 4vw, 36px); font-weight: 800; color: #fff; margin-bottom: 6px; text-transform: none !important; }
        .page-hero p { font-size: 14px; color: rgba(255,255,255,0.4); margin-bottom: 24px; }

        /* Stats bar */
        .stats-bar {
            display: inline-flex; gap: 0;
            background: rgba(255,255,255,0.06); border: 1px solid rgba(255,255,255,0.1);
            border-radius: 14px; overflow: hidden;
        }
        .stat-item { padding: 12px 24px; text-align: center; border-right: 1px solid rgba(255,255,255,0.08); }
        .stat-item:last-child { border: none; }
        .stat-num { font-family: 'Montserrat', sans-serif; font-size: 18px; font-weight: 800; }
        .stat-label { font-size: 10px; color: rgba(255,255,255,0.3); text-transform: uppercase; letter-spacing: 1px; margin-top: 3px; }

        /* WHITE CONTENT */
        .content-section { background: #fff; padding: 50px 20px 80px; }
        .content-inner { max-width: 860px; margin: 0 auto; display: grid; grid-template-columns: 1fr 1.2fr; gap: 28px; align-items: start; }
        @media (max-width: 768px) { .content-inner { grid-template-columns: 1fr; } }

        /* Cards */
        .info-card, .edit-card {
            background: #f8f9fa; border: 1.5px solid #e9ecef;
            border-radius: 20px; overflow: hidden;
        }
        .card-header {
            padding: 18px 22px 14px; border-bottom: 1px solid #e9ecef;
            background: #fff; display: flex; align-items: center; gap: 10px;
        }
        .ch-icon { width: 36px; height: 36px; border-radius: 10px; display: flex; align-items: center; justify-content: center; font-size: 15px; }
        .card-header h3 { font-family: 'Montserrat', sans-serif; font-size: 15px; font-weight: 700; color: #1a1a1a; margin: 0; text-transform: none !important; }

        /* Info rows */
        .card-body { padding: 16px 22px; }
        .info-row { display: flex; align-items: flex-start; gap: 12px; padding: 10px 0; border-bottom: 1px solid #f0f0f0; }
        .info-row:last-child { border: none; }
        .info-icon { width: 32px; height: 32px; border-radius: 8px; background: #f0f0f0; display: flex; align-items: center; justify-content: center; font-size: 13px; color: #666; flex-shrink: 0; }
        .info-label { font-size: 10px; font-weight: 700; text-transform: uppercase; letter-spacing: 1px; color: #aaa; margin-bottom: 3px; }
        .info-value { font-size: 14px; font-weight: 600; color: #1a1a1a; }

        /* Quick links */
        .quick-links { display: flex; flex-direction: column; gap: 8px; padding: 16px 22px; }
        .quick-link {
            display: flex; align-items: center; gap: 10px;
            padding: 12px 14px; border-radius: 10px;
            background: #fff; border: 1.5px solid #e9ecef;
            text-decoration: none; color: #1a1a1a;
            font-size: 13px; font-weight: 600;
            transition: all 0.2s;
        }
        .quick-link:hover { border-color: #ccc; box-shadow: 0 2px 8px rgba(0,0,0,0.06); text-decoration: none; color: #1a1a1a; }
        .quick-link .ql-icon { width: 32px; height: 32px; border-radius: 8px; display: flex; align-items: center; justify-content: center; font-size: 14px; flex-shrink: 0; }
        .quick-link .ql-arrow { margin-left: auto; color: #ccc; }

        /* DARK STATS */
        .stats-section { background: #0a0a0a; padding: 50px 20px; }
        .stats-row { display: flex; justify-content: center; gap: 40px; flex-wrap: wrap; }
        .s-stat { text-align: center; }
        .s-num { font-family: 'Montserrat', sans-serif; font-size: 28px; font-weight: 800; }
        .s-label { font-size: 12px; color: rgba(255,255,255,0.35); margin-top: 4px; }

        /* Success/Error msg */
        .alert-msg { padding: 10px 16px; border-radius: 10px; font-size: 13px; margin-bottom: 16px; display: flex; align-items: center; gap: 8px; }
        .alert-success { background: #f0fff8; border: 1px solid #2ecc9a; color: #2ecc9a; }
        .alert-error   { background: #fff0f0; border: 1px solid #ff5050; color: #ff5050; }
    </style>
</head>
<body oncontextmenu="return false;">
<?php include('header.php'); ?>

<!-- DARK HERO -->
<div class="page-hero">
    <div class="avatar"><?= strtoupper(substr($row['cust_fname'], 0, 1)) ?></div>
    <h1><?= htmlspecialchars($row['cust_fname'] . ' ' . $row['cust_lname']) ?></h1>
    <p><?= htmlspecialchars($row['cust_email']) ?></p>
    <div class="stats-bar">
        <div class="stat-item"><div class="stat-num" style="color:#f5a623;"><?= $cust_count ?></div><div class="stat-label">Customize</div></div>
        <div class="stat-item"><div class="stat-num" style="color:#5ecfa8;"><?= $predef_count ?></div><div class="stat-label">Special</div></div>
        <div class="stat-item"><div class="stat-num" style="color:#5ea0ff;"><?= $trans_count ?></div><div class="stat-label">Transport</div></div>
    </div>
</div>

<!-- WHITE CONTENT -->
<div class="content-section">
    <div class="content-inner">

        <!-- LEFT: Info -->
        <div class="info-card">
            <div class="card-header">
                <div class="ch-icon" style="background:rgba(245,166,35,0.1);border:1px solid rgba(245,166,35,0.2);color:#d48a1a;"><span class="fa fa-user"></span></div>
                <h3>Profile Details</h3>
            </div>
            <div class="card-body">
                <div class="info-row">
                    <div class="info-icon"><span class="fa fa-user"></span></div>
                    <div><div class="info-label">Full Name</div><div class="info-value"><?= htmlspecialchars($row['cust_fname'] . ' ' . $row['cust_lname']) ?></div></div>
                </div>
                <div class="info-row">
                    <div class="info-icon"><span class="fa fa-envelope"></span></div>
                    <div><div class="info-label">Email</div><div class="info-value"><?= htmlspecialchars($row['cust_email']) ?></div></div>
                </div>
                <div class="info-row">
                    <div class="info-icon"><span class="fa fa-phone"></span></div>
                    <div><div class="info-label">Mobile</div><div class="info-value"><?= htmlspecialchars($row['cust_mobile']) ?></div></div>
                </div>
                <div class="info-row">
                    <div class="info-icon"><span class="fa fa-venus-mars"></span></div>
                    <div><div class="info-label">Gender</div><div class="info-value"><?= htmlspecialchars($row['cust_gender']) ?></div></div>
                </div>
                <div class="info-row">
                    <div class="info-icon"><span class="fa fa-birthday-cake"></span></div>
                    <div><div class="info-label">Birthday</div><div class="info-value"><?= $row['cust_birthdate'] ? date('d M Y', strtotime($row['cust_birthdate'])) : 'N/A' ?></div></div>
                </div>
                <div class="info-row">
                    <div class="info-icon"><span class="fa fa-map-marker"></span></div>
                    <div><div class="info-label">Location</div><div class="info-value"><?= htmlspecialchars(($row['c_name'] ?? '') . ', ' . ($row['s_name'] ?? '')) ?></div></div>
                </div>
                <div class="info-row">
                    <div class="info-icon"><span class="fa fa-home"></span></div>
                    <div><div class="info-label">Address</div><div class="info-value"><?= htmlspecialchars($row['cust_address'] ?? 'N/A') ?></div></div>
                </div>
            </div>

            <!-- Quick Links -->
            <div style="padding: 0 22px 6px; font-size:10px; font-weight:700; letter-spacing:2px; text-transform:uppercase; color:#aaa;">Quick Links</div>
            <div class="quick-links">
                <a href="cust_history.php" class="quick-link">
                    <div class="ql-icon" style="background:rgba(94,160,255,0.1);color:#5ea0ff;"><span class="fa fa-history"></span></div>
                    My Bookings
                    <span class="ql-arrow fa fa-chevron-right"></span>
                </a>
                <a href="feedback.php" class="quick-link">
                    <div class="ql-icon" style="background:rgba(94,207,168,0.1);color:#2ecc9a;"><span class="fa fa-comment"></span></div>
                    Give Feedback
                    <span class="ql-arrow fa fa-chevron-right"></span>
                </a>
                <a href="logout.php" class="quick-link">
                    <div class="ql-icon" style="background:rgba(255,80,80,0.1);color:#ff5050;"><span class="fa fa-sign-out"></span></div>
                    Logout
                    <span class="ql-arrow fa fa-chevron-right"></span>
                </a>
            </div>
        </div>

        <!-- RIGHT: Edit Form -->
        <div class="edit-card">
            <div class="card-header">
                <div class="ch-icon" style="background:rgba(94,160,255,0.1);border:1px solid rgba(94,160,255,0.2);color:#5ea0ff;"><span class="fa fa-pencil"></span></div>
                <h3>Update Profile</h3>
            </div>
            <div class="card-body">
                <?php if (isset($_GET['success'])): ?>
                <div class="alert-msg alert-success"><span class="fa fa-check-circle"></span> Profile updated successfully!</div>
                <?php elseif (isset($_GET['error'])): ?>
                <div class="alert-msg alert-error"><span class="fa fa-times-circle"></span> Something went wrong. Please try again.</div>
                <?php endif; ?>

                <form action="update_profile.php" method="POST">
                    <input type="hidden" name="cust_id" value="<?= $cust_id ?>">

                    <div style="display:grid; grid-template-columns:1fr 1fr; gap:14px; margin-bottom:14px;">
                        <div>
                            <label style="font-size:10px;font-weight:700;letter-spacing:1px;text-transform:uppercase;color:#aaa;display:block;margin-bottom:6px;">First Name</label>
                            <input type="text" name="fname" value="<?= htmlspecialchars($row['cust_fname']) ?>" class="form-control" style="border-radius:10px;border:1.5px solid #e9ecef;padding:10px 14px;font-size:14px;">
                        </div>
                        <div>
                            <label style="font-size:10px;font-weight:700;letter-spacing:1px;text-transform:uppercase;color:#aaa;display:block;margin-bottom:6px;">Last Name</label>
                            <input type="text" name="lname" value="<?= htmlspecialchars($row['cust_lname']) ?>" class="form-control" style="border-radius:10px;border:1.5px solid #e9ecef;padding:10px 14px;font-size:14px;">
                        </div>
                    </div>

                    <div style="margin-bottom:14px;">
                        <label style="font-size:10px;font-weight:700;letter-spacing:1px;text-transform:uppercase;color:#aaa;display:block;margin-bottom:6px;">Mobile</label>
                        <input type="text" name="mobile" value="<?= htmlspecialchars($row['cust_mobile']) ?>" class="form-control" style="border-radius:10px;border:1.5px solid #e9ecef;padding:10px 14px;font-size:14px;">
                    </div>

                    <div style="margin-bottom:14px;">
                        <label style="font-size:10px;font-weight:700;letter-spacing:1px;text-transform:uppercase;color:#aaa;display:block;margin-bottom:6px;">Gender</label>
                        <select name="gender" class="form-control" style="border-radius:10px;border:1.5px solid #e9ecef;padding:10px 14px;font-size:14px;appearance:none;">
                            <option value="Male"   <?= $row['cust_gender']==='Male'   ? 'selected':'' ?>>Male</option>
                            <option value="Female" <?= $row['cust_gender']==='Female' ? 'selected':'' ?>>Female</option>
                            <option value="Other"  <?= $row['cust_gender']==='Other'  ? 'selected':'' ?>>Other</option>
                        </select>
                    </div>

                    <div style="margin-bottom:14px;">
                        <label style="font-size:10px;font-weight:700;letter-spacing:1px;text-transform:uppercase;color:#aaa;display:block;margin-bottom:6px;">Birthday</label>
                        <input type="date" name="birthdate" value="<?= $row['cust_birthdate'] ?>" class="form-control" style="border-radius:10px;border:1.5px solid #e9ecef;padding:10px 14px;font-size:14px;">
                    </div>

                    <div style="display:grid; grid-template-columns:1fr 1fr; gap:14px; margin-bottom:14px;">
                        <div>
                            <label style="font-size:10px;font-weight:700;letter-spacing:1px;text-transform:uppercase;color:#aaa;display:block;margin-bottom:6px;">State</label>
                            <select name="state" id="stateSelect" onchange="getCities(this.value)" class="form-control" style="border-radius:10px;border:1.5px solid #e9ecef;padding:10px 14px;font-size:14px;height:42px;background:#fff;">
                                <option value="">Select State</option>
                                <?php
                                $states = mysqli_query($conn, "SELECT * FROM state ORDER BY s_name");
                                foreach ($states as $s) {
                                    $sel = ($row['cust_state'] == $s['s_id']) ? 'selected' : '';
                                    echo "<option value='{$s['s_id']}' $sel>{$s['s_name']}</option>";
                                }
                                ?>
                            </select>
                        </div>
                        <div>
                            <label style="font-size:10px;font-weight:700;letter-spacing:1px;text-transform:uppercase;color:#aaa;display:block;margin-bottom:6px;">City</label>
                            <select name="city" id="citySelect" class="form-control" style="border-radius:10px;border:1.5px solid #e9ecef;padding:10px 14px;font-size:14px;height:42px;background:#fff;">
                                <option value="">Select City</option>
                                <?php
                                if ($row['cust_state'] > 0) {
                                    $cities = mysqli_query($conn, "SELECT * FROM city WHERE s_id=" . intval($row['cust_state']) . " ORDER BY c_name");
                                    foreach ($cities as $c) {
                                        $sel = ($row['cust_city'] == $c['c_id']) ? 'selected' : '';
                                        echo "<option value='{$c['c_id']}' $sel>{$c['c_name']}</option>";
                                    }
                                }
                                ?>
                            </select>
                        </div>
                    </div>

                    <div style="margin-bottom:14px;">
                        <label style="font-size:10px;font-weight:700;letter-spacing:1px;text-transform:uppercase;color:#aaa;display:block;margin-bottom:6px;">Address</label>
                        <textarea name="address" rows="2" class="form-control" style="border-radius:10px;border:1.5px solid #e9ecef;padding:10px 14px;font-size:14px;resize:none;"><?= htmlspecialchars($row['cust_address']) ?></textarea>
                    </div>

                    <hr style="border-color:#e9ecef;margin:18px 0;">

                    <div style="margin-bottom:14px;">
                        <label style="font-size:10px;font-weight:700;letter-spacing:1px;text-transform:uppercase;color:#aaa;display:block;margin-bottom:6px;">New Password <span style="color:#ccc;font-weight:400;">(leave blank to keep current)</span></label>
                        <input type="password" name="new_password" placeholder="Enter new password" class="form-control" style="border-radius:10px;border:1.5px solid #e9ecef;padding:10px 14px;font-size:14px;">
                    </div>

                    <button type="submit" style="width:100%;background:linear-gradient(135deg,#f5a623,#d48a1a);border:none;border-radius:12px;color:#fff;font-size:14px;font-weight:700;font-family:'Montserrat',sans-serif;padding:13px;cursor:pointer;transition:transform 0.2s;text-transform:none;">
                        <span class="fa fa-save"></span> &nbsp; Save Changes
                    </button>
                </form>
            </div>
        </div>

    </div>
</div>

<!-- DARK STATS -->
<div class="stats-section">
    <div class="stats-row">
        <div class="s-stat"><div class="s-num" style="color:#f5a623;"><?= $cust_count ?></div><div class="s-label">Customize</div></div>
        <div class="s-stat"><div class="s-num" style="color:#5ecfa8;"><?= $predef_count ?></div><div class="s-label">Special</div></div>
        <div class="s-stat"><div class="s-num" style="color:#5ea0ff;"><?= $trans_count ?></div><div class="s-label">Transport</div></div>
    </div>
</div>

<?php include('footer.php'); ?>
<script>
function getCities(stateId) {
    if (!stateId) {
        $('#citySelect').html('<option value="">Select City</option>');
        return;
    }
    $.ajax({
        type: 'POST',
        url:  'city.php',
        data: 's_id=' + stateId,
        success: function(html) {
            $('#citySelect').html(html);
        }
    });
}
</script>
</body>
</html>