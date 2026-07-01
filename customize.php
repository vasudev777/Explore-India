<?php
include('db.php');
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>Customize Package – Explore India</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <link href="css/bootstrap.css" rel='stylesheet' type='text/css'/>
    <link href="css/style.css" rel='stylesheet' type='text/css'/>
    <link href="css/font-awesome.min.css" rel="stylesheet">
    <link href="//fonts.googleapis.com/css?family=Montserrat:400,500,600,700,800,900" rel="stylesheet">
    <link href="//fonts.googleapis.com/css?family=Open+Sans:300,400,600,700" rel="stylesheet">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
    <script src="js/disable.js"></script>

    <script>
    $(document).ready(function () {
        $('#country').on('change', function () {
            var stateID = $(this).val();
            if (stateID) {
                $('#hotelList').html('<div class="hotel-placeholder"><span class="fa fa-spinner fa-spin"></span><br>Loading hotels...</div>');
                $.ajax({
                    type: 'POST',
                    url: 'ajaxFile.php',
                    data: 's_id=' + stateID,
                    success: function (html) {
                        $('#hotelList').html(html);
                    }
                });
            } else {
                $('#hotelList').html('<div class="hotel-placeholder"><span class="fa fa-hotel"></span>Select a state to see hotels</div>');
            }
        });
    });
    </script>

    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            background: #0a0a0a;
            font-family: 'Open Sans', sans-serif;
            color: #fff;
            overflow-x: hidden;
            /* NO overflow:hidden on body — footer fix */
        }

        /* ══ PAGE HERO ══ */
        .page-hero {
            position: relative;
            padding: 130px 20px 70px;
            text-align: center;
            background: linear-gradient(160deg, #0a0a0a 0%, #0d1f1a 50%, #0a0a0a 100%);
        }
        .hero-badge {
            display: inline-block;
            font-size: 10px; font-weight: 700;
            letter-spacing: 3px; text-transform: uppercase;
            color: rgba(255,255,255,0.4);
            border: 1px solid rgba(255,255,255,0.12);
            padding: 5px 14px; border-radius: 20px;
            margin-bottom: 18px;
        }
        .page-hero h1 {
            font-family: 'Montserrat', sans-serif;
            font-size: clamp(32px, 6vw, 58px);
            font-weight: 900; color: #fff;
            line-height: 1.08; letter-spacing: -1.5px;
            margin-bottom: 14px;
            text-transform: none !important;
        }
        .page-hero h1 span { color: #5ecfa8; }
        .page-hero .hero-sub {
            font-size: clamp(13px, 2vw, 16px);
            color: rgba(255,255,255,0.45);
            max-width: 460px; margin: 0 auto;
            line-height: 1.7;
        }

        /* ══ MAIN LAYOUT ══ */
        .main-wrap {
            max-width: 1080px;
            margin: 0 auto;
            padding: 60px 20px 80px;
            display: grid;
            grid-template-columns: 1fr 1.2fr;
            gap: 28px;
            align-items: start;
        }

        /* ══ INFO CARD ══ */
        .info-card {
            background: #141414;
            border: 1px solid rgba(255,255,255,0.07);
            border-radius: 20px;
            padding: 30px 26px;
        }
        .ic-badge {
            display: inline-block;
            font-size: 9px; font-weight: 700;
            letter-spacing: 2px; text-transform: uppercase;
            color: #5ecfa8;
            background: rgba(94,207,168,0.08);
            border: 1px solid rgba(94,207,168,0.2);
            padding: 4px 12px; border-radius: 20px;
            margin-bottom: 16px;
        }
        .info-card h2 {
            font-family: 'Montserrat', sans-serif;
            font-size: 22px; font-weight: 800;
            color: #fff; margin-bottom: 10px;
            text-transform: none !important;
        }
        .info-card h2 span { color: #5ecfa8; }
        .info-card > p {
            font-size: 13px;
            color: rgba(255,255,255,0.42);
            line-height: 1.75; margin-bottom: 24px;
        }
        .features { display: flex; flex-direction: column; gap: 14px; }
        .feature-item { display: flex; align-items: flex-start; gap: 12px; }
        .feat-icon {
            width: 36px; height: 36px; flex-shrink: 0;
            border-radius: 10px;
            background: rgba(94,207,168,0.1);
            border: 1px solid rgba(94,207,168,0.2);
            display: flex; align-items: center; justify-content: center;
            color: #5ecfa8; font-size: 14px;
        }
        .feat-text h4 {
            font-family: 'Montserrat', sans-serif;
            font-size: 13px; font-weight: 700;
            color: #fff; margin-bottom: 2px;
            text-transform: none !important;
        }
        .feat-text p {
            font-size: 12px; color: rgba(255,255,255,0.35); margin: 0;
        }

        /* ══ FORM CARD ══ */
        .form-card {
            background: #141414;
            border: 1px solid rgba(255,255,255,0.07);
            border-radius: 20px;
            overflow: hidden;
        }
        .form-header {
            padding: 22px 26px 18px;
            border-bottom: 1px solid rgba(255,255,255,0.06);
        }
        .form-header h3 {
            font-family: 'Montserrat', sans-serif;
            font-size: 17px; font-weight: 700;
            color: #fff; margin-bottom: 3px;
            text-transform: none !important;
        }
        .form-header p { font-size: 12px; color: rgba(255,255,255,0.35); }

        .form-body { padding: 24px 26px 28px; }

        .field-group { margin-bottom: 18px; }
        .field-label {
            display: block;
            font-size: 11px; font-weight: 700;
            letter-spacing: 1px; text-transform: uppercase;
            color: rgba(255,255,255,0.38);
            margin-bottom: 7px;
        }
        .field-input,
        .field-select {
            width: 100%;
            background: rgba(255,255,255,0.04);
            border: 1px solid rgba(255,255,255,0.1);
            border-radius: 10px;
            color: #fff;
            font-size: 14px;
            padding: 11px 14px;
            outline: none;
            transition: border-color 0.2s, background 0.2s;
            font-family: 'Open Sans', sans-serif;
            appearance: none;
            -webkit-appearance: none;
        }
        .field-input:focus,
        .field-select:focus {
            border-color: rgba(94,207,168,0.5);
            background: rgba(255,255,255,0.06);
        }
        .field-input::-webkit-calendar-picker-indicator { filter: invert(1); cursor: pointer; }
        .field-select option { background: #1a1a1a; color: #fff; }

        .select-wrap { position: relative; }
        .select-wrap::after {
            content: '\f107'; font-family: 'FontAwesome';
            position: absolute; right: 14px; top: 50%;
            transform: translateY(-50%);
            color: rgba(255,255,255,0.4);
            pointer-events: none; font-size: 14px;
        }

        /* Hotel checkboxes */
        .hotel-list-wrap {
            background: rgba(255,255,255,0.03);
            border: 1px solid rgba(255,255,255,0.1);
            border-radius: 10px;
            padding: 12px;
            max-height: 220px;
            overflow-y: auto;
            scrollbar-width: thin;
            scrollbar-color: rgba(94,207,168,0.3) transparent;
        }
        .hotel-list-wrap::-webkit-scrollbar { width: 4px; }
        .hotel-list-wrap::-webkit-scrollbar-thumb { background: rgba(94,207,168,0.3); border-radius: 4px; }

        .city-group { margin-bottom: 12px; }
        .city-label {
            font-size: 10px; font-weight: 700;
            letter-spacing: 1.5px; text-transform: uppercase;
            color: rgba(94,207,168,0.7);
            margin-bottom: 6px; padding-left: 4px;
        }
        .city-label .fa { margin-right: 5px; }

        .hotel-checkbox {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 8px 10px;
            border-radius: 8px;
            cursor: pointer;
            transition: background 0.15s;
            margin-bottom: 2px;
        }
        .hotel-checkbox:hover { background: rgba(255,255,255,0.05); }
        .hotel-checkbox input[type="checkbox"] { display: none; }

        .hotel-check-box {
            width: 18px; height: 18px; flex-shrink: 0;
            border-radius: 5px;
            border: 1.5px solid rgba(255,255,255,0.25);
            background: transparent;
            display: flex; align-items: center; justify-content: center;
            transition: all 0.15s;
        }
        .hotel-checkbox input:checked ~ .hotel-check-box {
            background: #5ecfa8;
            border-color: #5ecfa8;
        }
        .hotel-checkbox input:checked ~ .hotel-check-box::after {
            content: '\f00c';
            font-family: 'FontAwesome';
            font-size: 10px;
            color: #000;
        }
        .hotel-name {
            font-size: 13.5px;
            color: rgba(255,255,255,0.75);
        }
        .hotel-checkbox input:checked ~ .hotel-check-box + .hotel-name,
        .hotel-checkbox input:checked ~ .hotel-name {
            color: #5ecfa8;
            font-weight: 600;
        }

        .no-hotels {
            font-size: 13px;
            color: rgba(255,255,255,0.3);
            text-align: center;
            padding: 16px 0;
        }

        .hotel-placeholder {
            font-size: 13px;
            color: rgba(255,255,255,0.25);
            text-align: center;
            padding: 20px 0;
        }
        .hotel-placeholder .fa { font-size: 20px; display: block; margin-bottom: 8px; opacity: 0.4; }

        .select-hint {
            font-size: 11px; color: rgba(255,255,255,0.25);
            margin-top: 6px; display: block;
        }

        .form-divider {
            border: none;
            border-top: 1px solid rgba(255,255,255,0.06);
            margin: 20px 0;
        }

        .btn-create {
            width: 100%;
            background: linear-gradient(135deg, #5ecfa8, #3ab88a);
            border: none; border-radius: 12px;
            color: #000; font-size: 15px; font-weight: 700;
            font-family: 'Montserrat', sans-serif;
            padding: 14px; cursor: pointer;
            transition: transform 0.2s, box-shadow 0.2s;
            letter-spacing: 0.3px;
        }
        .btn-create:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 24px rgba(94,207,168,0.3);
        }
        .btn-create:active { transform: translateY(0); }

        .secure-note {
            text-align: center; font-size: 11.5px;
            color: rgba(255,255,255,0.22); margin-top: 14px;
        }
        .secure-note .fa { color: #5ecfa8; margin-right: 4px; }

        /* ══ RESPONSIVE ══ */
        @media (max-width: 768px) {
            .main-wrap {
                grid-template-columns: 1fr;
                padding: 40px 16px 60px;
            }
        }
    </style>
</head>
<body oncontextmenu="return false;">

<?php include('header.php'); ?>

<!-- PAGE HERO -->
<div class="page-hero">
    <div class="hero-badge">✨ Build Your Trip</div>
    <h1>Customize Your <span>Package</span></h1>
    <p class="hero-sub">Pick your state, hotels, and days — we'll craft the perfect itinerary just for you.</p>
</div>

<!-- MAIN -->
<div class="main-wrap">

    <!-- LEFT: Info -->
    <div class="info-card">
        <div class="ic-badge">Why Customize?</div>
        <h2>Your Trip,<br><span>Your Way</span></h2>
        <p>Design every detail of your journey — from the destination to the duration. No fixed plans, just freedom.</p>
        <div class="features">
            <div class="feature-item">
                <div class="feat-icon"><span class="fa fa-map-marker"></span></div>
                <div class="feat-text">
                    <h4>Choose Your State</h4>
                    <p>Pick from all major Indian states</p>
                </div>
            </div>
            <div class="feature-item">
                <div class="feat-icon"><span class="fa fa-bed"></span></div>
                <div class="feat-text">
                    <h4>Select Your Hotels</h4>
                    <p>Multiple hotels, multiple cities</p>
                </div>
            </div>
            <div class="feature-item">
                <div class="feat-icon"><span class="fa fa-calendar"></span></div>
                <div class="feat-text">
                    <h4>Set Your Duration</h4>
                    <p>1 to 15 days — you decide</p>
                </div>
            </div>
            <div class="feature-item">
                <div class="feat-icon"><span class="fa fa-inr"></span></div>
                <div class="feat-text">
                    <h4>Best Price</h4>
                    <p>Transparent pricing, no hidden charges</p>
                </div>
            </div>
        </div>
    </div>

    <!-- RIGHT: Form -->
    <div class="form-card">
        <div class="form-header">
            <h3><span class="fa fa-magic"></span> &nbsp;Create Package</h3>
            <p>Fill in the details below to build your trip</p>
        </div>
        <div class="form-body">
            <form action="request.php" method="post">

                <div class="field-group">
                    <label class="field-label">Travel Date</label>
                    <input type="date" name="date" class="field-input"
                           min="<?php echo date('Y-m-d', strtotime('+1 day')); ?>" required>
                </div>

                <div class="field-group">
                    <label class="field-label">Number of Days</label>
                    <div class="select-wrap">
                        <select name="day" class="field-select" required>
                            <option value="">Select days</option>
                            <?php for($i=1; $i<=15; $i++) echo "<option value='$i'>$i Day".($i>1?'s':'')."</option>"; ?>
                        </select>
                    </div>
                </div>

                <div class="field-group">
                    <label class="field-label">State</label>
                    <div class="select-wrap">
                        <select name="country" id="country" class="field-select" required>
                            <option value="">Select a state</option>
                            <?php
                            $query = "SELECT * FROM state ORDER BY s_name ASC";
                            $run_query = mysqli_query($conn, $query);
                            if ($run_query && mysqli_num_rows($run_query) > 0) {
                                while ($row = mysqli_fetch_array($run_query)) {
                                    echo "<option value='{$row['s_id']}'>{$row['s_name']}</option>";
                                }
                            }
                            ?>
                        </select>
                    </div>
                </div>

                <div class="field-group">
                    <label class="field-label">Hotels</label>
                    <div class="hotel-list-wrap" id="hotelList">
                        <div class="hotel-placeholder">
                            <span class="fa fa-hotel"></span>
                            Select a state to see hotels
                        </div>
                    </div>
                    <span class="select-hint">
                        <span class="fa fa-check-square-o"></span>
                        Select one or more hotels
                    </span>
                </div>

                <hr class="form-divider">

                <button type="submit" name="button" class="btn-create">
                    <span class="fa fa-magic"></span> &nbsp; Create My Package
                </button>

                <p class="secure-note">
                    <span class="fa fa-lock"></span> Your data is safe &nbsp;|&nbsp;
                    <span class="fa fa-check-circle"></span> Instant confirmation
                </p>

            </form>
        </div>
    </div>

</div>

<?php include('footer.php'); ?>

</body>
</html>