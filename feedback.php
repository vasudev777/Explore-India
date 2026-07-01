<?php
include('db.php');
session_start();
if (!isset($_SESSION['uemail'])) { header('Location: login.php'); exit; }

$success = '';
$error   = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $comment = trim(mysqli_real_escape_string($conn, $_POST['comment'] ?? ''));
    $rating  = intval($_POST['rating'] ?? 5);
    $type    = mysqli_real_escape_string($conn, $_POST['type'] ?? 'website');
    $cust_id = intval($_SESSION['ucust_id']);

    if (empty($comment)) {
        $error = 'Please write your feedback before submitting.';
    } else {
        $sql = "INSERT INTO feedback (cust_id, message, rating, type) VALUES ('$cust_id', '$comment', '$rating', '$type')";
        if (mysqli_query($conn, $sql)) {
            $success = 'Thank you for your feedback!';
        } else {
            $error = 'Something went wrong. Please try again.';
        }
    }
}

$types = [
    'transport'          => ['label' => 'Transport',          'icon' => '✈️', 'color' => '#5ea0ff'],
    'customized_package' => ['label' => 'Customize Package',  'icon' => '🗺️', 'color' => '#f5a623'],
    'special_package'    => ['label' => 'Special Package',    'icon' => '⭐', 'color' => '#5ecfa8'],
    'local_guide'        => ['label' => 'Local Guide',        'icon' => '👤', 'color' => '#b47cff'],
    'website'            => ['label' => 'Website / App',      'icon' => '💻', 'color' => '#ff6b6b'],
];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>Feedback – Explore India</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
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

        .page-hero {
            padding: 110px 20px 60px;
            text-align: center;
            background: linear-gradient(160deg, #0a0a0a 0%, #12082a 50%, #0a0a0a 100%);
        }
        .eyebrow { font-size: 11px; font-weight: 700; letter-spacing: 4px; text-transform: uppercase; color: rgba(255,255,255,0.35); margin-bottom: 12px; }
        .page-hero h1 { font-family: 'Montserrat', sans-serif; font-size: clamp(28px, 5vw, 48px); font-weight: 800; color: #fff; margin-bottom: 10px; text-transform: none !important; }
        .page-hero h1 span { color: #f5a623; }
        .page-hero p { font-size: 15px; color: rgba(255,255,255,0.42); max-width: 420px; margin: 0 auto; line-height: 1.6; }

        .main-wrap {
            max-width: 900px; margin: 0 auto;
            padding: 60px 20px 90px;
            display: grid; grid-template-columns: 1fr 1.2fr;
            gap: 32px; align-items: start;
        }

        /* Info card */
        .info-card { background: #141414; border: 1px solid rgba(255,255,255,0.07); border-radius: 20px; padding: 30px 26px; }
        .ic-badge { display: inline-block; font-size: 9px; font-weight: 700; letter-spacing: 2px; text-transform: uppercase; color: #f5a623; background: rgba(245,166,35,0.08); border: 1px solid rgba(245,166,35,0.2); padding: 4px 12px; border-radius: 20px; margin-bottom: 16px; }
        .info-card h2 { font-family: 'Montserrat', sans-serif; font-size: 20px; font-weight: 800; color: #fff; margin-bottom: 10px; text-transform: none !important; }
        .info-card h2 span { color: #f5a623; }
        .info-card > p { font-size: 13px; color: rgba(255,255,255,0.42); line-height: 1.75; margin-bottom: 24px; }
        .feature-list { display: flex; flex-direction: column; gap: 14px; }
        .feature-item { display: flex; align-items: flex-start; gap: 12px; }
        .feat-icon { width: 36px; height: 36px; flex-shrink: 0; border-radius: 10px; background: rgba(245,166,35,0.1); border: 1px solid rgba(245,166,35,0.2); display: flex; align-items: center; justify-content: center; color: #f5a623; font-size: 14px; }
        .feat-text h4 { font-family: 'Montserrat', sans-serif; font-size: 13px; font-weight: 700; color: #fff; margin-bottom: 2px; text-transform: none !important; }
        .feat-text p  { font-size: 12px; color: rgba(255,255,255,0.35); margin: 0; }

        /* Form card */
        .form-card { background: #141414; border: 1px solid rgba(255,255,255,0.07); border-radius: 20px; overflow: hidden; }
        .form-header { padding: 20px 24px 16px; border-bottom: 1px solid rgba(255,255,255,0.06); }
        .form-header h3 { font-family: 'Montserrat', sans-serif; font-size: 17px; font-weight: 700; color: #fff; margin-bottom: 3px; text-transform: none !important; }
        .form-header p  { font-size: 12px; color: rgba(255,255,255,0.35); }
        .form-body { padding: 24px; }

        /* Type selector */
        .type-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 8px; margin-bottom: 20px; }
        .type-radio { display: none; }
        .type-label {
            display: flex; flex-direction: column; align-items: center; gap: 5px;
            padding: 12px 8px; border-radius: 12px;
            border: 1.5px solid rgba(255,255,255,0.08);
            background: rgba(255,255,255,0.03);
            cursor: pointer; transition: all 0.2s; text-align: center;
        }
        .type-label:hover { border-color: rgba(245,166,35,0.3); }
        .type-emoji { font-size: 20px; }
        .type-name { font-size: 10px; font-weight: 600; color: rgba(255,255,255,0.5); line-height: 1.2; }
        .type-radio:checked + .type-label { background: rgba(245,166,35,0.1); border-color: #f5a623; }
        .type-radio:checked + .type-label .type-name { color: #f5a623; }

        /* Star rating */
        .star-group { display: flex; gap: 6px; margin-bottom: 20px; flex-direction: row-reverse; justify-content: flex-end; }
        .star-group input { display: none; }
        .star-group label { font-size: 28px; color: rgba(255,255,255,0.15); cursor: pointer; transition: color 0.15s; }
        .star-group input:checked ~ label,
        .star-group label:hover,
        .star-group label:hover ~ label { color: #f5a623; }

        .field-label { display: block; font-size: 11px; font-weight: 700; letter-spacing: 1px; text-transform: uppercase; color: rgba(255,255,255,0.38); margin-bottom: 8px; }

        .feedback-textarea {
            width: 100%; min-height: 130px;
            background: rgba(255,255,255,0.04);
            border: 1px solid rgba(255,255,255,0.1);
            border-radius: 10px; color: #fff;
            font-size: 14px; padding: 14px 16px;
            outline: none; resize: vertical;
            transition: border-color 0.2s;
            font-family: 'Open Sans', sans-serif; line-height: 1.6;
        }
        .feedback-textarea::placeholder { color: rgba(255,255,255,0.2); }
        .feedback-textarea:focus { border-color: rgba(245,166,35,0.5); }
        .char-count { font-size: 11px; color: rgba(255,255,255,0.2); text-align: right; margin-top: 6px; }

        .alert-box { padding: 12px 16px; border-radius: 10px; font-size: 13px; margin-bottom: 18px; }
        .alert-success { background: rgba(94,207,168,0.1); border: 1px solid rgba(94,207,168,0.25); color: #5ecfa8; }
        .alert-error   { background: rgba(255,80,80,0.1);  border: 1px solid rgba(255,80,80,0.25);  color: #ff6b6b; }

        .btn-submit { width: 100%; border: none; border-radius: 12px; background: linear-gradient(135deg, #f5a623, #d48a1a); color: #000; font-size: 15px; font-weight: 700; font-family: 'Montserrat', sans-serif; padding: 14px; cursor: pointer; transition: transform 0.2s, box-shadow 0.2s; margin-top: 16px; text-transform: none !important; }
        .btn-submit:hover { transform: translateY(-2px); box-shadow: 0 8px 24px rgba(245,166,35,0.3); }

        /* Success */
        .success-state { text-align: center; padding: 40px 20px; }
        .success-icon { font-size: 56px; margin-bottom: 16px; }
        .success-state h3 { font-family: 'Montserrat', sans-serif; font-size: 20px; font-weight: 700; color: #fff; margin-bottom: 8px; text-transform: none !important; }
        .success-state p { font-size: 13px; color: rgba(255,255,255,0.4); margin-bottom: 20px; }
        .btn-back { display: inline-flex; align-items: center; gap: 6px; padding: 10px 22px; border-radius: 20px; background: rgba(245,166,35,0.1); border: 1px solid rgba(245,166,35,0.25); color: #f5a623; font-size: 13px; font-weight: 600; text-decoration: none; transition: all 0.2s; }
        .btn-back:hover { background: rgba(245,166,35,0.2); color: #f5a623; text-decoration: none; }

        @media (max-width: 768px) {
            .main-wrap { grid-template-columns: 1fr; gap: 20px; }
            .type-grid { grid-template-columns: repeat(3, 1fr); }
        }
    </style>
</head>
<body oncontextmenu="return false;">
<?php include('header.php'); ?>

<div class="page-hero">
    <p class="eyebrow">We value your opinion</p>
    <h1>Share Your <span>Feedback</span></h1>
    <p>Help us improve your travel experience by sharing your thoughts with us.</p>
</div>

<div class="main-wrap">

    <!-- LEFT -->
    <div class="info-card">
        <div class="ic-badge">Your Voice Matters</div>
        <h2>Help Us <span>Improve</span></h2>
        <p>Your feedback helps us make Explore India better for every traveller.</p>
        <div class="feature-list">
            <div class="feature-item">
                <div class="feat-icon"><span class="fa fa-star"></span></div>
                <div class="feat-text"><h4>Rate Your Experience</h4><p>Tell us how we did with a star rating</p></div>
            </div>
            <div class="feature-item">
                <div class="feat-icon"><span class="fa fa-tags"></span></div>
                <div class="feat-text"><h4>Choose Category</h4><p>Transport, Package, Guide or Website</p></div>
            </div>
            <div class="feature-item">
                <div class="feat-icon"><span class="fa fa-comment"></span></div>
                <div class="feat-text"><h4>Share Your Thoughts</h4><p>Write about your booking experience</p></div>
            </div>
        </div>
    </div>

    <!-- RIGHT -->
    <div class="form-card">
        <?php if ($success): ?>
        <div class="success-state">
            <div class="success-icon">🎉</div>
            <h3>Thank You!</h3>
            <p>Your feedback has been submitted successfully.</p>
            <a href="index.php" class="btn-back"><span class="fa fa-home"></span> Back to Home</a>
        </div>
        <?php else: ?>
        <div class="form-header">
            <h3><span class="fa fa-comment-o"></span> &nbsp;Write Feedback</h3>
            <p>We read every feedback personally</p>
        </div>
        <div class="form-body">
            <?php if ($error): ?>
            <div class="alert-box alert-error"><span class="fa fa-exclamation-circle"></span> <?= $error ?></div>
            <?php endif; ?>

            <form action="feedback.php" method="POST">

                <!-- Type selector -->
                <div style="margin-bottom:20px;">
                    <label class="field-label">Feedback For</label>
                    <div class="type-grid">
                        <?php foreach ($types as $key => $t): ?>
                        <div>
                            <input type="radio" name="type" id="type_<?= $key ?>" value="<?= $key ?>" class="type-radio"
                                   <?= ($key === 'website') ? 'checked' : '' ?>>
                            <label for="type_<?= $key ?>" class="type-label">
                                <span class="type-emoji"><?= $t['icon'] ?></span>
                                <span class="type-name"><?= $t['label'] ?></span>
                            </label>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>

                <!-- Star Rating -->
                <div style="margin-bottom:20px;">
                    <label class="field-label">Your Rating</label>
                    <div class="star-group">
                        <input type="radio" name="rating" id="s5" value="5" checked>
                        <label for="s5">★</label>
                        <input type="radio" name="rating" id="s4" value="4">
                        <label for="s4">★</label>
                        <input type="radio" name="rating" id="s3" value="3">
                        <label for="s3">★</label>
                        <input type="radio" name="rating" id="s2" value="2">
                        <label for="s2">★</label>
                        <input type="radio" name="rating" id="s1" value="1">
                        <label for="s1">★</label>
                    </div>
                </div>

                <!-- Message -->
                <div>
                    <label class="field-label">Your Feedback</label>
                    <textarea name="comment" class="feedback-textarea" id="feedbackText"
                        placeholder="Tell us about your experience..."
                        maxlength="500" required></textarea>
                    <div class="char-count"><span id="charCount">0</span> / 500</div>
                </div>

                <button type="submit" class="btn-submit">
                    <span class="fa fa-paper-plane"></span> &nbsp; Submit Feedback
                </button>
            </form>
        </div>
        <?php endif; ?>
    </div>

</div>

<?php include('footer.php'); ?>
<script>
var ta = document.getElementById('feedbackText');
var cc = document.getElementById('charCount');
if (ta) { ta.addEventListener('input', function() { cc.textContent = this.value.length; }); }
</script>
</body>
</html>