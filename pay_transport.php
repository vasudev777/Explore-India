<?php
include('db.php');
session_start();
if (!isset($_SESSION['uemail'])) { header('Location: login.php'); exit; }

$cust_id = intval($_SESSION['ucust_id']);
$r = mysqli_query($conn, "SELECT * FROM customer_details WHERE cust_id=$cust_id");
$cust = mysqli_fetch_assoc($r);

// Transport type
$type  = htmlspecialchars($_POST['type']  ?? '');
$from  = htmlspecialchars($_POST['from']  ?? $_POST['from_city'] ?? '');
$to    = htmlspecialchars($_POST['to']    ?? $_POST['to_city']   ?? '');
$date  = htmlspecialchars($_POST['date']  ?? date('Y-m-d'));
$price = floatval($_POST['price'] ?? $_POST['total'] ?? 0);

// Type specific
if ($type === 'flight') {
    $icon        = '✈️';
    $color       = '#5ea0ff';
    $hero_bg     = 'linear-gradient(160deg, #0a0a0a 0%, #0d1220 50%, #0a0a0a 100%)';
    $title       = 'Flight Booking';
    $extra_lines = [
        'Flight'     => htmlspecialchars($_POST['flight_no'] ?? ''),
        'Airline'    => htmlspecialchars($_POST['airline']   ?? ''),
        'Departure'  => htmlspecialchars($_POST['dep_time']  ?? ''),
        'Arrival'    => htmlspecialchars($_POST['arr_time']  ?? ''),
        'Duration'   => htmlspecialchars($_POST['duration']  ?? ''),
        'Class'      => ucfirst($_POST['class'] ?? ''),
        'Passengers' => intval($_POST['passengers'] ?? 1),
        'Seats'      => htmlspecialchars($_POST['seats'] ?? 'N/A'),
    ];
} elseif ($type === 'train') {
    $icon        = '🚂';
    $color       = '#f5a623';
    $hero_bg     = 'linear-gradient(160deg, #0a0a0a 0%, #0d1a0a 50%, #0a0a0a 100%)';
    $title       = 'Train Booking';
    $extra_lines = [
        'Train'      => htmlspecialchars($_POST['train_name'] ?? '') . ' #' . ($_POST['train_no'] ?? ''),
        'Departure'  => htmlspecialchars($_POST['dep_time']   ?? ''),
        'Arrival'    => htmlspecialchars($_POST['arr_time']   ?? ''),
        'Duration'   => htmlspecialchars($_POST['duration']   ?? ''),
        'Class'      => htmlspecialchars($_POST['class']      ?? ''),
        'Passengers' => intval($_POST['passengers']           ?? 1),
        'Seats'      => htmlspecialchars($_POST['seats']      ?? 'N/A'),
    ];
} else { // cab
    $icon        = '🚗';
    $color       = '#5ecfa8';
    $hero_bg     = 'linear-gradient(160deg, #0a0a0a 0%, #001a0d 50%, #0a0a0a 100%)';
    $title       = 'Cab Booking';
    $extra_lines = [
        'Cab Type'    => htmlspecialchars($_POST['cab_name']  ?? ''),
        'Pickup Time' => htmlspecialchars($_POST['time']      ?? ''),
        'Distance'    => number_format(intval($_POST['distance'] ?? 0)) . ' km',
        'Duration'    => htmlspecialchars($_POST['duration']  ?? ''),
    ];
}

