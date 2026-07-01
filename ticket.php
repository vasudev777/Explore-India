<?php
include('db.php');
session_start();
?>
<html lang="en">
<head>
    <title>Explore India</title>
    <!-- for-mobile-apps -->
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta http-equiv="Content-Type" ontent="text/html; charset=utf-8"/>
    <meta name="keywords"
          content="Client Responsive web template, Bootstrap Web Templates, Flat Web Templates, Android Compatible web template,Smartphone Compatible web template, free webdesigns for Nokia, Samsung, LG, SonyEricsson, Motorola web design"
    />
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
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
    <script src="js/disable.js"></script>
    <!-- //google fonts -->
    <style type="text/css">
        #holder {
            height: 230px;
            width: 920px;
            background-color: #F5F5F5;
            border: 1px solid #A4A4A4;
            margin-left: 20px;
        }

        #place {
            position: relative;
            margin: 12px;
        }

        #place a {
            font-size: 16px;
            font-weight: bold;
            padding: 12px;
        }

        #place li {
            list-style: none outside none;
            position: absolute;
        }

        #place li:hover {
            background-color: yellow;
        }

        #place .seat {
            background: url("images/ig.png") no-repeat scroll 0 0 transparent;
            height: 41px;
            width: 40px;
            display: block;
        }

        #place .selectedSeat {
            background-image: url("images/ir.png");
        }

        #place .selectingSeat {
            background-image: url("images/ib.png");
        }

        #place .row-3, #place .row-4 {
        }

        #seatDescription li {
            verticle-align: middle;
            list-style: none outside none;
            padding-left: 35px;
            height: 35px;
            margin-left: 20px;
            /*float:left;*/
        }
    </style>
</head>
<body>
<!-- header -->
<?php include('header.php');
?>

