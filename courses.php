<?php
session_start();
require __DIR__ . '/config/db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: auth/login.php");
    exit();
}

$student_id = $_SESSION['user_id'];

/* ENROLL ACTION */
if (isset($_POST['enroll'])) {
    $course_id = (int)$_POST['course_id'];

    // prevent duplicate enroll
    $check = $conn->prepare("SELECT * FROM enrollments WHERE student_id=? AND course_id=?");
    $check->bind_param("ii", $student_id, $course_id);
    $check->execute();
    $result = $check->get_result();

    if ($result->num_rows == 0) {
        $stmt = $conn->prepare("INSERT INTO enrollments (student_id, course_id) VALUES (?, ?)");
        $stmt->bind_param("ii", $student_id, $course_id);
        $stmt->execute();
    }

    header("Location: " . $_SERVER['PHP_SELF']);
    exit();
}

/* FETCH COURSES */
$courses = [];
$res = $conn->query("SELECT * FROM courses");

while ($row = $res->fetch_assoc()) {
    $courses[] = $row;
}

/* FETCH ENROLLED COURSES */
$enrolled = [];
$en = $conn->prepare("SELECT course_id FROM enrollments WHERE student_id=?");
$en->bind_param("i", $student_id);
$en->execute();
$r = $en->get_result();

while ($row = $r->fetch_assoc()) {
    $enrolled[] = $row['course_id'];
}
?>

<!DOCTYPE html>
<html>
<head>
<title>Courses</title>

<style>
body {
    margin:0;
    font-family:Segoe UI;
    background:
    linear-gradient(rgba(10,25,47,0.75), rgba(2,12,27,0.9)),
    url('assets/images/bg (3).jpg');
    background-size:cover;
    color:#e6f1ff;
}

.sidebar {
    width:230px;
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
    margin-left:250px;
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

/* BUTTON */
button {
    padding:10px;
    border:none;
    border-radius:8px;
    background:#4da3ff;
    color:white;
    cursor:pointer;
}

button.enrolled {
    background:gray;
    cursor:not-allowed;
}
</style>

</head>

<body>

<div class="sidebar">
    <h2>LMS</h2>
    <a href="dashboard.php">Dashboard</a>
    <a href="courses.php">My Courses</a>
    <a href="assignments.php">Assignments</a>
    <a href="profile.php">Profile</a>
    <a href="auth/logout.php">Logout</a>
</div>

<div class="main">

<h1>📚 Courses</h1>

<div class="grid">

<?php foreach ($courses as $c): ?>
    <div class="card">
        <h3><?php echo htmlspecialchars($c['title']); ?></h3>
        <p><?php echo htmlspecialchars($c['description']); ?></p>

        <?php if (in_array($c['id'], $enrolled)): ?>
            <button class="enrolled">Enrolled</button>
        <?php else: ?>
            <form method="POST">
                <input type="hidden" name="course_id" value="<?php echo $c['id']; ?>">
                <button name="enroll">Enroll</button>
            </form>
        <?php endif; ?>
    </div>
<?php endforeach; ?>

</div>

</div>

</body>
</html>