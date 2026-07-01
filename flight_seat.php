<?php
include('db.php');
session_start();
if (!isset($_SESSION['uemail'])) { header('Location: login.php'); exit; }
if (!isset($_POST['flight_no'])) { header('Location: packages.php'); exit; }

$flight_no  = htmlspecialchars($_POST['flight_no']  ?? '');
$airline    = htmlspecialchars($_POST['airline']     ?? '');
$from       = htmlspecialchars($_POST['from']        ?? '');
$to         = htmlspecialchars($_POST['to']          ?? '');
$dep_time   = htmlspecialchars($_POST['dep_time']    ?? '');
$arr_time   = htmlspecialchars($_POST['arr_time']    ?? '');
$duration   = htmlspecialchars($_POST['duration']    ?? '');
$date       = htmlspecialchars($_POST['date']        ?? '');
$passengers = intval($_POST['passengers']            ?? 1);
$class      = htmlspecialchars($_POST['class']       ?? 'economy');
$price      = intval($_POST['price']                 ?? 0);

srand(crc32($flight_no . $date));
$booked_seats = [];
for ($i = 0; $i < 70; $i++) $booked_seats[] = rand(1, 180);
$booked_seats = array_unique($booked_seats);

$price_per = $passengers > 0 ? round($price / $passengers) : $price;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>Select Seats – <?= $flight_no ?> – Explore India</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="css/bootstrap.css" rel='stylesheet' type='text/css'/>
    <link href="css/style.css" rel='stylesheet' type='text/css'/>
    <link href="css/font-awesome.min.css" rel="stylesheet">
    <link href="//fonts.googleapis.com/css?family=Montserrat:400,600,700,800" rel="stylesheet">
    <link href="//fonts.googleapis.com/css?family=Open+Sans:400,600" rel="stylesheet">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
        body { background: #0a0a0a; font-family: 'Open Sans', sans-serif; color: #fff; overflow-x: hidden; }

        /* DARK HERO */
        .page-hero {
            padding: 100px 20px 40px;
            background: linear-gradient(160deg, #0a0a0a 0%, #0d1220 50%, #0a0a0a 100%);
            text-align: center;
        }
        .hero-badge { display: inline-block; font-size: 10px; font-weight: 700; letter-spacing: 3px; text-transform: uppercase; color: rgba(255,255,255,0.4); border: 1px solid rgba(255,255,255,0.12); padding: 5px 14px; border-radius: 20px; margin-bottom: 14px; }
        .page-hero h1 { font-family: 'Montserrat', sans-serif; font-size: clamp(22px, 4vw, 38px); font-weight: 800; color: #fff; margin-bottom: 10px; text-transform: none !important; }
        .page-hero h1 span { color: #5ea0ff; }
        .flight-bar { display: inline-flex; align-items: center; gap: 14px; flex-wrap: wrap; justify-content: center; background: rgba(94,160,255,0.08); border: 1px solid rgba(94,160,255,0.2); border-radius: 30px; padding: 10px 24px; margin: 14px auto; font-size: 13px; color: rgba(255,255,255,0.6); }
        .flight-bar strong { color: #5ea0ff; }
        .flight-bar .sep { color: rgba(255,255,255,0.15); }

        /* WHITE SEAT SECTION */
        .seat-section { background: #fff; padding: 0; }
        .seat-wrap { max-width: 1060px; margin: 0 auto; padding: 40px 20px 60px; display: grid; grid-template-columns: 1fr 300px; gap: 28px; align-items: start; }
        @media (max-width: 768px) { .seat-wrap { grid-template-columns: 1fr; } }

        /* Plane */
        .plane-container { background: #f8f9fa; border: 1.5px solid #e9ecef; border-radius: 20px; overflow: hidden; }
        .plane-header { padding: 16px 20px; border-bottom: 1px solid #e9ecef; display: flex; align-items: center; justify-content: space-between; }
        .plane-header h3 { font-family: 'Montserrat', sans-serif; font-size: 15px; font-weight: 700; color: #1a1a1a; text-transform: none !important; }
        .plane-header p { font-size: 12px; color: #888; margin: 0; }

        /* Legend */
        .legend { display: flex; gap: 16px; flex-wrap: wrap; padding: 12px 20px; border-bottom: 1px solid #e9ecef; }
        .legend-item { display: flex; align-items: center; gap: 6px; font-size: 11px; color: #666; }
        .legend-box { width: 14px; height: 14px; border-radius: 3px; }
        .legend-box.available { background: rgba(46,204,154,0.2); border: 1px solid #2ecc9a; }
        .legend-box.booked    { background: rgba(255,80,80,0.15); border: 1px solid #ff5050; }
        .legend-box.selected  { background: rgba(94,160,255,0.25); border: 1px solid #5ea0ff; }

        /* Seat Map */
        .seat-map { padding: 6px 10px 10px; overflow-x: auto; display: flex; flex-direction: column; align-items: center; }

        /* Plane nose shape */
        .plane-nose {
            text-align: center;
            padding: 6px 0 2px;
        }
        .plane-nose svg { width: 50px; }

        /* Plane tail shape */
        .plane-tail {
            text-align: center;
            padding: 2px 0 6px;
        }
        .plane-tail svg { width: 44px; }

        /* Exit row marker */
        .exit-row {
            display: flex; align-items: center; gap: 6px;
            margin: 3px 0;
        }
        .exit-label { font-size: 8px; font-weight: 700; color: #e67e22; letter-spacing: 1px; white-space: nowrap; }
        .exit-line  { flex: 1; height: 1px; background: rgba(230,126,34,0.25); border-top: 1px dashed rgba(230,126,34,0.4); }

        /* Col headers */
        .seat-cols-header {
            display: grid;
            grid-template-columns: 18px 22px 22px 22px 12px 22px 22px 22px 18px;
            gap: 2px; margin-bottom: 3px; padding: 0 2px;
        }
        .col-label { font-size: 8px; font-weight: 700; color: #bbb; text-align: center; letter-spacing: 1px; width: 22px; }

        /* Seat row */
        .seat-row {
            display: grid;
            grid-template-columns: 18px 22px 22px 22px 12px 22px 22px 22px 18px;
            gap: 2px; margin-bottom: 2px; align-items: center;
        }
        .row-num { font-size: 8px; color: #ccc; text-align: center; width: 18px; }

        /* SEAT — small with headrest */
        .seat {
            width: 22px;
            height: 26px;
            border-radius: 4px 4px 2px 2px;
            border: 1px solid;
            cursor: pointer;
            transition: all 0.12s;
            display: flex; align-items: center; justify-content: center;
            font-size: 6px; font-weight: 700;
            position: relative;
            flex-shrink: 0;
        }
        /* Headrest nub */
        .seat::before {
            content: '';
            position: absolute;
            top: -1px; left: 22%; right: 22%;
            height: 2px; border-radius: 1px 1px 0 0;
        }
        .seat.available {
            background: rgba(46,204,154,0.1);
            border-color: rgba(46,204,154,0.4);
            color: #2ecc9a;
        }
        .seat.available::before { background: rgba(46,204,154,0.35); }
        .seat.available:hover {
            background: rgba(46,204,154,0.25);
            border-color: #2ecc9a;
            transform: scale(1.12);
            z-index: 2;
            box-shadow: 0 2px 8px rgba(46,204,154,0.3);
        }
        .seat.booked {
            background: rgba(255,80,80,0.07);
            border-color: rgba(255,80,80,0.2);
            color: rgba(255,80,80,0.35);
            cursor: not-allowed;
        }
        .seat.booked::before { background: rgba(255,80,80,0.15); }
        .seat.selected {
            background: rgba(94,160,255,0.2);
            border-color: #5ea0ff;
            color: #5ea0ff;
            transform: scale(1.1);
            z-index: 2;
            box-shadow: 0 2px 10px rgba(94,160,255,0.35);
        }
        .seat.selected::before { background: #5ea0ff; }

        /* Booking Card */
        .booking-card { background: #f8f9fa; border: 1.5px solid #e9ecef; border-radius: 20px; overflow: hidden; position: sticky; top: 100px; }
        .booking-header { padding: 18px 22px 14px; border-bottom: 1px solid #e9ecef; background: #fff; }
        .booking-header h3 { font-family: 'Montserrat', sans-serif; font-size: 15px; font-weight: 700; color: #1a1a1a; text-transform: none !important; }
        .booking-body { padding: 18px 22px; }
        .info-row { display: flex; justify-content: space-between; padding: 8px 0; border-bottom: 1px solid #f0f0f0; }
        .info-row:last-child { border: none; }
        .info-label { font-size: 11px; color: #888; text-transform: uppercase; letter-spacing: 0.5px; }
        .info-value { font-size: 13px; font-weight: 600; color: #1a1a1a; }
        .selected-seats-box { background: rgba(94,160,255,0.06); border: 1px solid rgba(94,160,255,0.2); border-radius: 10px; padding: 12px 14px; margin: 14px 0; min-height: 50px; }
        .selected-label { font-size: 10px; font-weight: 700; letter-spacing: 1px; text-transform: uppercase; color: #888; margin-bottom: 8px; }
        .seat-tags { display: flex; flex-wrap: wrap; gap: 6px; }
        .seat-tag { padding: 3px 10px; border-radius: 12px; background: rgba(94,160,255,0.1); border: 1px solid rgba(94,160,255,0.25); font-size: 12px; font-weight: 700; color: #5ea0ff; }
        .seat-placeholder { font-size: 12px; color: #bbb; }
        .total-box { background: #fff; border: 1.5px solid #e9ecef; border-radius: 12px; padding: 14px 16px; display: flex; justify-content: space-between; align-items: center; margin-bottom: 16px; }
        .total-label { font-size: 11px; font-weight: 700; text-transform: uppercase; color: #888; letter-spacing: 1px; }
        .total-amount { font-family: 'Montserrat', sans-serif; font-size: 24px; font-weight: 800; color: #1a1a1a; }
        .btn-confirm { width: 100%; border: none; border-radius: 12px; background: linear-gradient(135deg, #5ea0ff, #3a7fd4); color: #fff; font-size: 15px; font-weight: 700; font-family: 'Montserrat', sans-serif; padding: 14px; cursor: pointer; transition: transform 0.2s, box-shadow 0.2s; text-transform: none !important; }
        .btn-confirm:hover { transform: translateY(-2px); box-shadow: 0 8px 24px rgba(94,160,255,0.3); }
        .btn-confirm:disabled { background: #e0e0e0; color: #aaa; cursor: not-allowed; transform: none; box-shadow: none; }
        .warn-msg { font-size: 12px; color: #ff5050; text-align: center; margin-top: 10px; display: none; }

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
    <div class="hero-badge">✈️ Seat Selection</div>
    <h1><?= $from ?> → <span><?= $to ?></span></h1>
    <div class="flight-bar">
        <span><strong><?= $flight_no ?></strong></span>
        <span class="sep">|</span>
        <span><?= $airline ?></span>
        <span class="sep">|</span>
        <span><?= date('D, d M Y', strtotime($date)) ?></span>
        <span class="sep">|</span>
        <span>Dep: <strong><?= $dep_time ?></strong></span>
        <span class="sep">|</span>
        <span>Arr: <strong><?= $arr_time ?></strong></span>
        <span class="sep">|</span>
        <span><?= $duration ?></span>
    </div>
</div>

<!-- WHITE SEAT MAP -->
<div class="seat-section">
    <form action="pay_transport.php" method="POST" id="bookingForm">
        <input type="hidden" name="type"       value="flight">
        <input type="hidden" name="flight_no"  value="<?= $flight_no ?>">
        <input type="hidden" name="airline"    value="<?= $airline ?>">
        <input type="hidden" name="from"       value="<?= $from ?>">
        <input type="hidden" name="to"         value="<?= $to ?>">
        <input type="hidden" name="dep_time"   value="<?= $dep_time ?>">
        <input type="hidden" name="arr_time"   value="<?= $arr_time ?>">
        <input type="hidden" name="duration"   value="<?= $duration ?>">
        <input type="hidden" name="date"       value="<?= $date ?>">
        <input type="hidden" name="passengers" value="<?= $passengers ?>">
        <input type="hidden" name="class"      value="<?= $class ?>">
        <input type="hidden" name="price"      value="<?= $price ?>">
        <input type="hidden" name="seats"      id="selectedSeatsInput" value="">

        <div class="seat-wrap">
            <!-- LEFT: Seat Map -->
            <div class="plane-container">
                <div class="plane-header">
                    <div>
                        <h3>✈️ <?= ucfirst($class) ?> Class</h3>
                        <p>Select <?= $passengers ?> seat<?= $passengers > 1 ? 's' : '' ?></p>
                    </div>
                    <div style="font-size:12px; color:#aaa;" id="seatCounter">0 / <?= $passengers ?> selected</div>
                </div>
                <div class="legend">
                    <div class="legend-item"><div class="legend-box available"></div> Available</div>
                    <div class="legend-item"><div class="legend-box booked"></div> Booked</div>
                    <div class="legend-item"><div class="legend-box selected"></div> Selected</div>
                </div>
                <div class="seat-map">

                    <!-- Plane nose SVG -->
                    <div class="plane-nose">
                        <svg viewBox="0 0 70 55" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M35 3 C18 18 10 32 8 52 L62 52 C60 32 52 18 35 3Z"
                                  fill="rgba(94,160,255,0.06)" stroke="#d0d8e8" stroke-width="1.2"/>
                            <!-- Windows -->
                            <circle cx="26" cy="28" r="2.5" fill="rgba(94,160,255,0.15)" stroke="#c8d4e8" stroke-width="0.8"/>
                            <circle cx="35" cy="22" r="2.5" fill="rgba(94,160,255,0.15)" stroke="#c8d4e8" stroke-width="0.8"/>
                            <circle cx="44" cy="28" r="2.5" fill="rgba(94,160,255,0.15)" stroke="#c8d4e8" stroke-width="0.8"/>
                            <!-- Wings hint -->
                            <path d="M8 52 L2 42 L8 46" fill="rgba(200,210,230,0.3)" stroke="#d0d8e8" stroke-width="0.8"/>
                            <path d="M62 52 L68 42 L62 46" fill="rgba(200,210,230,0.3)" stroke="#d0d8e8" stroke-width="0.8"/>
                        </svg>
                    </div>

                    <!-- Col headers -->
                    <div class="seat-cols-header">
                        <div></div>
                        <div class="col-label">A</div>
                        <div class="col-label">B</div>
                        <div class="col-label">C</div>
                        <div></div>
                        <div class="col-label">D</div>
                        <div class="col-label">E</div>
                        <div class="col-label">F</div>
                        <div></div>
                    </div>

                    <?php
                    $seat_num = 1;
                    for ($row = 1; $row <= 30; $row++):
                        // Emergency exit rows
                        if ($row === 11):
                    ?>
                    <div class="exit-row">
                        <span class="exit-label">⚠ EXIT</span>
                        <div class="exit-line"></div>
                        <span class="exit-label">EXIT ⚠</span>
                    </div>
                    <?php endif; ?>

                    <div class="seat-row">
                        <div class="row-num"><?= $row ?></div>

                        <?php foreach (['A','B','C'] as $col):
                            $is_booked = in_array($seat_num, $booked_seats);
                            $seat_id   = $row . $col;
                        ?>
                        <div class="seat <?= $is_booked ? 'booked' : 'available' ?>"
                             data-seat="<?= $seat_id ?>"
                             <?= $is_booked ? '' : 'onclick="toggleSeat(this)"' ?>>
                            <?= $is_booked ? '' : $seat_id ?>
                        </div>
                        <?php $seat_num++; endforeach; ?>

                        <div></div>

                        <?php foreach (['D','E','F'] as $col):
                            $is_booked = in_array($seat_num, $booked_seats);
                            $seat_id   = $row . $col;
                        ?>
                        <div class="seat <?= $is_booked ? 'booked' : 'available' ?>"
                             data-seat="<?= $seat_id ?>"
                             <?= $is_booked ? '' : 'onclick="toggleSeat(this)"' ?>>
                            <?= $is_booked ? '' : $seat_id ?>
                        </div>
                        <?php $seat_num++; endforeach; ?>

                        <div class="row-num"><?= $row ?></div>
                    </div>
                    <?php endfor; ?>

                    <!-- Plane tail SVG -->
                    <div class="plane-tail">
                        <svg viewBox="0 0 60 28" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M30 26 L6 4 L54 4 Z"
                                  fill="rgba(94,160,255,0.05)" stroke="#d0d8e8" stroke-width="1.2"/>
                            <path d="M1 26 L18 10 L18 26 Z" fill="rgba(200,210,230,0.2)"/>
                            <path d="M59 26 L42 10 L42 26 Z" fill="rgba(200,210,230,0.2)"/>
                        </svg>
                    </div>

                </div>
            </div>

            <!-- RIGHT: Booking Summary -->
            <div class="booking-card">
                <div class="booking-header"><h3>Booking Summary</h3></div>
                <div class="booking-body">
                    <div class="info-row"><span class="info-label">Flight</span><span class="info-value"><?= $flight_no ?></span></div>
                    <div class="info-row"><span class="info-label">Route</span><span class="info-value"><?= $from ?> → <?= $to ?></span></div>
                    <div class="info-row"><span class="info-label">Date</span><span class="info-value"><?= date('d M Y', strtotime($date)) ?></span></div>
                    <div class="info-row"><span class="info-label">Class</span><span class="info-value"><?= ucfirst($class) ?></span></div>
                    <div class="info-row"><span class="info-label">Passengers</span><span class="info-value"><?= $passengers ?></span></div>

                    <div class="selected-seats-box">
                        <div class="selected-label">Selected Seats</div>
                        <div class="seat-tags" id="seatTags">
                            <span class="seat-placeholder">No seats selected</span>
                        </div>
                    </div>

                    <div class="total-box">
                        <div class="total-label">Total</div>
                        <div class="total-amount" id="totalAmount">₹<?= number_format($price) ?></div>
                    </div>

                    <button type="submit" class="btn-confirm" id="confirmBtn" disabled>
                        <span class="fa fa-lock"></span> &nbsp; Confirm & Pay
                    </button>
                    <p class="warn-msg" id="warnMsg">Select <?= $passengers ?> seat<?= $passengers>1?'s':'' ?></p>
                </div>
            </div>
        </div>
    </form>
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
var maxSeats     = <?= $passengers ?>;
var pricePerSeat = <?= $price_per ?>;
var selected     = [];

function toggleSeat(el) {
    var seat = el.dataset.seat;
    if (el.classList.contains('selected')) {
        el.classList.remove('selected');
        el.classList.add('available');
        el.textContent = seat;
        selected = selected.filter(s => s !== seat);
    } else {
        if (selected.length >= maxSeats) {
            document.getElementById('warnMsg').style.display = 'block';
            setTimeout(() => document.getElementById('warnMsg').style.display = 'none', 2000);
            return;
        }
        el.classList.remove('available');
        el.classList.add('selected');
        el.textContent = seat;
        selected.push(seat);
    }
    updateSummary();
}

function updateSummary() {
    var tagsEl  = document.getElementById('seatTags');
    var btn     = document.getElementById('confirmBtn');
    var total   = document.getElementById('totalAmount');
    var input   = document.getElementById('selectedSeatsInput');
    var counter = document.getElementById('seatCounter');

    counter.textContent = selected.length + ' / ' + maxSeats + ' selected';

    tagsEl.innerHTML = selected.length === 0
        ? '<span class="seat-placeholder">No seats selected</span>'
        : selected.map(s => '<span class="seat-tag">' + s + '</span>').join('');

    total.textContent = '₹' + (pricePerSeat * Math.max(selected.length, 1)).toLocaleString('en-IN');
    input.value = selected.join(',');
    btn.disabled = selected.length !== maxSeats;
    if (selected.length !== maxSeats) document.getElementById('warnMsg').style.display = 'none';
}
</script>
</body>
</html>