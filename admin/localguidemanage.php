<?php
include('db.php');
include('check.php');

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Load and clear flash messages from session
$msg = $_SESSION['msg'] ?? '';
$msg_type = $_SESSION['msg_type'] ?? '';
unset($_SESSION['msg']);
unset($_SESSION['msg_type']);

// Import PHPMailer classes into the global namespace
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

// Path adjusted as file is inside admin/ folder, phpmailer is outside in root
require '../phpmailer/src/Exception.php';
require '../phpmailer/src/PHPMailer.php';
require '../phpmailer/src/SMTP.php';

$msg = '';
$msg_type = '';

// ===== EMAIL SENDER FUNCTIONS =====
function sendApprovalEmail($toEmail, $toName) {
    $mail = new PHPMailer(true);
    try {
        $mail->isSMTP();
        $mail->Host       = 'smtp.gmail.com';
        $mail->SMTPAuth   = true;
        $mail->Username   = 'shram0610@gmail.com';
        $mail->Password   = 'uhnrjrocoecdeizv';  // App password
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port       = 587;

        $mail->setFrom('shram0610@gmail.com', 'Explore India');
        $mail->addAddress($toEmail, $toName);
        $mail->isHTML(true);
        $mail->Subject = 'Application Approved - Explore India';
        
        $mail->Body    = '
        <div style="font-family:Arial,sans-serif;max-width:500px;margin:0 auto;background:#f8fafc;border-radius:16px;overflow:hidden;border:1px solid #e2e8f0;">
            <div style="background:linear-gradient(135deg,#FF782C,#F39C12);padding:32px;text-align:center;">
                <h2 style="color:#fff;margin:0;font-size:24px;font-family:\'Montserrat\',sans-serif;">🇮🇳 Explore India</h2>
                <p style="color:rgba(255,255,255,0.9);margin:5px 0 0;font-size:13px;font-weight:600;">LOCAL GUIDE PORTAL</p>
            </div>
            <div style="padding:32px;background:#fff;">
                <p style="color:#0f172a;font-size:16px;margin-bottom:12px;font-weight:700;">Hello '.$toName.',</p>
                <p style="color:#475569;font-size:14.5px;line-height:1.6;">Congratulations! Your application to join Explore India as a Local Guide has been reviewed and **Approved** by our administrator team.</p>
                <div style="background:#f1f5f9;border-left:4px solid #10B981;border-radius:8px;padding:16px;margin:20px 0;">
                    <span style="font-size:14px;color:#1e293b;font-weight:700;">What\'s Next?</span>
                    <p style="margin:5px 0 0;font-size:13px;color:#475569;">You can now log in to your Local Guide Dashboard to manage bookings, edit packages, and interact with travelers.</p>
                </div>
                <hr style="border:none;border-top:1px solid #e2e8f0;margin:24px 0;">
                <p style="color:#94a3b8;font-size:11px;text-align:center;">© 2026 Explore India. All Rights Reserved.</p>
            </div>
        </div>';
        $mail->send();
        return true;
    } catch (Exception $e) {
        return false;
    }
}

function sendRejectionEmail($toEmail, $toName, $reason) {
    $mail = new PHPMailer(true);
    try {
        $mail->isSMTP();
        $mail->Host       = 'smtp.gmail.com';
        $mail->SMTPAuth   = true;
        $mail->Username   = 'shram0610@gmail.com';
        $mail->Password   = 'uhnrjrocoecdeizv';  // App password
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port       = 587;

        $mail->setFrom('shram0610@gmail.com', 'Explore India');
        $mail->addAddress($toEmail, $toName);
        $mail->isHTML(true);
        $mail->Subject = 'Application Update - Explore India';
        
        $mail->Body    = '
        <div style="font-family:Arial,sans-serif;max-width:500px;margin:0 auto;background:#f8fafc;border-radius:16px;overflow:hidden;border:1px solid #e2e8f0;">
            <div style="background:linear-gradient(135deg,#EF4444,#DC2626);padding:32px;text-align:center;">
                <h2 style="color:#fff;margin:0;font-size:24px;font-family:\'Montserrat\',sans-serif;">🇮🇳 Explore India</h2>
                <p style="color:rgba(255,255,255,0.9);margin:5px 0 0;font-size:13px;font-weight:600;">LOCAL GUIDE PORTAL</p>
            </div>
            <div style="padding:32px;background:#fff;">
                <p style="color:#0f172a;font-size:16px;margin-bottom:12px;font-weight:700;">Hello '.$toName.',</p>
                <p style="color:#475569;font-size:14.5px;line-height:1.6;">Thank you for your interest in joining Explore India. We regret to inform you that your application has been **Declined** due to the following reason:</p>
                <div style="background:#fff5f5;border:1px solid #fed7d7;border-radius:12px;padding:18px;margin:20px 0;color:#9b2c2c;font-size:14px;font-weight:600;line-height:1.5;">
                    " '.htmlspecialchars($reason).' "
                </div>
                <p style="color:#475569;font-size:13.5px;">If you have any questions or would like to submit correct details, feel free to apply again.</p>
                <hr style="border:none;border-top:1px solid #e2e8f0;margin:24px 0;">
                <p style="color:#94a3b8;font-size:11px;text-align:center;">© 2026 Explore India. All Rights Reserved.</p>
            </div>
        </div>';
        $mail->send();
        return true;
    } catch (Exception $e) {
        return false;
    }
}

