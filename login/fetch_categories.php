<?php
include 'connect.php';

// Query to fetch categories
$query = "SELECT * FROM categories";
$result = mysqli_query($conn, $query);

$categories = [];
if ($result) {
    while ($row = mysqli_fetch_assoc($result)) {
        $categories[] = $row;
    }
}

echo json_encode($categories);

// Close the database connection
mysqli_close($conn);
?>
