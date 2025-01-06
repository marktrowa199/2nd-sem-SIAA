<?php

include("searchbar_connect.php");

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

if (isset($_POST['input'])) {
    // Sanitize the user input to prevent SQL injection
    $input = mysqli_real_escape_string($con, $_POST['input']);

    // Prepare the query to search for matches
    $query = "SELECT * FROM searchperson WHERE event_name LIKE '{$input}%' OR category LIKE '{$input}%' OR event_date LIKE '{$input}%' OR event_time LIKE '{$input}%'";

    $result = mysqli_query($con, $query);

    if (mysqli_num_rows($result) > 0) { ?>

        <table class="table table-bordered table-striped mt-4">
            <thead>
                <tr>
                    <th>Id</th>
                    <th>Event Name</th>
                    <th>Category</th>
                    <th>Event Date</th>
                    <th>Event Time</th>
                    <th>Coordinator Name</th>
                    <th>Description</th>
                </tr>
            </thead>
            <tbody>
                <?php
                while ($row = mysqli_fetch_assoc($result)) {
                    $id = htmlspecialchars($row['id']);
                    $event_name = htmlspecialchars($row['event_name']);
                    $category = htmlspecialchars($row['category']);
                    $event_date = htmlspecialchars($row['event_date']);
                    $event_time = htmlspecialchars($row['event_time']);
                    $coordinator_name = htmlspecialchars($row['coordinator_name']);
                    $description = htmlspecialchars($row['description']);
                    ?>
                    <tr>
                        <td><?php echo $id; ?></td>
                        <td><?php echo $event_name; ?></td>
                        <td><?php echo $category; ?></td>
                        <td><?php echo $event_date; ?></td>
                        <td><?php echo $event_time; ?></td>
                        <td><?php echo $coordinator_name; ?></td>
                        <td><?php echo $description; ?></td>
                    </tr>
                    <?php
                }
                ?>
            </tbody>
        </table>

    <?php
    } else {
        echo "<h6 class='text-danger text-center mt-3'>No data Found</h6>";
    }
} else {
    echo "<h6 class='text-danger text-center mt-3'>Invalid Request</h6>";
}
?>
