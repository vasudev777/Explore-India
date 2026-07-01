<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
include('db.php');
session_start();

// Login check
if (!isset($_SESSION['uemail'])) {
    header('Location: login.php');
    exit;
}

// POST check
if (empty($_POST['hotels'])) {
    header('Location: customize.php?error=no_hotels');
    exit;
}

// ── Process hotels ──
$hotel_ids   = $_POST['hotels']; // array from checkboxes
$hid         = implode(",", array_map('intval', $hotel_ids));
$sum         = 0;
$hotels_data = [];

foreach ($hotel_ids as $hid_single) {
    $hid_single = intval($hid_single);
    $res = mysqli_query($conn, "SELECT * FROM hotel WHERE h_id='$hid_single'");
    if ($res && $row = mysqli_fetch_assoc($res)) {
        $sum += $row['h_price'];
        $hotels_data[] = $row;
    }
}

// ── State name ──
$st_id   = intval($_POST['country']);
$result1 = mysqli_query($conn, "SELECT * FROM state WHERE s_id='$st_id'");
$state   = $result1 ? mysqli_fetch_assoc($result1) : ['s_name' => ''];

// ── Local guide ──
$lgresult = mysqli_query($conn, "SELECT * FROM local_guide WHERE s_id='$st_id'");
$guide    = $lgresult ? mysqli_fetch_assoc($lgresult) : null;

