<html>
<head>

    <style>
        /*hidden arrow*/
        body {
            background-image: url(images/lgwall.jpg);
        }

        input::-webkit-outer-spin-button,
        input::-webkit-inner-spin-button {
            -webkit-appearance: none;
            margin: 0;
        }

        div {
            margin-bottom: 230px;
            width: 370px;
            padding: 35px 25px;
            border: 5px solid gray;
            border-radius: 12px;
        }

        h1 {
            margin-top: 80px;
            font-size: 40px;
        }

        input[type=email] {
            width: 100%;
            padding: 15px 20px;
            margin: 11px 0;
            display: inline-block;
            border: 1px solid red;
            border-radius: 4px;
        }

        input[type=number] {
            width: 100%;
            padding: 15px 20px;
            margin: 11px 0;
            display: inline-block;
            border: 1px solid red;
            border-radius: 4px;
        }

        input[type=text] {
            width: 100%;
            padding: 15px 20px;
            margin: 11px 0;
            display: inline-block;
            border: 1px solid red;
            border-radius: 4px;
        }

        input[type=password] {
            width: 100%;
            padding: 15px 20px;
            margin: 11px 0;
            display: inline-block;
            border: 1px solid red;
            border-radius: 4px;
        }

        input[type=submit] {
            width: 50%;
            color: white;
            background-color: blue;
            padding: 15px 15px;
            margin: 11px 0;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }

        input[type=option] {
            width: 50%;
            color: white;
            background-color: blue;
            padding: 15px 15px;
            margin: 11px 0;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }
    </style>

</head>
<body oncontextmenu="return false;">
<?php
include($_SERVER['DOCUMENT_ROOT'] . '/trip/db.php');
// include 'header.php';
?>
<center>
    <form name="registration " onsubmit="return validatereg(this);" method="POST" action="lgverify.php">
        <legend>
            <h1 style="color:#ffffff;background-color: #000000;width: 500;">Local Guide Register</h1>
        </legend>
        <div>
            <h3>
                <input type="number" name="status" value="1" hidden="true">
                <input type="text" name="name" placeholder="Name" required><br>
                <input type="number" name="mobile" placeholder="Mobile" required><br>
                <input type="email" name="email" placeholder="Email" required>
                <input type="text" name="Language" placeholder="Language" required>
                <select name="state" style=" width: 100%;
        padding: 15px 20px;
        margin: 11px 0;
        display: inline-block;
        border: 1px solid red;
        border-radius: 4px;">
                    <option>State</option>
                    <?php
                    $sql = "SELECT * FROM State";
                    $result = mysqli_query($conn, $sql);
                    foreach ($result as $state) {
                        ?>
                        <option value="<?php $state["s_id"]; ?>"><?php echo $state["s_name"]; ?></option>


                    <?php }
                    ?>
                </select>
                <input type="password" name="password" placeholder="Password" required>
                <input type="submit" value="Signup"><br>
                <a href="http://localhost/trip/localguide/nice-html/ltr/login.php">Already have an account!</a>
            </h3>
        </div>
    </form>
</center>
<script type="text/javascript" src="signup.js"></script>
</body>
</html>
