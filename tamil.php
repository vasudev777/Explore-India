<?php include('db.php');
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>Tamil Nadu – Explore India</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <link href="css/bootstrap.css" rel='stylesheet' type='text/css'/>
    <link href="css/style.css" rel='stylesheet' type='text/css'/>
    <link href="css/font-awesome.min.css" rel="stylesheet">
    <link rel="stylesheet" href="css/fling.css">
    <link href="//fonts.googleapis.com/css?family=Montserrat:400,500,600,700,800,900" rel="stylesheet">
    <link href="//fonts.googleapis.com/css?family=Open+Sans:300,400,600,700" rel="stylesheet">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
    <script src="js/disable.js"></script>

    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            background: #0a0a0a;
            font-family: 'Open Sans', sans-serif;
            color: #fff;
            overflow-x: hidden;
        }

        /* ═══════════════════════════
           VIDEO HERO
        ═══════════════════════════ */
        .video-hero {
            position: relative;
            width: 100%;
            height: 100vh;
            min-height: 480px;
            overflow: hidden;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .hero-video {
            position: absolute;
            top: 50%;
            left: 50%;
            width: max(100vw, 177.78vh);
            height: max(56.25vw, 100vh);
            transform: translate(-50%, -50%);
            object-fit: cover;
            pointer-events: none;
        }
        .video-overlay {
            position: absolute;
            inset: 0;
            background: linear-gradient(
                to bottom,
                rgba(0,0,0,0.38) 0%,
                rgba(0,0,0,0.10) 35%,
                rgba(0,0,0,0.62) 75%,
                rgba(0,0,0,0.93) 100%
            );
            z-index: 1;
        }

        /* Mute / Unmute Button */
        .mute-btn {
            position: absolute;
            bottom: 28px;
            right: 24px;
            z-index: 10;
            width: 44px;
            height: 44px;
            border-radius: 50%;
            border: 1.5px solid rgba(255,255,255,0.35);
            background: rgba(0,0,0,0.45);
            color: #fff;
            font-size: 16px;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: background 0.2s, border-color 0.2s, transform 0.15s;
            backdrop-filter: blur(6px);
            -webkit-backdrop-filter: blur(6px);
        }
        .mute-btn:hover {
            background: rgba(0,0,0,0.65);
            border-color: rgba(255,255,255,0.6);
            transform: scale(1.08);
        }

        .hero-content {
            position: relative;
            z-index: 2;
            text-align: center;
            padding: 0 24px;
            max-width: 680px;
        }
        .hero-badge {
            display: inline-block;
            font-size: 10px;
            font-weight: 700;
            letter-spacing: 3px;
            text-transform: uppercase;
            color: rgba(255,255,255,0.6);
            border: 1px solid rgba(255,255,255,0.22);
            padding: 5px 14px;
            border-radius: 20px;
            margin-bottom: 16px;
        }
        .hero-content h1 {
            font-family: 'Montserrat', sans-serif;
            font-size: clamp(36px, 8vw, 74px);
            font-weight: 800;
            color: #fff;
            line-height: 1.08;
            margin-bottom: 14px;
            letter-spacing: -1px;
        }
        .hero-content h1 span { color: #b47cff; }
        .hero-content p {
            font-size: clamp(13px, 2vw, 17px);
            color: rgba(255,255,255,0.68);
            line-height: 1.65;
            margin-bottom: 28px;
        }
        .hero-scroll-btn {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            font-size: 13px;
            font-weight: 600;
            color: #fff;
            background: rgba(180,124,255,0.18);
            border: 1px solid rgba(180,124,255,0.4);
            padding: 10px 22px;
            border-radius: 30px;
            cursor: pointer;
            text-decoration: none;
            transition: background 0.2s, border-color 0.2s;
        }
        .hero-scroll-btn:hover {
            background: rgba(180,124,255,0.28);
            border-color: rgba(180,124,255,0.65);
            color: #fff;
            text-decoration: none;
        }
        .scroll-hint {
            position: absolute;
            bottom: 28px;
            left: 50%;
            transform: translateX(-50%);
            z-index: 2;
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 6px;
            color: rgba(255,255,255,0.38);
            font-size: 10px;
            letter-spacing: 2px;
            text-transform: uppercase;
            animation: bobble 2.2s ease-in-out infinite;
        }
        @keyframes bobble {
            0%,100% { transform: translateX(-50%) translateY(0); }
            50%      { transform: translateX(-50%) translateY(7px); }
        }

        /* ═══════════════════════════
           MAIN CONTENT
        ═══════════════════════════ */
        .main-content {
            background: #0a0a0a;
            padding: 64px 16px 88px;
        }
        .content-wrapper {
            max-width: 1080px;
            margin: 0 auto;
        }
        .section-label {
            font-size: 10px;
            font-weight: 700;
            letter-spacing: 4px;
            text-transform: uppercase;
            color: rgba(255,255,255,0.28);
            margin-bottom: 8px;
        }
        .section-heading {
            font-family: 'Montserrat', sans-serif;
            font-size: clamp(22px, 4vw, 32px);
            font-weight: 700;
            color: #fff;
            margin-bottom: 40px;
        }
        .section-heading span { color: #b47cff; }

        .two-col {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 28px;
            align-items: start;
        }

        /* ── Image Slider Box ── */
        .slider-box {
            background: #141414;
            border: 1px solid rgba(255,255,255,0.07);
            border-radius: 20px;
            overflow: hidden;
        }
        .slider-info { padding: 20px 22px; }

        /* Custom 5-image Slider for Tamil Nadu */
        .fling-minislide-5 {
            width: 100%;
            height: 260px;
            overflow: hidden;
            position: relative;
        }
        .fling-minislide-5 img {
            position: absolute;
            animation: fling-minislide-5 25s infinite;
            opacity: 0;
            width: 100%;
            height: 100%;
            object-fit: cover;
            display: block;
        }
        @keyframes fling-minislide-5 {
            0% { opacity: 0; }
            4% { opacity: 1; }
            20% { opacity: 1; }
            24% { opacity: 0; }
            100% { opacity: 0; }
        }
        .fling-minislide-5 img:nth-child(5) { animation-delay: 0s; }
        .fling-minislide-5 img:nth-child(4) { animation-delay: 5s; }
        .fling-minislide-5 img:nth-child(3) { animation-delay: 10s; }
        .fling-minislide-5 img:nth-child(2) { animation-delay: 15s; }
        .fling-minislide-5 img:nth-child(1) { animation-delay: 20s; }
        
        @media (max-width: 768px) {
            .fling-minislide-5 {
                height: 200px;
            }
        }
        .slider-info h3 {
            font-family: 'Montserrat', sans-serif;
            font-size: 18px;
            font-weight: 700;
            color: #fff;
            margin-bottom: 8px;
        }
        .slider-info p {
            font-size: 13px;
            color: rgba(255,255,255,0.50);
            line-height: 1.6;
        }
        .highlights {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
            margin-top: 16px;
        }
        .highlight-chip {
            display: flex;
            align-items: center;
            gap: 6px;
            font-size: 11.5px;
            font-weight: 600;
            color: #b47cff;
            background: rgba(180,124,255,0.08);
            border: 1px solid rgba(180,124,255,0.22);
            padding: 5px 12px;
            border-radius: 20px;
        }

        /* ── Itinerary + Booking Box ── */
        .package-box {
            background: #141414;
            border: 1px solid rgba(255,255,255,0.07);
            border-radius: 20px;
            overflow: hidden;
            display: flex;
            flex-direction: column;
        }
        .package-header {
            padding: 20px 22px 16px;
            border-bottom: 1px solid rgba(255,255,255,0.06);
        }
        .package-header h3 {
            font-family: 'Montserrat', sans-serif;
            font-size: 16px;
            font-weight: 700;
            color: #fff;
            margin-bottom: 4px;
        }
        .package-header p { font-size: 12px; color: rgba(255,255,255,0.38); }

        .itinerary-list {
            padding: 16px 22px;
            flex: 1;
            max-height: 340px;
            overflow-y: auto;
            scrollbar-width: thin;
            scrollbar-color: rgba(180,124,255,0.3) transparent;
        }
        .itinerary-list::-webkit-scrollbar { width: 4px; }
        .itinerary-list::-webkit-scrollbar-track { background: transparent; }
        .itinerary-list::-webkit-scrollbar-thumb { background: rgba(180,124,255,0.3); border-radius: 4px; }

        .itin-item {
            display: flex;
            gap: 14px;
            padding-bottom: 16px;
            position: relative;
        }
        .itin-item:not(:last-child)::after {
            content: '';
            position: absolute;
            left: 14px;
            top: 28px;
            bottom: 0;
            width: 1px;
            background: rgba(180,124,255,0.15);
        }
        .itin-dot-wrap { flex-shrink: 0; }
        .itin-dot {
            width: 28px;
            height: 28px;
            border-radius: 50%;
            background: rgba(180,124,255,0.12);
            border: 1.5px solid rgba(180,124,255,0.35);
            color: #b47cff;
            font-size: 9.5px;
            font-weight: 700;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .itin-info { flex: 1; padding-top: 3px; }
        .itin-hotel {
            font-size: 13px;
            font-weight: 600;
            color: rgba(255,255,255,0.88);
            margin-bottom: 2px;
        }
        .itin-city { font-size: 11.5px; color: rgba(255,255,255,0.38); }

        .package-footer {
            padding: 18px 22px;
            border-top: 1px solid rgba(255,255,255,0.06);
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
        }
        .price-block { line-height: 1.2; }
        .price-label {
            font-size: 10.5px;
            color: rgba(255,255,255,0.35);
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        .price-value {
            font-family: 'Montserrat', sans-serif;
            font-size: 24px;
            font-weight: 800;
            color: #fff;
        }
        .price-value span { font-size: 13px; font-weight: 500; color: rgba(255,255,255,0.4); }
        .book-btn {
            display: inline-flex;
            align-items: center;
            gap: 7px;
            font-size: 13.5px;
            font-weight: 700;
            color: #0a0a0a;
            background: #b47cff;
            border: none;
            padding: 12px 26px;
            border-radius: 30px;
            cursor: pointer;
            transition: background 0.2s, transform 0.15s;
            white-space: nowrap;
        }
        .book-btn:hover { background: #c49aff; transform: scale(1.04); color: #0a0a0a; }

        /* ═══════════════════════════
           RESPONSIVE
        ═══════════════════════════ */
        @media (max-width: 768px) {
            .two-col { grid-template-columns: 1fr; gap: 20px; }
            .slider-box .fling-minislide img { height: 220px; }
            .itinerary-list { max-height: 260px; }
        }
        @media (max-width: 600px) {
            .video-hero { height: 60vh; min-height: 360px; }
            .hero-content h1 { font-size: clamp(28px, 9vw, 40px); }
            .hero-content p { font-size: 13px; }
            .mute-btn { bottom: 16px; right: 16px; width: 38px; height: 38px; font-size: 14px; }
            .main-content { padding: 44px 14px 64px; }
            .package-footer { flex-direction: column; align-items: flex-start; gap: 14px; }
            .book-btn { width: 100%; justify-content: center; }
        }
    </style>
</head>
<body oncontextmenu="return false;">

<?php include('header.php'); ?>

<!-- ══ VIDEO HERO ══ -->
<div class="video-hero">
    <video
        id="tamilVideo"
        class="hero-video"
        autoplay
        muted
        loop
        playsinline>
        <source src="https://res.cloudinary.com/dkf2ala8d/video/upload/v1781852287/vidssave.com_Best_Travel_Destinations_in_Tamil_Nadu___Must_visit_places_in_Tamil_Nadu_-_TAMIL_NADU_TOURISM_1080P_tfoqbl.mp4" type="video/mp4">
    </video>

    <div class="video-overlay"></div>

    <div class="hero-content">
        <div class="hero-badge">South India</div>
        <h1>Explore <span>Tamil Nadu</span></h1>
        <p>Ancient Dravidian temples, vibrant silk culture, misty Nilgiris and pristine coastal beauty.</p>
        <a href="#package" class="hero-scroll-btn">
            <span class="fa fa-calendar"></span> View Package
        </a>
    </div>

    <button class="mute-btn" id="muteBtn" title="Unmute">
        <span class="fa fa-volume-off" id="muteIcon"></span>
    </button>

    <div class="scroll-hint">
        <span class="fa fa-chevron-down"></span>
        <span>Scroll</span>
    </div>
</div>

<!-- ══ MAIN CONTENT ══ -->
<div class="main-content" id="package">
    <div class="content-wrapper">
        <p class="section-label">Tamil Nadu Package</p>
        <h2 class="section-heading">Your Journey <span>Awaits</span></h2>

        <div class="two-col">

            <!-- LEFT: Image Slider -->
            <div class="slider-box">
                <div class="fling-minislide-5">
                    <img src="images/TAMIL1.jpg"     alt="Madurai"/>
                    <img src="images/TAMIL2.jpg" alt="Ooty"/>
                    <img src="images/TAMIL3.jpg" alt="Rameswaram"/>
                    <img src="images/TAMIL4.jpg"        alt="Kanchipuram"/>
                    <img src="images/TAMIL5.jpg"        alt="Chennai"/>
                </div>
                <div class="slider-info">
                    <h3>Tamil Nadu Highlights</h3>
                    <p>From the towering gopurams of Madurai to the serene hill stations of Ooty, Tamil Nadu is a land of timeless culture and stunning landscapes.</p>
                    <div class="highlights">
                        <span class="highlight-chip"><span class="fa fa-institution"></span> Madurai</span>
                        <span class="highlight-chip"><span class="fa fa-tree"></span> Ooty</span>
                        <span class="highlight-chip"><span class="fa fa-anchor"></span> Rameswaram</span>
                        <span class="highlight-chip"><span class="fa fa-university"></span> Kanchipuram</span>
                        <span class="highlight-chip"><span class="fa fa-building"></span> Chennai</span>
                    </div>
                </div>
            </div>

            <!-- RIGHT: Itinerary + Booking -->
            <div class="package-box">
                <div class="package-header">
                    <h3>Day-wise Itinerary</h3>
                    <p>Hotel stays included • All major cities covered</p>
                </div>

                <div class="itinerary-list">
                <?php
                $sqlhotel = "SELECT * FROM package WHERE pa_name='Tamil Nadu' LIMIT 1";
                $result   = mysqli_query($conn, $sqlhotel);
                $jk       = $result ? mysqli_fetch_assoc($result) : null;

                if ($jk):
                    $hotel_ids = explode(",", $jk['h_id']);
                    $day = 1;
                    foreach ($hotel_ids as $hid):
                        $sql3 = "SELECT h.h_name, c.c_name FROM hotel h
                                 JOIN city c ON h.c_id = c.c_id
                                 WHERE h.h_id = '$hid'";
                        $r3   = mysqli_query($conn, $sql3);
                        $row3 = $r3 ? mysqli_fetch_assoc($r3) : null;
                        if (!$row3) { $day++; continue; }
                ?>
                    <div class="itin-item">
                        <div class="itin-dot-wrap">
                            <div class="itin-dot">D<?= $day ?></div>
                        </div>
                        <div class="itin-info">
                            <div class="itin-hotel"><?= htmlspecialchars($row3['h_name']) ?></div>
                            <div class="itin-city"><?= htmlspecialchars($row3['c_name']) ?></div>
                        </div>
                    </div>
                <?php $day++; endforeach; endif; ?>
                </div>

                <form action="predefine_book.php" method="post">
                    <?php if ($jk): ?>
                    <input type="hidden" name="pack" value="<?= $jk['pa_id'] ?>">
                    <?php endif; ?>
                    <div class="package-footer">
                        <div class="price-block">
                            <div class="price-label">Package Price</div>
                            <?php
                            $sql5  = "SELECT price FROM package WHERE pa_name='Tamil Nadu' LIMIT 1";
                            $r5    = mysqli_query($conn, $sql5);
                            $row5  = $r5 ? mysqli_fetch_assoc($r5) : null;
                            $price = $row5 ? $row5['price'] : 0;
                            ?>
                            <div class="price-value">₹<?= number_format($price) ?> <span>INR</span></div>
                        </div>
                        <button type="submit" name="button" class="book-btn">
                            <span class="fa fa-check-circle"></span> Book Now
                        </button>
                    </div>
                </form>
            </div>

        </div>
    </div>
</div>

<?php include('footer.php'); ?>

<script>
(function () {
    var video = document.getElementById('tamilVideo');
    var btn   = document.getElementById('muteBtn');
    var icon  = document.getElementById('muteIcon');

    if (!btn || !video) return;

    btn.addEventListener('click', function () {
        if (video.muted) {
            video.muted  = false;
            video.volume = 1;
            video.play().catch(function () {
                video.muted    = true;
                icon.className = 'fa fa-volume-off';
                btn.title      = 'Unmute';
            });
            icon.className = 'fa fa-volume-up';
            btn.title      = 'Mute';
        } else {
            video.muted    = true;
            icon.className = 'fa fa-volume-off';
            btn.title      = 'Unmute';
        }
    });

    var scrollBtn = document.querySelector('.hero-scroll-btn');
    if (scrollBtn) {
        scrollBtn.addEventListener('click', function (e) {
            e.preventDefault();
            var target = document.getElementById('package');
            if (target) target.scrollIntoView({ behavior: 'smooth' });
        });
    }
})();
</script>

</body>
</html>