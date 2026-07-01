<?php
if (session_status() === PHP_SESSION_NONE) session_start();
include('db.php');
?>
<header>
    <div class="top-head container">
        <div class="ml-auto text-right right-p">
            <ul>
                <li class="mr-3">
                    <span class="fa fa-phone">&nbsp;&nbsp;</span>
                    <a href="tel:1800405025">1800-405025</a>
                </li>
            </ul>
        </div>
    </div>
    <div class="container">
        <nav class="py-3 d-lg-flex">
            <div id="logo">
                <h1><a href="index.php"><span class="fa fa-globe"></span>Explore India</a></h1>
            </div>
            <label for="drop" class="toggle"><span class="fa fa-bars"></span></label>
            <input type="checkbox" id="drop"/>

            <ul class="menu ml-auto mt-1">
                <li class="active"><a href="index.php">Home</a></li>
                <li><a href="guide_login.php">Guide Portal</a></li>
                <li><a href="admin/login.php">Admin Panel</a></li>

                <?php if (isset($_SESSION['uemail'])): ?>
                    <?php
                    // FIX: ucust_id use karo, uid nahi
                    $fname = $_SESSION['uname'] ?? '';
                    // DB se fresh fetch (optional but safe)
                    if (isset($_SESSION['ucust_id'])) {
                        $id  = intval($_SESSION['ucust_id']);
                        $res = mysqli_query($conn, "SELECT cust_fname FROM customer_details WHERE cust_id=$id");
                        if ($res && mysqli_num_rows($res) > 0) {
                            $fname = mysqli_fetch_assoc($res)['cust_fname'];
                        }
                    }
                    ?>
                    <li><a href="packages.php">Services</a></li>
                    <li class="ei-dropdown">
                        <button class="ei-dropbtn">
                            <span class="fa fa-user-circle"></span>
                            &nbsp;<?php echo htmlspecialchars($fname); ?>
                            &nbsp;<span class="fa fa-caret-down" style="font-size:11px;"></span>
                        </button>
                        <div class="ei-dropdown-content">
                            <a href="profile.php"><span class="fa fa-user"></span> My Profile</a>
                            <a href="cust_history.php"><span class="fa fa-history"></span> History</a>
                            <a href="feedback.php"><span class="fa fa-comment"></span> Feedback</a>
                            <hr style="margin:4px 0; border-color:rgba(0,0,0,0.08);">
                            <a href="logout.php" style="color:#e74c3c !important;">
                                <span class="fa fa-sign-out"></span> Log Out
                            </a>
                        </div>
                    </li>

                <?php else: ?>
                    <li><a href="login.php">Login</a></li>
                    <li><a href="registration.php">Register</a></li>
                <?php endif; ?>
            </ul>
        </nav>
    </div>

    <style>
    /* ── User dropdown ── */
    .ei-dropdown {
        position: relative;
        display: inline-block;
    }

    /* FIX: Always white text with subtle bg — visible on ALL pages */
    .ei-dropbtn {
        background: rgba(255,255,255,0.12);
        border: 1px solid rgba(255,255,255,0.25);
        border-radius: 20px;
        cursor: pointer;
        font-size: 14px;
        font-weight: 600;
        color: #ffffff !important;   /* always white */
        padding: 6px 14px;
        display: flex;
        align-items: center;
        gap: 4px;
        transition: background 0.2s, border-color 0.2s;
        font-family: inherit;
        white-space: nowrap;
        text-shadow: 0 1px 3px rgba(0,0,0,0.5); /* dark pages pe readable */
    }
    .ei-dropbtn:hover {
        background: rgba(255,255,255,0.22);
        border-color: rgba(255,255,255,0.45);
    }

    /* Dropdown menu */
    .ei-dropdown-content {
        display: none;
        position: absolute;
        right: 0;
        top: calc(100% + 8px);
        background: #ffffff;
        min-width: 180px;
        box-shadow: 0 12px 32px rgba(0,0,0,0.18);
        z-index: 9999;
        border-radius: 12px;
        padding: 6px;
        border: 1px solid rgba(0,0,0,0.06);
    }
    .ei-dropdown-content a {
        color: #222 !important;
        padding: 10px 14px;
        display: flex;
        align-items: center;
        gap: 9px;
        text-decoration: none;
        font-size: 13.5px;
        font-weight: 500;
        border-radius: 8px;
        transition: background 0.15s;
    }
    .ei-dropdown-content a:hover {
        background: #f5f5f5;
        text-decoration: none;
    }
    .ei-dropdown-content a .fa {
        width: 16px;
        text-align: center;
        color: #888;
    }
    .ei-dropdown:hover .ei-dropdown-content { display: block; }
    </style>
</header>