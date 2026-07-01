<?php
include('db.php');
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>Transport – Explore India</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <link href="css/bootstrap.css" rel='stylesheet' type='text/css'/>
    <link href="css/style.css" rel='stylesheet' type='text/css'/>
    <link href="css/font-awesome.min.css" rel="stylesheet">
    <link href="//fonts.googleapis.com/css?family=Montserrat:400,500,600,700,800,900" rel="stylesheet">
    <link href="//fonts.googleapis.com/css?family=Open+Sans:300,400,600,700" rel="stylesheet">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
    <script src="js/disable.js"></script>
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
        body { background: #0a0a0a; font-family: 'Open Sans', sans-serif; color: #fff; overflow-x: hidden; }

        /* ═══════════════════════════
           PAGE HERO — DARK (same)
        ═══════════════════════════ */
        .page-hero {
            padding: 120px 20px 60px;
            text-align: center;
            background: linear-gradient(160deg, #0a0a0a 0%, #12082a 50%, #0a0a0a 100%);
        }
        .eyebrow {
            font-size: 11px; font-weight: 700; letter-spacing: 4px;
            text-transform: uppercase; color: rgba(255,255,255,0.35); margin-bottom: 12px;
        }
        .page-hero h1 {
            font-family: 'Montserrat', sans-serif;
            font-size: clamp(28px, 7vw, 68px); font-weight: 900;
            color: #fff; margin-bottom: 14px;
            letter-spacing: -2px; text-transform: none !important;
            line-height: 1.05;
        }
        .hero-content h1 .blue   { color: #5ea0ff; }
        .hero-content h1 .orange { color: #f5a623; }
        .hero-content h1 .green  { color: #5ecfa8; }
        .page-hero p {
            font-size: clamp(13px, 2vw, 16px); color: rgba(255,255,255,0.45);
            max-width: 480px; margin: 0 auto 48px; line-height: 1.65;
        }

        /* ═══════════════════════════
           TAB SWITCHER — dark
        ═══════════════════════════ */
        .tab-wrap {
            display: inline-flex;
            background: rgba(255,255,255,0.05);
            border: 1px solid rgba(255,255,255,0.08);
            border-radius: 16px; padding: 5px; gap: 4px;
            margin-bottom: 0;
        }
        .tab-btn {
            display: flex; align-items: center; gap: 8px;
            padding: 12px 24px; border-radius: 12px;
            border: none; background: transparent;
            color: rgba(255,255,255,0.45);
            font-size: 14px; font-weight: 600;
            cursor: pointer; transition: all 0.2s;
            font-family: 'Open Sans', sans-serif;
            white-space: nowrap;
        }
        .tab-btn:hover { color: rgba(255,255,255,0.8); }
        .tab-btn.active.flight { background: rgba(94,160,255,0.15); color: #5ea0ff; border: 1px solid rgba(94,160,255,0.3); }
        .tab-btn.active.train  { background: rgba(245,166,35,0.15); color: #f5a623; border: 1px solid rgba(245,166,35,0.3); }
        .tab-btn.active.cab    { background: rgba(94,207,168,0.15); color: #5ecfa8; border: 1px solid rgba(94,207,168,0.3); }
        .tab-btn .tab-icon { font-size: 18px; }

        /* ═══════════════════════════
           WHITE MIDDLE SECTION
        ═══════════════════════════ */
        .search-section {
            background: #ffffff;
            padding: 60px 20px;
        }

        .search-section-inner {
            max-width: 780px;
            margin: 0 auto;
        }

        /* ═══════════════════════════
           SEARCH FORMS — Light
        ═══════════════════════════ */
        .search-panel { display: none; width: 100%; }
        .search-panel.active { display: block; }

        .search-card {
            background: #f8f9fa;
            border: 1.5px solid #e9ecef;
            border-radius: 20px;
            padding: 32px 28px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.06);
        }

        .field-row { display: grid; gap: 14px; margin-bottom: 14px; }
        .field-row.two       { grid-template-columns: 1fr auto 1fr; align-items: center; }
        .field-row.three     { grid-template-columns: 1fr 1fr 1fr; }
        .field-row.two-equal { grid-template-columns: 1fr 1fr; }

        .swap-btn {
            width: 38px; height: 38px; border-radius: 50%;
            background: #fff;
            border: 1.5px solid #dee2e6;
            color: #666; font-size: 14px;
            cursor: pointer; display: flex;
            align-items: center; justify-content: center;
            transition: all 0.2s; flex-shrink: 0;
        }
        .swap-btn:hover { background: #f0f0f0; color: #333; border-color: #aaa; }

        .field-group { display: flex; flex-direction: column; gap: 6px; }

        .field-label {
            font-size: 10px; font-weight: 700;
            letter-spacing: 1.5px; text-transform: uppercase;
            color: #888;
        }

        .field-input, .field-select {
            width: 100%;
            background: #ffffff;
            border: 1.5px solid #dee2e6;
            border-radius: 10px;
            color: #1a1a1a;
            font-size: 14px; padding: 12px 14px;
            outline: none; transition: border-color 0.2s;
            font-family: 'Open Sans', sans-serif;
            appearance: none; -webkit-appearance: none;
        }
        .field-input:focus, .field-select:focus {
            border-color: #5ea0ff;
            background: #f0f6ff;
        }
        .field-select option { background: #fff; color: #1a1a1a; }

        /* Passenger counter */
        .pax-counter {
            display: flex; align-items: center; gap: 10px;
            padding: 8px 14px;
            background: #fff;
            border: 1.5px solid #dee2e6;
            border-radius: 10px;
        }
        .pax-btn {
            width: 28px; height: 28px; border-radius: 50%;
            background: #f0f0f0; border: none;
            color: #333; font-size: 16px; cursor: pointer;
            display: flex; align-items: center; justify-content: center;
            transition: background 0.15s; font-weight: 700;
        }
        .pax-btn:hover { background: #ddd; }
        .pax-count { font-size: 16px; font-weight: 700; color: #1a1a1a; min-width: 20px; text-align: center; }

        /* Coach type pills */
        .class-pills { display: flex; flex-wrap: wrap; gap: 8px; margin-top: 4px; }
        .class-pill { display: none; }
        .class-label {
            padding: 7px 14px; border-radius: 20px;
            border: 1.5px solid #dee2e6;
            background: #fff;
            font-size: 12px; font-weight: 600;
            color: #666; cursor: pointer;
            transition: all 0.2s;
        }
        .class-label:hover { border-color: #f5a623; color: #f5a623; }
        .class-pill:checked + .class-label {
            background: rgba(245,166,35,0.1);
            border-color: #f5a623; color: #d48a1a;
        }

        /* Cab type cards */
        .cab-grid { display: grid; grid-template-columns: repeat(3,1fr); gap: 10px; }
        .cab-radio { display: none; }
        .cab-label {
            display: flex; flex-direction: column; align-items: center;
            gap: 6px; padding: 14px 10px; border-radius: 12px;
            border: 1.5px solid #dee2e6;
            background: #fff;
            cursor: pointer; transition: all 0.2s; text-align: center;
        }
        .cab-label:hover { border-color: rgba(94,207,168,0.5); }
        .cab-radio:checked + .cab-label {
            background: rgba(94,207,168,0.08);
            border-color: #5ecfa8;
        }
        .cab-emoji { font-size: 26px; }
        .cab-name { font-size: 12px; font-weight: 700; color: #1a1a1a; }
        .cab-rate { font-size: 11px; color: #888; }
        .cab-radio:checked + .cab-label .cab-name { color: #3ab88a; }
        .cab-radio:checked + .cab-label .cab-rate { color: rgba(94,207,168,0.8); }

        /* PNR divider */
        .pnr-divider {
            display: flex; align-items: center; gap: 12px;
            margin: 20px 0; color: #aaa; font-size: 12px;
        }
        .pnr-divider::before, .pnr-divider::after {
            content: ''; flex: 1; height: 1px;
            background: #dee2e6;
        }

        /* Search buttons */
        .btn-search {
            width: 100%; border: none; border-radius: 12px;
            font-size: 15px; font-weight: 700;
            font-family: 'Montserrat', sans-serif;
            padding: 14px; cursor: pointer;
            transition: transform 0.2s, box-shadow 0.2s;
            margin-top: 20px; letter-spacing: 0.3px;
            text-transform: none !important;
        }
        .btn-search.flight { background: linear-gradient(135deg, #5ea0ff, #3a7fd4); color: #fff; }
        .btn-search.train  { background: linear-gradient(135deg, #f5a623, #d48a1a); color: #fff; }
        .btn-search.cab    { background: linear-gradient(135deg, #5ecfa8, #3ab88a); color: #fff; }
        .btn-search.pnr    { background: linear-gradient(135deg, #b47cff, #8a4fd4); color: #fff; }
        .btn-search:hover  { transform: translateY(-2px); box-shadow: 0 8px 24px rgba(0,0,0,0.15); }

        /* ═══════════════════════════
           STATS — Dark bottom
        ═══════════════════════════ */
        .stats-section {
            background: #0a0a0a;
            padding: 50px 20px;
        }
        .stats-row {
            display: flex; justify-content: center; gap: 40px;
            flex-wrap: wrap;
        }
        .stat-item { text-align: center; }
        .stat-num { font-family: 'Montserrat', sans-serif; font-size: 28px; font-weight: 800; }
        .stat-num.blue   { color: #5ea0ff; }
        .stat-num.orange { color: #f5a623; }
        .stat-num.green  { color: #5ecfa8; }
        .stat-label { font-size: 12px; color: rgba(255,255,255,0.35); margin-top: 4px; }

        /* Responsive */
        @media (max-width: 700px) {
            .field-row.two       { grid-template-columns: 1fr; }
            .field-row.three     { grid-template-columns: 1fr 1fr; }
            .field-row.two-equal { grid-template-columns: 1fr; }
            .tab-btn span.tab-text { display: none; }
            .tab-btn { padding: 12px 16px; }
            .search-card { padding: 20px 16px; }
            .swap-btn { display: none; }
        }
    </style>
</head>
<body oncontextmenu="return false;">
<?php include('header.php'); ?>

<!-- DARK HERO — Same -->
<div class="page-hero">
    <p class="eyebrow">✈️ 🚂 🚕 All In One</p>
    <div class="hero-content">
        <h1>
            <span class="blue">Fly</span>,
            <span class="orange">Ride</span> &amp;
            <span class="green">Drive</span><br>Across India
        </h1>
    </div>
    <p>Book flights, trains and cabs — all from one place. Real routes, real airlines, real trains.</p>

    <!-- Tabs -->
    <div class="tab-wrap">
        <button class="tab-btn flight active" onclick="switchTab('flight')">
            <span class="tab-icon">✈️</span>
            <span class="tab-text">Flights</span>
        </button>
        <button class="tab-btn train" onclick="switchTab('train')">
            <span class="tab-icon">🚂</span>
            <span class="tab-text">Trains</span>
        </button>
        <button class="tab-btn cab" onclick="switchTab('cab')">
            <span class="tab-icon">🚕</span>
            <span class="tab-text">Cabs</span>
        </button>
    </div>
</div>

<!-- WHITE MIDDLE SECTION — Forms -->
<div class="search-section">
    <div class="search-section-inner">

        <!-- FLIGHT -->
        <div class="search-panel active" id="panel-flight">
            <div class="search-card">
                <form action="flight_results.php" method="POST">
                    <div class="field-row two">
                        <div class="field-group">
                            <label class="field-label">From</label>
                            <select name="from_city" class="field-select" required id="flight-from">
                                <option value="">Select City</option>
                                <?php
                                $cities = mysqli_query($conn, "SELECT * FROM transport_cities ORDER BY city_name");
                                while ($c = mysqli_fetch_assoc($cities)):
                                ?>
                                <option value="<?= $c['city_id'] ?>">
                                    <?= htmlspecialchars($c['city_name']) ?> (<?= $c['airport_code'] ?>)
                                </option>
                                <?php endwhile; ?>
                            </select>
                        </div>
                        <button type="button" class="swap-btn" onclick="swapCities('flight')">
                            <span class="fa fa-exchange"></span>
                        </button>
                        <div class="field-group">
                            <label class="field-label">To</label>
                            <select name="to_city" class="field-select" required id="flight-to">
                                <option value="">Select City</option>
                                <?php
                                $cities2 = mysqli_query($conn, "SELECT * FROM transport_cities ORDER BY city_name");
                                while ($c = mysqli_fetch_assoc($cities2)):
                                ?>
                                <option value="<?= $c['city_id'] ?>">
                                    <?= htmlspecialchars($c['city_name']) ?> (<?= $c['airport_code'] ?>)
                                </option>
                                <?php endwhile; ?>
                            </select>
                        </div>
                    </div>
                    <div class="field-row three">
                        <div class="field-group">
                            <label class="field-label">Date</label>
                            <input type="date" name="date" class="field-input" min="<?= date('Y-m-d') ?>" required>
                        </div>
                        <div class="field-group">
                            <label class="field-label">Passengers</label>
                            <div class="pax-counter">
                                <button type="button" class="pax-btn" onclick="changePax(-1)">−</button>
                                <span class="pax-count" id="paxCount">1</span>
                                <button type="button" class="pax-btn" onclick="changePax(1)">+</button>
                                <input type="hidden" name="passengers" id="paxInput" value="1">
                            </div>
                        </div>
                        <div class="field-group">
                            <label class="field-label">Class</label>
                            <select name="class" class="field-select">
                                <option value="economy">Economy</option>
                                <option value="business">Business</option>
                                <option value="first">First Class</option>
                            </select>
                        </div>
                    </div>
                    <button type="submit" class="btn-search flight">
                        <span class="fa fa-search"></span> &nbsp; Search Flights
                    </button>
                </form>
            </div>
        </div>

        <!-- TRAIN -->
        <div class="search-panel" id="panel-train">
            <div class="search-card">
                <form action="train_results.php" method="POST">
                    <div class="field-row two">
                        <div class="field-group">
                            <label class="field-label">From Station</label>
                            <select name="from_city" class="field-select" required id="train-from">
                                <option value="">Select City</option>
                                <?php
                                $tc = mysqli_query($conn, "SELECT * FROM transport_cities ORDER BY city_name");
                                while ($c = mysqli_fetch_assoc($tc)):
                                ?>
                                <option value="<?= htmlspecialchars($c['city_code']) ?>">
                                    <?= htmlspecialchars($c['city_name']) ?>
                                </option>
                                <?php endwhile; ?>
                            </select>
                        </div>
                        <button type="button" class="swap-btn" onclick="swapCities('train')">
                            <span class="fa fa-exchange"></span>
                        </button>
                        <div class="field-group">
                            <label class="field-label">To Station</label>
                            <select name="to_city" class="field-select" required id="train-to">
                                <option value="">Select City</option>
                                <?php
                                $tc2 = mysqli_query($conn, "SELECT * FROM transport_cities ORDER BY city_name");
                                while ($c = mysqli_fetch_assoc($tc2)):
                                ?>
                                <option value="<?= htmlspecialchars($c['city_code']) ?>">
                                    <?= htmlspecialchars($c['city_name']) ?>
                                </option>
                                <?php endwhile; ?>
                            </select>
                        </div>
                    </div>
                    <div class="field-row two-equal">
                        <div class="field-group">
                            <label class="field-label">Date</label>
                            <input type="date" name="date" class="field-input" min="<?= date('Y-m-d') ?>" required>
                        </div>
                        <div class="field-group">
                            <label class="field-label">Passengers</label>
                            <div class="pax-counter">
                                <button type="button" class="pax-btn" onclick="changePaxTrain(-1)">−</button>
                                <span class="pax-count" id="trainPaxCount">1</span>
                                <button type="button" class="pax-btn" onclick="changePaxTrain(1)">+</button>
                                <input type="hidden" name="passengers" id="trainPaxInput" value="1">
                            </div>
                        </div>
                    </div>
                    <div class="field-group" style="margin-bottom:6px;">
                        <label class="field-label">Class</label>
                        <div class="class-pills">
                            <input type="radio" name="class" id="sl" value="SL" class="class-pill" checked>
                            <label for="sl" class="class-label">Sleeper (SL)</label>
                            <input type="radio" name="class" id="3a" value="3A" class="class-pill">
                            <label for="3a" class="class-label">3rd AC</label>
                            <input type="radio" name="class" id="2a" value="2A" class="class-pill">
                            <label for="2a" class="class-label">2nd AC</label>
                            <input type="radio" name="class" id="1a" value="1A" class="class-pill">
                            <label for="1a" class="class-label">1st AC</label>
                            <input type="radio" name="class" id="cc" value="CC" class="class-pill">
                            <label for="cc" class="class-label">Chair Car</label>
                        </div>
                    </div>
                    <button type="submit" class="btn-search train">
                        <span class="fa fa-search"></span> &nbsp; Search Trains
                    </button>
                </form>

              
          
            </div>
        </div>

        <!-- CAB -->
        <div class="search-panel" id="panel-cab">
            <div class="search-card">
                <form action="cab_results.php" method="POST">
                    <div class="field-row two">
                        <div class="field-group">
                            <label class="field-label">Pickup City</label>
                            <select name="from_city" class="field-select" required id="cab-from">
                                <option value="">Select City</option>
                                <?php
                                $tc3 = mysqli_query($conn, "SELECT * FROM transport_cities ORDER BY city_name");
                                while ($c = mysqli_fetch_assoc($tc3)):
                                ?>
                                <option value="<?= $c['city_id'] ?>">
                                    <?= htmlspecialchars($c['city_name']) ?>
                                </option>
                                <?php endwhile; ?>
                            </select>
                        </div>
                        <button type="button" class="swap-btn" onclick="swapCities('cab')">
                            <span class="fa fa-exchange"></span>
                        </button>
                        <div class="field-group">
                            <label class="field-label">Drop City</label>
                            <select name="to_city" class="field-select" required id="cab-to">
                                <option value="">Select City</option>
                                <?php
                                $tc4 = mysqli_query($conn, "SELECT * FROM transport_cities ORDER BY city_name");
                                while ($c = mysqli_fetch_assoc($tc4)):
                                ?>
                                <option value="<?= $c['city_id'] ?>">
                                    <?= htmlspecialchars($c['city_name']) ?>
                                </option>
                                <?php endwhile; ?>
                            </select>
                        </div>
                    </div>
                    <div class="field-row two-equal">
                        <div class="field-group">
                            <label class="field-label">Pickup Date</label>
                            <input type="date" name="date" class="field-input" min="<?= date('Y-m-d') ?>" required>
                        </div>
                        <div class="field-group">
                            <label class="field-label">Pickup Time</label>
                            <input type="time" name="time" class="field-input" value="10:00">
                        </div>
                    </div>
                    <div class="field-group" style="margin-bottom:6px;">
                        <label class="field-label">Cab Type</label>
                        <div class="cab-grid">
                            <div>
                                <input type="radio" name="cab_type" id="mini" value="mini" class="cab-radio" checked>
                                <label for="mini" class="cab-label">
                                    <span class="cab-emoji">🚗</span>
                                    <span class="cab-name">Mini</span>
                                    <span class="cab-rate">₹12/km</span>
                                </label>
                            </div>
                            <div>
                                <input type="radio" name="cab_type" id="sedan" value="sedan" class="cab-radio">
                                <label for="sedan" class="cab-label">
                                    <span class="cab-emoji">🚙</span>
                                    <span class="cab-name">Sedan</span>
                                    <span class="cab-rate">₹18/km</span>
                                </label>
                            </div>
                            <div>
                                <input type="radio" name="cab_type" id="suv" value="suv" class="cab-radio">
                                <label for="suv" class="cab-label">
                                    <span class="cab-emoji">🚐</span>
                                    <span class="cab-name">SUV</span>
                                    <span class="cab-rate">₹25/km</span>
                                </label>
                            </div>
                        </div>
                    </div>
                    <button type="submit" class="btn-search cab">
                        <span class="fa fa-car"></span> &nbsp; Search Cabs
                    </button>
                </form>
            </div>
        </div>

    </div>
</div>

<!-- DARK STATS — Same -->
<div class="stats-section">
    <div class="stats-row">
        <div class="stat-item">
            <div class="stat-num blue">500+</div>
            <div class="stat-label">Flight Routes</div>
        </div>
        <div class="stat-item">
            <div class="stat-num orange">1000+</div>
            <div class="stat-label">Train Routes</div>
        </div>
        <div class="stat-item">
            <div class="stat-num green">40</div>
            <div class="stat-label">Cities Covered</div>
        </div>
    </div>
</div>

<?php include('footer.php'); ?>

<script>
function switchTab(type) {
    document.querySelectorAll('.tab-btn').forEach(b => b.classList.remove('active'));
    document.querySelectorAll('.search-panel').forEach(p => p.classList.remove('active'));
    document.querySelector('.tab-btn.' + type).classList.add('active');
    document.getElementById('panel-' + type).classList.add('active');
}
var pax = 1;
function changePax(d) {
    pax = Math.max(1, Math.min(9, pax + d));
    document.getElementById('paxCount').textContent = pax;
    document.getElementById('paxInput').value = pax;
}
var trainPax = 1;
function changePaxTrain(d) {
    trainPax = Math.max(1, Math.min(9, trainPax + d));
    document.getElementById('trainPaxCount').textContent = trainPax;
    document.getElementById('trainPaxInput').value = trainPax;
}
function swapCities(type) {
    var from = document.getElementById(type + '-from');
    var to   = document.getElementById(type + '-to');
    var tmp  = from.value;
    from.value = to.value;
    to.value   = tmp;
}
</script>
</body>
</html>