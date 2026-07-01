<?php
include('db.php');
session_start();

// Authentication Gate
if (!isset($_SESSION['guide_id'])) {
    header('Location: guide_login.php');
    exit;
}

$guide_id = $_SESSION['guide_id'];

$profile_success = '';
$profile_error = '';

// Handle Profile Updates
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'update_profile') {
    $name = mysqli_real_escape_string($conn, trim($_POST['name']));
    $mobile = mysqli_real_escape_string($conn, trim($_POST['mobile']));
    $language = mysqli_real_escape_string($conn, trim($_POST['language']));
    $password = trim($_POST['password']);
    
    if (!empty($password)) {
        // Plaintext comparison/storage as per original schema
        $update_query = "UPDATE local_guide SET localg_name='$name', localg_mobile='$mobile', localg_language='$language', localg_password='$password' WHERE localg_id='$guide_id'";
    } else {
        $update_query = "UPDATE local_guide SET localg_name='$name', localg_mobile='$mobile', localg_language='$language' WHERE localg_id='$guide_id'";
    }
    
    if (mysqli_query($conn, $update_query)) {
        $_SESSION['guide_name'] = $name;
        $profile_success = "Your profile has been successfully updated!";
    } else {
        $profile_error = "Error updating profile: " . mysqli_error($conn);
    }
}

// Fetch guide profile details
$guide_res = mysqli_query($conn, "SELECT lg.*, s.s_name FROM local_guide lg LEFT JOIN state s ON lg.s_id = s.s_id WHERE lg.localg_id='$guide_id'");
$guide_data = mysqli_fetch_assoc($guide_res);

// Fetch assigned customized bookings from local_guide_request
$query = "SELECT lgr.*, cd.cust_fname, cd.cust_lname, cd.cust_email, cd.cust_mobile, s.s_name as state_name
          FROM local_guide_request lgr
          LEFT JOIN customer_details cd ON lgr.cust_id = cd.cust_id
          LEFT JOIN state s ON lgr.s_id = s.s_id
          WHERE lgr.localg_id = '$guide_id'
          ORDER BY lgr.lgr_id DESC";
$bookings_res = mysqli_query($conn, $query);
$total_bookings = $bookings_res ? mysqli_num_rows($bookings_res) : 0;

