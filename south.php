<?php  
include('db.php');
session_start();
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <title>South India – Explore India</title>
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
        }

        /* ── Navbar ── */
        .top-head {
            padding: 10px 0;
            color: #fff;
        }
        .top-head ul {
            list-style: none;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: flex-end;
        }
        nav {
            display: flex;
            align-items: center;
            padding: 12px 0;
        }
        #logo h1 a {
            color: #fff;
            text-decoration: none;
            font-family: 'Montserrat', sans-serif;
            font-size: 22px;
            font-weight: 700;
        }

        /* ── Hero Section ── */
        .hero {
            position: relative;
            min-height: 340px;
            background: linear-gradient(135deg, #0a2e1a 0%, #1a5c3a 40%, #0d3b2e 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            text-align: center;
            padding: 60px 20px 50px;
            overflow: hidden;
        }
        .hero::before {
            content: '';
            position: absolute;
            inset: 0;
            background: url('images/south-bg.jpg') center/cover no-repeat;
            opacity: 0.25;
        }
        .hero-content {
            position: relative;
            z-index: 1;
        }
        .hero-content h2 {
            font-family: 'Montserrat', sans-serif;
            font-size: 42px;
            font-weight: 700;
            color: #fff;
            margin: 0 0 12px;
            letter-spacing: 1px;
        }
        .hero-content p {
            font-size: 16px;
            color: rgba(255,255,255,0.75);
            margin: 0;
        }

        /* ── Cards Section ── */
        .cards-section {
            padding: 60px 20px 80px;
            max-width: 1100px;
            margin: 0 auto;
        }
        .section-title {
            text-align: center;
            font-family: 'Montserrat', sans-serif;
            font-size: 13px;
            font-weight: 600;
            letter-spacing: 3px;
            text-transform: uppercase;
            color: rgba(255,255,255,0.45);
            margin-bottom: 40px;
        }

        .states-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 24px;
        }

        .state-card {
            background: #161616;
            border: 1px solid rgba(255,255,255,0.08);
            border-radius: 16px;
            overflow: hidden;
            text-decoration: none;
            display: block;
            transition: transform 0.25s ease, border-color 0.25s ease, box-shadow 0.25s ease;
        }
        .state-card:hover {
            transform: translateY(-6px);
            border-color: rgba(255,255,255,0.22);
            box-shadow: 0 20px 40px rgba(0,0,0,0.5);
            text-decoration: none;
        }

        .card-thumb {
            height: 190px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 56px;
            position: relative;
            overflow: hidden;
        }
        .card-thumb.kerala     { background: linear-gradient(135deg, #0d3b2e, #1a6b52); }
        .card-thumb.tamilnadu  { background: linear-gradient(135deg, #3b1a00, #7a3500); }
        .card-thumb.karnataka  { background: linear-gradient(135deg, #1a0a3b, #3d1a7a); }
        .card-thumb.andhra     { background: linear-gradient(135deg, #0c2e0c, #1e6b1e); }

        .card-thumb span {
            font-size: 60px;
            line-height: 1;
        }

        .card-body {
            padding: 20px 22px 24px;
        }
        .card-body h3 {
            font-family: 'Montserrat', sans-serif;
            font-size: 20px;
            font-weight: 600;
            color: #ffffff;
            margin: 0 0 8px;
        }
        .card-body p {
            font-size: 13px;
            color: rgba(255,255,255,0.55);
            margin: 0 0 16px;
            line-height: 1.6;
        }
        .card-tags {
            display: flex;
            flex-wrap: wrap;
            gap: 6px;
            margin-bottom: 18px;
        }
        .tag {
            font-size: 11px;
            font-weight: 600;
            letter-spacing: 0.5px;
            padding: 4px 10px;
            border-radius: 20px;
            border: 1px solid;
        }
        .tag.kerala     { color: #5ecfa8; border-color: rgba(94,207,168,0.3); background: rgba(94,207,168,0.08); }
        .tag.tamilnadu  { color: #f5a623; border-color: rgba(245,166,35,0.3); background: rgba(245,166,35,0.08); }
        .tag.karnataka  { color: #b388ff; border-color: rgba(179,136,255,0.3); background: rgba(179,136,255,0.08); }
        .tag.andhra     { color: #7ed56f; border-color: rgba(126,213,111,0.3); background: rgba(126,213,111,0.08); }

        .card-cta {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            font-size: 13px;
            font-weight: 600;
            color: rgba(255,255,255,0.9);
            letter-spacing: 0.3px;
        }
        .card-cta span { font-size: 16px; transition: transform 0.2s; }
        .state-card:hover .card-cta span { transform: translateX(4px); }
    </style>
</head>

<body oncontextmenu="return false;">

 <?php  include('header.php');  ?>
        
        
<!-- Hero -->
<div class="hero">
    <div class="hero-content">
        <h2>South India</h2>
        <p>Discover the backwaters, temples, and tropical beauty of Southern India</p>
    </div>
</div>

<!-- Cards -->
<div class="cards-section">
    <p class="section-title">Choose a State</p>
    <div class="states-grid">

        <!-- Kerala -->
        <a href="ker.php" class="state-card">
            <div class="card-thumb kerala">
                <span>🌴</span>
            </div>
            <div class="card-body">
                <h3>Kerala</h3>
                <p>God's Own Country — serene backwaters, lush hill stations, and golden beaches.</p>
                <div class="card-tags">
                    <span class="tag kerala">Alleppey</span>
                    <span class="tag kerala">Munnar</span>
                    <span class="tag kerala">Kovalam</span>
                </div>
                <div class="card-cta">Explore Kerala <span>→</span></div>
            </div>
        </a>

        <!-- Tamil Nadu -->
        <a href="tamil.php" class="state-card">
            <div class="card-thumb tamilnadu">
                <span>🛕</span>
            </div>
            <div class="card-body">
                <h3>Tamil Nadu</h3>
                <p>Ancient Dravidian temples, Marina Beach, and the cultural heart of South India.</p>
                <div class="card-tags">
                    <span class="tag tamilnadu">Chennai</span>
                    <span class="tag tamilnadu">Madurai</span>
                    <span class="tag tamilnadu">Ooty</span>
                </div>
                <div class="card-cta">Explore Tamil Nadu <span>→</span></div>
            </div>
        </a>



    </div>
</div>
<?php   
    include('footer.php');
    ?>
</body>
</html>