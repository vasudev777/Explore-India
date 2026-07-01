<?php
include('db.php');
session_start();

$from_city_id = $_POST['from_city'] ?? '';
$to_city_id   = $_POST['to_city']   ?? '';
$date         = $_POST['date']       ?? date('Y-m-d');
$time         = $_POST['time']       ?? '10:00';
$cab_type     = $_POST['cab_type']   ?? 'mini';

// Get city details with lat/lng
$from_city = $to_city = [];
$r1 = mysqli_query($conn, "SELECT * FROM transport_cities WHERE city_id='".intval($from_city_id)."' LIMIT 1");
if ($r1) $from_city = mysqli_fetch_assoc($r1);
$r2 = mysqli_query($conn, "SELECT * FROM transport_cities WHERE city_id='".intval($to_city_id)."' LIMIT 1");
if ($r2) $to_city = mysqli_fetch_assoc($r2);

$from_name = $from_city['city_name'] ?? 'Source';
$to_name   = $to_city['city_name']   ?? 'Destination';

// Haversine formula
function haversine($lat1, $lon1, $lat2, $lon2) {
    $R = 6371;
    $dLat = deg2rad($lat2 - $lat1);
    $dLon = deg2rad($lon2 - $lon1);
    $a = sin($dLat/2)*sin($dLat/2) + cos(deg2rad($lat1))*cos(deg2rad($lat2))*sin($dLon/2)*sin($dLon/2);
    $c = 2*atan2(sqrt($a), sqrt(1-$a));
    return round($R * $c);
}

$distance = 500; // default
if ($from_city && $to_city && $from_city['lat'] && $to_city['lat']) {
    $distance = haversine($from_city['lat'], $from_city['lng'], $to_city['lat'], $to_city['lng']);
    if ($distance < 10) $distance = 500; // fallback if same city
}

$duration_hrs = round($distance / 60, 1);
$duration_str = ($duration_hrs >= 1) ? floor($duration_hrs).'h '.round(($duration_hrs - floor($duration_hrs)) * 60).'m' : round($duration_hrs * 60).'m';

