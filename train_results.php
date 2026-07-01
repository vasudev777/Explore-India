<?php
include('db.php');
session_start();

$from_city  = preg_replace('/[^A-Za-z0-9]/', '', $_POST['from_city'] ?? '');
$to_city    = preg_replace('/[^A-Za-z0-9]/', '', $_POST['to_city'] ?? '');
$date       = $_POST['date']       ?? date('Y-m-d');
$passengers = (int)($_POST['passengers'] ?? 1);
$class      = $_POST['class']      ?? 'SL';

// Get city names from transport_cities
$from_name = $from_city;
$to_name   = $to_city;
$r1 = mysqli_query($conn, "SELECT city_name FROM transport_cities WHERE city_code='".mysqli_real_escape_string($conn,$from_city)."' LIMIT 1");
if ($r1 && $row = mysqli_fetch_assoc($r1)) $from_name = $row['city_name'];
$r2 = mysqli_query($conn, "SELECT city_name FROM transport_cities WHERE city_code='".mysqli_real_escape_string($conn,$to_city)."' LIMIT 1");
if ($r2 && $row = mysqli_fetch_assoc($r2)) $to_name = $row['city_name'];

// RapidAPI Train Search
$trains = [];
$api_error = '';

$curl = curl_init();
curl_setopt_array($curl, [
    CURLOPT_URL => "https://indian-railway-irctc.p.rapidapi.com/api/trains-between-stations?fromStationCode={$from_city}&toStationCode={$to_city}&dateOfJourney=" . date('Ymd', strtotime($date)),
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_TIMEOUT => 10,
    CURLOPT_HTTPHEADER => [
        "X-RapidAPI-Key: d7813542dcmsh804a59410d771cdp147cebjsnf254e5d75d3",
        "X-RapidAPI-Host: indian-railway-irctc.p.rapidapi.com"
    ],
]);
$response = curl_exec($curl);
curl_close($curl);

$data = json_decode($response, true);
if (isset($data['data']) && count($data['data']) > 0) {
    $trains = $data['data'];
} else {
    $api_error = 'Showing sample trains for this route.';
    $trains = [
        ['train_number'=>'12301','train_name'=>'Rajdhani Express','from_station_name'=>$from_name,'to_station_name'=>$to_name,'from_std'=>'16:55','to_std'=>'10:00','duration'=>'17h 05m','classes'=>['SL','3A','2A','1A']],
        ['train_number'=>'12951','train_name'=>'Mumbai Rajdhani','from_station_name'=>$from_name,'to_station_name'=>$to_name,'from_std'=>'17:40','to_std'=>'08:35','duration'=>'14h 55m','classes'=>['3A','2A','1A']],
        ['train_number'=>'12909','train_name'=>'Garib Rath Express','from_station_name'=>$from_name,'to_station_name'=>$to_name,'from_std'=>'21:30','to_std'=>'14:15','duration'=>'16h 45m','classes'=>['3A']],
        ['train_number'=>'19019','train_name'=>'Saurashtra Mail','from_station_name'=>$from_name,'to_station_name'=>$to_name,'from_std'=>'06:20','to_std'=>'23:45','duration'=>'17h 25m','classes'=>['SL','3A','2A']],
    ];
}

