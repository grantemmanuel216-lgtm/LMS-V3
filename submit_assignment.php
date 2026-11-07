<?php
session_start();
include 'db.php';

if (!isset($_SESSION['student_id'])) {
    header("Location: login.php");
    exit();
}

if (isset($_POST['submit'])) {

    $assignment_id = intval($_GET['id']); // FIXED HERE
    $student_id = $_SESSION['student_id'];

    if (isset($_FILES['file']) && $_FILES['file']['error'] === 0) {

        $file = $_FILES['file'];

        $filename = time() . "_" . basename($file['name']); // safer
        $target_dir = "uploads/";
        $target_file = $target_dir . $filename;

        if (move_uploaded_file($file['tmp_name'], $target_file)) {

            $penalty = 0;

            $stmt = $conn->prepare("
                INSERT INTO submissions 
                (assignment_id, student_id, filename, penalty) 
                VALUES (?, ?, ?, ?)
            ");

            $stmt->bind_param(
                "iisi",
                $assignment_id,
                $student_id,
                $filename,
                $penalty
            );

            $stmt->execute();
            $stmt->close();
        }
    }

    header("Location: dashboard.php");
    exit();
}
?>

<!DOCTYPE html>
<html>
<head>
<title>Submit Assignment</title>

<style>
body {
    background:#0a192f;
    color:white;
    font-family:Segoe UI;
    display:flex;
    justify-content:center;
    align-items:center;
    height:100vh;
}

.card {
    background:#112240;
    padding:30px;
    border-radius:15px;
    width:400px;
}

input, button {
    width:100%;
    padding:12px;
    margin-top:15px;
    border:none;
    border-radius:8px;
}

button {
    background:#4da3ff;
    color:white;
}
</style>

</head>

<body>

<div class="card">

<h2>📤 Submit Assignment</h2>
<form method="POST" enctype="multipart/form-data">

<input type="file" name="file" required>

<button name="submit">Submit Work</button>

</form>

</div>

</body>
</html>