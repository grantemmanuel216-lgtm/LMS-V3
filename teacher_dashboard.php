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
$result = $conn->query("SELECT * FROM courses");
while ($row = $result->fetch_assoc()) {
    $courses[] = $row;
}

/* ADD ASSIGNMENT */
if (isset($_POST['add'])) {
    $title = trim($_POST['title']);
    $course = (int)$_POST['course'];
    $due = $_POST['due'];

    if ($title && $course && $due) {
        $stmt = $conn->prepare("INSERT INTO assignments (course_id, title, due_date) VALUES (?, ?, ?)");
        $stmt->bind_param("iss", $course, $title, $due);
        $stmt->execute();
        header("Location: " . $_SERVER['PHP_SELF']); // prevent resubmit
        exit();
    }
}

/* FETCH ASSIGNMENTS WITH COURSE NAME */
$assignments = [];
$res = $conn->query("
    SELECT assignments.*, courses.title AS course_name 
    FROM assignments 
    JOIN courses ON assignments.course_id = courses.id
");

while ($a = $res->fetch_assoc()) {
    $assignments[] = $a;
}
?>

<!DOCTYPE html>
<html>
<head>
<title>Teacher Dashboard</title>

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

/* SIDEBAR */
.sidebar {
    width: 240px;
    height: 100vh;
    position: fixed;
    background: rgba(2,12,27,0.95);
    padding: 25px;
    backdrop-filter: blur(10px);
}

.sidebar h2 {
    color: #4da3ff;
}

.sidebar a {
    display: block;
    margin: 12px 0;
    color: #8892b0;
    text-decoration: none;
    transition: 0.2s;
}

.sidebar a:hover {
    color: #4da3ff;
    transform: translateX(6px);
}

/* MAIN */
.main {
    margin-left: 260px;
    padding: 40px;
}

/* GRID */
.grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 20px;
}

/* CARD */
.card {
    background: rgba(17, 34, 64, 0.75);
    padding: 20px;
    border-radius: 15px;
    backdrop-filter: blur(12px);
    box-shadow: 0 10px 30px rgba(0,0,0,0.4);
    transition: 0.25s;
}

.card:hover {
    transform: translateY(-5px);
}

/* HEADINGS */
h1 { margin-bottom: 10px; }
h2 { margin: 40px 0 15px; color: #4da3ff; }

/* FORM */
form.card input,
form.card select {
    width: 100%;
    padding: 12px;
    margin: 10px 0;
    border-radius: 8px;
    border: none;
    background: #0a192f;
    color: white;
}

form.card button {
    width: 100%;
    padding: 12px;
    border-radius: 8px;
    border: none;
    background: #4da3ff;
    color: white;
    font-weight: bold;
    cursor: pointer;
}

form.card button:hover {
    background: #2f89ff;
}

/* TEXT */
p { color: #a8b2d1; }

/* SECTION SPACING */
.section {
    margin-bottom: 50px;
}
</style>

</head>

<body>

<div class="sidebar">
    <h2>Teacher Panel</h2>
    <a href="teacher_dashboard.php">Dashboard</a>
    <a href="manage_courses.php">Manage Courses</a>
    <a href="teacher_assignments.php">add assignments</a>
    <a href="students.php">Students</a>
    <a href="auth/logout.php">Logout</a>
    <a href="teacher_submissions.php">Submissions</a>
    <a href="teacher_attendance.php">Attendance</a> 

</div>

<div class="main">

    <h1>Welcome, <?php echo htmlspecialchars($_SESSION['username']); ?> 👨‍🏫</h1>

    <!-- STATS -->
    <div class="grid section">
        <div class="card">
            <h3>📚 Courses</h3>
            <p style="font-size:28px;font-weight:bold;">
                <?php echo count($courses); ?>
            </p>
        </div>

        <div class="card">
            <h3>📝 Assignments</h3>
            <p style="font-size:28px;font-weight:bold;">
                <?php echo count($assignments); ?>
            </p>
        </div>

        <div class="card">
            <h3>👥 Students</h3>
            <p style="font-size:28px;font-weight:bold;">25</p>
        </div>
    </div>

    <!-- COURSES -->
    <div class="section">
        <h2>Courses</h2>
        <div class="grid">
            <?php foreach ($courses as $course): ?>
                <div class="card">
                    <h3><?php echo htmlspecialchars($course['title']); ?></h3>
                    <p><?php echo htmlspecialchars($course['description']); ?></p>
                </div>
            <?php endforeach; ?>
        </div>
    </div>

    <!-- ADD ASSIGNMENT -->
    <div class="section">
        <h2>Add Assignment</h2>

        <form method="POST" class="card">
            <input name="title" placeholder="Assignment Title" required>

            <select name="course" required>
                <option value="">Select Course</option>
                <?php foreach ($courses as $course): ?>
                    <option value="<?php echo $course['id']; ?>">
                        <?php echo htmlspecialchars($course['title']); ?>
                    </option>
                <?php endforeach; ?>
            </select>

            <input type="date" name="due" required>
            <button name="add">Create Assignment</button>
        </form>
    </div>

    <!-- ASSIGNMENTS -->
    <div class="section">
        <h2>Assignments</h2>

        <?php if (empty($assignments)): ?>
            <p>No assignments yet. Create one above 👆</p>
        <?php endif; ?>

        <div class="grid">
            <?php foreach ($assignments as $a): ?>
                <div class="card">
                    <h4><?php echo htmlspecialchars($a['title']); ?></h4>
                    <p>📘 <?php echo htmlspecialchars($a['course_name']); ?></p>
                    <p>📅 Due: <?php echo $a['due_date']; ?></p>
                </div>
            <?php endforeach; ?>
        </div>
    </div>

</div>

</body>
</html>