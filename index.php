<?php session_start();
include('db.php');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>Explore India</title>
          <script src="https://sdk.mappls.com/map/sdk/web?v=3.0&access_token=<Static Key>"></script>

    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <link href="css/bootstrap.css" rel='stylesheet' type='text/css'/>
    <link href="css/style.css" rel='stylesheet' type='text/css'/>
    <link href="css/css_slider.css" type="text/css" rel="stylesheet" media="all">
    <link href="css/font-awesome.min.css" rel="stylesheet">
    <link href="//fonts.googleapis.com/css?family=Montserrat:400,500,600,700,800,900" rel="stylesheet">
    <link href="//fonts.googleapis.com/css?family=Open+Sans:300,400,600,700" rel="stylesheet">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
    <script src="js/disable.js"></script>

    <style>
        *, *::before, *::after { box-sizing: border-box; }

        /* ═══════════════════════════
           HERO / BANNER
        ═══════════════════════════ */
        .hero-section {
            position: relative;
            width: 100%;
            height: 100vh;
            min-height: 500px;
            overflow: hidden;
            display: flex;
            align-items: center;
        }

        /* Slideshow images behind */
        .hero-slides {
            position: absolute;
            inset: 0;
        }
        .hero-slide {
            position: absolute;
            inset: 0;
            background-size: cover;
            background-position: center;
            opacity: 0;
            transition: opacity 0.8s ease;
        }
        .hero-slide.active { opacity: 1; }

        /* Each region has its own bg image */
        .hero-slide.north  { background-image: url('images/northindia.jpg'); }
        .hero-slide.south  { background-image: url('images/southindia.jpg'); }
        .hero-slide.east   { background-image: url('images/eastindia2.jpg'); }
        .hero-slide.west   { background-image: url('images/westindia.jpg'); }

        /* Dark overlay */
        .hero-overlay {
            position: absolute;
            inset: 0;
            background: linear-gradient(
                to right,
                rgba(0,0,0,0.75) 0%,
                rgba(0,0,0,0.35) 55%,
                rgba(0,0,0,0.15) 100%
            );
            z-index: 1;
        }

        /* Content on top */
        .hero-content {
            position: relative;
            z-index: 2;
            width: 100%;
            padding: 0 5%;
            display: flex;
            align-items: center;
            gap: 40px;
        }

        /* Left: title + subtitle */
        .hero-left {
            flex: 1;
            max-width: 520px;
        }
        .hero-eyebrow {
            font-size: 11px;
            font-weight: 700;
            letter-spacing: 4px;
            text-transform: uppercase;
            color: rgba(255,255,255,0.55);
            margin-bottom: 14px;
        }
        .hero-title {
            font-family: 'Montserrat', sans-serif;
            font-size: clamp(32px, 6vw, 64px);
            font-weight: 800;
            color: #fff;
            line-height: 1.08;
            margin-bottom: 16px;
            letter-spacing: -1px;
        }
        .hero-title span { color: #f5a623; }
        .hero-subtitle {
            font-size: clamp(13px, 1.8vw, 16px);
            color: rgba(255,255,255,0.65);
            line-height: 1.65;
            margin-bottom: 32px;
            max-width: 420px;
        }

        /* Region buttons */
        .region-btns {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
        }
        .region-btn {
            display: inline-flex;
            align-items: center;
            gap: 7px;
            font-size: 13px;
            font-weight: 600;
            color: rgba(255,255,255,0.75);
            background: rgba(255,255,255,0.09);
            border: 1.5px solid rgba(255,255,255,0.18);
            padding: 10px 20px;
            border-radius: 30px;
            cursor: pointer;
            text-decoration: none;
            transition: all 0.22s ease;
        }
        .region-btn:hover,
        .region-btn.active {
            background: #f5a623;
            border-color: #f5a623;
            color: #0a0a0a;
            text-decoration: none;
            transform: translateY(-2px);
        }
        .region-btn .fa { font-size: 14px; }

        /* Right: destination image card */
        .hero-right {
            flex-shrink: 0;
            width: clamp(220px, 30vw, 380px);
        }
        .dest-card {
            background: rgba(255,255,255,0.07);
            border: 1px solid rgba(255,255,255,0.14);
            border-radius: 20px;
            overflow: hidden;
            backdrop-filter: blur(6px);
        }
        .dest-card-img {
            width: 100%;
            height: 220px;
            object-fit: cover;
            display: block;
            transition: opacity 0.5s ease;
        }
        .dest-card-body {
            padding: 16px 18px;
        }
        .dest-card-name {
            font-family: 'Montserrat', sans-serif;
            font-size: 18px;
            font-weight: 700;
            color: #fff;
            margin-bottom: 4px;
        }
        .dest-card-sub {
            font-size: 12px;
            color: rgba(255,255,255,0.45);
            margin-bottom: 12px;
        }
        .dest-card-link {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            font-size: 12px;
            font-weight: 600;
            color: #f5a623;
            text-decoration: none;
        }
        .dest-card-link:hover { color: #f7c060; text-decoration: none; }

        /* ═══════════════════════════
           ABOUT SECTION (Taj Mahal)
        ═══════════════════════════ */
        /* Keep original style.css styles, just small tweaks */

        /* ═══════════════════════════
           TOP PLACES
        ═══════════════════════════ */
        .team-grid img {
            width: 100%;
            height: 160px;
            object-fit: cover;
            border-radius: 10px;
        }

        /* ═══════════════════════════
           MAP FIX
        ═══════════════════════════ */
        .map iframe {
            width: 100%;
            max-width: 550px;
            height: 350px;
            border-radius: 12px;
        }

        /* ═══════════════════════════
           RESPONSIVE
        ═══════════════════════════ */
        @media (max-width: 768px) {
            .hero-content {
                flex-direction: column;
                align-items: flex-start;
                padding: 0 6% 60px;
                gap: 24px;
            }
            .hero-right { display: none; } /* hide card on mobile — bg image enough */
            .hero-title  { font-size: clamp(28px, 9vw, 44px); }
            .hero-section { height: auto; min-height: 100vh; }
        }
        @media (max-width: 480px) {
            .region-btn { font-size: 12px; padding: 8px 14px; }
        }
        
        .region-btn.active {
    background: #f5a623 !important;
    border-color: #f5a623 !important;
    color: #0a0a0a !important;
}
    </style>
</head>

<!--Tawk.to-->
<script type="text/javascript">
var Tawk_API=Tawk_API||{}, Tawk_LoadStart=new Date();
(function(){
var s1=document.createElement("script"),s0=document.getElementsByTagName("script")[0];
s1.async=true;
s1.src='https://embed.tawk.to/61ac9d19c82c976b71bfb65d/1fm54bb8p';
s1.charset='UTF-8';
s1.setAttribute('crossorigin','*');
s0.parentNode.insertBefore(s1,s0);
})();
</script>

<body oncontextmenu="return false;">

<?php include('header.php'); ?>

<!-- ══ HERO BANNER ══ -->
<div class="hero-section" id="home">

    <!-- Background slides (one per region) -->
    <div class="hero-slides">
        <div class="hero-slide north active" id="slide-north"></div>
        <div class="hero-slide south"        id="slide-south"></div>
        <div class="hero-slide east"         id="slide-east"></div>
        <div class="hero-slide west"         id="slide-west"></div>
    </div>

    <div class="hero-overlay"></div>

    <div class="hero-content">

        <!-- LEFT -->
        <div class="hero-left">
            <p class="hero-eyebrow">Incredible India</p>
            <h1 class="hero-title">Discover <span id="heroRegionName">North</span> India</h1>
            <p class="hero-subtitle" id="heroSubtitle">From the snow-capped peaks of Kashmir to the sacred ghats of Varanasi — North India is a land of timeless wonder.</p>

            <!-- Region selector buttons -->
            <div class="region-btns">
                <a href="#" class="region-btn active" id="btn-north"
                   data-region="north"
                   data-name="North"
                   data-sub="From the snow-capped peaks of Kashmir to the sacred ghats of Varanasi — North India is a land of timeless wonder."
                   data-img="images/northindia.jpg"
                   data-dest="Jammu & Kashmir"
                   data-destsub="Srinagar • Gulmarg • Sonamarg"
                   data-link="jk.php">
                    <span class="fa fa-compass"></span> North India
                </a>
                <a href="#" class="region-btn" id="btn-south"
                   data-region="south"
                   data-name="South"
                   data-sub="Backwaters, ancient temples, spice gardens and pristine coastlines — South India will leave you spellbound."
                   data-img="images/southindia.jpg"
                   data-dest="Kerala"
                   data-destsub="Munnar • Alleppey • Kovalam"
                   data-link="ker.php">
                    <span class="fa fa-compass"></span> South India
                </a>
                <a href="#" class="region-btn" id="btn-east"
                   data-region="east"
                   data-name="East"
                   data-sub="Tea gardens, the Sundarbans, ancient monasteries and the soul of Bengal await in East India."
                   data-img="images/east_bg.jpg"
                   data-dest="West Bengal"
                   data-destsub="Darjeeling • Kolkata • Sundarbans"
                   data-link="wb.php">
                    <span class="fa fa-compass"></span> East India
                </a>
                <a href="#" class="region-btn" id="btn-west"
                   data-region="west"
                   data-name="West"
                   data-sub="Deserts, forts, wildlife sanctuaries and the White Rann — West India is vibrant and royal."
                   data-img="images/westindia.jpg"
                   data-dest="Rajasthan"
                   data-destsub="Jaipur • Udaipur • Jaisalmer"
                   data-link="rj.php">
                    <span class="fa fa-compass"></span> West India
                </a>
            </div>
        </div>

   
    </div>
</div>

<!-- ══ MISSION SECTION ══ -->
<?php include('mission_section.php'); ?>

<!-- ══ EXPLORE INDIA ADVANTAGES ══ -->
<style>
    /* Premium Features Cards styling */
    .feature-card {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 24px;
        padding: 35px 24px;
        text-align: center;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        height: 100%;
        box-shadow: 0 10px 30px rgba(15, 23, 42, 0.02);
    }
    .feature-card:hover {
        transform: translateY(-8px);
        box-shadow: 0 20px 40px rgba(255, 120, 44, 0.08);
        border-color: rgba(255, 120, 44, 0.25);
    }
    
    /* Dynamic Circle for Icon */
    .feature-icon-wrap {
        width: 70px;
        height: 70px;
        border-radius: 20px;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 24px;
        font-size: 26px;
        color: #fff;
        transition: transform 0.3s;
    }
    .feature-card:hover .feature-icon-wrap {
        transform: scale(1.1) rotate(5deg);
    }
    
    .feature-card h4 {
        font-family: 'Montserrat', sans-serif;
        font-size: 17px;
        font-weight: 800;
        color: #0F172A;
        margin-bottom: 12px;
    }
    .feature-card p {
        font-size: 13.5px;
        color: #64748B;
        line-height: 1.6;
        margin: 0;
    }
</style>

<section class="py-5" id="features" style="background: #F8FAFC;">
    <div class="container py-md-5">
        <!-- Section Title Header -->
        <div class="text-center mb-5">
            <h3 class="heading text-center mb-2" style="font-family: 'Montserrat', sans-serif; font-weight:800; color:#0F172A;">The Explore India Advantage</h3>
            <p style="color:#64748B; font-size:14.5px; max-width:550px; margin: 0 auto;">Everything you need for an unforgettable, seamless journey across India, built into one platform.</p>
        </div>
        
        <div class="row">
            <!-- Card 1: Verified Guides -->
            <div class="col-lg-3 col-md-6 mb-4">
                <div class="feature-card">
                    <div class="feature-icon-wrap" style="background: linear-gradient(135deg, #3B82F6, #1D4ED8); box-shadow: 0 8px 20px rgba(59,130,246,0.25);">
                        <i class="fa fa-users"></i>
                    </div>
                    <h4>Verified Local Guides</h4>
                    <p>Connect with certified, admin-approved local guides who know every hidden tale and route of their city.</p>
                </div>
            </div>

            <!-- Card 2: Custom Itinerary -->
            <div class="col-lg-3 col-md-6 mb-4">
                <div class="feature-card">
                    <div class="feature-icon-wrap" style="background: linear-gradient(135deg, #FF782C, #F39C12); box-shadow: 0 8px 20px rgba(255,120,44,0.25);">
                        <i class="fa fa-magic"></i>
                    </div>
                    <h4>Custom Itineraries</h4>
                    <p>Build your own trip structure! Pick a state, choose your favorite hotels, and customize your stay day-by-day.</p>
                </div>
            </div>

            <!-- Card 3: Integrated Transport -->
            <div class="col-lg-3 col-md-6 mb-4">
                <div class="feature-card">
                    <div class="feature-icon-wrap" style="background: linear-gradient(135deg, #10B981, #059669); box-shadow: 0 8px 20px rgba(16,185,129,0.25);">
                        <i class="fa fa-plane"></i>
                    </div>
                    <h4>Unified Bookings</h4>
                    <p>Instantly book train routes, flights, and local cab rides in one single secure transaction page.</p>
                </div>
            </div>

            <!-- Card 4: Predefined Packages -->
            <div class="col-lg-3 col-md-6 mb-4">
                <div class="feature-card">
                    <div class="feature-icon-wrap" style="background: linear-gradient(135deg, #8B5CF6, #6D28D9); box-shadow: 0 8px 20px rgba(139,92,246,0.25);">
                        <i class="fa fa-briefcase"></i>
                    </div>
                    <h4>Special Packages</h4>
                    <p>Prefer pre-planned journeys? Select from our premium tour packages curated for the ultimate sightseeing experience.</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- ══ TESTIMONIALS ══ -->
<?php
// DB se feedback fetch
$fb_sql = "SELECT f.message, f.rating, f.type,
                  CONCAT(c.cust_fname, ' ', c.cust_lname) as full_name,
                  c.cust_gender
           FROM feedback f
           JOIN customer_details c ON c.cust_id = f.cust_id
           ORDER BY f.cust_id DESC";
$fb_res   = mysqli_query($conn, $fb_sql);
$feedbacks = [];
if ($fb_res) {
    while ($row = mysqli_fetch_assoc($fb_res)) {
        $feedbacks[] = $row;
    }
}

// Fallback
if (empty($feedbacks)) {
    $feedbacks = [
        ['message'=>'Flight booking was super smooth!', 'rating'=>5, 'type'=>'transport', 'full_name'=>'Rahul S.', 'cust_gender'=>'Male'],
        ['message'=>'Customized my package in minutes!', 'rating'=>5, 'type'=>'customized_package', 'full_name'=>'Priya M.', 'cust_gender'=>'Female'],
        ['message'=>'Kerala package exceeded expectations!', 'rating'=>5, 'type'=>'special_package', 'full_name'=>'Amit K.', 'cust_gender'=>'Male'],
        ['message'=>'Local guide was very knowledgeable.', 'rating'=>5, 'type'=>'local_guide', 'full_name'=>'Neha R.', 'cust_gender'=>'Female'],
        ['message'=>'Website is easy to navigate!', 'rating'=>4, 'type'=>'website', 'full_name'=>'Vikram P.', 'cust_gender'=>'Male'],
        ['message'=>'Train seat selection is amazing!', 'rating'=>5, 'type'=>'transport', 'full_name'=>'Sneha D.', 'cust_gender'=>'Female'],
    ];
}

$chunks = array_chunk($feedbacks, 3);
$total_slides = count($chunks);

$type_labels = [
    'transport'          => ['label'=>'Transport',         'icon'=>'✈️', 'color'=>'#5ea0ff'],
    'customized_package' => ['label'=>'Customize Package', 'icon'=>'🗺️', 'color'=>'#f5a623'],
    'special_package'    => ['label'=>'Special Package',   'icon'=>'⭐', 'color'=>'#5ecfa8'],
    'local_guide'        => ['label'=>'Local Guide',       'icon'=>'👤', 'color'=>'#b47cff'],
    'website'            => ['label'=>'Website/App',       'icon'=>'💻', 'color'=>'#ff6b6b'],
];
?>

<section class="testimonials py-5" id="testimonials">
    <div class="container py-md-5">
        <h3 class="heading heading1 text-center mb-3 mb-sm-5">Client Reviews</h3>

        <!-- Slider wrapper -->
        <div class="test-slider-wrap" style="position:relative;">

            <!-- LEFT button -->
            <?php if ($total_slides > 1): ?>
            <button class="test-nav-btn test-prev" onclick="changeSlide(-1)" title="Previous">
                <span class="fa fa-chevron-left"></span>
            </button>
            <?php endif; ?>

            <div class="test-slider" id="testSlider">
                <?php foreach ($chunks as $ci => $chunk): ?>
                <div class="test-slide <?= $ci === 0 ? 'active' : '' ?>">
                    <div class="row">
                        <?php foreach ($chunk as $fb):
                            $name    = htmlspecialchars($fb['full_name']);
                            $msg     = htmlspecialchars($fb['message']);
                            $rating  = intval($fb['rating'] ?? 5);
                            $type    = $fb['type'] ?? 'website';
                            $tinfo   = $type_labels[$type] ?? $type_labels['website'];
                            $initial = strtoupper(substr($name, 0, 1));
                            $gender  = strtolower($fb['cust_gender'] ?? 'male');
                            $avatarBg = $gender === 'female'
                                ? 'linear-gradient(135deg,#b47cff,#8a4fd4)'
                                : 'linear-gradient(135deg,#f5a623,#d48a1a)';
                        ?>
                        <div class="col-lg-4 col-sm-6 mb-4">
                            <div class="test-card">
                                <!-- Type badge -->
                                <div class="test-type-badge" style="color:<?= $tinfo['color'] ?>;border-color:<?= $tinfo['color'] ?>22;background:<?= $tinfo['color'] ?>11;">
                                    <?= $tinfo['icon'] ?> <?= $tinfo['label'] ?>
                                </div>

                                <!-- Message -->
                                <p class="test-msg">
                                    <span class="fa fa-quote-left test-quote"></span>
                                    <?= $msg ?>
                                    <span class="fa fa-quote-right test-quote"></span>
                                </p>

                                <!-- Stars -->
                                <div class="test-stars">
                                    <?php for ($s = 1; $s <= 5; $s++): ?>
                                    <span class="fa fa-star <?= $s <= $rating ? 'star-filled' : 'star-empty' ?>"></span>
                                    <?php endfor; ?>
                                </div>

                                <!-- Author -->
                                <div class="test-author">
                                    <div class="test-avatar" style="background:<?= $avatarBg ?>;">
                                        <?= $initial ?>
                                    </div>
                                    <div class="test-author-info">
                                        <div class="test-name"><?= $name ?></div>
                                        <div class="test-verified"><span class="fa fa-check-circle"></span> Verified Customer</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>

            <!-- RIGHT button -->
            <?php if ($total_slides > 1): ?>
            <button class="test-nav-btn test-next" onclick="changeSlide(1)" title="Next">
                <span class="fa fa-chevron-right"></span>
            </button>
            <?php endif; ?>
        </div>

        <!-- Dots -->
        <?php if ($total_slides > 1): ?>
        <div class="test-dots" id="testDots">
            <?php for ($i = 0; $i < $total_slides; $i++): ?>
            <span class="test-dot <?= $i === 0 ? 'active' : '' ?>" onclick="goToSlide(<?= $i ?>)"></span>
            <?php endfor; ?>
        </div>
        <?php endif; ?>

    </div>
</section>

<style>
/* Cards */
.test-card {
    background: #1a1a2e;
    border: 1px solid rgba(255,255,255,0.08);
    border-radius: 16px;
    padding: 22px 20px;
    height: 100%;
    display: flex; flex-direction: column; gap: 12px;
    transition: transform 0.2s, box-shadow 0.2s;
}
.test-card:hover {
    transform: translateY(-4px);
    box-shadow: 0 12px 32px rgba(0,0,0,0.4);
}

.test-type-badge {
    display: inline-flex; align-items: center; gap: 5px;
    font-size: 10px; font-weight: 700; letter-spacing: 0.5px;
    padding: 4px 10px; border-radius: 20px; border: 1px solid;
    width: fit-content;
}

.test-msg {
    font-size: 13.5px; color: rgba(255,255,255,0.75);
    line-height: 1.65; flex: 1;
}
.test-quote { color: #f5a623; font-size: 11px; opacity: 0.6; }

.test-stars { display: flex; gap: 3px; }
.star-filled { color: #f5a623; font-size: 13px; }
.star-empty  { color: rgba(255,255,255,0.15); font-size: 13px; }

.test-author { display: flex; align-items: center; gap: 10px; margin-top: 4px; }
.test-avatar {
    width: 40px; height: 40px; border-radius: 50%;
    display: flex; align-items: center; justify-content: center;
    font-family: 'Montserrat', sans-serif;
    font-size: 16px; font-weight: 800; color: #000;
    flex-shrink: 0;
}
.test-name     { font-size: 13px; font-weight: 700; color: #fff; }
.test-verified { font-size: 10px; color: #5ecfa8; margin-top: 2px; }
.test-verified .fa { font-size: 9px; }

/* Slider */
.test-slider { overflow: hidden; }
.test-slide  { display: none; animation: fadeUp 0.5s ease; }
.test-slide.active { display: block; }
@keyframes fadeUp {
    from { opacity: 0; transform: translateY(16px); }
    to   { opacity: 1; transform: translateY(0); }
}

/* Nav buttons */
.test-nav-btn {
    position: absolute; top: 50%; transform: translateY(-50%);
    width: 40px; height: 40px; border-radius: 50%;
    background: rgba(255,255,255,0.08);
    border: 1px solid rgba(255,255,255,0.15);
    color: #fff; font-size: 14px; cursor: pointer;
    display: flex; align-items: center; justify-content: center;
    transition: all 0.2s; z-index: 10;
}
.test-nav-btn:hover { background: rgba(245,166,35,0.2); border-color: #f5a623; color: #f5a623; }
.test-prev { left: -20px; }
.test-next { right: -20px; }

/* Dots */
.test-dots { display: flex; justify-content: center; gap: 8px; margin-top: 24px; }
.test-dot  { width: 8px; height: 8px; border-radius: 50%; background: rgba(255,255,255,0.2); cursor: pointer; transition: all 0.3s; }
.test-dot.active { background: #f5a623; width: 24px; border-radius: 4px; }

@media (max-width: 600px) {
    .test-prev { left: -10px; }
    .test-next { right: -10px; }
}
</style>

<script>
(function() {
    var current = 0;
    var total   = <?= $total_slides ?>;
    var timer;

    function goToSlide(n) {
        var slides = document.querySelectorAll('.test-slide');
        var dots   = document.querySelectorAll('.test-dot');
        slides[current].classList.remove('active');
        if (dots[current]) dots[current].classList.remove('active');
        current = ((n % total) + total) % total;
        slides[current].classList.add('active');
        if (dots[current]) dots[current].classList.add('active');
    }

    window.goToSlide = goToSlide;

    window.changeSlide = function(dir) {
        clearInterval(timer);
        goToSlide(current + dir);
        autoPlay();
    };

    function autoPlay() {
        timer = setInterval(function() { goToSlide(current + 1); }, 3000);
    }

    var slider = document.getElementById('testSlider');
    if (slider) {
        slider.addEventListener('mouseenter', function() { clearInterval(timer); });
        slider.addEventListener('mouseleave', function() { autoPlay(); });
    }

    if (total > 1) autoPlay();
})();
</script>

<!-- Leaflet Map CSS & JS CDN (Loaded locally for instant execution) -->
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

<!-- ══ CONTACT SECTION WITH FAQ & BRANCH MAP ══ -->
<style>
    /* Contact Cards Styling */
    .contact-info-card {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 20px;
        padding: 24px 18px;
        transition: transform 0.2s, box-shadow 0.2s;
        height: 100%;
        box-shadow: 0 4px 15px rgba(15, 23, 42, 0.01);
    }
    .contact-info-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 10px 25px rgba(255, 120, 44, 0.06);
        border-color: rgba(255, 120, 44, 0.2);
    }
    .contact-icon-circle {
        width: 46px;
        height: 46px;
        border-radius: 50%;
        background: rgba(255, 120, 44, 0.08);
        color: #FF782C;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 16px;
        margin: 0 auto 12px;
    }
    .contact-info-card h6 {
        font-family: 'Montserrat', sans-serif;
        font-size: 14.5px;
        font-weight: 800;
        color: #0F172A;
        margin-bottom: 6px;
    }
    .contact-info-card p, .contact-info-card a {
        font-size: 12.5px;
        color: #64748B;
        text-decoration: none;
        line-height: 1.5;
    }
    .contact-info-card a:hover { color: #FF782C; }

    /* FAQ Collapsible Panel Styling */
    .faq-container {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 24px;
        padding: 30px;
        height: 100%;
        box-shadow: 0 10px 30px rgba(0,0,0,0.01);
    }
    .faq-container h4 {
        font-family: 'Montserrat', sans-serif;
        font-size: 18px;
        font-weight: 800;
        color: #0F172A;
        margin-bottom: 20px;
        display: flex;
        align-items: center;
        gap: 8px;
    }
    .faq-item {
        border-bottom: 1px solid #E2E8F0;
        padding: 16px 0;
    }
    .faq-item:last-child {
        border-bottom: none;
    }
    .faq-question {
        font-family: 'Montserrat', sans-serif;
        font-size: 13.5px;
        font-weight: 700;
        color: #1E293B;
        cursor: pointer;
        display: flex;
        justify-content: space-between;
        align-items: center;
        user-select: none;
    }
    .faq-question:hover {
        color: #FF782C;
    }
    .faq-question .faq-icon {
        font-size: 12px;
        color: #94A3B8;
        transition: transform 0.2s;
    }
    .faq-answer {
        max-height: 0;
        overflow: hidden;
        transition: max-height 0.2s ease-out;
        font-size: 13px;
        color: #64748B;
        line-height: 1.6;
    }
    .faq-item.active .faq-answer {
        max-height: 100px;
        margin-top: 10px;
    }
    .faq-item.active .faq-question .faq-icon {
        transform: rotate(180deg);
        color: #FF782C;
    }

    /* Leaflet custom map container */
    #leaflet-map {
        width: 100%;
        height: 100%;
        min-height: 440px;
        border-radius: 24px;
        border: 1px solid #e2e8f0;
        box-shadow: 0 10px 30px rgba(0,0,0,0.01);
        z-index: 10;
    }
</style>

<section class="py-5" id="contact" style="background: #ffffff;">
    <div class="container py-md-5">
        <!-- Section Header -->
        <div class="text-center mb-5">
            <h3 class="heading text-center mb-2" style="font-family: 'Montserrat', sans-serif; font-weight:800; color:#0F172A;">Get In Touch</h3>
            <p style="color:#64748B; font-size:14.5px;">Have questions? Find quick answers below or explore our branch offices map.</p>
        </div>

        <!-- Contact Cards row -->
        <div class="row text-center mb-5">
            <div class="col-md-4 mb-4">
                <div class="contact-info-card">
                    <div class="contact-icon-circle">
                        <span class="fa fa-map-marker"></span>
                    </div>
                    <h6>Location</h6>
                    <p>Explore India, Gujarat, India.</p>
                </div>
            </div>
            <div class="col-md-4 mb-4">
                <div class="contact-info-card">
                    <div class="contact-icon-circle">
                        <span class="fa fa-envelope-open-o"></span>
                    </div>
                    <h6>Phone & Email</h6>
                    <p style="font-weight:700; color:#0F172A; margin-bottom: 2px;">📞 1800-405025</p>
                    <a href="mailto:exploreindiaplaner@gmail.com">exploreindiaplaner@gmail.com</a>
                </div>
            </div>
            <div class="col-md-4 mb-4">
                <div class="contact-info-card">
                    <div class="contact-icon-circle">
                        <span class="fa fa-building"></span>
                    </div>
                    <h6>Our Branches</h6>
                    <p>Vadodara (HQ) • Delhi • Mumbai • Bangalore • Kolkata</p>
                </div>
            </div>
        </div>

        <!-- FAQ and Map Grid -->
        <div class="contact-grids">
            <div class="row">
                <!-- Left: FAQ Accordion -->
                <div class="col-lg-6 mb-4">
                    <div class="faq-container">
                        <h4><i class="fa fa-question-circle" style="color:#FF782C;"></i> Frequently Asked Questions</h4>
                        
                        <div class="faq-item">
                            <div class="faq-question" onclick="toggleFaq(this)">
                                1. How do I book a verified Local Guide?
                                <i class="fa fa-chevron-down faq-icon"></i>
                            </div>
                            <div class="faq-answer">
                                Simply navigate to the Local Guides page, filter by your preferred destination/state, select a guide, and book instantly.
                            </div>
                        </div>

                        <div class="faq-item">
                            <div class="faq-question" onclick="toggleFaq(this)">
                                2. Can I customize my itinerary after booking?
                                <i class="fa fa-chevron-down faq-icon"></i>
                            </div>
                            <div class="faq-answer">
                                Yes, custom bookings are adjustable. You can modify hotel choices or itinerary days through your profile dashboard before final confirmation.
                            </div>
                        </div>

                        <div class="faq-item">
                            <div class="faq-question" onclick="toggleFaq(this)">
                                3. What is the booking cancellation policy?
                                <i class="fa fa-chevron-down faq-icon"></i>
                            </div>
                            <div class="faq-answer">
                                You can cancel any booking up to 48 hours prior to the travel date for a 100% refund, processed directly to your source payment gateway.
                            </div>
                        </div>

                        <div class="faq-item">
                            <div class="faq-question" onclick="toggleFaq(this)">
                                4. Are payment gateways secured?
                                <i class="fa fa-chevron-down faq-icon"></i>
                            </div>
                            <div class="faq-answer">
                                Yes. All transactions use industry-standard SSL encryption and secure API routes to ensure your banking details are never stored.
                            </div>
                        </div>
                    </div>
                </div>
              
                <!-- Right: Interactive Leaflet Map with Multiple Pins -->
                <div class="col-lg-6 mb-4">
                    <div id="leaflet-map"></div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Leaflet Map Initialization and Markers script -->
<script>
// FAQ Accordion Toggle JS
function toggleFaq(element) {
    const parent = element.parentElement;
    parent.classList.toggle('active');
}

// Leaflet Map Script
document.addEventListener("DOMContentLoaded", function () {
    // Center map on India coordinates [Latitude, Longitude], Zoom level 5
    var map = L.map('leaflet-map').setView([21.8000, 79.0000], 5);

    // Load OpenStreetMap tiles
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '© OpenStreetMap contributors',
        maxZoom: 18
    }).addTo(map);

    // Define location coordinates
    var locations = [
        { lat: 22.3072, lng: 73.1812, title: "<b>Explore India HQ</b><br>Vadodara Office" },
        { lat: 28.6139, lng: 77.2090, title: "<b>Explore India</b><br>Delhi Branch Office" },
        { lat: 19.0760, lng: 72.8777, title: "<b>Explore India</b><br>Mumbai Branch Office" },
        { lat: 12.9716, lng: 77.5946, title: "<b>Explore India</b><br>Bangalore Branch Office" },
        { lat: 22.5726, lng: 88.3639, title: "<b>Explore India</b><br>Kolkata Branch Office" }
    ];

    // Add markers and bind popups to map
    locations.forEach(function (loc) {
        var marker = L.marker([loc.lat, loc.lng]).addTo(map);
        marker.bindPopup(loc.title);
    });
});
</script>

<!-- ══ FOOTER ══ -->




<div class="move-top text-right">
    <a href="#home" class="move-top">
        <span class="fa fa-angle-up mb-3" aria-hidden="true"></span>
    </a>
</div>

<!-- ══ HERO SLIDESHOW JS ══ -->
<script>
(function () {
    var regions = {
        north: {
            name: 'North',
            sub:  'From the snow-capped peaks of Kashmir to the sacred ghats of Varanasi — North India is a land of timeless wonder.',
            img:  'images/northindia.jpg',
            dest: 'Jammu & Kashmir',
            dsub: 'Srinagar • Gulmarg • Sonamarg',
            link: 'jk.php'
        },
        south: {
            name: 'South',
            sub:  'Backwaters, ancient temples, spice gardens and pristine coastlines — South India will leave you spellbound.',
            img:  'images/southindia.jpg',
            dest: 'Kerala',
            dsub: 'Munnar • Alleppey • Kovalam',
            link: 'ker.php'
        },
        east: {
            name: 'East',
            sub:  'Tea gardens, the Sundarbans, ancient monasteries and the soul of Bengal await in East India.',
            img:  'images/eastindia2.jpg',
            dest: 'West Bengal',
            dsub: 'Darjeeling • Kolkata • Sundarbans',
            link: 'wb.php'
        },
        west: {
            name: 'West',
            sub:  'Deserts, forts, wildlife sanctuaries and the White Rann — West India is vibrant and royal.',
            img:  'images/westindia.jpg',
            dest: 'Rajasthan',
            dsub: 'Jaipur • Udaipur • Jaisalmer',
            link: 'rj.php'
        }
    };

    var regionKeys  = ['north', 'south', 'east', 'west'];
    var currentRegion = 'north';

    function switchRegion(key) {
        if (key === currentRegion) return;
        var r = regions[key];

        // ✅ Slide transition
        document.querySelector('.hero-slide.active').classList.remove('active');
        document.getElementById('slide-' + key).classList.add('active');

        // ✅ Text update
        document.getElementById('heroRegionName').textContent = r.name;
        document.getElementById('heroSubtitle').textContent   = r.sub;

        // ✅ Button active
        document.querySelectorAll('.region-btn').forEach(function(b) {
            b.classList.remove('active');
        });
        document.getElementById('btn-' + key).classList.add('active');

        currentRegion = key;
    }

    // ✅ Button click
    document.querySelectorAll('.region-btn').forEach(function(btn) {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            var key = this.getAttribute('data-region');
            if (key === currentRegion) {
                // Same button dobara click = navigate
                window.location.href = this.getAttribute('data-link');
            } else {
                switchRegion(key);
            }
        });
    });

    // ✅ Auto cycle — north se NEXT se shuru
    var idx = 0; // north already active hai
    setInterval(function () {
        idx = (idx + 1) % regionKeys.length;
        switchRegion(regionKeys[idx]);
    }, 5000);

})();
</script>
<?php include('footer.php'); ?>
</body>
</html>