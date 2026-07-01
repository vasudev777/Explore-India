<?php
include('db.php');
session_start();

$pnr    = '';
$result = null;
$error  = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['pnr'])) {
    $pnr     = preg_replace('/[^0-9]/', '', $_POST['pnr']);
    $api_key = 'd7813542dcmsh804a59410d771cdp147cebjsnf254e5d75d3';
    $url     = 'https://indian-railway-irctc.p.rapidapi.com/api/pnr-status?pnrNumber=' . $pnr;

    $ch = curl_init();
    curl_setopt_array($ch, [
        CURLOPT_URL            => $url,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_TIMEOUT        => 12,
        CURLOPT_HTTPHEADER     => [
            'X-RapidAPI-Key: ' . $api_key,
            'X-RapidAPI-Host: indian-railway-irctc.p.rapidapi.com'
        ]
    ]);
    $response = curl_exec($ch);
    curl_close($ch);

    if ($response) {
        $data = json_decode($response, true);
        if (isset($data['data'])) {
            $result = $data['data'];
        } elseif (isset($data['body'])) {
            $result = $data['body'];
        } else {
            $error = 'Invalid PNR or no data found.';
        }
    } else {
        $error = 'Could not connect to Railway API.';
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>PNR Status – Explore India</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="css/bootstrap.css" rel='stylesheet' type='text/css'/>
    <link href="css/style.css" rel='stylesheet' type='text/css'/>
    <link href="css/font-awesome.min.css" rel="stylesheet">
    <link href="//fonts.googleapis.com/css?family=Montserrat:400,600,700,800" rel="stylesheet">
    <link href="//fonts.googleapis.com/css?family=Open+Sans:400,600" rel="stylesheet">
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
        body { background: #0a0a0a; font-family: 'Open Sans', sans-serif; color: #fff; min-height: 100vh; }

        .page-wrap {
            min-height: 100vh;
            padding: 120px 20px 80px;
            background: linear-gradient(160deg, #0a0a0a 0%, #1a0e00 50%, #0a0a0a 100%);
            display: flex; flex-direction: column; align-items: center;
        }
        .hero-badge { display: inline-block; font-size: 10px; font-weight: 700; letter-spacing: 3px; text-transform: uppercase; color: rgba(255,255,255,0.4); border: 1px solid rgba(255,255,255,0.12); padding: 5px 14px; border-radius: 20px; margin-bottom: 16px; }
        h1 { font-family: 'Montserrat', sans-serif; font-size: clamp(26px, 5vw, 44px); font-weight: 800; color: #fff; margin-bottom: 8px; text-transform: none !important; text-align: center; }
        h1 span { color: #f5a623; }
        .sub { font-size: 14px; color: rgba(255,255,255,0.4); text-align: center; margin-bottom: 36px; }

        /* PNR form */
        .pnr-card { background: #141414; border: 1px solid rgba(255,255,255,0.07); border-radius: 20px; padding: 32px 28px; width: 100%; max-width: 480px; margin-bottom: 28px; }
        .field-label { display: block; font-size: 11px; font-weight: 700; letter-spacing: 1px; text-transform: uppercase; color: rgba(255,255,255,0.35); margin-bottom: 8px; }
        .pnr-input {
            width: 100%; background: rgba(255,255,255,0.05); border: 1px solid rgba(255,255,255,0.12);
            border-radius: 10px; color: #fff; font-size: 20px; font-weight: 700;
            padding: 14px 16px; outline: none; letter-spacing: 4px;
            text-align: center; font-family: 'Montserrat', sans-serif;
            transition: border-color 0.2s;
        }
        .pnr-input:focus { border-color: rgba(245,166,35,0.5); }
        .pnr-input::placeholder { font-size: 14px; letter-spacing: 0; font-weight: 400; color: rgba(255,255,255,0.2); }
        .btn-check { width: 100%; border: none; border-radius: 12px; background: linear-gradient(135deg, #f5a623, #d48a1a); color: #000; font-size: 15px; font-weight: 700; font-family: 'Montserrat', sans-serif; padding: 14px; cursor: pointer; transition: transform 0.2s; margin-top: 16px; }
        .btn-check:hover { transform: translateY(-2px); }

        /* Result card */
        .result-card { background: #141414; border: 1px solid rgba(255,255,255,0.07); border-radius: 20px; overflow: hidden; width: 100%; max-width: 600px; }
        .result-header { padding: 18px 24px; border-bottom: 1px solid rgba(255,255,255,0.06); display: flex; align-items: center; justify-content: space-between; }
        .result-header h3 { font-family: 'Montserrat', sans-serif; font-size: 16px; font-weight: 700; color: #fff; text-transform: none !important; }
        .pnr-badge { padding: 4px 12px; border-radius: 20px; background: rgba(245,166,35,0.1); border: 1px solid rgba(245,166,35,0.25); font-size: 13px; font-weight: 700; color: #f5a623; }

        .result-body { padding: 20px 24px; }
        .info-row { display: flex; justify-content: space-between; padding: 10px 0; border-bottom: 1px solid rgba(255,255,255,0.05); }
        .info-row:last-child { border: none; }
        .info-label { font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px; color: rgba(255,255,255,0.35); }
        .info-value { font-size: 13px; font-weight: 600; color: #fff; text-align: right; }

        /* Passengers table */
        .pax-table { width: 100%; border-collapse: collapse; margin-top: 16px; }
        .pax-table th { font-size: 10px; font-weight: 700; text-transform: uppercase; letter-spacing: 1px; color: rgba(255,255,255,0.3); padding: 8px 10px; text-align: left; border-bottom: 1px solid rgba(255,255,255,0.06); }
        .pax-table td { font-size: 13px; color: rgba(255,255,255,0.75); padding: 10px 10px; border-bottom: 1px solid rgba(255,255,255,0.04); }
        .status-badge { padding: 3px 10px; border-radius: 12px; font-size: 11px; font-weight: 700; }
        .status-cnf { background: rgba(94,207,168,0.12); border: 1px solid rgba(94,207,168,0.25); color: #5ecfa8; }
        .status-wl  { background: rgba(245,166,35,0.12); border: 1px solid rgba(245,166,35,0.25); color: #f5a623; }
        .status-can { background: rgba(255,80,80,0.12); border: 1px solid rgba(255,80,80,0.25); color: #ff5050; }

        .alert-box { padding: 14px 18px; border-radius: 10px; font-size: 13px; width: 100%; max-width: 480px; }
        .alert-error { background: rgba(255,80,80,0.1); border: 1px solid rgba(255,80,80,0.25); color: #ff6b6b; }

        .back-btn { display: inline-flex; align-items: center; gap: 6px; color: rgba(255,255,255,0.5); font-size: 13px; text-decoration: none; margin-bottom: 20px; align-self: flex-start; padding-left: calc((100% - 480px)/2); transition: color 0.2s; }
        .back-btn:hover { color: #f5a623; text-decoration: none; }
    </style>
</head>
<body oncontextmenu="return false;">
<?php include('header.php'); ?>

<div class="page-wrap">
    <a href="transport.php" class="back-btn"><span class="fa fa-arrow-left"></span> Back to Transport</a>
    <div class="hero-badge">🎫 PNR Status</div>
    <h1>Check Your <span>PNR</span></h1>
    <p class="sub">Enter your 10-digit PNR number to get live booking status</p>

    <!-- PNR Form -->
    <div class="pnr-card">
        <form action="pnr_status.php" method="POST">
            <label class="field-label">PNR Number</label>
            <input type="number" name="pnr" class="pnr-input"
                   placeholder="Enter 10-digit PNR"
                   value="<?= htmlspecialchars($pnr) ?>"
                   maxlength="10" required>
            <button type="submit" class="btn-check">
                <span class="fa fa-search"></span> &nbsp; Check Status
            </button>
        </form>
    </div>

    <?php if ($error): ?>
    <div class="alert-box alert-error">
        <span class="fa fa-exclamation-circle"></span> <?= $error ?>
    </div>
    <?php endif; ?>

    <?php if ($result): ?>
    <div class="result-card">
        <div class="result-header">
            <h3>🚂 PNR Details</h3>
            <span class="pnr-badge"><?= $pnr ?></span>
        </div>
        <div class="result-body">
            <?php
            $train_name = $result['trainName']   ?? $result['train_name']   ?? 'N/A';
            $train_num  = $result['trainNumber']  ?? $result['train_number'] ?? 'N/A';
            $from       = $result['boardingPoint'] ?? $result['from']        ?? 'N/A';
            $to         = $result['destinationStation'] ?? $result['to']     ?? 'N/A';
            $doj        = $result['dateOfJourney'] ?? $result['doj']         ?? 'N/A';
            $cls        = $result['classType']     ?? $result['class']       ?? 'N/A';
            $chart_st   = $result['chartPrepared'] ?? false;
            $passengers = $result['passengerList'] ?? $result['passengers']  ?? [];
            ?>
            <div class="info-row">
                <span class="info-label">Train</span>
                <span class="info-value"><?= htmlspecialchars($train_name) ?> (#<?= htmlspecialchars($train_num) ?>)</span>
            </div>
            <div class="info-row">
                <span class="info-label">From → To</span>
                <span class="info-value"><?= htmlspecialchars($from) ?> → <?= htmlspecialchars($to) ?></span>
            </div>
            <div class="info-row">
                <span class="info-label">Date of Journey</span>
                <span class="info-value"><?= htmlspecialchars($doj) ?></span>
            </div>
            <div class="info-row">
                <span class="info-label">Class</span>
                <span class="info-value"><?= htmlspecialchars($cls) ?></span>
            </div>
            <div class="info-row">
                <span class="info-label">Chart Status</span>
                <span class="info-value" style="color: <?= $chart_st ? '#5ecfa8' : '#f5a623' ?>">
                    <?= $chart_st ? '✅ Prepared' : '⏳ Not Prepared' ?>
                </span>
            </div>

            <?php if (!empty($passengers)): ?>
            <table class="pax-table">
                <tr>
                    <th>#</th>
                    <th>Booking Status</th>
                    <th>Current Status</th>
                    <th>Coach/Seat</th>
                </tr>
                <?php foreach ($passengers as $pi => $p): ?>
                <tr>
                    <td><?= $pi + 1 ?></td>
                    <td><?= htmlspecialchars($p['bookingStatus'] ?? $p['booking_status'] ?? 'N/A') ?></td>
                    <td>
                        <?php
                        $st = $p['currentStatus'] ?? $p['current_status'] ?? 'N/A';
                        $cls = stripos($st, 'CNF') !== false ? 'status-cnf' : (stripos($st, 'WL') !== false ? 'status-wl' : 'status-can');
                        ?>
                        <span class="status-badge <?= $cls ?>"><?= htmlspecialchars($st) ?></span>
                    </td>
                    <td><?= htmlspecialchars($p['coachNo'] ?? $p['seat'] ?? 'N/A') ?></td>
                </tr>
                <?php endforeach; ?>
            </table>
            <?php endif; ?>
        </div>
    </div>
    <?php endif; ?>
</div>

<?php include('footer.php'); ?>
</body>
</html>