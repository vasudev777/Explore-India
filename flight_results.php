<?php
include('db.php');
session_start();

$from_city_id = intval($_POST['from_city'] ?? 0);
$to_city_id   = intval($_POST['to_city']   ?? 0);
$date         = $_POST['date']       ?? date('Y-m-d');
$passengers   = (int)($_POST['passengers'] ?? 1);
$class        = $_POST['class']      ?? 'economy';

// Get city details
$from_city = $to_city = [];
if ($from_city_id) {
    $r = mysqli_query($conn, "SELECT * FROM transport_cities WHERE city_id='" . intval($from_city_id) . "'");
    $from_city = mysqli_fetch_assoc($r);
}
if ($to_city_id) {
    $r = mysqli_query($conn, "SELECT * FROM transport_cities WHERE city_id='" . intval($to_city_id) . "'");
    $to_city = mysqli_fetch_assoc($r);
}

$from_code = $from_city['airport_code'] ?? $from_city_id;
$to_code   = $to_city['airport_code']   ?? $to_city_id;
$from_name = $from_city['city_name']    ?? $from_city_id;
$to_name   = $to_city['city_name']      ?? $to_city_id;

// AviationStack API
$api_key  = '401f56ea04eaf5d97f09c5d6a75fa46a';
$url      = "http://api.aviationstack.com/v1/flights?access_key={$api_key}&dep_iata={$from_code}&arr_iata={$to_code}&limit=20";
$flights  = [];
$api_error = '';

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_TIMEOUT, 10);
$response = curl_exec($ch);
curl_close($ch);

$data = json_decode($response, true);
if (isset($data['data']) && count($data['data']) > 0) {
    $flights = $data['data'];
} else {
    $api_error = 'Showing sample flights for this route.';
    $flights = [
        ['airline'=>['name'=>'IndiGo','iata'=>'6E'],'flight'=>['iata'=>'6E-201'],'departure'=>['iata'=>$from_code,'scheduled'=>$date.'T06:00:00+05:30'],'arrival'=>['iata'=>$to_code,'scheduled'=>$date.'T08:10:00+05:30']],
        ['airline'=>['name'=>'Air India','iata'=>'AI'],'flight'=>['iata'=>'AI-101'],'departure'=>['iata'=>$from_code,'scheduled'=>$date.'T09:30:00+05:30'],'arrival'=>['iata'=>$to_code,'scheduled'=>$date.'T11:45:00+05:30']],
        ['airline'=>['name'=>'SpiceJet','iata'=>'SG'],'flight'=>['iata'=>'SG-301'],'departure'=>['iata'=>$from_code,'scheduled'=>$date.'T13:15:00+05:30'],'arrival'=>['iata'=>$to_code,'scheduled'=>$date.'T15:20:00+05:30']],
        ['airline'=>['name'=>'Vistara','iata'=>'UK'],'flight'=>['iata'=>'UK-501'],'departure'=>['iata'=>$from_code,'scheduled'=>$date.'T16:45:00+05:30'],'arrival'=>['iata'=>$to_code,'scheduled'=>$date.'T18:55:00+05:30']],
        ['airline'=>['name'=>'Go First','iata'=>'G8'],'flight'=>['iata'=>'G8-401'],'departure'=>['iata'=>$from_code,'scheduled'=>$date.'T20:00:00+05:30'],'arrival'=>['iata'=>$to_code,'scheduled'=>$date.'T22:10:00+05:30']],
    ];
}

$prices = ['economy'=>[2499,3299,3999,4599,5199],'business'=>[8999,10999,12499,13999,15499],'first'=>[18999,21999,24999,27999,29999]];
$price_list = $prices[$class] ?? $prices['economy'];
$emoji_map = ['6E'=>'🔵','AI'=>'🔴','SG'=>'🌶️','UK'=>'⭐','G8'=>'🟢'];

