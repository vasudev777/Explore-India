<?php
include('db.php');
session_start();
?>
<?php //   session_start();

header("Pragma: no-cache");
header("Cache-Control: no-cache");
header("Expires: 0");
?>
<!DOCTYPE html>
<html>
<head>
    <title>Booking Table</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
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

</head>
<body>
<header style="margin-top: -15px;">
    <?php include('header.php'); ?>
</header>
<br><br><br><br><br><br><br><br>
<center>
    <h4>Thank You, Payment succesfull!!</h4>

    <hr style="margin-left:100px;margin-right: 100px;">
    <image src="a1.gif" width="200" height="170">

        <?php

        include 'payment_gateway/src/instamojo.php';

        $api = new Instamojo\Instamojo('test_791b1a811916484a8c5f0d9049e', 'test_21a9ccaa772ee68a4e499c5abc0', 'https://test.instamojo.com/api/1.1/');

        $payid = $_GET["payment_request_id"];

        try {
            $response = $api->paymentRequestStatus($payid);
            echo "<h4>Payment ID: " . $response['payments'][0]['payment_id'] . "</h4>";
            echo "<h4>Name: " . $response['payments'][0]['buyer_name'] . "</h4>";
            echo "<h4>Email: " . $response['payments'][0]['buyer_email'] . "</h4>";
            echo "<h4>Phone: " . $response['payments'][0]['buyer_phone'] . "</h4>";
            echo "<h4>Total Amount : " . $response['payments'][0]['amount'] . "</h4>";
            echo "<h4>Payment Way : " . $response['payments'][0]['status'] . "</h4>";

            $payment_id = $response['payments'][0]['payment_id'];
            $name = $response['payments'][0]['buyer_name'];
            $email = $response['payments'][0]['buyer_email'];
            $phone = $response['payments'][0]['buyer_phone'];
            $amount = $response['payments'][0]['amount'];
            $payment_way = $response['payments'][0]['status'];

            //echo "<pre>";
            //print_r($response);
//echo "</pre>";

        } catch (Exception $e) {
            print('Error: ' . $e->getMessage());
        }
        ?>

        <?php
        include('db.php');


        $uid = $_SESSION['uid'];
        $packid = $_SESSION['packid'];
        $date = $_SESSION['date'];
        $amount;
        $payment_id;

        $sql = "INSERT INTO packbook (pa_id,cust_id,pack_date,pack_price,payment_id)
	VALUES('$packid','$uid','$date','$amount','$payment_id')";
        mysqli_query($conn, $sql);

        $sqlpack = "SELECT * FROM package WHERE pa_id='$packid'";
        $result = mysqli_query($conn, $sqlpack);
        foreach ($result as $packname) {
            $packagename = $packname["pa_name"];
        }

        $fname = $name;
        $email;
        $price = $amount;
        $date;
        $packagename;
        $packagetype = "Predefined Package";


        //             Mail Code Starts From Here
        //         Let Do Some Networking

        require 'phpmail/PHPMailerAutoload.php';
        $mail = new PHPMailer;

        $headers = 'MIME-Version: 1.0' . "\r\n";
        $headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";

        //         $mail->SMTPDebug = 3;                               // Enable verbose debug output

        $mail->isSMTP();                                      // Set mailer to use SMTP
        $mail->SMTPSecure = 'STARTTLS';
        $mail->Host = 'smtp.gmail.com';
        $mail->Port = 587;
        $mail->SMTPAuth = true;                               // Enable SMTP authentication
        $mail->Username = 'exploreindiaplaner@gmail.com';                 // SMTP username
        $mail->Password = 's3770k3g';                           // SMTP password

        $mail->setFrom('exploreindia@gmail.com', 'Explore India');
        $mail->addAddress($email, $fname);     // Add a recipient

        // $mail->addReplyTo('info@example.com', 'Information');
        $mail->isHTML(true);                                  // Set email format to HTML

        $mail->Subject = 'Details of ' . $packagename;

        //$message = '<html><body>';

        $mail->Body .= '<h3 style="color:black;">Explore India</h3>';

        $mail->Body .= '<h3 style="color:black;">Your Package Details</h3>';

        $mail->Body .= '<table border="1px;" style="border-radius:10px;" width="100%;">';

        $mail->Body .= '<tr>';
        $mail->Body .= '<td style="border-radius:10px;"><p style="color:green;font-size:18px;" >Package Type:- </td><td colspan="3" style="border-radius:10px;">' . $packagetype . '</td></p>';
        $mail->Body .= '</tr>';

        $mail->Body .= '<tr>';
        $mail->Body .= '<td style="border-radius:10px;"><p style="color:green;font-size:18px;" >Package:- </td><td colspan="3" style="border-radius:10px;">' . $packagename . '</td></p>';
        $mail->Body .= '</tr>';


        $mail->Body .= '<td style="border-radius:10px;"><p style="color:green;font-size:18px;" >Price: </td><td colspan="3" style="border-radius:10px;">' . $price . '</td></p>';
        $mail->Body .= '</tr>';

        $mail->Body .= '<tr>';
        $mail->Body .= '<td style="border-radius:10px;"><p style="color:green;font-size:18px;" >Date: </td><td colspan="3" style="border-radius:10px;">' . $date . '</td></p>';
        $mail->Body .= '</tr>';


        $mail->Body .= '</table>';

        $mail->Body .= '<h2 style="color:red;">Thank You!!!</h2>';
        $mail->Body .= '<h4 style="color:green">Regards</h4>';
        $mail->Body .= '<h3 style="color:#CD4542;">Explore India Team</h3>';

        //$mail->Body .= '</body></html>';


        if ($mail->send()) {

//             echo 'Success.';

        } else {
//
//             echo 'Message could not be sent.';
//             echo 'Mailer Error: ' . $mail->ErrorInfo;
        }


        ?>


</center>
</body>
</html>