// ===== CONTROLLER OPERATIONS =====

// 1. APPROVE GUIDE
if (isset($_GET['approve'])) {
    $id = intval($_GET['approve']);
    $res = mysqli_query($conn, "SELECT localg_name, localg_email FROM local_guide WHERE localg_id='$id'");
    if ($row = mysqli_fetch_assoc($res)) {
        $name = $row['localg_name'];
        $email = $row['localg_email'];
        
        mysqli_query($conn, "UPDATE local_guide SET localg_approve='1' WHERE localg_id='$id'");
        sendApprovalEmail($email, $name);
        
        $_SESSION['msg'] = "Local guide application approved and confirmation email sent!";
        $_SESSION['msg_type'] = "success";
        
        header("Location: localguidemanage.php");
        exit;
    }
}

// 2. REJECT GUIDE (With reason, then Delete)
if (isset($_POST['reject_guide'])) {
    $id = intval($_POST['reject_id']);
    $reason = trim($_POST['reject_reason']);
    
    $res = mysqli_query($conn, "SELECT localg_name, localg_email FROM local_guide WHERE localg_id='$id'");
    if ($row = mysqli_fetch_assoc($res)) {
        $name = $row['localg_name'];
        $email = $row['localg_email'];
        
        sendRejectionEmail($email, $name, $reason);
        mysqli_query($conn, "DELETE FROM local_guide WHERE localg_id='$id'");
        
        $_SESSION['msg'] = "Guide application rejected, notification email sent, and record deleted.";
        $_SESSION['msg_type'] = "success";
        
        header("Location: localguidemanage.php");
        exit;
    }
}

// 3. TOGGLE BLOCK STATUS (Only for Approved Guides)
if (isset($_GET['toggle_block'])) {
    $id = intval($_GET['toggle_block']);
    $res = mysqli_query($conn, "SELECT status, localg_approve FROM local_guide WHERE localg_id='$id'");
    if ($row = mysqli_fetch_assoc($res)) {
        if ($row['localg_approve'] == 1) {
            $new_status = ($row['status'] == 1) ? 0 : 1;
            mysqli_query($conn, "UPDATE local_guide SET status='$new_status' WHERE localg_id='$id'");
            
            $_SESSION['msg'] = "Local guide block status updated successfully!";
            $_SESSION['msg_type'] = "success";
            
            header("Location: localguidemanage.php");
            exit;
        }
    }
}

