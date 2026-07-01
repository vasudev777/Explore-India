<?PHP

$conn = mysqli_connect("localhost", "root", "", "exploreindia") or die(mysqli_error());

session_start();
if (isset($_POST['votp'])) {
    $votp = $_POST['votp'];
    if ($votp == $_SESSION['sendotp']) {
        $t_name = $_POST['name'];
        $t_email = $_POST['email'];
        $t_password = $_POST['password'];
        $t_mobile = $_POST['mobile'];
        $t_address = $_POST['address'];
        $t_language = $_POST['Language'];
        $t_status = $_POST['status'];

        // $sql="INSERT INTO customer_details(cust_fname,cust_lname,cust_gender,cust_email,cust_password,cust_mobile,cust_address,cust_birthdate)values ('$t_fname','$t_lname','$t_gender','$t_email','$t_password','$t_mobile','$t_address','$t_birthdate')";
        //            echo $sql;

        $sql = "INSERT INTO local_guide(localg_name,localg_mobile,localg_email,localg_language,localg_place,localg_password,status)values ('$t_name','$t_mobile','$t_email','$t_language','$t_address','$t_password','$t_status')";
        echo $sql;
        if (!mysqli_query($conn, $sql)) {
            header("location:lgregistration.php");
        } else {

            header("location:lglogin.htm");
        }
    }
}
?>