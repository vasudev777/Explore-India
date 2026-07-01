t
<html>
<head>
    <title>LOGIN</title>
    <style>

        input[type=email] {
            padding: 20px;
            margin: 5px;
            border: 1px solid red;
            border-radius: 5px;
            width: 70%;
        }

        input[type=password] {
            padding: 20px;
            margin: 5px;
            border: 1px solid red;
            border-radius: 5px;
            width: 70%;
        }

        input[type=submit] {
            float: left;
            width: 30%;
            padding: 15px;
            border: 1px solid lightsalmon;
            cursor: pointer;
            margin-top: 35px;
            margin-left: 65px;
        }

        input[type=button] {


            float: none;
            width: 30%;
            padding: 15px;
            border: 1px solid lightsalmon;
            cursor: pointer;


        }

        div {
            border: 1px solid rosybrown;;
            border-style: groove;
            margin: 50px 400px 400px 400px;
            border-radius: 15px;
            border-width: 3px;
        }

        h1 {
            font-family: 'Times New Roman', Times, serif;
            font-size: 30px;
            text-align: center;
            color: teal;
        }
    </style>
</head>
<body><br><br><br>
<h1>LOGIN</h1>
<center>
    <div>
        <form name="f1" method="POST" onsubmit=" return my(this);" action="login_db_js.php">
            <input type="email" name="email" placeholder="Email"><br><br>
            <input type="password" name="pass" placeholder="Password"><br><br>
            <input type="submit" name="submit" value="Login"><br><br>
            <input type="button" name="signup" value="signup" onclick="window.location.href='signup1.htm'">
            <br><br>
            <h3>
                <a href="#">Forgot password?</a>
            </h3>
        </form>
    </div>
</center>

<script type="text/javascript">
    function my(form) {
        var p1 = form.pass.value;

        if (p1.length == " ") {
            alert("Enter Password");
            return false;
        }
        if (p1.length < 8) {
            alert("password must have more then 8 character !!!");
            return false;
        } else {
            window.location.href = "homepg.html";
        }
    }

</script>
</body>
</html>                