<section class="some-content py-5" id="about">
    <div class="container py-md-5">
        <div class="row about-vv-top mt-2">
            <div class="col-lg-6 about-img mt-md-4 mt-sm-4">
                <!-- <img src="images/ab1.jpg" class="img-fluid" alt="" hidden="true"> -->
                <form action="booking_process.php" method="POST">
                    <?php
                    $x = $_POST['train_id'];
                    $a = explode(",", $x);
                    $c = sizeof($a);

                    for ($x = 0; $x < $c; $x++) {
                        $a[1];
                        $a[0];
                    }


                    ?>
                    <input type="hidden" value="<?php echo $_SESSION['train_id'] = $a[0]; ?>">
                    <input type="hidden" value="<?php echo $_SESSION['train_price'] = $a[1]; ?>">

                    <input type="hidden" value="<?php echo $_SESSION['bdate'] = $_POST['date']; ?>">
                    <input type="hidden" value="<?php echo $_SESSION['coach'] = $_POST['coach']; ?>">


                    <h4 style="color: #ffffff;" class="title-hny">Select Tickets</h4>
                    <hr>
                    <div id="holder">
                        <ul id="place">
                        </ul>
                    </div>
                    <br>
                    <ul id="seatDescription">
                        <li><b>Train Price <?php echo $a[1]; ?></b></li>
                        <li style="background:url('images/ig.png') no-repeat scroll 0 0 transparent;">Available Seat
                        </li>
                        <li style="background:url('images/ir.png') no-repeat scroll 0 0 transparent;">Book Seat</li>
                        <li style="background:url('images/ib.png') no-repeat scroll 0 0 transparent;">Selected Seat</li>
                    </ul>

                    <br>
                    &nbsp;&nbsp;&nbsp;
                    <div style="width:100%">

                        <?php if (isset($_SESSION['uemail'])) { ?>

                            <input type="button" id="btnShowNew" class="read-more btn" Value="Book" name="Book"
                                   style="float: 1000px;">
                        <?php } else { ?>

                            <button onclick="myFunction()" class="read-more btn" disabled="true">Book</button>

                            <?php
                        } ?>
                        <input type="hidden" id="btnShow" class="read-more btn" value="Show All"/></div>
                </form>


                <?php // $booked = array(10,11,20,45,60);
                $arr = array();
                // $arr1=array();
                $arr2 = array();
                $sheet = "";
                $sh = "";
                $sdate = mysqli_real_escape_string($conn, $_POST['date'] ?? '');
                $cno = mysqli_real_escape_string($conn, $_POST['coach'] ?? '');
                $train_id = intval($_POST['train_id'] ?? 0);
                $sql = "SELECT * from ticket where tic_date='$sdate' and coach_no='$cno' and t_id='$train_id'";
                $res = mysqli_query($conn, $sql);
                foreach ($res as $row) {
                    $sheet = $row['tic_seats'];
                    $sh = $sheet . "," . $sh;
                }

                $sh = substr($sh, 0, strlen($sh) - 1);
                $arr = explode(',', $sh);
                $arr = array_map('intval', $arr);

                ?>
                <!-- Script -->
                <script>
                    function myFunction() {
                        alert("Hi First Login After Your.....Booking ");
                        //window.open('booktrain.php','_self');

                    }
                </script>
                <script type="text/javascript">
                    var settings = {
                        rows: 4,
                        cols: 18,
                        rowCssPrefix: 'row-',
                        colCssPrefix: 'col-',
                        seatWidth: 50,
                        seatHeight: 50,
                        seatCss: 'seat',
                        selectedSeatCss: 'selectedSeat',
                        selectingSeatCss: 'selectingSeat'
                    };
                    var init = function (reservedSeat) {
                        var str = [], seatNo, className;
                        for (i = 0; i < settings.rows; i++) {
                            for (j = 0; j < settings.cols; j++) {
                                seatNo = (i + j * settings.rows + 1);
                                className = settings.seatCss + ' ' + settings.rowCssPrefix + i.toString() + ' ' + settings.colCssPrefix + j.toString();
                                if ($.isArray(reservedSeat) && $.inArray(seatNo, reservedSeat) != -1) {
                                    className += ' ' + settings.selectedSeatCss;
                                }
                                str.push('<li class="' + className + '"' +
                                    'style="top:' + (i * settings.seatHeight).toString() + 'px;left:' + (j * settings.seatWidth).toString() + 'px">' +
                                    '<a title="' + seatNo + '">' + seatNo + '</a>' +
                                    '</li>');
                            }
                        }
                        $('#place').html(str.join(''));
                    };
                    //case I: Show from starting
                    //init();
                    //Case II: If already booked
                    var booked = <?php echo json_encode($arr); ?>;
                    //alert(typeof complexArray);
                    //var arr = Object.entries(complexArray);
                    //alert(typeof booked);
                    var bookedSeats = booked; /// Boking Sleb
                    init(bookedSeats);
                    $('.' + settings.seatCss).click(function () {
                        if ($(this).hasClass(settings.selectedSeatCss)) {
                            alert('This seat is already reserved');
                        } else {
                            $(this).toggleClass(settings.selectingSeatCss);
                        }
                    });
                    $('#btnShow').click(function () {
                        var str = [];
                        $.each($('#place li.' + settings.selectedSeatCss + ' a, #place li.' + settings.selectingSeatCss + ' a'), function (index, value) {
                            str.push($(this).attr('title'));
                        });
                        alert(str.join(','));
                    })
                    $('#btnShowNew').click(function () {
                        var str = [], item;
                        $.each($('#place li.' + settings.selectingSeatCss + ' a'), function (index, value) {
                            item = $(this).attr('title');
                            str.push(item);
                        });
                        //  alert(str.join(','));
                        // Add By Jigar
                        var sheet = str.join(',');
                        // var url = "ticket.php?sheet=" + encodeURIComponent(sheet);
                        var url = "payment_gateway/pay.php?sheet=" + encodeURIComponent(sheet);


                        window.location.href = url;
                    })
                </script>
            </div>
        </div>
    </div>
</section>
<!-- footer -->
<?php include($_SERVER['DOCUMENT_ROOT'] . '/trip/footer.php'); ?>
</body>
</html>