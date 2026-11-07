<?php
$conn = mysqli_connect(
    "localhost",
    "root",
    "",
    "lms"   
);

if (!$conn) {
    die("Database connection failed: " . mysqli_connect_error());
}
?>