// Fetch all guides with State & City Names
$guides_res = mysqli_query($conn, "SELECT lg.*, s.s_name, c.c_name 
                                   FROM local_guide lg
                                   LEFT JOIN state s ON lg.s_id = s.s_id 
                                   LEFT JOIN city c ON lg.c_id = c.c_id 
                                   ORDER BY lg.localg_id DESC");

// Stats for mini cards
$count_all = mysqli_num_rows(mysqli_query($conn, "SELECT * FROM local_guide"));
$count_approved = mysqli_num_rows(mysqli_query($conn, "SELECT * FROM local_guide WHERE localg_approve=1"));
$count_pending = mysqli_num_rows(mysqli_query($conn, "SELECT * FROM local_guide WHERE localg_approve=0"));
$count_blocked = mysqli_num_rows(mysqli_query($conn, "SELECT * FROM local_guide WHERE status=1 AND localg_approve=1"));

$page_title = "Local Guides"; // Sets Topbar Title dynamically
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Guides – Explore India Admin</title>
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
        .za-table tr:hover td { background: #F8FAFC; }

        /* BADGES */
        .za-badge { display: inline-flex; align-items: center; gap: 5px; padding: 4px 10px; border-radius: 50px; font-size: 11px; font-weight: 700; text-transform: uppercase; }
        .za-badge.green  { background: rgba(16,185,129,0.1); color: var(--green); }
        .za-badge.red    { background: rgba(239,68,68,0.1); color: var(--red); }
        .za-badge.gray   { background: #E2E8F0; color: var(--text); }
        .za-badge.blue   { background: rgba(59,130,246,0.1); color: var(--blue); }
        .za-badge.orange { background: rgba(255,120,44,0.1); color: var(--orange); }

        /* BUTTONS */
        .za-btn { display: inline-flex; align-items: center; gap: 6px; padding: 8px 16px; border-radius: 10px; font-size: 13px; font-weight: 600; cursor: pointer; transition: all 0.2s; border: none; text-decoration: none; font-family: 'Open Sans', sans-serif; white-space: nowrap; }
        .za-btn:hover { transform: translateY(-1px); text-decoration: none; }
        .za-btn-outline { background: transparent; color: var(--text); border: 1.5px solid var(--border); }
        .za-btn-outline:hover { border-color: var(--dark); color: var(--dark); }
        .za-btn-red { background: var(--red); color: #fff; }
        .za-btn-red:hover { background: #dc2626; color: #fff; }
        .za-btn-green { background: var(--green); color: #fff; }
        .za-btn-green:hover { background: #059669; color: #fff; }
        .za-btn-sm { padding: 6px 12px; font-size: 12px; border-radius: 8px; }

        /* ALERTS */
        .za-alert { padding: 12px 16px; border-radius: 10px; font-size: 13.5px; font-weight: 500; margin-bottom: 20px; display: flex; align-items: center; gap: 10px; }
        .za-alert.success { background: rgba(16,185,129,0.08); color: #065f46; border-left: 4px solid var(--green); }
        .za-alert.error   { background: rgba(239,68,68,0.08); color: #991b1b; border-left: 4px solid var(--red); }

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
            <h1>Local Guide Directory</h1>
            <p>Approve new guide registrations, reject with explanation mails, and block active profiles.</p>
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
                    <div class="stat-label">Total Guides</div>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon" style="background: var(--green-grad);">✅</div>
                <div class="stat-info">
                    <div class="stat-num"><?= $count_approved ?></div>
                    <div class="stat-label">Approved</div>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon" style="background: var(--orange-grad);">⏳</div>
                <div class="stat-info">
                    <div class="stat-num"><?= $count_pending ?></div>
                    <div class="stat-label">Pending Approval</div>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon" style="background: var(--red-grad);">🚫</div>
                <div class="stat-info">
                    <div class="stat-num"><?= $count_blocked ?></div>
                    <div class="stat-label">Blocked</div>
                </div>
            </div>
        </div>

        <!-- Table Card -->
        <div class="za-card">
            <div class="za-card-header">
                <div class="za-card-title">All Local Guides List (<?= mysqli_num_rows($guides_res) ?>)</div>
            </div>

            <div style="overflow-x:auto;">
                <?php if (mysqli_num_rows($guides_res) > 0): ?>
                    <table class="za-table">
                        <thead>
                            <tr>
                                <th>#ID</th>
                                <th>Guide Profile</th>
                                <th>Languages</th>
                                <th>Email Verify</th>
                                <th>Approval Status</th>
                                <th>Block Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($c = mysqli_fetch_assoc($guides_res)): ?>
                                <tr>
                                    <td style="color:var(--muted); font-size:12px; font-weight:700;">#<?= $c['localg_id'] ?></td>
                                    <td>
                                        <div style="display:flex; align-items:center; gap:12px;">
                                            <div class="za-cust-avatar" style="background: <?= $c['localg_approve'] == 1 ? 'var(--orange-grad)' : 'var(--blue-grad)' ?>">
                                                <?= substr($c['localg_name'], 0, 1) ?>
                                            </div>
                                            <div>
                                                <div style="font-weight:700; color:var(--dark);"><?= htmlspecialchars($c['localg_name']) ?></div>
                                                <div style="font-size:12px; color:var(--muted);"><?= htmlspecialchars($c['localg_email']) ?></div>
                                                <div style="font-size:12px; color:var(--muted);">📞 <?= htmlspecialchars($c['localg_mobile']) ?></div>
                                            </div>
                                        </div>
                                    </td>
                                    <td style="font-weight:600; color:var(--text);"><?= htmlspecialchars($c['localg_language']) ?></td>
                                    <td>
                                        <?php if ($c['localg_emailverify'] == 1): ?>
                                            <span class="za-badge green"><i class="fa fa-envelope-o"></i> Verified</span>
                                        <?php else: ?>
                                            <span class="za-badge gray"><i class="fa fa-envelope-open-o"></i> Unverified</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php if ($c['localg_approve'] == 1): ?>
                                            <span class="za-badge green"><i class="fa fa-check-circle"></i> Approved</span>
                                        <?php else: ?>
                                            <span class="za-badge orange"><i class="fa fa-clock-o"></i> Pending</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php if ($c['localg_approve'] == 1): ?>
                                            <?php if ($c['status'] == 1): ?>
                                                <span class="za-badge red"><i class="fa fa-ban"></i> Blocked</span>
                                            <?php else: ?>
                                                <span class="za-badge green"><i class="fa fa-circle"></i> Active</span>
                                            <?php endif; ?>
                                        <?php else: ?>
                                            <span class="za-badge gray">Not Approved Yet</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <div style="display:flex; gap:6px;">
                                            <!-- View Profile Button -->
                                            <button class="za-btn za-btn-outline za-btn-sm" onclick='openViewModal(<?= json_encode($c, JSON_HEX_APOS | JSON_HEX_QUOT) ?>)'>
                                                <i class="fa fa-eye"></i> View
                                            </button>

                                            <?php if ($c['localg_approve'] == 0): ?>
                                                <!-- Approve Button -->
                                                <a href="localguidemanage.php?approve=<?= $c['localg_id'] ?>" class="za-btn za-btn-green za-btn-sm" onclick="return confirm('Approve this guide application?')">
                                                    <i class="fa fa-check"></i> Approve
                                                </a>
                                                <!-- Reject Button (Opens Modal) -->
                                                <button class="za-btn za-btn-red za-btn-sm" onclick="openRejectModal(<?= $c['localg_id'] ?>, '<?= addslashes($c['localg_name']) ?>')">
                                                    <i class="fa fa-times"></i> Reject
                                                </button>
                                            <?php else: ?>
                                                <!-- Block / Unblock Button (Only visible after approval) -->
                                                <?php if ($c['status'] == 1): ?>
                                                    <a href="localguidemanage.php?toggle_block=<?= $c['localg_id'] ?>" class="za-btn za-btn-outline za-btn-sm" style="color:var(--green); border-color:var(--green);">
                                                        <i class="fa fa-check"></i> Unblock
                                                    </a>
                                                <?php else: ?>
                                                    <a href="localguidemanage.php?toggle_block=<?= $c['localg_id'] ?>" class="za-btn za-btn-outline za-btn-sm" style="color:var(--red); border-color:var(--red);" onclick="return confirm('Block this local guide account?')">
                                                        <i class="fa fa-ban"></i> Block
                                                    </a>
                                                <?php endif; ?>
                                            <?php endif; ?>
                                        </div>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                <?php else: ?>
                    <div class="za-empty">
                        <i class="fa fa-user-circle-o"></i>
                        <p>No local guides found in the database.</p>
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

<!-- VIEW DETAILS MODAL -->
<div class="za-modal-overlay" id="viewModal">
    <div class="za-modal">
        <div class="za-modal-header">
            <div class="za-modal-title">👤 Guide Profile Details</div>
            <button class="za-modal-close" onclick="closeModal('viewModal')">✕</button>
        </div>
        <div class="za-modal-body">
            <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom: 20px; padding-bottom: 15px; border-bottom: 1px solid var(--border);">
                <div>
                    <span style="font-size: 11px; color: var(--muted); text-transform: uppercase; font-weight: 700; letter-spacing: 0.5px;">Guide ID</span>
                    <h3 id="view_id" style="font-family:'Montserrat',sans-serif; font-size: 22px; font-weight: 800; color: var(--dark); margin-top: 2px;">#0000</h3>
                </div>
                <div id="view_status_badges"></div>
            </div>

            <!-- Guide Bio Block -->
            <div style="margin-bottom: 20px;">
                <label class="za-label">Guide Identity</label>
                <div style="background: #F8FAFC; border: 1.5px solid var(--border); border-radius: 12px; padding: 14px 18px;">
                    <div style="font-weight: 700; color: var(--dark); font-size: 16px; margin-bottom: 8px;" id="view_name">Guide Name</div>
                    <div style="font-size: 13.5px; color: var(--text); line-height: 1.6;">
                        <i class="fa fa-language" style="width: 18px; color: var(--muted);"></i> Languages: <strong id="view_languages" style="color:var(--orange);"></strong><br>
                        <i class="fa fa-envelope-o" style="width: 18px; color: var(--muted);"></i> Email Verify: <span id="view_verified"></span><br>
                        <i class="fa fa-check-circle-o" style="width: 18px; color: var(--muted);"></i> Approval: <span id="view_approved"></span>
                    </div>
                </div>
            </div>

            <!-- Contact Grid -->
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

            <!-- Location Map -->
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px; border-top: 1px solid var(--border); padding-top: 20px;">
                <div>
                    <label class="za-label">Operating City</label>
                    <p id="view_city" style="font-size:15px; font-weight:700; color:var(--dark);"></p>
                </div>
                <div>
                    <label class="za-label">Operating State</label>
                    <p id="view_state" style="font-size:15px; font-weight:700; color:var(--dark);"></p>
                </div>
            </div>
        </div>
        <div class="za-modal-footer">
            <button type="button" class="za-btn za-btn-outline" onclick="closeModal('viewModal')">Close</button>
        </div>
    </div>
</div>

<!-- REJECTION REASON MODAL -->
<div class="za-modal-overlay" id="rejectModal">
    <div class="za-modal">
        <div class="za-modal-header" style="background:#fffcfc; border-bottom-color:#fed7d7;">
            <div class="za-modal-title" style="color:var(--red);"><i class="fa fa-times-circle"></i> Reject Application</div>
            <button class="za-modal-close" onclick="closeModal('rejectModal')">✕</button>
        </div>
        <form method="POST" action="localguidemanage.php">
            <input type="hidden" name="reject_id" id="reject_id_val">
            <input type="hidden" name="reject_guide" value="1">
            
            <div class="za-modal-body">
                <p style="font-size:13.5px; color:var(--text); margin-bottom:18px; line-height:1.5;">
                    Provide a reason for rejecting <strong id="reject_guide_name"></strong>'s application. An email notification with this reason will be sent, and their record will be deleted.
                </p>
                
                <div class="form-group" style="margin-bottom:0;">
                    <label style="color:var(--red);">Rejection Reason</label>
                    <textarea name="reject_reason" class="form-control" style="height:120px; resize:none;" placeholder="Enter details (e.g. Invalid/unclear government credentials or details mismatch.)" required></textarea>
                </div>
            </div>
            <div class="za-modal-footer" style="background:#fafafa;">
                <button type="button" class="za-btn za-btn-outline" onclick="closeModal('rejectModal')">Cancel</button>
                <button type="submit" class="za-btn za-btn-red">Confirm Reject & Delete</button>
            </div>
        </form>
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

function openViewModal(guide) {
    document.getElementById('view_id').innerText = '#' + guide.localg_id;
    document.getElementById('view_name').innerText = guide.localg_name;
    document.getElementById('view_languages').innerText = guide.localg_language;
    document.getElementById('view_mobile').innerText = guide.localg_mobile;
    document.getElementById('view_email').innerText = guide.localg_email;
    
    document.getElementById('view_city').innerText = guide.c_name ? guide.c_name : 'N/A';
    document.getElementById('view_state').innerText = guide.s_name ? guide.s_name : 'N/A';

    // Email Verify Status Mapping
    var verifySpan = document.getElementById('view_verified');
    if (guide.localg_emailverify == 1) {
        verifySpan.className = 'za-badge green';
        verifySpan.innerHTML = '<i class="fa fa-envelope-o"></i> Verified';
    } else {
        verifySpan.className = 'za-badge gray';
        verifySpan.innerHTML = '<i class="fa fa-envelope-open-o"></i> Unverified';
    }

    // Approval Mapping
    var approveSpan = document.getElementById('view_approved');
    if (guide.localg_approve == 1) {
        approveSpan.className = 'za-badge green';
        approveSpan.innerHTML = '<i class="fa fa-check-circle"></i> Approved';
    } else {
        approveSpan.className = 'za-badge orange';
        approveSpan.innerHTML = '<i class="fa fa-clock-o"></i> Pending';
    }

    // Status Badges (Top Right of Modal)
    var statusDiv = document.getElementById('view_status_badges');
    if (guide.localg_approve == 1) {
        if (guide.status == 1) {
            statusDiv.innerHTML = '<span class="za-badge red"><i class="fa fa-ban"></i> Blocked</span>';
        } else {
            statusDiv.innerHTML = '<span class="za-badge green"><i class="fa fa-circle"></i> Active</span>';
        }
    } else {
        statusDiv.innerHTML = '<span class="za-badge orange"><i class="fa fa-clock-o"></i> Pending Admin Approval</span>';
    }

    openModal('viewModal');
}

function openRejectModal(id, name) {
    document.getElementById('reject_id_val').value = id;
    document.getElementById('reject_guide_name').innerText = name;
    openModal('rejectModal');
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