// Helper function to decode hotel names
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
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>Guide Dashboard – Explore India</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="css/bootstrap.css" rel='stylesheet' type='text/css'/>
    <link href="css/font-awesome.min.css" rel="stylesheet">
    <link href="//fonts.googleapis.com/css?family=Montserrat:400,500,600,700,800,900" rel="stylesheet">
    <style>
        body {
            background: #F8FAFC;
            font-family: 'Open Sans', sans-serif;
            color: #334155;
            min-height: 100vh;
        }

        /* Navbar Styling */
        .guide-navbar {
            background: #ffffff;
            border-bottom: 1px solid #E2E8F0;
            padding: 15px 0;
            position: sticky;
            top: 0;
            z-index: 100;
            box-shadow: 0 4px 20px rgba(15, 23, 42, 0.02);
        }
        .navbar-brand-custom {
            font-family: 'Montserrat', sans-serif;
            font-weight: 800;
            font-size: 20px;
            color: #0F172A;
            text-decoration: none;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        .navbar-brand-custom span {
            color: #FF782C;
        }
        .logout-btn {
            background: #FEF2F2;
            color: #EF4444;
            border: 1px solid #FEE2E2;
            padding: 6px 16px;
            border-radius: 8px;
            font-size: 13.5px;
            font-weight: 600;
            text-decoration: none;
            transition: all 0.2s;
        }
        .logout-btn:hover {
            background: #EF4444;
            color: #fff;
            text-decoration: none;
        }

        /* Profile Banner */
        .profile-banner {
            background: #ffffff;
            border: 1px solid #E2E8F0;
            border-radius: 20px;
            padding: 24px;
            margin-top: 30px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 15px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.01);
        }
        .guide-avatar {
            width: 55px;
            height: 55px;
            border-radius: 50%;
            background: linear-gradient(135deg, #FF782C, #F39C12);
            color: #fff;
            font-size: 22px;
            font-weight: 800;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Montserrat', sans-serif;
        }
        .guide-welcome h3 {
            font-family: 'Montserrat', sans-serif;
            font-size: 19px;
            font-weight: 800;
            color: #0F172A;
            margin: 0;
        }
        .guide-welcome p {
            font-size: 13px;
            color: #64748B;
            margin: 4px 0 0;
        }
        .badge-verified {
            font-size: 11px;
            font-weight: 700;
            background: #ECFDF5;
            border: 1px solid #D1FAE5;
            color: #065F46;
            padding: 4px 12px;
            border-radius: 12px;
            display: inline-block;
        }

        /* Bookings Section */
        .section-title {
            font-family: 'Montserrat', sans-serif;
            font-size: 17px;
            font-weight: 800;
            color: #0F172A;
            margin: 30px 0 15px;
        }
        .dashboard-card {
            background: #ffffff;
            border: 1px solid #E2E8F0;
            border-radius: 20px;
            padding: 24px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.01);
        }

        /* Custom Table Styling */
        .table-responsive {
            margin: 0;
        }
        .custom-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 14px;
        }
        .custom-table th {
            color: #475569;
            font-weight: 800;
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            padding: 12px 16px;
            border-bottom: 1.5px solid #E2E8F0;
        }
        .custom-table td {
            padding: 16px;
            border-bottom: 1px solid #F1F5F9;
            vertical-align: middle;
            color: #334155;
        }
        .custom-table tr:hover td {
            background: #F8FAFC;
        }
        .ref-cell {
            font-family: monospace;
            font-size: 14.5px;
            font-weight: 700;
            color: #FF782C;
        }
        .cust-name {
            font-weight: 700;
            color: #0F172A;
            margin-bottom: 2px;
        }
        .cust-email {
            font-size: 11.5px;
            color: #64748B;
        }
        .badge-status {
            font-size: 11px;
            font-weight: 700;
            padding: 3px 10px;
            border-radius: 10px;
            display: inline-block;
        }
        .badge-status.active-trip {
            background: #ECFDF5;
            border: 1px solid #D1FAE5;
            color: #065F46;
        }
        .details-btn {
            background: #ffffff;
            border: 1px solid #E2E8F0;
            color: #475569;
            padding: 6px 14px;
            border-radius: 8px;
            font-size: 12.5px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.2s;
        }
        .details-btn:hover {
            border-color: #FF782C;
            color: #FF782C;
            background: rgba(255, 120, 44, 0.03);
        }

        /* Empty State */
        .empty-state {
            text-align: center;
            padding: 50px 20px;
            color: #64748B;
        }
        .empty-state i {
            font-size: 36px;
            margin-bottom: 12px;
            opacity: 0.3;
            display: block;
        }

        /* Dynamic Details Modal */
        .modal-overlay {
            position: fixed;
            top: 0; left: 0; right: 0; bottom: 0;
            background: rgba(15, 23, 42, 0.3);
            backdrop-filter: blur(8px);
            z-index: 1000;
            display: none;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        .modal-box {
            background: #ffffff;
            border: 1px solid #E2E8F0;
            border-radius: 20px;
            width: 100%;
            max-width: 480px;
            box-shadow: 0 20px 50px rgba(15, 23, 42, 0.08);
            animation: modalFade 0.2s ease-out;
        }
        @keyframes modalFade {
            from { transform: translateY(15px); opacity: 0; }
            to { transform: translateY(0); opacity: 1; }
        }
        .modal-header {
            padding: 20px 24px;
            border-bottom: 1px solid #E2E8F0;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .modal-title {
            font-family: 'Montserrat', sans-serif;
            font-weight: 800;
            font-size: 16px;
            color: #0F172A;
        }
        .modal-close {
            background: none;
            border: none;
            color: #64748B;
            font-size: 18px;
            cursor: pointer;
            outline: none;
        }
        .modal-body {
            padding: 24px;
            color: #334155;
        }
        .modal-label {
            font-size: 10px;
            font-weight: 800;
            color: #64748B;
            letter-spacing: 0.5px;
            text-transform: uppercase;
            margin-bottom: 6px;
            display: block;
        }
        .modal-value-box {
            background: #F8FAFC;
            border: 1px solid #E2E8F0;
            border-radius: 12px;
            padding: 12px 16px;
            margin-bottom: 16px;
        }

        /* Profile Modal input form styling */
        .form-group-custom {
            margin-bottom: 16px;
        }
        .form-input-custom {
            width: 100%;
            border: 1.5px solid #E2E8F0;
            border-radius: 10px;
            padding: 10px 14px;
            font-size: 13.5px;
            color: #0F172A;
            background: #FFFFFF;
            outline: none;
            transition: all 0.2s;
        }
        .form-input-custom:focus {
            border-color: #FF782C;
            box-shadow: 0 0 0 3px rgba(255, 120, 44, 0.12);
        }
        .btn-submit {
            background: linear-gradient(135deg, #FF782C, #F39C12);
            color: #fff;
            font-weight: 700;
            border: none;
            border-radius: 10px;
            padding: 10px 20px;
            font-size: 14px;
            cursor: pointer;
            box-shadow: 0 4px 15px rgba(255, 120, 44, 0.2);
            transition: all 0.2s;
        }
        .btn-submit:hover {
            transform: translateY(-1px);
            box-shadow: 0 6px 20px rgba(255, 120, 44, 0.3);
            color: #fff;
        }
    </style>
</head>
<body>

<!-- Navigation Bar -->
<div class="guide-navbar">
    <div class="container d-flex justify-content-between align-items-center">
        <a href="" class="navbar-brand-custom">
            <span class="fa fa-globe"></span> Explore <span>India</span>
        </a>
        <a href="guide_logout.php" class="logout-btn"><i class="fa fa-sign-out"></i> Log Out</a>
    </div>
</div>

<!-- Main Container -->
<div class="container">
    
    <!-- Profile Alerts -->
    <?php if ($profile_success): ?>
        <div class="alert alert-success mt-4" style="border-radius:12px; font-size:14px; background:#ECFDF5; border-color:#D1FAE5; color:#065F46; padding:14px 18px;">
            ✨ <?= $profile_success ?>
        </div>
    <?php endif; ?>
    <?php if ($profile_error): ?>
        <div class="alert alert-danger mt-4" style="border-radius:12px; font-size:14px; background:#FEF2F2; border-color:#FEE2E2; color:#991B1B; padding:14px 18px;">
            ⚠️ <?= $profile_error ?>
        </div>
    <?php endif; ?>

    <!-- Profile Greeting Card -->
    <div class="profile-banner">
        <div class="d-flex align-items-center gap-3" style="gap:15px;">
            <div class="guide-avatar">
                <?= $guide_data['localg_name'] ? substr($guide_data['localg_name'], 0, 1) : 'G' ?>
            </div>
            <div class="guide-welcome">
                <h3>Welcome back, <?= htmlspecialchars($guide_data['localg_name']) ?>!</h3>
                <p>📍 Operating State: <strong><?= htmlspecialchars($guide_data['s_name'] ?? 'Not set') ?></strong> • Language: <strong><?= htmlspecialchars($guide_data['localg_language']) ?></strong></p>
            </div>
        </div>
        <div class="d-flex align-items-center gap-2" style="gap:10px;">
            <button class="details-btn" onclick="openProfileModal()"><i class="fa fa-edit"></i> Edit Profile</button>
            <span class="badge-verified"><i class="fa fa-check-circle"></i> Verified</span>
        </div>
    </div>

    <!-- Assigned Trips Log -->
    <h3 class="section-title"><i class="fa fa-map-signs" style="color:#FF782C; margin-right:8px;"></i> My Assigned Travel Schedules</h3>
    
    <div class="dashboard-card mb-5">
        <div class="table-responsive">
            <?php if ($total_bookings > 0): ?>
                <table class="custom-table">
                    <thead>
                        <tr>
                            <th>Ref Code</th>
                            <th>Traveler Details</th>
                            <th>Destination State</th>
                            <th>Start Date</th>
                            <th>Hotel Details</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($row = mysqli_fetch_assoc($bookings_res)): ?>
                            <?php 
                            $hotel_names = getHotelNames($conn, $row['h_id']); 
                            $ref_code = 'LGR' . str_pad($row['lgr_id'], 4, '0', STR_PAD_LEFT);
                            ?>
                            <tr>
                                <td class="ref-cell"><?= $ref_code ?></td>
                                <td>
                                    <div class="cust-name"><?= htmlspecialchars($row['cust_fname'] . ' ' . $row['cust_lname']) ?></div>
                                    <div class="cust-email"><?= htmlspecialchars($row['cust_email']) ?></div>
                                </td>
                                <td style="font-weight:700; color:#3B82F6;"><?= htmlspecialchars($row['state_name'] ?? 'Multiple States') ?></td>
                                <td style="font-weight:600;"><?= date('d M Y', strtotime($row['date'])) ?></td>
                                <td style="max-width: 220px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">
                                    <?= $hotel_names ?>
                                </td>
                                <td>
                                    <span class="badge-status active-trip">Assigned</span>
                                </td>
                                <td>
                                    <button class="details-btn" 
                                            onclick='openDetailsModal(<?= json_encode($row, JSON_HEX_APOS | JSON_HEX_QUOT) ?>, "<?= addslashes($hotel_names) ?>", "<?= $ref_code ?>")'>
                                        <i class="fa fa-eye"></i> Details
                                    </button>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <div class="empty-state">
                    <i class="fa fa-folder-open-o"></i>
                    <p>No travel request schedules are currently assigned to you.</p>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Details Modal -->
<div class="modal-overlay" id="detailsModal">
    <div class="modal-box">
        <div class="modal-header">
            <span class="modal-title">📋 Trip Schedule Details</span>
            <button class="modal-close" onclick="closeDetailsModal()">✕</button>
        </div>
        <div class="modal-body">
            <label class="modal-label">Request Reference</label>
            <div class="modal-value-box" id="m_ref" style="font-family: monospace; font-size:16px; font-weight:700; color:#FF782C;">LGR0001</div>

            <label class="modal-label">Customer Profile</label>
            <div class="modal-value-box">
                <div style="font-weight:700;" id="m_name">Name</div>
                <div style="font-size:12.5px; color:#64748B; margin-top:2px;" id="m_email">email@example.com</div>
                <div style="font-size:12.5px; color:#64748B;" id="m_phone">📞 Phone</div>
            </div>

            <label class="modal-label">Destination & Start Date</label>
            <div class="modal-value-box">
                📍 State: <strong id="m_state" style="color:#3B82F6;">State Name</strong><br>
                📅 Start Date: <strong id="m_date">10 Jun 2026</strong>
            </div>

            <label class="modal-label">Booked Accommodation</label>
            <div class="modal-value-box" id="m_hotels" style="font-weight:600; font-size:13px; line-height:1.5; color:#475569;">Hotels</div>
        </div>
    </div>
</div>

<!-- Edit Profile Modal -->
<div class="modal-overlay" id="profileModal">
    <div class="modal-box">
        <div class="modal-header">
            <span class="modal-title">👤 Edit My Profile</span>
            <button class="modal-close" onclick="closeProfileModal()">✕</button>
        </div>
        <form method="POST" action="">
            <input type="hidden" name="action" value="update_profile">
            <div class="modal-body" style="padding-top:15px; padding-bottom:15px;">
                <div class="form-group-custom">
                    <label class="modal-label">Full Name</label>
                    <input type="text" name="name" class="form-input-custom" value="<?= htmlspecialchars($guide_data['localg_name']) ?>" required>
                </div>
                <div class="form-group-custom">
                    <label class="modal-label">Mobile Number</label>
                    <input type="tel" name="mobile" class="form-input-custom" value="<?= htmlspecialchars($guide_data['localg_mobile']) ?>" pattern="[0-9]{10}" required>
                </div>
                <div class="form-group-custom">
                    <label class="modal-label">Languages Spoken</label>
                    <input type="text" name="language" class="form-input-custom" value="<?= htmlspecialchars($guide_data['localg_language']) ?>" required>
                </div>
                <div class="form-group-custom">
                    <label class="modal-label">Change Password (Leave blank to keep current)</label>
                    <input type="password" name="password" class="form-input-custom" placeholder="••••••••">
                </div>
            </div>
            <div style="padding:15px 24px; border-top:1px solid #E2E8F0; display:flex; gap:10px; justify-content:flex-end;">
                <button type="button" class="details-btn" onclick="closeProfileModal()">Cancel</button>
                <button type="submit" class="btn-submit" style="width:auto; margin:0; padding:8px 24px;">Save Changes</button>
            </div>
        </form>
    </div>
</div>

<script>
// Details Modal Functions
function openDetailsModal(bookingData, hotelNames, refCode) {
    document.getElementById('m_ref').innerText = refCode;
    document.getElementById('m_name').innerText = bookingData.cust_fname + ' ' + bookingData.cust_lname;
    document.getElementById('m_email').innerText = bookingData.cust_email;
    document.getElementById('m_phone').innerText = '📞 ' + bookingData.cust_mobile;
    document.getElementById('m_state').innerText = bookingData.state_name ? bookingData.state_name : 'Multiple States';
    
    // Format Date
    var d = new Date(bookingData.date);
    var dateString = d.toLocaleDateString('en-GB', { day: 'numeric', month: 'short', year: 'numeric' });
    document.getElementById('m_date').innerText = dateString;
    
    document.getElementById('m_hotels').innerHTML = hotelNames;
    
    document.getElementById('detailsModal').style.display = 'flex';
}

function closeDetailsModal() {
    document.getElementById('detailsModal').style.display = 'none';
}

// Profile Modal Functions
function openProfileModal() {
    document.getElementById('profileModal').style.display = 'flex';
}

function closeProfileModal() {
    document.getElementById('profileModal').style.display = 'none';
}

// Close modals on Escape key
window.addEventListener('keydown', function (e) {
    if (e.key === 'Escape') {
        closeDetailsModal();
        closeProfileModal();
    }
});
</script>

</body>
</html>
