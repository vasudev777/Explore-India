<?php
<?php
 include('db.php');
session_start();

if (!empty($_POST["c_id"])) {
    $c_id = $_POST["c_id"];
    $query = "SELECT * FROM hotel WHERE c_id ='$c_id' ";
    $results = mysqli_query($conn, $query);

    foreach ($results as $hotel) {
        ?>
        <option value="<?php echo $hotel["h_id"]; ?>"><?php echo $hotel["h_name"]; ?></option>

        <?php
    }
}
?>
