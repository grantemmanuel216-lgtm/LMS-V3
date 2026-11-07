<?php
session_start();
require __DIR__ . '/config/db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: auth/login.php");
    exit();
}

if ($_SESSION['role'] !== 'teacher') {
    header("Location: dashboard.php");
    exit();
}

/* FETCH STUDENTS */
$students = [];
$res = $conn->query("SELECT * FROM users WHERE role = 'student'");

while ($row = $res->fetch_assoc()) {
    $students[] = $row;
}

/* ADD STUDENT */
if (isset($_POST['add_student'])) {
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);

    if ($name && $email) {
        $password = password_hash("password123", PASSWORD_DEFAULT);

        $stmt = $conn->prepare("INSERT INTO users (username, email, password, role) VALUES (?, ?, ?, 'student')");
        $stmt->bind_param("sss", $name, $email, $password);
        $stmt->execute();

        header("Location: " . $_SERVER['PHP_SELF']);
        exit();
    }
}
?>

<!DOCTYPE html>
<html>
<head>
<title>Students</title>

<style>
* { box-sizing: border-box; }

body {
    margin: 0;
    font-family: 'Segoe UI', sans-serif;
    background:
        linear-gradient(rgba(10,25,47,0.75), rgba(2,12,27,0.9)),
        url('assets/images/bg (3).jpg');
    background-size: cover;
    background-attachment: fixed;
    color: #e6f1ff;
}

.sidebar {
    width: 240px;
    height: 100vh;
    position: fixed;
    background: rgba(2,12,27,0.95);
    padding: 25px;
}

.sidebar h2 { color: #4da3ff; }

.sidebar a {
    display: block;
    margin: 12px 0;
    color: #8892b0;
    text-decoration: none;
}

.sidebar a:hover {
    color: #4da3ff;
    transform: translateX(5px);
}

.main {
    margin-left: 260px;
    padding: 40px;
}

.grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 20px;
}

.card {
    background: rgba(17, 34, 64, 0.75);
    padding: 20px;
    border-radius: 15px;
    backdrop-filter: blur(10px);
    box-shadow: 0 10px 30px rgba(0,0,0,0.4);
    transition: 0.25s;
}

.card:hover {
    transform: translateY(-5px);
}

h2 { color: #4da3ff; margin-top: 40px; }

form.card input {
    width: 100%;
    padding: 10px;
    margin: 10px 0;
    border-radius: 8px;
    border: none;
    background: #0a192f;
    color: white;
}

form.card button {
    width: 100%;
    padding: 10px;
    border: none;
    border-radius: 8px;
    background: #4da3ff;
    color: white;
    cursor: pointer;
}

p { color: #a8b2d1; }
</style>

</head>

<body>

<div class="sidebar">
    <h2>Teacher Panel</h2>
    <a href="dashboard.php">Dashboard</a>
    <a href="#">Manage Courses</a>
    <a href="#">Assignments</a>
    <a href="students.php">Students</a>
    <a href="auth/logout.php">Logout</a>
</div>

<div class="main">

    <h1>👨‍🎓 Students</h1>

    <!-- STATS -->
    <div class="grid">
        <div class="card">
            <h3>Total Students</h3>
            <p style="font-size:28px;font-weight:bold;">
                <?php echo count($students); ?>
            </p>
        </div>
    </div>

    <!-- ADD STUDENT -->
    <h2>Add Student</h2>
    <form method="POST" class="card">
        <input name="name" placeholder="Student Name" required>
        <input name="email" placeholder="Student Email" required>
        <button name="add_student">Add Student</button>
    </form>

    <!-- STUDENT LIST -->
    <h2>All Students</h2>

    <?php if (empty($students)): ?>
        <p>No students yet 👀</p>
    <?php endif; ?>

    <div class="grid">
        <?php foreach ($students as $s): ?>
            <div class="card">
                <h3><?php echo htmlspecialchars($s['username']); ?></h3>
                <p>📧 <?php echo htmlspecialchars($s['email']); ?></p>
                <p>🆔 ID: <?php echo $s['id']; ?></p>
            </div>
        <?php endforeach; ?>
    </div>

</div>

</body>
</html>