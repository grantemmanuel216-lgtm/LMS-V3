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

/* FETCH COURSES */
$courses = [];
$res = $conn->query("SELECT * FROM courses");
while ($row = $res->fetch_assoc()) {
    $courses[] = $row;
}

/* ADD ASSIGNMENT */
if (isset($_POST['add_assignment'])) {
    $title = trim($_POST['title']);
    $course = (int)$_POST['course'];
    $due = $_POST['due'];

    if ($title && $course && $due) {
        $stmt = $conn->prepare("INSERT INTO assignments (course_id, title, due_date) VALUES (?, ?, ?)");
        $stmt->bind_param("iss", $course, $title, $due);
        $stmt->execute();

        header("Location: " . $_SERVER['PHP_SELF']);
        exit();
    }
}

/* FETCH ASSIGNMENTS */
$assignments = [];
$q = $conn->query("
    SELECT assignments.*, courses.title AS course_name 
    FROM assignments 
    JOIN courses ON assignments.course_id = courses.id
");

while ($a = $q->fetch_assoc()) {
    $assignments[] = $a;
}
?>

<!DOCTYPE html>
<html>
<head>
<title>Assignments</title>

<style>
body {
    margin:0;
    font-family:Segoe UI;
    background:linear-gradient(rgba(10,25,47,0.75), rgba(2,12,27,0.9)),
    url('assets/images/bg (3).jpg');
    background-size:cover;
    color:#e6f1ff;
}

.sidebar {
    width:240px;
    height:100vh;
    position:fixed;
    background:#020c1b;
    padding:20px;
}

.sidebar a {
    display:block;
    margin:12px 0;
    color:#8892b0;
    text-decoration:none;
}

.sidebar a:hover { color:#4da3ff; }

.main {
    margin-left:260px;
    padding:40px;
}

.grid {
    display:grid;
    grid-template-columns:repeat(auto-fit,minmax(250px,1fr));
    gap:20px;
}

.card {
    background:#112240;
    padding:20px;
    border-radius:12px;
}

input, select {
    width:100%;
    padding:10px;
    margin:10px 0;
    border:none;
    border-radius:8px;
}

button {
    width:100%;
    padding:10px;
    background:#4da3ff;
    border:none;
    border-radius:8px;
    color:white;
}
</style>

</head>

<body>

<div class="sidebar">
    <h2>Teacher Panel</h2>
    <a href="teacher_dashboard.php">Dashboard</a>
    <a href="teacher_assignments.php">Assignments</a>
    <a href="students.php">Students</a>
    <a href="auth/logout.php">Logout</a>
</div>

<div class="main">

<h1>📝 Assignments</h1>

<h2>Add Assignment</h2>
<form method="POST" class="card">
    <input name="title" placeholder="Assignment Title" required>

    <select name="course" required>
        <option value="">Select Course</option>
        <?php foreach ($courses as $c): ?>
            <option value="<?php echo $c['id']; ?>">
                <?php echo htmlspecialchars($c['title']); ?>
            </option>
        <?php endforeach; ?>
    </select>

    <input type="date" name="due" required>
    <button name="add_assignment">Create</button>
</form>

<h2>All Assignments</h2>

<div class="grid">
<?php foreach ($assignments as $a): ?>
    <div class="card">
        <h3><?php echo htmlspecialchars($a['title']); ?></h3>
        <p>📘 <?php echo htmlspecialchars($a['course_name']); ?></p>
        <p>📅 Due: <?php echo $a['due_date']; ?></p>
    </div>
<?php endforeach; ?>
</div>

</div>

</body>
</html>