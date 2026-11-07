<?php
session_start();
require __DIR__ . '/config/db.php';

if ($_SESSION['role'] !== 'teacher') {
    header("Location: dashboard.php");
    exit();
}

if (isset($_POST['create'])) {
    $title = $_POST['title'];
    $desc = $_POST['description'];
    $teacher_id = $_SESSION['user_id'];

    $sql = "INSERT INTO courses (title, description, teacher_id) VALUES (?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssi", $title, $desc, $teacher_id);
    $stmt->execute();
}
?>

<form method="POST">
    <input name="title" placeholder="Course Title" required>
    <textarea name="description" placeholder="Description"></textarea>
    <button name="create">Create Course</button>
</form>