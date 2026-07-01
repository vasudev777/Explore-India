<?php include($_SERVER['DOCUMENT_ROOT'] . '/trip/db.php'); ?>
<html>
<head>
    <title></title>
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
    <!-- Disable Ctrl+U -->
    <link rel="stylesheet" href="css/fling.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
    <script src="js/disable.js"></script>
    <!-- //Disable Ctrl+U -->
    <!-- google fonts -->
    <link href="//fonts.googleapis.com/css?family=Montserrat:100,100i,200,200i,300,300i,400,400i,500,500i,600,600i,700,700i,800,800i,900,900i"
          rel="stylesheet">
    <link href="//fonts.googleapis.com/css?family=Open+Sans:300,300i,400,400i,600,600i,700,700i,800,800i"
          rel="stylesheet">
</head>
<body oncontextmenu="return false;">
<?php include($_SERVER['DOCUMENT_ROOT'] . '/trip/header.php');
?>
<br><br>
<section class="some-content py-5" id="about">

    <div class="container py-md-5">
        <h4 class="title-hny  mb-md-5">Himachal Pradesh Package</h4>
        <div class="row about-vv-top mt-2">
            <div class="col-lg-6 about-info">

                <br>
                <div class="fling-minislide">
                    <img src="images/Mandi.jpg" alt="Slide 4"/>
                    <img src="images/Manali.jpg" alt="Slide 3"/>
                    <img src="images/dharamsala.jpg" alt="Slide 2"/>
                    <img src="images/kullu (2).jpg" alt="Slide 1"/>
                </div>
                <?php
                $sqlhotel = "SELECT * FROM package  WHERE pa_name='Himachal Prades'";
                //echo $sqlhotel;
                ?>

                <div class="read-more-button mt-4">
                </div>
            </div>
            <div class="col-lg-6 about-img mt-md-4 mt-sm-4">

                <form action="predefine_book.php" method="post">
                    <h4 style="color: #ffffff;" class="title-hny"></h4>
                    <?php
                    echo "<b>Itinerary </b><br><br>";
                    $result = mysqli_query($conn, $sqlhotel);
                    foreach ($result as $jk) {

                        $a = explode(",", $jk['h_id']);
                        $c = sizeof($a);
                        $cnt = 1;
                        for ($x = 0; $x < $c; $x++) {
                            $sql3 = "SELECT * FROM hotel where h_id='$a[$x]'";

                            if ($result3 = mysqli_query($conn, $sql3)) {

                                while ($row3 = mysqli_fetch_assoc($result3)) {
                                    echo '<b>Day </b>' . $cnt++ . ':-' . $row3['h_name'] . ' <br> ';


                                    $cid = $row3['c_id'];
                                    $sql4 = "SELECT * FROM city where c_id='$cid'";
                                    if ($result4 = mysqli_query($conn, $sql4)) {

                                        while ($row4 = mysqli_fetch_assoc($result4)) {
                                            echo '<b>Place Name:- </b>' . $row4['c_name'] . ' <br><br> ';

                                        }

                                    }
                                }

                            }
                        }


                    }
                    $sql5 = "SELECT  price FROM package  WHERE pa_name='Himachal Pradesh'";
                    if ($result5 = mysqli_query($conn, $sql5)) {

                        while ($row5 = mysqli_fetch_assoc($result5)) {
                            $p = $row5['price'];
                            echo "<b>Price</b>=$p INR ";
                        }
                    }
                    ?>
                    <hr>
                    <input type="hidden" name="pack" value="<?php echo $jk['pa_id']; ?>">
                    <?php if (isset($_SESSION['uemail'])) { ?>

                        <input type="button" id="btnShowNew" class="read-more btn" Value="Book" name="Book"
                               style="float: 1000px;">
                    <?php } else { ?>

                        <button onclick="myFunction()" class="read-more btn" disabled="true">Book</button>

                        <?php
                    } ?>
                    <!-- <button type="submit" name="button" class="read-more btn" >Book</button> -->
                </form>

            </div>
        </div>
    </div>
</section>
<?php include 'footer.php'; ?>
</body>
</html>