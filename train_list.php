<?php
include('db.php');
session_start();
// Create connection
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>Explore India</title>
    <!-- for-mobile-apps -->
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <meta name="keywords"
          content="Client Responsive web template, Bootstrap Web Templates, Flat Web Templates, Android Compatible web template,Smartphone Compatible web template, free webdesigns for Nokia, Samsung, LG, SonyEricsson, Motorola web design"/>
    <!-- css files -->
    <link href="css/bootstrap.css" rel='stylesheet' type='text/css'/>
    <!-- bootstrap css -->
    <link href="css/style.css" rel='stylesheet' type='text/css'/>
    <!-- custom css -->
    <link href="css/css_slider.css" type="text/css" rel="stylesheet" media="all">
    <link href="css/font-awesome.min.css" rel="stylesheet">
    <!-- fontawesome css -->
    <!-- //css files -->
    <!-- google fonts -->
    <link href="//fonts.googleapis.com/css?family=Montserrat:100,100i,200,200i,300,300i,400,400i,500,500i,600,600i,700,700i,800,800i,900,900i"
          rel="stylesheet">
    <link href="//fonts.googleapis.com/css?family=Open+Sans:300,300i,400,400i,600,600i,700,700i,800,800i"
          rel="stylesheet">
    <!-- //google fonts -->
    <!-- Disable Ctrl+U -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
    <script src="js/disable.js"></script>
    <!-- //Disable Ctrl+U -->
    <style type="text/css">
        /* Style the video: 100% width and height to cover the entire window */
        #myVideo {
            position: fixed;
            right: 0;
            bottom: 0;
            min-width: 100%;
            min-height: 100%;
        }

        /* Add some content at the bottom of the video/page */
        .content {
            position: fixed;
            bottom: 0;
            background: rgba(0, 0, 0, 0.5);
            color: #f1f1f1;
            width: 100%;
            padding: 20px;
        }
    </style>
</head>
<body>
<header style="margin-top:-15px;">
    <!-- header -->
    <?php include('header.php');
    ?>
    <video autoplay muted loop id="myVideo">
        <source src="images/train.mp4" type="video/mp4">
    </video>


    <center>
        <div class="col-md-6 px-lg-3 px-0">
            <div class="banner-form-w3 ml-lg-5">
                <div class="padding">
                    <form action="ticket.php" method="POST">
                        <h5 class="mb-3"><span class="fa fa-train"></span>&nbsp;&nbsp;&nbsp;Train List </h5>
                        <div class="form-style-w3layout" style="color: ">
                            <!-- <label name="Email" class="mb-3">Email</label> -->
                            <?php
                            $source = mysqli_real_escape_string($conn, $_POST['source'] ?? '');
                            $destination = mysqli_real_escape_string($conn, $_POST['destination'] ?? '');
                            $sdate = mysqli_real_escape_string($conn, $_POST['date'] ?? '');
                            $cno = mysqli_real_escape_string($conn, $_POST['coach'] ?? '');

                            $sql = "SELECT * FROM trains WHERE via LIKE '%" . $source . "%' and via LIKE '%" . $destination . "%'";

                            if ($result = mysqli_query($conn, $sql)) {
                                while ($row = mysqli_fetch_assoc($result)) {

                                    ?>
                                    <input type="hidden" name="date" value="<?php echo $sdate; ?>">
                                    <input type="hidden" name="coach" value="<?php echo $cno; ?>">

                                    <input type="radio" id="<?php echo $row['t_id']; ?>" name="train_id"
                                           value="<?php echo $row['t_id'] . ',' . $row['t_price']; ?>">
                                    <label for="<?php echo $row['t_id']; ?>"
                                           style="color:#fff"><?php echo $row['t_name'] . ' ' . $row['t_price']; ?></label>
                                    <br>


                                <?php }
                            } ?>
                            <br> <br>
                            <?php if (mysqli_num_rows($result) > 0) { ?>
                                <button type="submit" name="submit" value="submit" Class="btn">Find</button>
                            <?php } else { ?>
                                <h5 class="mb-3"> No Train Found</h5>
                            <?php } ?>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </center>
</header>
<!-- //header -->
</body>
</html>