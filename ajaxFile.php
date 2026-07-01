<?php
include('db.php');

// ── Customize page: State select → Hotels as CHECKBOXES ──
if (isset($_POST["s_id"])) {
    $s_id = mysqli_real_escape_string($conn, $_POST['s_id']);
    $query = "SELECT * FROM hotel NATURAL JOIN city WHERE hotel.s_id = '$s_id' ORDER BY c_name, h_name ASC";
    $run_query = mysqli_query($conn, $query);
    $count = mysqli_num_rows($run_query);

    if ($count > 0) {
        $current_city = '';
        while ($row = mysqli_fetch_array($run_query)) {
            $h_id   = $row['h_id'];
            $h_name = htmlspecialchars($row['h_name']);
            $c_name = htmlspecialchars($row['c_name']);

            // City group heading
            if ($c_name !== $current_city) {
                if ($current_city !== '') echo '</div>';
                $current_city = $c_name;
                echo '<div class="city-group">';
                echo '<div class="city-label"><span class="fa fa-map-marker"></span> ' . $c_name . '</div>';
            }

            echo '
            <label class="hotel-checkbox">
                <input type="checkbox" name="hotels[]" value="' . $h_id . '">
                <span class="hotel-check-box"></span>
                <span class="hotel-name">' . $h_name . '</span>
            </label>';
        }
        echo '</div>';
    } else {
        echo '<p class="no-hotels">No hotels found for this state.</p>';
    }
}

// ── Registration/other pages: City select → Hotels as OPTIONS (unchanged) ──
if (isset($_POST["c_id"])) {
    $c_id = mysqli_real_escape_string($conn, $_POST['c_id']);
    $query = "SELECT * FROM hotel WHERE c_id = '$c_id' ORDER BY h_name ASC";
    $run_query = mysqli_query($conn, $query);
    $count = mysqli_num_rows($run_query);

    if ($count > 0) {
        echo '<option value="">Select city</option>';
        while ($row = mysqli_fetch_array($run_query)) {
            $h_id   = $row['h_id'];
            $h_name = $row['h_name'];
            echo "<option value='$h_id'>$h_name</option>";
        }
    } else {
        echo '<option value="">City not available</option>';
    }
}
?>