$date = htmlspecialchars($_POST['date'] ?? '');
$day  = htmlspecialchars($_POST['day'] ?? '');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>Review Package – Explore India</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <link href="css/bootstrap.css" rel='stylesheet' type='text/css'/>
    <link href="css/style.css" rel='stylesheet' type='text/css'/>
    <link href="css/font-awesome.min.css" rel="stylesheet">
    <link href="//fonts.googleapis.com/css?family=Montserrat:400,500,600,700,800" rel="stylesheet">
    <link href="//fonts.googleapis.com/css?family=Open+Sans:300,400,600,700" rel="stylesheet">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>

    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
        body { background: #0a0a0a; font-family: 'Open Sans', sans-serif; color: #fff; overflow-x: hidden; }

        /* PAGE HERO */
        .page-hero {
            padding: 120px 20px 60px;
            text-align: center;
            background: linear-gradient(160deg, #0a0a0a 0%, #0d1a10 50%, #0a0a0a 100%);
        }
        .hero-badge {
            display: inline-block;
            font-size: 10px; font-weight: 700; letter-spacing: 3px;
            text-transform: uppercase; color: rgba(255,255,255,0.4);
            border: 1px solid rgba(255,255,255,0.12);
            padding: 5px 14px; border-radius: 20px; margin-bottom: 16px;
        }
        .page-hero h1 {
            font-family: 'Montserrat', sans-serif;
            font-size: clamp(28px, 5vw, 52px); font-weight: 900;
            color: #fff; letter-spacing: -1px; margin-bottom: 10px;
            text-transform: none !important;
        }
        .page-hero h1 span { color: #5ecfa8; }
        .page-hero p { font-size: 14px; color: rgba(255,255,255,0.4); }

        /* MAIN WRAP */
        .main-wrap {
            max-width: 900px;
            margin: 0 auto;
            padding: 50px 20px 80px;
            display: grid;
            grid-template-columns: 1.1fr 0.9fr;
            gap: 28px;
            align-items: start;
        }
        @media (max-width: 768px) { .main-wrap { grid-template-columns: 1fr; } }

        /* CARDS */
        .review-card, .guide-card {
            background: #141414;
            border: 1px solid rgba(255,255,255,0.07);
            border-radius: 20px;
            overflow: hidden;
        }
        .card-header {
            padding: 18px 24px 14px;
            border-bottom: 1px solid rgba(255,255,255,0.06);
            display: flex; align-items: center; gap: 10px;
        }
        .card-header .ch-icon {
            width: 34px; height: 34px; border-radius: 10px;
            background: rgba(94,207,168,0.1);
            border: 1px solid rgba(94,207,168,0.2);
            display: flex; align-items: center; justify-content: center;
            color: #5ecfa8; font-size: 14px; flex-shrink: 0;
        }
        .card-header h3 {
            font-family: 'Montserrat', sans-serif;
            font-size: 15px; font-weight: 700; color: #fff; margin: 0;
            text-transform: none !important;
        }
        .card-header p { font-size: 11px; color: rgba(255,255,255,0.3); margin: 0; }
        .card-body { padding: 20px 24px; }

        /* INFO ROWS */
        .info-row {
            display: flex; justify-content: space-between;
            align-items: center; gap: 12px;
            padding: 10px 0;
            border-bottom: 1px solid rgba(255,255,255,0.05);
        }
        .info-row:last-child { border-bottom: none; }
        .info-label {
            font-size: 11px; font-weight: 700;
            letter-spacing: 1px; text-transform: uppercase;
            color: rgba(255,255,255,0.35); flex-shrink: 0;
        }
        .info-value {
            font-size: 13px; font-weight: 600;
            color: #fff; text-align: right;
        }

        /* Hotel List */
        .hotels-label {
            font-size: 11px; font-weight: 700;
            letter-spacing: 1px; text-transform: uppercase;
            color: rgba(255,255,255,0.35);
            margin: 16px 0 10px;
        }
        .hotel-list { display: flex; flex-direction: column; gap: 8px; }
        .hotel-item {
            background: rgba(255,255,255,0.03);
            border: 1px solid rgba(255,255,255,0.06);
            border-radius: 10px;
            padding: 10px 14px;
            display: flex; justify-content: space-between; align-items: center;
            gap: 10px;
        }
        .hotel-name { font-size: 13px; font-weight: 600; color: #fff; }
        .hotel-price {
            font-size: 12px; font-weight: 700; color: #5ecfa8;
            background: rgba(94,207,168,0.08);
            border: 1px solid rgba(94,207,168,0.2);
            padding: 3px 10px; border-radius: 20px;
            white-space: nowrap; flex-shrink: 0;
        }

        /* Total Box */
        .total-box {
            background: rgba(94,207,168,0.06);
            border: 1px solid rgba(94,207,168,0.2);
            border-radius: 12px;
            padding: 16px 18px;
            display: flex; justify-content: space-between; align-items: center;
            margin-top: 16px;
        }
        .total-label {
            font-size: 12px; font-weight: 700;
            letter-spacing: 1px; text-transform: uppercase;
            color: rgba(255,255,255,0.5);
        }
        .total-value {
            font-family: 'Montserrat', sans-serif;
            font-size: 26px; font-weight: 800; color: #5ecfa8;
        }
        .total-value span { font-size: 13px; font-weight: 500; color: rgba(255,255,255,0.4); }

        /* Guide Card */
        .guide-card .ch-icon {
            background: rgba(245,166,35,0.1) !important;
            border-color: rgba(245,166,35,0.2) !important;
            color: #f5a623 !important;
        }
        .guide-detail {
            display: flex; align-items: flex-start; gap: 12px;
            padding: 12px 0;
            border-bottom: 1px solid rgba(255,255,255,0.05);
        }
        .guide-detail:last-child { border-bottom: none; }
        .gd-icon {
            width: 32px; height: 32px; flex-shrink: 0;
            border-radius: 8px;
            background: rgba(245,166,35,0.08);
            border: 1px solid rgba(245,166,35,0.15);
            display: flex; align-items: center; justify-content: center;
            color: #f5a623; font-size: 13px;
        }
        .gd-info { flex: 1; }
        .gd-label {
            font-size: 10px; font-weight: 700; letter-spacing: 1px;
            text-transform: uppercase; color: rgba(255,255,255,0.3); margin-bottom: 3px;
        }
        .gd-value { font-size: 14px; font-weight: 600; color: #fff; }

        /* Book Button */
        .btn-book {
            width: 100%;
            background: linear-gradient(135deg, #5ecfa8, #3ab88a);
            border: none; border-radius: 12px;
            color: #000; font-size: 15px; font-weight: 700;
            font-family: 'Montserrat', sans-serif;
            padding: 14px; cursor: pointer;
            transition: transform 0.2s, box-shadow 0.2s;
            margin-top: 20px; letter-spacing: 0.3px;
            display: block;
        }
        .btn-book:hover { transform: translateY(-2px); box-shadow: 0 8px 24px rgba(94,207,168,0.3); }
        .secure-note {
            text-align: center; font-size: 11px;
            color: rgba(255,255,255,0.25); margin-top: 12px;
        }
        .secure-note .fa { color: #5ecfa8; margin-right: 4px; }

        /* No guide */
        .no-guide {
            background: #141414;
            border: 1px solid rgba(255,255,255,0.07);
            border-radius: 20px;
            padding: 20px 24px;
            text-align: center;
            color: rgba(255,255,255,0.3);
            font-size: 13px;
            margin-bottom: 20px;
        }
        .no-guide .fa { font-size: 28px; display: block; margin-bottom: 8px; opacity: 0.3; }
    </style>
</head>
<body oncontextmenu="return false;">

<?php include('header.php'); ?>

<!-- PAGE HERO -->
<div class="page-hero">
    <div class="hero-badge">📋 Almost There!</div>
    <h1>Review Your <span>Package</span></h1>
    <p>Check your details before proceeding to payment</p>
</div>

<form action="pay_cust.php" method="post">
    <!-- Hidden fields -->
    <input type="hidden" name="hid"     value="<?= htmlspecialchars($hid) ?>">
    <input type="hidden" name="total"   value="<?= $sum ?>">
    <input type="hidden" name="date"    value="<?= $date ?>">
    <input type="hidden" name="day"     value="<?= $day ?>">
    <input type="hidden" name="country" value="<?= $st_id ?>">
    <input type="hidden" name="state"   value="<?= htmlspecialchars($state['s_name']) ?>">
    <?php if (!empty($hotels_data)): ?>
    <input type="hidden" name="h_name"  value="<?= htmlspecialchars(end($hotels_data)['h_name']) ?>">
    <input type="hidden" name="h_price" value="<?= htmlspecialchars(end($hotels_data)['h_price']) ?>">
    <?php endif; ?>
    <?php if ($guide): ?>
    <input type="hidden" name="lg" value="<?= $guide['localg_id'] ?>">
    <?php endif; ?>

    <div class="main-wrap">

        <!-- LEFT: Package Review -->
        <div class="review-card">
            <div class="card-header">
                <div class="ch-icon"><span class="fa fa-suitcase"></span></div>
                <div>
                    <h3>Package Details</h3>
                    <p>Your selected trip summary</p>
                </div>
            </div>
            <div class="card-body">
                <div class="info-row">
                    <span class="info-label">Travel Date</span>
                    <span class="info-value"><?= $date ?></span>
                </div>
                <div class="info-row">
                    <span class="info-label">Duration</span>
                    <span class="info-value"><?= $day ?> Days</span>
                </div>
                <div class="info-row">
                    <span class="info-label">State</span>
                    <span class="info-value"><?= htmlspecialchars($state['s_name']) ?></span>
                </div>

                <!-- Hotels -->
                <div class="hotels-label">Selected Hotels</div>
                <div class="hotel-list">
                    <?php foreach ($hotels_data as $h): ?>
                    <div class="hotel-item">
                        <span class="hotel-name">
                            <span class="fa fa-bed" style="color:#5ecfa8; margin-right:6px;"></span>
                            <?= htmlspecialchars($h['h_name']) ?>
                        </span>
                        <span class="hotel-price">₹<?= number_format($h['h_price']) ?></span>
                    </div>
                    <?php endforeach; ?>
                </div>

                <!-- Total -->
                <div class="total-box">
                    <div class="total-label">Total Cost</div>
                    <div class="total-value">₹<?= number_format($sum) ?> <span>INR</span></div>
                </div>
            </div>
        </div>

        <!-- RIGHT: Guide + Book -->
        <div>
            <?php if ($guide): ?>
            <div class="guide-card" style="margin-bottom: 20px;">
                <div class="card-header">
                    <div class="ch-icon"><span class="fa fa-user"></span></div>
                    <div>
                        <h3>Your Local Guide</h3>
                        <p>Expert assigned for your trip</p>
                    </div>
                </div>
                <div class="card-body">
                    <div class="guide-detail">
                        <div class="gd-icon"><span class="fa fa-user-circle"></span></div>
                        <div class="gd-info">
                            <div class="gd-label">Name</div>
                            <div class="gd-value"><?= htmlspecialchars($guide['localg_name']) ?></div>
                        </div>
                    </div>
                    <div class="guide-detail">
                        <div class="gd-icon"><span class="fa fa-language"></span></div>
                        <div class="gd-info">
                            <div class="gd-label">Languages</div>
                            <div class="gd-value"><?= htmlspecialchars($guide['localg_language']) ?></div>
                        </div>
                    </div>
                    <div class="guide-detail">
                        <div class="gd-icon"><span class="fa fa-phone"></span></div>
                        <div class="gd-info">
                            <div class="gd-label">Contact</div>
                            <div class="gd-value"><?= htmlspecialchars($guide['localg_mobile']) ?></div>
                        </div>
                    </div>
                </div>
            </div>
            <?php else: ?>
            <div class="no-guide">
                <span class="fa fa-user-times"></span>
                No local guide available for this state
            </div>
            <?php endif; ?>

            <button type="submit" name="button" class="btn-book">
                <span class="fa fa-lock"></span> &nbsp; Proceed to Payment
            </button>
            <p class="secure-note">
                <span class="fa fa-shield"></span> 100% Secure &nbsp;|&nbsp;
                <span class="fa fa-check-circle"></span> Instant Confirmation
            </p>
        </div>

    </div>
</form>

<?php include('footer.php'); ?>
</body>
</html>