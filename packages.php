<?php
include('db.php');
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>Our Special Packages – Explore India</title>
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

        /* PAGE HERO */
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
            font-size: clamp(28px, 6vw, 52px); font-weight: 800;
            color: #fff; margin-bottom: 14px; letter-spacing: -1px; text-transform: none !important;
        }
        .page-hero h1 span { color: #f5a623; }
        .page-hero .sub {
            font-size: clamp(13px, 2vw, 16px); color: rgba(255,255,255,0.45);
            max-width: 480px; margin: 0 auto; line-height: 1.65;
        }

        /* SECTION */
        .sec-wrap { padding: 60px 20px 80px; max-width: 1080px; margin: 0 auto; }
        .sec-label {
            text-align: center; font-size: 10px; font-weight: 700;
            letter-spacing: 4px; text-transform: uppercase;
            color: rgba(255,255,255,0.28); margin-bottom: 10px;
        }
        .sec-heading {
            text-align: center; font-family: 'Montserrat', sans-serif;
            font-size: clamp(20px, 3.5vw, 30px); font-weight: 700;
            color: #fff !important; margin-bottom: 36px; text-transform: none !important;
        }
        .sec-heading span { color: #f5a623 !important; }

        /* SERVICE CARDS */
        .services-grid {
            display: grid; grid-template-columns: repeat(2, 1fr);
            gap: 20px; max-width: 800px; margin: 0 auto 70px;
        }
        .service-card {
            background: #141414; border: 1px solid rgba(255,255,255,0.07);
            border-radius: 20px; padding: 32px 28px;
            display: flex; flex-direction: column; align-items: center;
            text-align: center; gap: 14px; text-decoration: none !important;
            transition: transform 0.28s ease, border-color 0.28s ease, box-shadow 0.28s ease;
        }
        .service-card:hover {
            transform: translateY(-6px); border-color: rgba(255,255,255,0.18);
            box-shadow: 0 24px 48px rgba(0,0,0,0.6);
        }
        .service-icon {
            width: 60px; height: 60px; border-radius: 16px;
            display: flex; align-items: center; justify-content: center; font-size: 24px;
        }
        .service-icon.orange { background: rgba(245,166,35,0.12); color: #f5a623; border: 1px solid rgba(245,166,35,0.22); }
        .service-icon.blue   { background: rgba(94,160,255,0.12);  color: #5ea0ff; border: 1px solid rgba(94,160,255,0.22); }
        .service-card h4 {
            font-family: 'Montserrat', sans-serif; font-size: 17px; font-weight: 700;
            color: #fff !important; margin: 0; text-transform: none !important;
        }
        .service-card p { font-size: 13px; color: rgba(255,255,255,0.42); line-height: 1.7; margin: 0; text-transform: none !important; }
        .service-cta {
            margin-top: 4px; display: inline-flex; align-items: center; gap: 5px;
            font-size: 12.5px; font-weight: 600; text-decoration: none; text-transform: none !important;
        }
        .service-card.orange-card .service-cta { color: #f5a623; }
        .service-card.blue-card   .service-cta { color: #5ea0ff; }
        .cta-arrow { transition: transform 0.2s; display: inline-block; }
        .service-card:hover .cta-arrow { transform: translateX(4px); }

        /* REGION CARDS */
        .region-wrap {
            position: relative; overflow: hidden; border-radius: 16px;
            transition: transform 0.28s ease, box-shadow 0.28s ease;
            text-decoration: none !important; display: block;
        }
        .region-wrap:hover { transform: translateY(-5px); box-shadow: 0 28px 56px rgba(0,0,0,0.7); }
        .region-wrap .bg-image-left,
        .region-wrap .bg-image-right,
        .region-wrap .bg-image-bottom1,
        .region-wrap .bg-image-bottom2 {
            margin: 0 !important; border-radius: 16px; transition: transform 0.4s ease;
        }
        .region-wrap:hover .bg-image-left,
        .region-wrap:hover .bg-image-right,
        .region-wrap:hover .bg-image-bottom1,
        .region-wrap:hover .bg-image-bottom2 { transform: scale(1.04); }

        .region-overlay {
            position: absolute; inset: 0;
            background: linear-gradient(to top, rgba(0,0,0,0.82) 0%, rgba(0,0,0,0.25) 55%, rgba(0,0,0,0.05) 100%);
            border-radius: 16px; pointer-events: none; z-index: 1;
        }
        .region-text {
            position: absolute; bottom: 0; left: 0; right: 0;
            padding: 18px 20px; z-index: 2;
        }
        .region-badge {
            display: inline-block; font-size: 9px; font-weight: 600;
            letter-spacing: 1.5px; text-transform: uppercase !important;
            color: rgba(255,255,255,0.55); border: 1px solid rgba(255,255,255,0.2);
            padding: 3px 9px; border-radius: 20px; margin-bottom: 6px;
        }
        .region-text h4 {
            font-family: 'Montserrat', sans-serif !important;
            font-size: clamp(15px, 2.2vw, 20px) !important;
            font-weight: 800 !important; color: #fff !important;
            margin-bottom: 3px !important; line-height: 1.2 !important;
            text-transform: none !important; letter-spacing: normal !important;
        }
        .region-text h4 .hl { color: #f5a623 !important; }
        .region-sub {
            font-size: 11.5px !important; color: rgba(255,255,255,0.48) !important;
            margin-bottom: 8px !important; text-transform: none !important;
            font-family: 'Open Sans', sans-serif !important;
        }
        .region-explore {
            display: inline-flex; align-items: center; gap: 5px;
            font-size: 12px; font-weight: 600;
            color: rgba(255,255,255,0.80) !important; text-transform: none !important;
        }
        .region-explore .fa { transition: transform 0.2s; }
        .region-wrap:hover .region-explore .fa { transform: translateX(4px); }

        @media (max-width: 700px) {
            .services-grid { grid-template-columns: 1fr; }
            .service-card { padding: 22px 18px; }
        }
    </style>
</head>
<body>
<?php include('header.php'); ?>

<!-- PAGE HERO -->
<div class="page-hero">
    <p class="eyebrow">Explore India</p>
   <h1>Our <span>Services</span></h1>
    <p class="sub">Customize your dream trip or choose from our curated regional packages — we have something for every traveller.</p>
</div>

<!-- SERVICES -->
<div class="sec-wrap" style="padding-bottom: 0;">
    <p class="sec-label">What we offer</p>
  
    <div class="services-grid">
        <a href="customize.php" class="service-card orange-card">
            <div class="service-icon orange"><span class="fa fa-map-signs"></span></div>
            <h4>Customize Package</h4>
            <p>Plan your perfect trip — pick your cities, hotels and days exactly the way you want.</p>
            
             <div class="service-cta" style="color:#5ea0ff;">Customize now  <span class="cta-arrow">→</span></div>
        </a>
<a href="transport.php" class="service-card" style="border-color:rgba(94,160,255,0.15);">
    <div class="service-icon" style="background:rgba(94,160,255,0.12);color:#5ea0ff;border-color:rgba(94,160,255,0.22);">
        <span class="fa fa-plane"></span>
    </div>
    <h4>Flight / Train / Cab</h4>
    <p>Book flights, trains and cabs across India — all in one place.</p>
    <div class="service-cta" style="color:#5ea0ff;">Book now <span class="cta-arrow">→</span></div>
</a>
    </div>
</div>

<!-- OUR SPECIAL PACKAGES — North/South/East/West -->
<div class="sec-wrap">
    <p class="sec-label">Predefined</p>
   <h2 class="sec-heading"><span>Our</span> <span>Special</span> <span>Packages</span></h2>

    <div class="row" style="margin: 0;">

        <!-- North — big left -->
        <div class="col-md-6 p-md-0 mb-3 pr-md-2">
            <a href="north.php" class="region-wrap">
                <div class="bg-image-left" style="margin:0;"></div>
                <div class="region-overlay"></div>
                <div class="region-text">
                    <span class="region-badge">Special Package</span>
                    <h4>Discover <span class="hl">North</span> India</h4>
                    <p class="region-sub">Kashmir • Char Dham • Himachal</p>
                    <div class="region-explore">Explore packages <span class="fa fa-arrow-right"></span></div>
                </div>
            </a>
        </div>

        <!-- South + West + East -->
        <div class="col-md-6 p-md-0 pl-md-2">
            <a href="south.php" class="region-wrap" style="margin-bottom: 12px;">
                <div class="bg-image-right" style="margin:0;"></div>
                <div class="region-overlay"></div>
                <div class="region-text">
                    <span class="region-badge">Special Package</span>
                    <h4>Enchanted <span class="hl">South</span> India</h4>
                    <p class="region-sub">Kerala • Tamil Nadu</p>
                    <div class="region-explore">Explore packages <span class="fa fa-arrow-right"></span></div>
                </div>
            </a>

            <div class="row" style="margin: 0;">
                <div class="col-6 pl-0 pr-1">
                    <a href="west.php" class="region-wrap">
                        <div class="bg-image-bottom1" style="margin:0;"></div>
                        <div class="region-overlay"></div>
                        <div class="region-text">
                            <span class="region-badge">Package</span>
                            <h4>Vibrant <span class="hl">West</span></h4>
                            <p class="region-sub">Rajasthan • Gujarat • MP</p>
                            <div class="region-explore">Explore <span class="fa fa-arrow-right"></span></div>
                        </div>
                    </a>
                </div>
                <div class="col-6 pr-0 pl-1">
                    <a href="east.php" class="region-wrap">
                        <div class="bg-image-bottom2" style="margin:0;"></div>
                        <div class="region-overlay"></div>
                        <div class="region-text">
                            <span class="region-badge">Package</span>
                            <h4>Cultured <span class="hl">East</span></h4>
                            <p class="region-sub">Bengal • Assam • Odisha</p>
                            <div class="region-explore">Explore <span class="fa fa-arrow-right"></span></div>
                        </div>
                    </a>
                </div>
            </div>
        </div>

    </div>
</div>

<?php include('footer.php'); ?>
</body>
</html>