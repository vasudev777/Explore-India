<?php
include('db.php');
session_start();
if (!isset($_SESSION['uemail'])) { header('Location: login.php'); exit; }

$cust_id = intval($_SESSION['ucust_id']);

$custom_res    = mysqli_query($conn, "SELECT * FROM customize_booking WHERE cust_id=$cust_id ORDER BY created_at DESC");
$predef_res    = mysqli_query($conn, "SELECT pb.*, p.pa_name, p.h_id as pkg_hotels FROM predefine_booking pb LEFT JOIN package p ON p.pa_id=pb.pa_id WHERE pb.cust_id=$cust_id ORDER BY pb.created_at DESC");
$transport_res = mysqli_query($conn, "SELECT * FROM transport_bookings WHERE cust_id=$cust_id ORDER BY booked_at DESC");

$custom_count    = $custom_res    ? mysqli_num_rows($custom_res)    : 0;
$predef_count    = $predef_res    ? mysqli_num_rows($predef_res)    : 0;
$transport_count = $transport_res ? mysqli_num_rows($transport_res) : 0;
$total_count     = $custom_count + $predef_count + $transport_count;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>My Bookings – Explore India</title>
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

        /* ══ DARK HERO ══ */
        .page-hero {
            padding: 100px 20px 50px;
            background: linear-gradient(160deg, #0a0a0a 0%, #12082a 50%, #0a0a0a 100%);
            text-align: center;
        }
        .eyebrow { font-size: 11px; font-weight: 700; letter-spacing: 4px; text-transform: uppercase; color: rgba(255,255,255,0.35); margin-bottom: 12px; }
        .page-hero h1 { font-family: 'Montserrat', sans-serif; font-size: clamp(26px, 5vw, 46px); font-weight: 800; color: #fff; margin-bottom: 8px; text-transform: none !important; }
        .page-hero h1 span { color: #f5a623; }
        .page-hero p { font-size: 14px; color: rgba(255,255,255,0.4); }

        /* Stats bar */
        .stats-bar {
            display: flex; justify-content: center; gap: 0;
            max-width: 480px; margin: 24px auto 0;
            background: rgba(255,255,255,0.06);
            border: 1px solid rgba(255,255,255,0.1);
            border-radius: 14px; overflow: hidden;
        }
        .stat-item { flex: 1; padding: 14px 10px; text-align: center; border-right: 1px solid rgba(255,255,255,0.08); }
        .stat-item:last-child { border: none; }
        .stat-num { font-family: 'Montserrat', sans-serif; font-size: 20px; font-weight: 800; }
        .stat-label { font-size: 10px; color: rgba(255,255,255,0.3); text-transform: uppercase; letter-spacing: 1px; margin-top: 3px; }

        /* ══ WHITE CONTENT ══ */
        .content-section { background: #fff; padding: 0 20px 80px; }
        .content-inner { max-width: 900px; margin: 0 auto; }

        /* Tab Nav */
        .tab-nav {
            display: flex; gap: 8px; flex-wrap: wrap;
            padding: 32px 0 24px;
            border-bottom: 1px solid #e9ecef;
            margin-bottom: 28px;
        }
        .tab-btn {
            display: flex; align-items: center; gap: 7px;
            padding: 10px 20px; border-radius: 30px;
            border: 1.5px solid #e9ecef;
            background: #f8f9fa;
            color: #888;
            font-size: 13px; font-weight: 600;
            cursor: pointer; transition: all 0.2s;
            font-family: 'Open Sans', sans-serif;
        }
        .tab-btn:hover { border-color: #ccc; color: #1a1a1a; }
        .tab-btn.active-customize { background: rgba(245,166,35,0.08); border-color: rgba(245,166,35,0.4); color: #d48a1a; }
        .tab-btn.active-special   { background: rgba(94,207,168,0.08); border-color: rgba(94,207,168,0.4); color: #2ecc9a; }
        .tab-btn.active-transport { background: rgba(94,160,255,0.08); border-color: rgba(94,160,255,0.4); color: #5ea0ff; }
        .count-badge { width: 20px; height: 20px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 10px; font-weight: 800; }

        /* Section */
        .section { display: none; }
        .section.active { display: block; }
        .section-heading { font-family: 'Montserrat', sans-serif; font-size: 17px; font-weight: 700; color: #1a1a1a; margin-bottom: 18px; text-transform: none !important; display: flex; align-items: center; gap: 8px; }

        /* Booking Card */
        .booking-card {
            background: #f8f9fa; border: 1.5px solid #e9ecef;
            border-radius: 16px; overflow: hidden; margin-bottom: 14px;
            transition: border-color 0.2s, box-shadow 0.2s;
        }
        .booking-card:hover { border-color: #ccc; box-shadow: 0 4px 20px rgba(0,0,0,0.06); }

        /* Card Top */
        .card-top {
            padding: 14px 20px; display: flex; align-items: center;
            justify-content: space-between; flex-wrap: wrap; gap: 10px;
            border-bottom: 1px solid #e9ecef; background: #fff;
        }
        .card-ref { font-family: 'Montserrat', sans-serif; font-size: 14px; font-weight: 700; display: flex; align-items: center; gap: 8px; color: #1a1a1a; }
        .card-ref .icon { font-size: 18px; }
        .ref-num { font-size: 12px; color: #aaa; margin-top: 2px; font-family: 'Open Sans', sans-serif; font-weight: 400; }

        .status-badge { padding: 4px 12px; border-radius: 20px; font-size: 11px; font-weight: 700; }
        .status-confirmed { background: #f0fff8; border: 1px solid #2ecc9a; color: #2ecc9a; }
        .status-pending   { background: #fff8ec; border: 1px solid #f5a623; color: #d48a1a; }
        .status-cancelled { background: #fff0f0; border: 1px solid #ff5050; color: #ff5050; }

        /* Card Body */
        .card-body { padding: 16px 20px; }
        .detail-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(140px, 1fr)); gap: 14px; }
        .detail-label { font-size: 10px; font-weight: 700; letter-spacing: 1px; text-transform: uppercase; color: #aaa; margin-bottom: 4px; }
        .detail-value { font-size: 13px; font-weight: 600; color: #1a1a1a; }
        .detail-value.orange { color: #d48a1a; }
        .detail-value.green  { color: #2ecc9a; }
        .detail-value.blue   { color: #5ea0ff; }

        /* Hotel tags */
        .hotel-tags { display: flex; flex-wrap: wrap; gap: 5px; margin-top: 12px; }
        .hotel-tag { padding: 3px 10px; border-radius: 10px; font-size: 11px; font-weight: 600; background: rgba(245,166,35,0.08); border: 1px solid rgba(245,166,35,0.2); color: #d48a1a; }
        .hotel-tag.green { background: rgba(46,204,154,0.08); border-color: rgba(46,204,154,0.2); color: #2ecc9a; }
        .hotel-tag.blue  { background: rgba(94,160,255,0.08); border-color: rgba(94,160,255,0.2); color: #5ea0ff; }

        /* Card Footer */
        .card-footer {
            padding: 12px 20px; border-top: 1px solid #e9ecef;
            display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 8px;
            background: #fff;
        }
        .amount-big { font-family: 'Montserrat', sans-serif; font-size: 20px; font-weight: 800; }
        .booked-date { font-size: 11px; color: #aaa; }

        /* Empty state */
        .empty-state { text-align: center; padding: 50px 20px; background: #f8f9fa; border: 1.5px solid #e9ecef; border-radius: 16px; }
        .empty-state .icon { font-size: 40px; margin-bottom: 12px; opacity: 0.4; }
        .empty-state p { color: #aaa; font-size: 14px; margin-bottom: 16px; }
        .empty-btn { display: inline-flex; align-items: center; gap: 6px; padding: 10px 20px; border-radius: 20px; background: #f0f0f0; border: 1.5px solid #e0e0e0; color: #666; font-size: 13px; font-weight: 600; text-decoration: none; transition: all 0.2s; }
        .empty-btn:hover { background: #e5e5e5; color: #333; text-decoration: none; }

        /* DARK STATS */
        .stats-section { background: #0a0a0a; padding: 50px 20px; }
        .stats-row { display: flex; justify-content: center; gap: 40px; flex-wrap: wrap; }
        .s-stat { text-align: center; }
        .s-num { font-family: 'Montserrat', sans-serif; font-size: 28px; font-weight: 800; }
        .s-label { font-size: 12px; color: rgba(255,255,255,0.35); margin-top: 4px; }

        @media (max-width: 600px) {
            .detail-grid { grid-template-columns: repeat(2,1fr); }
            .stats-bar { flex-direction: column; }
        }
    </style>
</head>
<body oncontextmenu="return false;">
<?php include('header.php'); ?>

<!-- DARK HERO -->
<div class="page-hero">
    <p class="eyebrow">My Account</p>
    <h1>My <span>Bookings</span></h1>
    <p>All your travel bookings in one place</p>
    <div class="stats-bar">
        <div class="stat-item"><div class="stat-num" style="color:#f5a623;"><?= $custom_count ?></div><div class="stat-label">Customize</div></div>
        <div class="stat-item"><div class="stat-num" style="color:#5ecfa8;"><?= $predef_count ?></div><div class="stat-label">Special</div></div>
        <div class="stat-item"><div class="stat-num" style="color:#5ea0ff;"><?= $transport_count ?></div><div class="stat-label">Transport</div></div>
        <div class="stat-item"><div class="stat-num" style="color:#fff;"><?= $total_count ?></div><div class="stat-label">Total</div></div>
    </div>
</div>

<!-- WHITE CONTENT -->
<div class="content-section">
    <div class="content-inner">

        <!-- Tab Nav -->
        <div class="tab-nav">
            <button class="tab-btn active-customize" onclick="switchTab('customize')">
                🗺️ Customize
                <span class="count-badge" style="background:rgba(245,166,35,0.15);color:#d48a1a;"><?= $custom_count ?></span>
            </button>
            <button class="tab-btn" onclick="switchTab('special')">
                ⭐ Special Packages
                <span class="count-badge" style="background:rgba(46,204,154,0.15);color:#2ecc9a;"><?= $predef_count ?></span>
            </button>
            <button class="tab-btn" onclick="switchTab('transport')">
                ✈️ Transport
                <span class="count-badge" style="background:rgba(94,160,255,0.15);color:#5ea0ff;"><?= $transport_count ?></span>
            </button>
        </div>

        <!-- ① CUSTOMIZE -->
        <div class="section active" id="section-customize">
            <div class="section-heading"><span>🗺️</span> Customize Package Bookings</div>
            <?php if ($custom_count === 0): ?>
            <div class="empty-state">
                <div class="icon">🗺️</div>
                <p>No customize bookings yet</p>
                <a href="customize.php" class="empty-btn"><span class="fa fa-plus"></span> Create Package</a>
            </div>
            <?php else:
                mysqli_data_seek($custom_res, 0);
                while ($b = mysqli_fetch_assoc($custom_res)):
                    $hotel_ids = explode(',', $b['h_id']);
                    $hotel_names = [];
                    foreach ($hotel_ids as $hid) {
                        $hid = intval(trim($hid));
                        $hr = mysqli_query($conn, "SELECT h.h_name, c.c_name FROM hotel h JOIN city c ON h.c_id=c.c_id WHERE h.h_id=$hid");
                        if ($hr && $row = mysqli_fetch_assoc($hr)) $hotel_names[] = $row['h_name'] . ' (' . $row['c_name'] . ')';
                    }
                    $st = strtolower($b['status'] ?? 'confirmed');
                    $st_class = $st === 'pending' ? 'status-pending' : ($st === 'cancelled' ? 'status-cancelled' : 'status-confirmed');
            ?>
            <div class="booking-card">
                <div class="card-top">
                    <div class="card-ref">
                        <span class="icon">🗺️</span>
                        <div>
                            <div><?= htmlspecialchars($b['booking_ref'] ?? 'N/A') ?></div>
                            <div class="ref-num">Customize Package</div>
                        </div>
                    </div>
                    <span class="status-badge <?= $st_class ?>"><?= ucfirst($b['status'] ?? 'Confirmed') ?></span>
                </div>
                <div class="card-body">
                    <div class="detail-grid">
                        <div class="detail-item"><div class="detail-label">Travel Date</div><div class="detail-value"><?= date('d M Y', strtotime($b['date'])) ?></div></div>
                        <div class="detail-item"><div class="detail-label">Duration</div><div class="detail-value"><?= $b['day'] ?> Days</div></div>
                        <div class="detail-item"><div class="detail-label">Hotels</div><div class="detail-value"><?= count($hotel_ids) ?> Hotel<?= count($hotel_ids) > 1 ? 's' : '' ?></div></div>
                        <div class="detail-item"><div class="detail-label">Amount</div><div class="detail-value orange">₹<?= number_format($b['amount']) ?></div></div>
                    </div>
                    <?php if (!empty($hotel_names)): ?>
                    <div class="hotel-tags">
                        <?php foreach ($hotel_names as $hn): ?><span class="hotel-tag"><?= htmlspecialchars($hn) ?></span><?php endforeach; ?>
                    </div>
                    <?php endif; ?>
                </div>
                <div class="card-footer">
                    <div class="amount-big" style="color:#d48a1a;">₹<?= number_format($b['amount']) ?></div>
                    <div class="booked-date">Booked: <?= isset($b['created_at']) ? date('d M Y, h:i A', strtotime($b['created_at'])) : 'N/A' ?></div>
                </div>
            </div>
            <?php endwhile; endif; ?>
        </div>

        <!-- ② SPECIAL -->
        <div class="section" id="section-special">
            <div class="section-heading"><span>⭐</span> Special Package Bookings</div>
            <?php if ($predef_count === 0): ?>
            <div class="empty-state">
                <div class="icon">⭐</div>
                <p>No special package bookings yet</p>
                <a href="packages.php" class="empty-btn"><span class="fa fa-star"></span> Browse Packages</a>
            </div>
            <?php else:
                mysqli_data_seek($predef_res, 0);
                while ($b = mysqli_fetch_assoc($predef_res)):
                    $st = strtolower($b['status'] ?? 'confirmed');
                    $st_class = $st === 'pending' ? 'status-pending' : ($st === 'cancelled' ? 'status-cancelled' : 'status-confirmed');
            ?>
            <div class="booking-card">
                <div class="card-top">
                    <div class="card-ref">
                        <span class="icon">⭐</span>
                        <div>
                            <div><?= htmlspecialchars($b['booking_ref'] ?? 'N/A') ?></div>
                            <div class="ref-num">Special Package</div>
                        </div>
                    </div>
                    <span class="status-badge <?= $st_class ?>"><?= ucfirst($b['status'] ?? 'Confirmed') ?></span>
                </div>
                <div class="card-body">
                    <div class="detail-grid">
                        <div class="detail-item"><div class="detail-label">Package</div><div class="detail-value green"><?= htmlspecialchars($b['pa_name'] ?? 'N/A') ?></div></div>
                        <div class="detail-item"><div class="detail-label">Travel Date</div><div class="detail-value"><?= date('d M Y', strtotime($b['date'])) ?></div></div>
                        <div class="detail-item"><div class="detail-label">Amount</div><div class="detail-value green">₹<?= number_format($b['amount']) ?></div></div>
                        <div class="detail-item"><div class="detail-label">Payment ID</div><div class="detail-value" style="font-size:11px;"><?= htmlspecialchars(substr($b['payment_id'] ?? 'N/A', 0, 18)) ?>...</div></div>
                    </div>
                    <?php if (!empty($b['pkg_hotels'])):
                        $pkg_hids = explode(',', $b['pkg_hotels']);
                        echo '<div class="hotel-tags">';
                        foreach ($pkg_hids as $phid) {
                            $phid = intval(trim($phid));
                            $phr = mysqli_query($conn, "SELECT h.h_name, c.c_name FROM hotel h JOIN city c ON h.c_id=c.c_id WHERE h.h_id=$phid");
                            if ($phr && $prow = mysqli_fetch_assoc($phr))
                                echo '<span class="hotel-tag green">' . htmlspecialchars($prow['h_name'] . ' (' . $prow['c_name'] . ')') . '</span>';
                        }
                        echo '</div>';
                    endif; ?>
                </div>
                <div class="card-footer">
                    <div class="amount-big" style="color:#2ecc9a;">₹<?= number_format($b['amount']) ?></div>
                    <div class="booked-date">Booked: <?= isset($b['created_at']) ? date('d M Y, h:i A', strtotime($b['created_at'])) : 'N/A' ?></div>
                </div>
            </div>
            <?php endwhile; endif; ?>
        </div>

        <!-- ③ TRANSPORT -->
        <div class="section" id="section-transport">
            <div class="section-heading"><span>✈️</span> Transport Bookings</div>
            <?php if ($transport_count === 0): ?>
            <div class="empty-state">
                <div class="icon">✈️</div>
                <p>No transport bookings yet</p>
                <a href="booktrain.php" class="empty-btn"><span class="fa fa-plane"></span> Book Transport</a>
            </div>
            <?php else:
                mysqli_data_seek($transport_res, 0);
                while ($b = mysqli_fetch_assoc($transport_res)):
                    $type    = $b['type'];
                    $icon    = $type === 'flight' ? '✈️' : ($type === 'train' ? '🚂' : '🚗');
                    $color   = $type === 'flight' ? '#5ea0ff' : ($type === 'train' ? '#d48a1a' : '#2ecc9a');
                    $details = json_decode($b['details'] ?? '{}', true);
            ?>
            <div class="booking-card">
                <div class="card-top">
                    <div class="card-ref">
                        <span class="icon"><?= $icon ?></span>
                        <div>
                            <div><?= htmlspecialchars($b['booking_ref'] ?? strtoupper($type) . ' Booking') ?></div>
                            <div class="ref-num"><?= htmlspecialchars($b['from_city']) ?> → <?= htmlspecialchars($b['to_city']) ?></div>
                        </div>
                    </div>
                    <span class="status-badge status-confirmed"><?= $b['status'] ?></span>
                </div>
                <div class="card-body">
                    <div class="detail-grid">
                        <div class="detail-item"><div class="detail-label">From</div><div class="detail-value"><?= htmlspecialchars($b['from_city']) ?></div></div>
                        <div class="detail-item"><div class="detail-label">To</div><div class="detail-value"><?= htmlspecialchars($b['to_city']) ?></div></div>
                        <div class="detail-item"><div class="detail-label">Date</div><div class="detail-value"><?= date('d M Y', strtotime($b['travel_date'])) ?></div></div>
                        <div class="detail-item"><div class="detail-label">Fare</div><div class="detail-value" style="color:<?= $color ?>;">₹<?= number_format($b['fare']) ?></div></div>
                        <?php if ($type === 'flight' && !empty($details['airline'])): ?>
                        <div class="detail-item"><div class="detail-label">Airline</div><div class="detail-value"><?= htmlspecialchars($details['airline']) ?></div></div>
                        <div class="detail-item"><div class="detail-label">Flight</div><div class="detail-value"><?= htmlspecialchars($details['flight_no'] ?? '') ?></div></div>
                        <?php elseif ($type === 'train' && !empty($details['train_name'])): ?>
                        <div class="detail-item"><div class="detail-label">Train</div><div class="detail-value"><?= htmlspecialchars($details['train_name']) ?></div></div>
                        <div class="detail-item"><div class="detail-label">Class</div><div class="detail-value"><?= htmlspecialchars($details['class'] ?? '') ?></div></div>
                        <?php elseif ($type === 'cab' && !empty($details['cab_name'])): ?>
                        <div class="detail-item"><div class="detail-label">Cab</div><div class="detail-value"><?= htmlspecialchars($details['cab_name']) ?></div></div>
                        <div class="detail-item"><div class="detail-label">Distance</div><div class="detail-value"><?= htmlspecialchars($details['distance'] ?? '') ?> km</div></div>
                        <?php endif; ?>
                    </div>
                </div>
                <div class="card-footer">
                    <div class="amount-big" style="color:<?= $color ?>;">₹<?= number_format($b['fare']) ?></div>
                    <div class="booked-date">Booked: <?= date('d M Y, h:i A', strtotime($b['booked_at'])) ?></div>
                </div>
            </div>
            <?php endwhile; endif; ?>
        </div>

    </div>
</div>

<!-- DARK STATS -->
<div class="stats-section">
    <div class="stats-row">
        <div class="s-stat"><div class="s-num" style="color:#f5a623;"><?= $custom_count ?></div><div class="s-label">Customize</div></div>
        <div class="s-stat"><div class="s-num" style="color:#5ecfa8;"><?= $predef_count ?></div><div class="s-label">Special</div></div>
        <div class="s-stat"><div class="s-num" style="color:#5ea0ff;"><?= $transport_count ?></div><div class="s-label">Transport</div></div>
        <div class="s-stat"><div class="s-num" style="color:#fff;"><?= $total_count ?></div><div class="s-label">Total</div></div>
    </div>
</div>

<?php include('footer.php'); ?>

<script>
function switchTab(type) {
    document.querySelectorAll('.section').forEach(s => s.classList.remove('active'));
    document.querySelectorAll('.tab-btn').forEach(b => b.classList.remove('active-customize','active-special','active-transport'));
    document.getElementById('section-' + type).classList.add('active');
    var map = { customize: 0, special: 1, transport: 2 };
    document.querySelectorAll('.tab-btn')[map[type]].classList.add('active-' + type);
}
</script>
</body>
</html>