// Store all POST in session for success page
$_SESSION['pay_type']        = $type;
$_SESSION['pay_total']       = $price;
$_SESSION['pay_date']        = $date;
$_SESSION['pay_transport']   = $_POST;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>Payment – <?= $title ?> – Explore India</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="css/bootstrap.css" rel='stylesheet' type='text/css'/>
    <link href="css/style.css" rel='stylesheet' type='text/css'/>
    <link href="css/font-awesome.min.css" rel="stylesheet">
    <link href="//fonts.googleapis.com/css?family=Montserrat:400,600,700,800" rel="stylesheet">
    <link href="//fonts.googleapis.com/css?family=Open+Sans:400,600" rel="stylesheet">
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
        body { background: #f5f6fa; font-family: 'Open Sans', sans-serif; color: #1a1a1a; }

        /* DARK HERO */
        .page-hero { background: <?= $hero_bg ?>; padding: 100px 20px 50px; text-align: center; }
        .hero-badge { display: inline-block; font-size: 10px; font-weight: 700; letter-spacing: 3px; text-transform: uppercase; color: rgba(255,255,255,0.4); border: 1px solid rgba(255,255,255,0.12); padding: 5px 14px; border-radius: 20px; margin-bottom: 14px; }
        .page-hero h1 { font-family: 'Montserrat', sans-serif; font-size: clamp(24px, 5vw, 42px); font-weight: 900; color: #fff; margin-bottom: 8px; text-transform: none !important; }
        .page-hero p { font-size: 14px; color: rgba(255,255,255,0.4); }

        /* WHITE CONTENT */
        .content-section { padding: 40px 20px 80px; }
        .content-inner { max-width: 520px; margin: 0 auto; }

        .pay-card { background: #fff; border: 1.5px solid #e9ecef; border-radius: 20px; overflow: hidden; box-shadow: 0 8px 40px rgba(0,0,0,0.08); }

        .pay-header { padding: 20px 24px 16px; border-bottom: 1px solid #e9ecef; display: flex; align-items: center; gap: 12px; background: #fff; }
        .pay-icon { width: 42px; height: 42px; border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 20px; flex-shrink: 0; }
        .pay-header h3 { font-family: 'Montserrat', sans-serif; font-size: 16px; font-weight: 700; color: #1a1a1a; margin: 0; text-transform: none !important; }
        .pay-header p { font-size: 12px; color: #888; margin: 0; }

        .pay-body { padding: 22px 24px; }

        /* Route Visual */
        .route-visual { background: #f8f9fa; border-radius: 12px; padding: 16px 18px; margin-bottom: 18px; display: flex; align-items: center; gap: 12px; }
        .route-city { flex: 1; }
        .city-name { font-family: 'Montserrat', sans-serif; font-size: 18px; font-weight: 800; color: #1a1a1a; }
        .city-sub { font-size: 11px; color: #aaa; margin-top: 2px; }
        .route-arrow { font-size: 20px; flex-shrink: 0; }

        /* Order Summary */
        .order-summary { border: 1px solid #e9ecef; border-radius: 12px; overflow: hidden; margin-bottom: 18px; }
        .summary-row { display: flex; justify-content: space-between; padding: 9px 16px; border-bottom: 1px solid #f0f0f0; font-size: 13px; }
        .summary-row:last-child { border: none; }
        .summary-label { color: #888; }
        .summary-value { font-weight: 600; color: #1a1a1a; text-align: right; max-width: 55%; }

        /* Total */
        .total-row { display: flex; justify-content: space-between; align-items: center; background: #f8f9fa; border-radius: 12px; padding: 14px 16px; margin-bottom: 18px; }
        .total-label { font-size: 13px; font-weight: 700; color: #888; text-transform: uppercase; letter-spacing: 1px; }
        .total-amount { font-family: 'Montserrat', sans-serif; font-size: 24px; font-weight: 900; }

        /* Customer */
        .cust-info { background: #f8f9fa; border-radius: 12px; padding: 12px 16px; margin-bottom: 18px; }
        .cust-row { display: flex; gap: 10px; align-items: center; font-size: 13px; color: #555; padding: 4px 0; }
        .cust-row .fa { width: 16px; }

        .divider { border: none; border-top: 1px solid #e9ecef; margin: 18px 0; }

        .btn-pay { width: 100%; border: none; border-radius: 12px; font-size: 15px; font-weight: 700; font-family: 'Montserrat', sans-serif; padding: 15px; cursor: pointer; transition: transform 0.2s, box-shadow 0.2s; text-transform: none !important; }
        .btn-pay:hover { transform: translateY(-2px); }

        .secure-note { text-align: center; font-size: 11px; color: #aaa; margin-top: 12px; }
        .secure-note .fa { color: #2ecc9a; }

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
    <div class="hero-badge"><?= $icon ?> Secure Payment</div>
    <h1>Complete Your Booking</h1>
    <p>Powered by Razorpay — 100% Secure</p>
</div>

<!-- WHITE CONTENT -->
<div class="content-section">
    <div class="content-inner">
        <div class="pay-card">
            <div class="pay-header">
                <div class="pay-icon" style="background:<?= $color ?>15; border:1px solid <?= $color ?>40;">
                    <?= $icon ?>
                </div>
                <div>
                    <h3><?= $title ?></h3>
                    <p><?= $from ?> → <?= $to ?> · <?= date('d M Y', strtotime($date)) ?></p>
                </div>
            </div>
            <div class="pay-body">

                <!-- Route -->
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

                <!-- Details -->
                <div class="order-summary">
                    <div class="summary-row">
                        <span class="summary-label">Date</span>
                        <span class="summary-value"><?= date('D, d M Y', strtotime($date)) ?></span>
                    </div>
                    <?php foreach ($extra_lines as $label => $val): ?>
                    <div class="summary-row">
                        <span class="summary-label"><?= $label ?></span>
                        <span class="summary-value"><?= $val ?></span>
                    </div>
                    <?php endforeach; ?>
                </div>

                <!-- Total -->
                <div class="total-row">
                    <span class="total-label">Total Fare</span>
                    <span class="total-amount" style="color:<?= $color ?>">₹<?= number_format($price) ?></span>
                </div>

                <!-- Customer -->
                <div class="cust-info">
                    <div class="cust-row"><span class="fa fa-user" style="color:<?= $color ?>"></span> <?= htmlspecialchars($cust['cust_fname'].' '.$cust['cust_lname']) ?></div>
                    <div class="cust-row"><span class="fa fa-envelope" style="color:<?= $color ?>"></span> <?= htmlspecialchars($cust['cust_email']) ?></div>
                    <div class="cust-row"><span class="fa fa-phone" style="color:<?= $color ?>"></span> <?= htmlspecialchars($cust['cust_mobile']) ?></div>
                </div>

                <hr class="divider">

                <button class="btn-pay" id="rzp-button"
                    style="background: linear-gradient(135deg, <?= $color ?>, <?= $color ?>cc); color: <?= $type==='train'?'#fff':'#000' ?>">
                    <span class="fa fa-lock"></span> &nbsp; Pay ₹<?= number_format($price) ?> Now
                </button>
                <p class="secure-note">
                    <span class="fa fa-shield"></span> 256-bit SSL &nbsp;|&nbsp;
                    <span class="fa fa-check-circle"></span> Instant Confirmation
                </p>
            </div>
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

<script src="https://checkout.razorpay.com/v1/checkout.js"></script>
<script>
var options = {
    key: "rzp_test_SnYyCiVFFiZhv3",
    amount: <?= $price * 100 ?>,
    currency: "INR",
    name: "Explore India",
    description: "<?= addslashes($title) ?> - <?= addslashes($from) ?> to <?= addslashes($to) ?>",
    image: "images/logo.png",
    handler: function(response) {
        var form = document.createElement('form');
        form.method = 'POST';
        form.action = 'payment_success.php';

        // All POST data pass karo
        var allData = <?= json_encode($_POST) ?>;
        allData['razorpay_payment_id'] = response.razorpay_payment_id;
        allData['pay_type'] = '<?= $type ?>';
        allData['price'] = '<?= $price ?>';

        for (var key in allData) {
            var input = document.createElement('input');
            input.type = 'hidden';
            input.name = key;
            input.value = allData[key];
            form.appendChild(input);
        }
        document.body.appendChild(form);
        form.submit();
    },
    prefill: {
        name: "<?= addslashes($cust['cust_fname'].' '.$cust['cust_lname']) ?>",
        email: "<?= addslashes($cust['cust_email']) ?>",
        contact: "<?= addslashes($cust['cust_mobile']) ?>"
    },
    theme: { color: "<?= $color ?>" },
    modal: { ondismiss: function() { alert('Payment cancelled. Please try again.'); } }
};
var rzp = new Razorpay(options);
document.getElementById('rzp-button').onclick = function(e) { rzp.open(); e.preventDefault(); };
</script>
</body>
</html>