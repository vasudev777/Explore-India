<?php
include('db.php');
session_start();
if (!isset($_SESSION['uemail'])) { header('Location: login.php'); exit; }
if (!isset($_POST['type']))      { header('Location: packages.php'); exit; }

$type    = htmlspecialchars($_POST['type']);
$cust_id = intval($_SESSION['ucust_id']);

mysqli_query($conn, "CREATE TABLE IF NOT EXISTS transport_bookings (
    booking_id  INT AUTO_INCREMENT PRIMARY KEY,
    cust_id     INT NOT NULL,
    type        VARCHAR(10) NOT NULL,
    from_city   VARCHAR(100),
    to_city     VARCHAR(100),
    travel_date DATE,
    details     TEXT,
    fare        DECIMAL(10,2),
    status      VARCHAR(20) DEFAULT 'Confirmed',
    booked_at   TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)");

$details = json_encode($_POST);
$from    = mysqli_real_escape_string($conn, $_POST['from'] ?? $_POST['from_city'] ?? '');
$to      = mysqli_real_escape_string($conn, $_POST['to']   ?? $_POST['to_city']   ?? '');
$date    = mysqli_real_escape_string($conn, $_POST['date'] ?? date('Y-m-d'));
$fare    = floatval($_POST['price'] ?? $_POST['total'] ?? $_POST['fare'] ?? 0);

$sql = "INSERT INTO transport_bookings (cust_id, type, from_city, to_city, travel_date, details, fare)
        VALUES ($cust_id, '$type', '$from', '$to', '$date', '".mysqli_real_escape_string($conn,$details)."', $fare)";
mysqli_query($conn, $sql);
$booking_id  = mysqli_insert_id($conn);
$booking_ref = strtoupper($type) . str_pad($booking_id, 6, '0', STR_PAD_LEFT);