// Fare by class
$fares = ['SL'=>450,'3A'=>1200,'2A'=>1800,'1A'=>3000,'CC'=>600];
$base_fare = $fares[$class] ?? 450;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>Train Results – Explore India</title>
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
        .page-hero { padding: 100px 20px 50px; background: linear-gradient(160deg, #0a0a0a 0%, #0d1a0a 50%, #0a0a0a 100%); text-align: center; }
        .eyebrow { font-size: 11px; font-weight: 700; letter-spacing: 4px; text-transform: uppercase; color: rgba(255,255,255,0.35); margin-bottom: 12px; }
        .page-hero h1 { font-family: 'Montserrat', sans-serif; font-size: clamp(24px, 5vw, 48px); font-weight: 900; color: #fff; margin-bottom: 10px; letter-spacing: -1px; text-transform: none !important; }
        .page-hero h1 .orange { color: #f5a623; }
        .page-hero p { font-size: 14px; color: rgba(255,255,255,0.4); margin-bottom: 24px; }
        .modify-btn { display: inline-flex; align-items: center; gap: 6px; font-size: 12px; font-weight: 600; color: #f5a623; background: rgba(245,166,35,0.1); border: 1px solid rgba(245,166,35,0.25); padding: 8px 18px; border-radius: 20px; text-decoration: none; transition: background 0.2s; }
        .modify-btn:hover { background: rgba(245,166,35,0.2); color: #f5a623; text-decoration: none; }

        /* WHITE RESULTS */
        .results-section { background: #fff; padding: 50px 20px 60px; }
        .results-inner { max-width: 900px; margin: 0 auto; }
        .results-header { display: flex; align-items: center; justify-content: space-between; margin-bottom: 24px; flex-wrap: wrap; gap: 10px; }
        .results-count { font-size: 13px; color: #888; }
        .results-count strong { color: #1a1a1a; font-size: 16px; }
        .notice { background: #fff8ec; border: 1px solid #f5a623; border-radius: 10px; padding: 10px 16px; font-size: 12px; color: #c87a00; margin-bottom: 20px; display: flex; align-items: center; gap: 8px; }

        /* Train Card */
        .train-card {
            background: #f8f9fa; border: 1.5px solid #e9ecef;
            border-radius: 16px; padding: 20px 24px; margin-bottom: 14px;
            transition: border-color 0.2s, box-shadow 0.2s;
        }
        .train-card:hover { border-color: #f5a623; box-shadow: 0 4px 20px rgba(245,166,35,0.1); }

        .train-top { display: grid; grid-template-columns: 1fr auto 1fr auto; gap: 16px; align-items: center; margin-bottom: 14px; }
        @media (max-width: 600px) { .train-top { grid-template-columns: 1fr 1fr; gap: 12px; } }

        .train-info .train-name { font-family: 'Montserrat', sans-serif; font-size: 15px; font-weight: 700; color: #1a1a1a; margin-bottom: 3px; }
        .train-info .train-num { font-size: 11px; color: #888; }

        .time-block { text-align: center; }
        .time { font-family: 'Montserrat', sans-serif; font-size: 22px; font-weight: 800; color: #1a1a1a; }
        .station { font-size: 11px; color: #888; margin-top: 2px; }

        .duration-block { text-align: center; min-width: 90px; }
        .dur-line-wrap { display: flex; align-items: center; gap: 6px; margin-bottom: 4px; }
        .dur-line { flex: 1; height: 1px; background: #dee2e6; }
        .dur-train { color: #f5a623; font-size: 13px; }
        .duration-time { font-size: 12px; color: #888; }

        .price-book { text-align: right; }
        .price { font-family: 'Montserrat', sans-serif; font-size: 22px; font-weight: 800; color: #1a1a1a; }
        .price-per { font-size: 11px; color: #aaa; margin-bottom: 8px; }

        /* Classes row */
        .train-classes { display: flex; flex-wrap: wrap; gap: 6px; }
        .class-tag { font-size: 11px; font-weight: 600; padding: 3px 10px; border-radius: 10px; border: 1px solid; }
        .class-tag.avail { color: #2ecc9a; border-color: rgba(46,204,154,0.3); background: rgba(46,204,154,0.07); }
        .class-tag.selected { color: #f5a623; border-color: rgba(245,166,35,0.3); background: rgba(245,166,35,0.1); font-weight: 700; }

        .btn-book { background: linear-gradient(135deg, #f5a623, #d48a1a); border: none; border-radius: 20px; color: #fff; font-size: 12px; font-weight: 700; font-family: 'Montserrat', sans-serif; padding: 9px 20px; cursor: pointer; transition: transform 0.2s, box-shadow 0.2s; text-transform: none !important; white-space: nowrap; }
        .btn-book:hover { transform: translateY(-2px); box-shadow: 0 6px 16px rgba(245,166,35,0.35); }

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
    <p class="eyebrow">🚂 Train Results</p>
    <h1><?= htmlspecialchars($from_name) ?> <span class="orange">→</span> <?= htmlspecialchars($to_name) ?></h1>
    <p><?= date('D, d M Y', strtotime($date)) ?> &nbsp;·&nbsp; <?= $passengers ?> Passenger<?= $passengers>1?'s':'' ?> &nbsp;·&nbsp; <?= $class ?></p>
    <a href="booktrain.php" class="modify-btn"><span class="fa fa-pencil"></span> Modify Search</a>
</div>

<!-- WHITE RESULTS -->
<div class="results-section">
    <div class="results-inner">
        <div class="results-header">
            <div class="results-count">Showing <strong><?= count($trains) ?> trains</strong></div>
        </div>
        <?php if ($api_error): ?>
        <div class="notice"><span class="fa fa-info-circle"></span> <?= $api_error ?></div>
        <?php endif; ?>

        <?php foreach ($trains as $i => $train):
            $tnum  = $train['train_number'] ?? ($train['trainNumber'] ?? 'XXXXX');
            $tname = $train['train_name']   ?? ($train['trainName']   ?? 'Express Train');
            $dep   = $train['from_std']     ?? ($train['departureTime'] ?? '00:00');
            $arr   = $train['to_std']       ?? ($train['arrivalTime']   ?? '00:00');
            $dur   = $train['duration']     ?? '—';
            $classes = $train['classes']    ?? [$class];
            $fare  = $base_fare * $passengers;
        ?>
        <div class="train-card">
            <div class="train-top">
                <div class="train-info">
                    <div class="train-name"><?= htmlspecialchars($tname) ?></div>
                    <div class="train-num">#<?= htmlspecialchars($tnum) ?></div>
                </div>
                <div>
                    <div class="time-block" style="margin-bottom:6px;">
                        <div class="time"><?= htmlspecialchars($dep) ?></div>
                        <div class="station"><?= htmlspecialchars($from_name) ?></div>
                    </div>
                    <div class="duration-block">
                        <div class="dur-line-wrap"><div class="dur-line"></div><span class="dur-train fa fa-train"></span><div class="dur-line"></div></div>
                        <div class="duration-time"><?= htmlspecialchars($dur) ?></div>
                    </div>
                    <div class="time-block" style="margin-top:6px;">
                        <div class="time"><?= htmlspecialchars($arr) ?></div>
                        <div class="station"><?= htmlspecialchars($to_name) ?></div>
                    </div>
                </div>
                <div></div>
                <div class="price-book">
                    <div class="price">₹<?= number_format($fare) ?></div>
                    <div class="price-per">for <?= $passengers ?> pax</div>
                    <form action="pay_transport.php" method="POST">
                        <input type="hidden" name="train_no"   value="<?= htmlspecialchars($tnum) ?>">
                        <input type="hidden" name="train_name" value="<?= htmlspecialchars($tname) ?>">
                        <input type="hidden" name="from"       value="<?= htmlspecialchars($from_name) ?>">
                        <input type="hidden" name="to"         value="<?= htmlspecialchars($to_name) ?>">
                        <input type="hidden" name="dep_time"   value="<?= htmlspecialchars($dep) ?>">
                        <input type="hidden" name="arr_time"   value="<?= htmlspecialchars($arr) ?>">
                        <input type="hidden" name="duration"   value="<?= htmlspecialchars($dur) ?>">
                        <input type="hidden" name="date"       value="<?= htmlspecialchars($date) ?>">
                        <input type="hidden" name="type"       value="train">
                        <input type="hidden" name="passengers" value="<?= $passengers ?>">
                        <input type="hidden" name="class"      value="<?= htmlspecialchars($class) ?>">
                        <input type="hidden" name="price"      value="<?= $fare ?>">
                        <button type="submit" class="btn-book">Select Seats →</button>
                    </form>
                </div>
            </div>
            <div class="train-classes">
                <?php foreach ((array)$classes as $c): ?>
                <span class="class-tag <?= $c === $class ? 'selected' : 'avail' ?>"><?= htmlspecialchars($c) ?></span>
                <?php endforeach; ?>
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