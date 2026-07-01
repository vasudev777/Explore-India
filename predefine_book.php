<?php include('db.php');
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>Book Package – Explore India</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <link href="css/bootstrap.css" rel='stylesheet' type='text/css'/>
    <link href="css/style.css" rel='stylesheet' type='text/css'/>
    <link href="css/font-awesome.min.css" rel="stylesheet">
    <link href="//fonts.googleapis.com/css?family=Montserrat:400,500,600,700" rel="stylesheet">
    <link href="//fonts.googleapis.com/css?family=Open+Sans:400,600" rel="stylesheet">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
    <script src="js/disable.js"></script>

    <style>
        * { box-sizing: border-box; }

        body {
            background: #0a0a0a;
            margin: 0;
            font-family: 'Open Sans', sans-serif;
            color: #fff;
        }

        /* ── Page Wrapper ── */
        .checkout-wrapper {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 100px 20px 60px;
            background: linear-gradient(135deg, #0a0a0a 0%, #111827 100%);
        }

        .checkout-card {
            background: #161616;
            border: 1px solid rgba(255,255,255,0.08);
            border-radius: 20px;
            overflow: hidden;
            width: 100%;
            max-width: 520px;
            box-shadow: 0 30px 60px rgba(0,0,0,0.5);
        }

        /* ── Package Banner ── */
        .package-banner {
            background: linear-gradient(135deg, #1a0533, #2d1b69, #11334d);
            padding: 28px 30px;
            position: relative;
            overflow: hidden;
        }
        .package-banner::before {
            content: '';
            position: absolute;
            top: -40px; right: -40px;
            width: 150px; height: 150px;
            border-radius: 50%;
            background: rgba(255,255,255,0.04);
        }
        .package-banner .badge-tag {
            display: inline-block;
            font-size: 11px;
            font-weight: 600;
            letter-spacing: 2px;
            text-transform: uppercase;
            color: rgba(255,255,255,0.5);
            margin-bottom: 8px;
        }
        .package-banner h2 {
            font-family: 'Montserrat', sans-serif;
            font-size: 24px;
            font-weight: 700;
            color: #fff;
            margin: 0 0 6px;
        }
        .package-banner .price-tag {
            font-size: 28px;
            font-weight: 700;
            color: #f5a623;
            font-family: 'Montserrat', sans-serif;
        }
        .package-banner .price-tag span {
            font-size: 14px;
            color: rgba(255,255,255,0.5);
            font-weight: 400;
        }

        /* ── Form Area ── */
        .checkout-form {
            padding: 30px;
        }

        .form-label {
            font-size: 12px;
            font-weight: 600;
            letter-spacing: 1px;
            text-transform: uppercase;
            color: rgba(255,255,255,0.45);
            display: block;
            margin-bottom: 8px;
        }

        .date-input {
            width: 100%;
            background: rgba(255,255,255,0.05);
            border: 1px solid rgba(255,255,255,0.12);
            border-radius: 10px;
            color: #fff;
            font-size: 15px;
            padding: 12px 16px;
            outline: none;
            transition: border-color 0.2s;
            cursor: pointer;
        }
        .date-input:focus {
            border-color: rgba(245,166,35,0.6);
            background: rgba(255,255,255,0.07);
        }
        .date-input::-webkit-calendar-picker-indicator {
            filter: invert(1);
            cursor: pointer;
        }

        /* ── Summary Box ── */
        .summary-box {
            background: rgba(255,255,255,0.03);
            border: 1px solid rgba(255,255,255,0.07);
            border-radius: 10px;
            padding: 16px 18px;
            margin: 20px 0;
        }
        .summary-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 6px 0;
            font-size: 14px;
        }
        .summary-row:not(:last-child) {
            border-bottom: 1px solid rgba(255,255,255,0.05);
        }
        .summary-row .label {
            color: rgba(255,255,255,0.5);
        }
        .summary-row .value {
            color: #fff;
            font-weight: 600;
        }
        .summary-row.total .label {
            color: rgba(255,255,255,0.8);
            font-weight: 600;
        }
        .summary-row.total .value {
            color: #f5a623;
            font-size: 16px;
        }

        /* ── Book Button ── */
        .btn-book {
            width: 100%;
            background: linear-gradient(135deg, #f5a623, #e8920e);
            border: none;
            border-radius: 12px;
            color: #000;
            font-size: 15px;
            font-weight: 700;
            font-family: 'Montserrat', sans-serif;
            letter-spacing: 0.5px;
            padding: 14px;
            cursor: pointer;
            transition: transform 0.2s, box-shadow 0.2s;
            margin-top: 6px;
        }
        .btn-book:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(245,166,35,0.35);
        }
        .btn-book:active {
            transform: translateY(0);
        }

        .secure-note {
            text-align: center;
            font-size: 12px;
            color: rgba(255,255,255,0.3);
            margin-top: 14px;
        }
        .secure-note .fa {
            color: #5ecfa8;
            margin-right: 4px;
        }
    </style>
</head>

<body oncontextmenu="return false;">

<?php include('header.php'); ?>

<div class="checkout-wrapper">
    <div class="checkout-card">

        <?php
            $pad = $_POST['pack'];
            $sql = "SELECT * FROM package WHERE pa_id='$pad'";
            $result = mysqli_query($conn, $sql);
            $package = mysqli_fetch_assoc($result);
        ?>

        <!-- Package Banner -->
        <div class="package-banner">
            <div class="badge-tag">✈ Selected Package</div>
            <h2><?php echo htmlspecialchars($package['pa_name']); ?></h2>
            <div class="price-tag">
                ₹<?php echo number_format($package['price']); ?>
                <span>/ person</span>
            </div>
        </div>

        <!-- Form -->
        <div class="checkout-form">
            <?php
            $st_id = intval($package['s_id']);
            $lgresult = mysqli_query($conn, "SELECT * FROM local_guide WHERE s_id='$st_id' AND localg_emailverify=1 AND localg_approve=1 ORDER BY RAND() LIMIT 1");
            $guide = $lgresult ? mysqli_fetch_assoc($lgresult) : null;
            ?>
            <form action="pay_pre.php" method="post">

                <input type="hidden" name="pack"      value="<?php echo $package['pa_id']; ?>">
                <input type="hidden" name="packid"    value="<?php echo $package['pa_id']; ?>">
                <input type="hidden" name="packname"  value="<?php echo htmlspecialchars($package['pa_name']); ?>">
                <input type="hidden" name="packprice" value="<?php echo $package['price']; ?>">
                <?php if ($guide): ?>
                <input type="hidden" name="lg"        value="<?php echo $guide['localg_id']; ?>">
                <?php endif; ?>

                <!-- Date Picker -->
                <label class="form-label">Select Travel Date</label>
                <input type="date" name="date" class="date-input"
                       min="<?php echo date('Y-m-d', strtotime('+1 day')); ?>" required>

                <?php if ($guide): ?>
                <!-- Assigned Local Guide Card -->
                <div style="background: rgba(255,255,255,0.03); border: 1px solid rgba(255,255,255,0.07); border-radius: 10px; padding: 16px 18px; margin: 20px 0;">
                    <div style="font-size: 11px; font-weight: 700; letter-spacing: 1px; text-transform: uppercase; color: rgba(255,255,255,0.35); margin-bottom: 10px;">Your Local Guide</div>
                    <div style="display: flex; align-items: center; gap: 12px;">
                        <div style="width: 38px; height: 38px; border-radius: 50%; background: linear-gradient(135deg, #f5a623, #e8920e); display: flex; align-items: center; justify-content: center; color: #000; font-weight: 700; font-family: 'Montserrat', sans-serif;">
                            <?php echo substr($guide['localg_name'], 0, 1); ?>
                        </div>
                        <div>
                            <div style="font-weight: 600; color: #fff; font-size: 14px;"><?php echo htmlspecialchars($guide['localg_name']); ?></div>
                            <div style="font-size: 11.5px; color: rgba(255,255,255,0.4); margin-top: 2px;">🗣 Speaks: <?php echo htmlspecialchars($guide['localg_language']); ?></div>
                        </div>
                    </div>
                </div>
                <?php endif; ?>

                <!-- Summary -->
                <div class="summary-box">
                    <div class="summary-row">
                        <span class="label">Package</span>
                        <span class="value"><?php echo htmlspecialchars($package['pa_name']); ?></span>
                    </div>
                    <div class="summary-row">
                        <span class="label">Package ID</span>
                        <span class="value">#<?php echo $package['pa_id']; ?></span>
                    </div>
                    <div class="summary-row total">
                        <span class="label">Total Amount</span>
                        <span class="value">₹<?php echo number_format($package['price']); ?></span>
                    </div>
                </div>

                <button type="submit" class="btn-book">
                    <span class="fa fa-lock"></span> &nbsp; Proceed to Payment
                </button>
            </form>

            <p class="secure-note">
                <span class="fa fa-shield"></span> 100% Secure Payment &nbsp;|&nbsp;
                <span class="fa fa-refresh"></span> Easy Cancellation
            </p>
        </div>

    </div>
</div>

<?php include('footer.php'); ?>

</body>
</html>
