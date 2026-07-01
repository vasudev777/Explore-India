<?php

include('db.php');


if (!empty($_POST["s_id"])) {
    $sid = $_POST["s_id"];
    $query = "SELECT * FROM city WHERE s_id=$sid ";
    $results = mysqli_query($conn, $query);

    foreach ($results as $city) {
        ?>
        <option value="<?php echo $city["c_id"]; ?>"><?php echo $city["c_name"]; ?>
        </option>
        <?php
    }
}
?>