if ($type === 'flight') {
    $icon = '✈️'; $color = '#5ea0ff';
    $hero_bg = 'linear-gradient(160deg, #0a0a0a 0%, #0d1220 50%, #0a0a0a 100%)';
    $summary = [
        'Flight'     => htmlspecialchars($_POST['flight_no'] ?? ''),
        'Airline'    => htmlspecialchars($_POST['airline']   ?? ''),
        'Route'      => htmlspecialchars($_POST['from'] ?? '') . ' → ' . htmlspecialchars($_POST['to'] ?? ''),
        'Date'       => date('D, d M Y', strtotime($date)),
        'Departure'  => htmlspecialchars($_POST['dep_time']  ?? ''),
        'Arrival'    => htmlspecialchars($_POST['arr_time']  ?? ''),
        'Duration'   => htmlspecialchars($_POST['duration']  ?? ''),
        'Class'      => ucfirst($_POST['class'] ?? ''),
        'Passengers' => intval($_POST['passengers'] ?? 1),
        'Seats'      => htmlspecialchars($_POST['seats'] ?? 'N/A'),
    ];
} elseif ($type === 'train') {
    $icon = '🚂'; $color = '#f5a623';
    $hero_bg = 'linear-gradient(160deg, #0a0a0a 0%, #0d1a0a 50%, #0a0a0a 100%)';
    $summary = [
        'Train'      => htmlspecialchars($_POST['train_name'] ?? '') . ' #' . ($_POST['train_no'] ?? ''),
        'Route'      => htmlspecialchars($from) . ' → ' . htmlspecialchars($to),
        'Date'       => date('D, d M Y', strtotime($date)),
        'Departure'  => htmlspecialchars($_POST['dep_time'] ?? ''),
        'Arrival'    => htmlspecialchars($_POST['arr_time'] ?? ''),
        'Duration'   => htmlspecialchars($_POST['duration'] ?? ''),
        'Class'      => htmlspecialchars($_POST['class']    ?? ''),
        'Passengers' => intval($_POST['passengers']         ?? 1),
        'Seats'      => htmlspecialchars($_POST['seats']    ?? 'N/A'),
    ];
} else {
    $icon = '🚕'; $color = '#5ecfa8';
    $hero_bg = 'linear-gradient(160deg, #0a0a0a 0%, #001a0d 50%, #0a0a0a 100%)';
    $summary = [
        'Cab Type'    => htmlspecialchars($_POST['cab_name']  ?? ''),
        'Route'       => htmlspecialchars($from) . ' → ' . htmlspecialchars($to),
        'Date'        => date('D, d M Y', strtotime($date)),
        'Pickup Time' => htmlspecialchars($_POST['time']      ?? ''),
        'Distance'    => number_format(intval($_POST['distance'] ?? 0)) . ' km',
        'Duration'    => htmlspecialchars($_POST['duration']  ?? ''),
    ];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>Booking Confirmed – Explore India</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="css/bootstrap.css" rel='stylesheet' type='text/css'/>
    <link href="css/style.css" rel='stylesheet' type='text/css'/>
    <link href="css/font-awesome.min.css" rel="stylesheet">
    <link href="//fonts.googleapis.com/css?family=Montserrat:400,600,700,800" rel="stylesheet">
    <link href="//fonts.googleapis.com/css?family=Open+Sans:400,600" rel="stylesheet">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
        body { background: #fff; font-family: 'Open Sans', sans-serif; color: #1a1a1a; overflow-x: hidden; }

        /* DARK HERO */
        .page-hero {
            background: <?= $hero_bg ?>;
            padding: 100px 20px 60px;
            text-align: center;
        }
        .hero-badge { display: inline-block; font-size: 10px; font-weight: 700; letter-spacing: 3px; text-transform: uppercase; color: rgba(255,255,255,0.4); border: 1px solid rgba(255,255,255,0.12); padding: 5px 14px; border-radius: 20px; margin-bottom: 16px; }
        .page-hero h1 { font-family: 'Montserrat', sans-serif; font-size: clamp(26px, 5vw, 46px); font-weight: 900; color: #fff; margin-bottom: 8px; text-transform: none !important; }
        .page-hero h1 span { color: <?= $color ?>; }
        .page-hero p { font-size: 14px; color: rgba(255,255,255,0.4); margin-bottom: 20px; }

        /* Ref badge in hero */
        .ref-badge {
            display: inline-flex; align-items: center; gap: 10px;
            background: rgba(255,255,255,0.06); border: 1px solid rgba(255,255,255,0.15);
            border-radius: 30px; padding: 10px 22px;
        }
        .ref-label { font-size: 11px; font-weight: 700; letter-spacing: 2px; text-transform: uppercase; color: rgba(255,255,255,0.4); }
        .ref-num { font-family: 'Montserrat', sans-serif; font-size: 20px; font-weight: 800; color: <?= $color ?>; letter-spacing: 2px; }
        .copy-btn { background: none; border: none; color: rgba(255,255,255,0.3); cursor: pointer; font-size: 14px; padding: 0; transition: color 0.2s; }
        .copy-btn:hover { color: <?= $color ?>; }

        /* WHITE CONTENT */
        .content-section { background: #fff; padding: 50px 20px 60px; }
        .content-inner { max-width: 580px; margin: 0 auto; }

        /* Success banner */
        .success-banner {
            background: #f0fff8; border: 1.5px solid #2ecc9a;
            border-radius: 14px; padding: 16px 20px;
            display: flex; align-items: center; gap: 14px;
            margin-bottom: 28px;
        }
        .success-icon { font-size: 28px; flex-shrink: 0; }
        .success-text h4 { font-family: 'Montserrat', sans-serif; font-size: 15px; font-weight: 700; color: #1a1a1a; margin-bottom: 3px; text-transform: none !important; }
        .success-text p { font-size: 12px; color: #666; margin: 0; }

        /* Ticket card */
        .ticket-card {
            background: #f8f9fa; border: 1.5px solid #e9ecef;
            border-radius: 20px; overflow: hidden;
            margin-bottom: 24px;
        }
        .ticket-header {
            padding: 18px 24px 14px; border-bottom: 1px solid #e9ecef;
            display: flex; align-items: center; gap: 12px;
            background: #fff;
        }
        .ticket-icon { width: 42px; height: 42px; border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 20px; flex-shrink: 0; }
        .ticket-header h3 { font-family: 'Montserrat', sans-serif; font-size: 15px; font-weight: 700; color: #1a1a1a; margin-bottom: 2px; text-transform: none !important; }
        .ticket-header p { font-size: 12px; color: #888; margin: 0; }
        .confirmed-badge { margin-left: auto; padding: 4px 12px; border-radius: 20px; background: #f0fff8; border: 1px solid #2ecc9a; font-size: 11px; font-weight: 700; color: #2ecc9a; white-space: nowrap; }

        /* Route visual */
        .route-visual {
            padding: 18px 24px; border-bottom: 1px solid #e9ecef;
            display: flex; align-items: center; gap: 12px;
            background: #fff;
        }
        .route-city { flex: 1; }
        .city-name { font-family: 'Montserrat', sans-serif; font-size: 20px; font-weight: 800; color: #1a1a1a; }
        .city-sub { font-size: 11px; color: #aaa; margin-top: 2px; }
        .route-arrow { font-size: 22px; flex-shrink: 0; }

        /* Details */
        .ticket-body { padding: 14px 24px; }
        .info-row { display: flex; justify-content: space-between; align-items: flex-start; padding: 9px 0; border-bottom: 1px solid #f0f0f0; }
        .info-row:last-child { border: none; }
        .info-label { font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px; color: #aaa; }
        .info-value { font-size: 13px; font-weight: 600; color: #1a1a1a; text-align: right; max-width: 60%; }

        /* Separator */
        .ticket-sep { display: flex; align-items: center; }
        .sep-circle-l, .sep-circle-r { width: 20px; height: 20px; border-radius: 50%; background: #fff; flex-shrink: 0; border: 1.5px solid #e9ecef; }
        .sep-line { flex: 1; border-top: 2px dashed #e9ecef; }

        /* Total */
        .ticket-total { padding: 16px 24px 20px; display: flex; justify-content: space-between; align-items: center; background: #fff; }
        .total-label { font-size: 12px; font-weight: 700; text-transform: uppercase; letter-spacing: 1px; color: #aaa; }
        .total-amount { font-family: 'Montserrat', sans-serif; font-size: 28px; font-weight: 900; color: <?= $color ?>; }

        /* Buttons */
        .action-btns { display: flex; gap: 12px; }
        .btn-action { flex: 1; padding: 13px; border-radius: 12px; font-size: 14px; font-weight: 700; font-family: 'Montserrat', sans-serif; cursor: pointer; border: none; transition: transform 0.2s, box-shadow 0.2s; text-align: center; text-decoration: none; display: flex; align-items: center; justify-content: center; gap: 7px; text-transform: none !important; }
        .btn-action:hover { transform: translateY(-2px); text-decoration: none; }
        .btn-home { background: #f0f0f0; color: #1a1a1a; }
        .btn-history { color: #000; }

        /* DARK STATS */
        .stats-section { background: #0a0a0a; padding: 50px 20px; }
        .stats-row { display: flex; justify-content: center; gap: 40px; flex-wrap: wrap; }
        .stat-item { text-align: center; }
        .stat-num { font-family: 'Montserrat', sans-serif; font-size: 28px; font-weight: 800; }
        .stat-num.blue { color: #5ea0ff; } .stat-num.orange { color: #f5a623; } .stat-num.green { color: #5ecfa8; }
        .stat-label { font-size: 12px; color: rgba(255,255,255,0.35); margin-top: 4px; }

        @media (max-width: 500px) { .action-btns { flex-direction: column; } }
    </style>
</head>
<body oncontextmenu="return false;">
<?php include('header.php'); ?>

<!-- DARK HERO -->
<div class="page-hero">
    <div class="hero-badge"><?= $icon ?> Booking Confirmed</div>
    <h1>Booking <span>Confirmed!</span></h1>
    <p>Your <?= ucfirst($type) ?> booking has been confirmed successfully.</p>
    <div class="ref-badge">
        <span class="ref-label">Booking Ref</span>
        <span class="ref-num" id="refNum"><?= $booking_ref ?></span>
        <button class="copy-btn" onclick="copyRef()" title="Copy"><span class="fa fa-copy"></span></button>
    </div>
</div>

<!-- WHITE CONTENT -->
<div class="content-section">
    <div class="content-inner">

        <!-- Success Banner -->
        <div class="success-banner">
            <div class="success-icon">✅</div>
            <div class="success-text">
                <h4>Payment Successful!</h4>
                <p>Your booking is confirmed. Check My Bookings for details.</p>
            </div>
        </div>

        <!-- Ticket -->
        <div class="ticket-card">
            <div class="ticket-header">
                <div class="ticket-icon" style="background:<?= $color ?>15; border:1px solid <?= $color ?>40;">
                    <?= $icon ?>
                </div>
                <div>
                    <h3><?= ucfirst($type) ?> Ticket</h3>
                    <p><?= date('D, d M Y', strtotime($date)) ?></p>
                </div>
                <div class="confirmed-badge">✅ Confirmed</div>
            </div>

            <div class="route-visual">
                <div class="route-city">
                    <div class="city-name"><?= htmlspecialchars($from) ?></div>
                    <div class="city-sub">Origin</div>
                </div>
                <div class="route-arrow"><?= $icon ?></div>
                <div class="route-city" style="text-align:right;">
                    <div class="city-name"><?= htmlspecialchars($to) ?></div>
                    <div class="city-sub">Destination</div>
                </div>
            </div>

            <div class="ticket-body">
                <?php foreach ($summary as $label => $value): ?>
                <div class="info-row">
                    <span class="info-label"><?= $label ?></span>
                    <span class="info-value"><?= $value ?></span>
                </div>
                <?php endforeach; ?>
            </div>

            <div class="ticket-sep">
                <div class="sep-circle-l"></div>
                <div class="sep-line"></div>
                <div class="sep-circle-r"></div>
            </div>

            <div class="ticket-total">
                <div class="total-label">Total Fare</div>
                <div class="total-amount">₹<?= number_format($fare) ?></div>
            </div>
        </div>

        <!-- Buttons -->
        <div class="action-btns">
            <a href="index.php" class="btn-action btn-home">
                <span class="fa fa-home"></span> Home
            </a>
            <a href="cust_history.php" class="btn-action btn-history" style="background: linear-gradient(135deg, <?= $color ?>, <?= $color ?>cc);">
                <span class="fa fa-history"></span> My Bookings
            </a>
        </div>

    </div>
</div>

<!-- DARK STATS -->
<div class="stats-section">
    <div class="stats-row">
        <div class="stat-item"><div class="stat-num blue">500+</div><div class="stat-label">Flight Routes</div></div>
        <div class="stat-item"><div class="stat-num orange">1000+</div><div class="stat-label">Train Routes</div></div>
        <div class="stat-item"><div class="stat-num green">40</div><div class="stat-label">Cities Covered</div></div>
    </div>
</div>

<?php include('footer.php'); ?>

<script>
function copyRef() {
    var text = document.getElementById('refNum').textContent;
    navigator.clipboard.writeText(text).then(function() {
        var btn = document.querySelector('.copy-btn');
        btn.innerHTML = '<span class="fa fa-check"></span>';
        setTimeout(function() { btn.innerHTML = '<span class="fa fa-copy"></span>'; }, 2000);
    });
}
</script>
</body>
</html>