function calcDur($dep, $arr) {
    try { $d=new DateTime($dep);$a=new DateTime($arr);$diff=$d->diff($a); return $diff->h.'h '.$diff->i.'m'; } catch(Exception $e) { return '2h 15m'; }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>Flight Results – Explore India</title>
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
        .page-hero {
            padding: 100px 20px 50px;
            background: linear-gradient(160deg, #0a0a0a 0%, #0a1220 50%, #0a0a0a 100%);
            text-align: center;
        }
        .eyebrow { font-size: 11px; font-weight: 700; letter-spacing: 4px; text-transform: uppercase; color: rgba(255,255,255,0.35); margin-bottom: 12px; }
        .page-hero h1 { font-family: 'Montserrat', sans-serif; font-size: clamp(24px, 5vw, 48px); font-weight: 900; color: #fff; margin-bottom: 10px; letter-spacing: -1px; text-transform: none !important; }
        .page-hero h1 .blue { color: #5ea0ff; }
        .page-hero p { font-size: 14px; color: rgba(255,255,255,0.4); margin-bottom: 24px; }
        .modify-btn {
            display: inline-flex; align-items: center; gap: 6px;
            font-size: 12px; font-weight: 600; color: #5ea0ff;
            background: rgba(94,160,255,0.1); border: 1px solid rgba(94,160,255,0.25);
            padding: 8px 18px; border-radius: 20px; text-decoration: none;
            transition: background 0.2s;
        }
        .modify-btn:hover { background: rgba(94,160,255,0.2); color: #5ea0ff; text-decoration: none; }

        /* WHITE RESULTS */
        .results-section { background: #fff; padding: 50px 20px 60px; }
        .results-inner { max-width: 900px; margin: 0 auto; }
        .results-header { display: flex; align-items: center; justify-content: space-between; margin-bottom: 24px; flex-wrap: wrap; gap: 10px; }
        .results-count { font-size: 13px; color: #888; }
        .results-count strong { color: #1a1a1a; font-size: 16px; }

        .notice {
            background: #fff8ec; border: 1px solid #f5a623;
            border-radius: 10px; padding: 10px 16px;
            font-size: 12px; color: #c87a00; margin-bottom: 20px;
            display: flex; align-items: center; gap: 8px;
        }

        /* Flight Card */
        .flight-card {
            background: #f8f9fa;
            border: 1.5px solid #e9ecef;
            border-radius: 16px; padding: 20px 24px;
            margin-bottom: 14px;
            display: grid;
            grid-template-columns: 1fr auto 1fr auto;
            gap: 16px; align-items: center;
            transition: border-color 0.2s, box-shadow 0.2s;
        }
        .flight-card:hover { border-color: #5ea0ff; box-shadow: 0 4px 20px rgba(94,160,255,0.12); }
        @media (max-width: 600px) { .flight-card { grid-template-columns: 1fr 1fr; gap: 12px; } }

        .airline-info { display: flex; align-items: center; gap: 10px; }
        .airline-logo { width: 42px; height: 42px; border-radius: 10px; background: #e8f0fe; border: 1px solid #c5d8ff; display: flex; align-items: center; justify-content: center; font-size: 20px; flex-shrink: 0; }
        .airline-name { font-family: 'Montserrat', sans-serif; font-size: 13px; font-weight: 700; color: #1a1a1a; }
        .flight-no { font-size: 11px; color: #888; margin-top: 2px; }

        .time-block { text-align: center; }
        .time { font-family: 'Montserrat', sans-serif; font-size: 22px; font-weight: 800; color: #1a1a1a; }
        .city-code { font-size: 11px; color: #888; margin-top: 2px; letter-spacing: 1px; }

        .duration-block { text-align: center; min-width: 100px; }
        .dur-line-wrap { display: flex; align-items: center; gap: 6px; margin-bottom: 4px; }
        .dur-line { flex: 1; height: 1px; background: #dee2e6; }
        .dur-plane { color: #5ea0ff; font-size: 13px; }
        .duration-time { font-size: 12px; color: #888; }
        .direct-badge { font-size: 10px; font-weight: 600; color: #2ecc9a; background: rgba(46,204,154,0.1); border: 1px solid rgba(46,204,154,0.3); padding: 2px 8px; border-radius: 10px; margin-top: 4px; display: inline-block; }

        .price-book { text-align: right; }
        .price-label { font-size: 10px; color: #888; text-transform: uppercase; letter-spacing: 1px; margin-bottom: 4px; }
        .price { font-family: 'Montserrat', sans-serif; font-size: 22px; font-weight: 800; color: #1a1a1a; }
        .price-per { font-size: 11px; color: #aaa; }
        .class-badge { display: inline-block; font-size: 10px; font-weight: 600; color: #5ea0ff; background: rgba(94,160,255,0.08); border: 1px solid rgba(94,160,255,0.2); padding: 2px 8px; border-radius: 10px; margin: 4px 0 8px; text-transform: capitalize; }
        .btn-book {
            background: linear-gradient(135deg, #5ea0ff, #3a7fd4);
            border: none; border-radius: 20px; color: #fff;
            font-size: 12px; font-weight: 700; font-family: 'Montserrat', sans-serif;
            padding: 9px 20px; cursor: pointer;
            transition: transform 0.2s, box-shadow 0.2s;
            text-transform: none !important; white-space: nowrap;
        }
        .btn-book:hover { transform: translateY(-2px); box-shadow: 0 6px 16px rgba(94,160,255,0.35); }

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
    <p class="eyebrow">✈️ Flight Results</p>
    <h1><?= htmlspecialchars($from_name) ?> <span class="blue">→</span> <?= htmlspecialchars($to_name) ?></h1>
    <p><?= date('D, d M Y', strtotime($date)) ?> &nbsp;·&nbsp; <?= $passengers ?> Passenger<?= $passengers>1?'s':'' ?> &nbsp;·&nbsp; <?= ucfirst($class) ?></p>
    <a href="booktrain.php" class="modify-btn"><span class="fa fa-pencil"></span> Modify Search</a>
</div>

<!-- WHITE RESULTS -->
<div class="results-section">
    <div class="results-inner">
        <div class="results-header">
            <div class="results-count">Showing <strong><?= count($flights) ?> flights</strong></div>
        </div>
        <?php if ($api_error): ?>
        <div class="notice"><span class="fa fa-info-circle"></span> <?= $api_error ?></div>
        <?php endif; ?>

        <?php foreach ($flights as $i => $f):
            $dep = isset($f['departure']['scheduled']) ? date('H:i', strtotime($f['departure']['scheduled'])) : '--:--';
            $arr = isset($f['arrival']['scheduled'])   ? date('H:i', strtotime($f['arrival']['scheduled']))   : '--:--';
            $dur = calcDur($f['departure']['scheduled'] ?? '', $f['arrival']['scheduled'] ?? '');
            $aname = $f['airline']['name'] ?? 'Airline';
            $aiata = $f['airline']['iata'] ?? '??';
            $fno   = $f['flight']['iata']  ?? 'FL-000';
            $emoji = $emoji_map[$aiata] ?? '✈️';
            $price = $price_list[$i % count($price_list)] * $passengers;
        ?>
        <div class="flight-card">
            <div class="airline-info">
                <div class="airline-logo"><?= $emoji ?></div>
                <div>
                    <div class="airline-name"><?= htmlspecialchars($aname) ?></div>
                    <div class="flight-no"><?= htmlspecialchars($fno) ?></div>
                </div>
            </div>
            <div>
                <div class="time-block" style="margin-bottom:8px;">
                    <div class="time"><?= $dep ?></div>
                    <div class="city-code"><?= $from_code ?></div>
                </div>
                <div class="duration-block">
                    <div class="dur-line-wrap"><div class="dur-line"></div><span class="dur-plane fa fa-plane"></span><div class="dur-line"></div></div>
                    <div class="duration-time"><?= $dur ?></div>
                    <span class="direct-badge">Non-stop</span>
                </div>
                <div class="time-block" style="margin-top:8px;">
                    <div class="time"><?= $arr ?></div>
                    <div class="city-code"><?= $to_code ?></div>
                </div>
            </div>
            <div></div>
            <div class="price-book">
                <div class="price-label">Total</div>
                <div class="price">₹<?= number_format($price) ?></div>
                <div class="price-per">for <?= $passengers ?> pax</div>
                <div class="class-badge"><?= $class ?></div><br>
                <form action="flight_seat.php" method="POST">
                    <input type="hidden" name="flight_no"  value="<?= htmlspecialchars($fno) ?>">
                    <input type="hidden" name="airline"    value="<?= htmlspecialchars($aname) ?>">
                    <input type="hidden" name="from"       value="<?= $from_code ?>">
                    <input type="hidden" name="to"         value="<?= $to_code ?>">
                    <input type="hidden" name="dep_time"   value="<?= $dep ?>">
                    <input type="hidden" name="arr_time"   value="<?= $arr ?>">
                    <input type="hidden" name="duration"   value="<?= $dur ?>">
                    <input type="hidden" name="date"       value="<?= htmlspecialchars($date) ?>">
                    <input type="hidden" name="passengers" value="<?= $passengers ?>">
                    <input type="hidden" name="class"      value="<?= htmlspecialchars($class) ?>">
                    <input type="hidden" name="price"      value="<?= $price ?>">
                    <button type="submit" class="btn-book">Select Seats →</button>
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