$cabs = [
    'mini'  => ['name'=>'Mini',  'emoji'=>'🚗', 'rate'=>12, 'desc'=>'Hatchback · 4 Seats · AC',    'color'=>'#5ea0ff'],
    'sedan' => ['name'=>'Sedan', 'emoji'=>'🚙', 'rate'=>18, 'desc'=>'Sedan · 4 Seats · AC',        'color'=>'#f5a623'],
    'suv'   => ['name'=>'SUV',   'emoji'=>'🚐', 'rate'=>25, 'desc'=>'SUV · 6 Seats · AC · Luxury', 'color'=>'#5ecfa8'],
];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>Cab Results – Explore India</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="css/bootstrap.css" rel='stylesheet' type='text/css'/>
    <link href="css/style.css" rel='stylesheet' type='text/css'/>
    <link href="css/font-awesome.min.css" rel="stylesheet">
    <link href="//fonts.googleapis.com/css?family=Montserrat:400,600,700,800,900" rel="stylesheet">
    <link href="//fonts.googleapis.com/css?family=Open+Sans:400,600" rel="stylesheet">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
        body { background: #fff; font-family: 'Open Sans', sans-serif; color: #1a1a1a; }

        /* DARK HERO */
        .page-hero { padding: 100px 20px 50px; background: linear-gradient(160deg, #0a0a0a 0%, #1a0a00 50%, #0a0a0a 100%); text-align: center; }
        .eyebrow { font-size: 11px; font-weight: 700; letter-spacing: 4px; text-transform: uppercase; color: rgba(255,255,255,0.35); margin-bottom: 12px; }
        .page-hero h1 { font-family: 'Montserrat', sans-serif; font-size: clamp(24px, 5vw, 48px); font-weight: 900; color: #fff; margin-bottom: 10px; letter-spacing: -1px; text-transform: none !important; }
        .page-hero h1 .green { color: #5ecfa8; }
        .page-hero p { font-size: 14px; color: rgba(255,255,255,0.4); margin-bottom: 8px; }
        .dist-badge { display: inline-flex; align-items: center; gap: 6px; font-size: 13px; font-weight: 700; color: #5ecfa8; background: rgba(94,207,168,0.1); border: 1px solid rgba(94,207,168,0.25); padding: 6px 16px; border-radius: 20px; margin: 8px 6px; }
        .modify-btn { display: inline-flex; align-items: center; gap: 6px; font-size: 12px; font-weight: 600; color: #5ecfa8; background: rgba(94,207,168,0.1); border: 1px solid rgba(94,207,168,0.25); padding: 8px 18px; border-radius: 20px; text-decoration: none; transition: background 0.2s; }
        .modify-btn:hover { background: rgba(94,207,168,0.2); color: #5ecfa8; text-decoration: none; }

        /* WHITE RESULTS */
        .results-section { background: #fff; padding: 50px 20px 60px; }
        .results-inner { max-width: 780px; margin: 0 auto; }
        .results-header { margin-bottom: 28px; }
        .results-title { font-family: 'Montserrat', sans-serif; font-size: 20px; font-weight: 800; color: #1a1a1a; margin-bottom: 6px; text-transform: none !important; }
        .results-sub { font-size: 13px; color: #888; }

        /* Cab Card */
        .cab-card {
            background: #f8f9fa; border: 1.5px solid #e9ecef;
            border-radius: 16px; padding: 22px 24px; margin-bottom: 14px;
            display: grid; grid-template-columns: auto 1fr auto;
            gap: 20px; align-items: center;
            transition: border-color 0.2s, box-shadow 0.2s;
            cursor: pointer;
        }
        .cab-card:hover { box-shadow: 0 4px 20px rgba(0,0,0,0.08); }
        .cab-card.recommended { border-color: #f5a623; background: #fffdf7; }
        @media (max-width: 600px) { .cab-card { grid-template-columns: 1fr; gap: 12px; } }

        .cab-emoji-wrap { font-size: 48px; text-align: center; width: 70px; }
        .cab-info .cab-name { font-family: 'Montserrat', sans-serif; font-size: 18px; font-weight: 800; color: #1a1a1a; margin-bottom: 4px; }
        .cab-info .cab-desc { font-size: 12px; color: #888; margin-bottom: 10px; }
        .cab-features { display: flex; flex-wrap: wrap; gap: 6px; }
        .feat-tag { font-size: 11px; font-weight: 600; padding: 3px 10px; border-radius: 10px; border: 1px solid #e0e0e0; background: #fff; color: #555; }
        .recommended-badge { font-size: 10px; font-weight: 700; color: #f5a623; background: rgba(245,166,35,0.1); border: 1px solid rgba(245,166,35,0.3); padding: 2px 8px; border-radius: 10px; margin-left: 8px; }

        .cab-price-book { text-align: right; }
        .rate-tag { font-size: 11px; color: #888; margin-bottom: 4px; }
        .cab-price { font-family: 'Montserrat', sans-serif; font-size: 26px; font-weight: 900; color: #1a1a1a; }
        .cab-price-per { font-size: 11px; color: #aaa; margin-bottom: 12px; }
        .btn-book { border: none; border-radius: 20px; color: #fff; font-size: 12px; font-weight: 700; font-family: 'Montserrat', sans-serif; padding: 10px 22px; cursor: pointer; transition: transform 0.2s, box-shadow 0.2s; text-transform: none !important; white-space: nowrap; }
        .btn-book:hover { transform: translateY(-2px); }

        /* DARK STATS */
        .stats-section { background: #0a0a0a; padding: 50px 20px; }
        .stats-row { display: flex; justify-content: center; gap: 40px; flex-wrap: wrap; }
        .stat-item { text-align: center; }
        .stat-num { font-family: 'Montserrat', sans-serif; font-size: 28px; font-weight: 800; }
        .stat-num.blue { color: #5ea0ff; } .stat-num.orange { color: #f5a623; } .stat-num.green { color: #5ecfa8; }
        .stat-label { font-size: 12px; color: rgba(255,255,255,0.35); margin-top: 4px; }
    </style>
</head>
<body oncontextmenu="return false;">
<?php include('header.php'); ?>

<!-- DARK HERO -->
<div class="page-hero">
    <p class="eyebrow">🚗 Cab Results</p>
    <h1><?= htmlspecialchars($from_name) ?> <span class="green">→</span> <?= htmlspecialchars($to_name) ?></h1>
    <p><?= date('D, d M Y', strtotime($date)) ?> &nbsp;·&nbsp; Pickup at <?= $time ?></p>
    <div>
        <span class="dist-badge"><span class="fa fa-road"></span> <?= $distance ?> km</span>
        <span class="dist-badge"><span class="fa fa-clock-o"></span> ~<?= $duration_str ?></span>
    </div>
    <br>
    <a href="booktrain.php" class="modify-btn"><span class="fa fa-pencil"></span> Modify Search</a>
</div>

<!-- WHITE RESULTS -->
<div class="results-section">
    <div class="results-inner">
        <div class="results-header">
            <div class="results-title">Available Cabs</div>
            <div class="results-sub"><?= $distance ?> km · Est. <?= $duration_str ?> · Intercity</div>
        </div>

        <?php foreach ($cabs as $key => $cab):
            $total = $cab['rate'] * $distance;
            $is_recommended = ($key === 'sedan');
            $is_selected    = ($key === $cab_type);
        ?>
        <div class="cab-card <?= $is_recommended ? 'recommended' : '' ?>">
            <div class="cab-emoji-wrap"><?= $cab['emoji'] ?></div>
            <div class="cab-info">
                <div class="cab-name">
                    <?= $cab['name'] ?>
                    <?php if ($is_recommended): ?><span class="recommended-badge">⭐ Popular</span><?php endif; ?>
                    <?php if ($is_selected): ?><span class="recommended-badge" style="color:#5ecfa8; border-color:rgba(94,207,168,0.3); background:rgba(94,207,168,0.08);">Your Choice</span><?php endif; ?>
                </div>
                <div class="cab-desc"><?= $cab['desc'] ?></div>
                <div class="cab-features">
                    <span class="feat-tag"><span class="fa fa-snowflake-o"></span> AC</span>
                    <span class="feat-tag"><span class="fa fa-shield"></span> Insured</span>
                    <span class="feat-tag"><span class="fa fa-check"></span> GPS Tracked</span>
                    <span class="feat-tag">₹<?= $cab['rate'] ?>/km</span>
                </div>
            </div>
            <div class="cab-price-book">
                <div class="rate-tag">Total Fare</div>
                <div class="cab-price">₹<?= number_format($total) ?></div>
                <div class="cab-price-per"><?= $distance ?> km × ₹<?= $cab['rate'] ?></div>
                <form action="pay_transport.php" method="POST">
                    <input type="hidden" name="type"       value="cab">
                    <input type="hidden" name="cab_type"   value="<?= $key ?>">
                    <input type="hidden" name="cab_name"   value="<?= $cab['name'] ?>">
                    <input type="hidden" name="from"       value="<?= htmlspecialchars($from_name) ?>">
                    <input type="hidden" name="to"         value="<?= htmlspecialchars($to_name) ?>">
                    <input type="hidden" name="date"       value="<?= htmlspecialchars($date) ?>">
                    <input type="hidden" name="time"       value="<?= htmlspecialchars($time) ?>">
                    <input type="hidden" name="distance"   value="<?= $distance ?>">
                    <input type="hidden" name="duration"   value="<?= $duration_str ?>">
                    <input type="hidden" name="price"      value="<?= $total ?>">
                    <button type="submit" class="btn-book" style="background: linear-gradient(135deg, <?= $cab['color'] ?>, <?= $cab['color'] ?>cc);">
                        Book <?= $cab['name'] ?> →
                    </button>
                </form>
            </div>
        </div>
        <?php endforeach; ?>
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
</body>
</html>