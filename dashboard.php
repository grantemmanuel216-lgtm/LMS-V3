<?php
session_start();

require __DIR__ . '/config/db.php';

if (!isset($_SESSION['user_id'])) {

    header("Location: auth/login.php");
    exit();
}

$student_id = $_SESSION['user_id'];

/* FETCH COURSES */
$courses = [];

$res = $conn->query("SELECT * FROM courses");

while ($row = $res->fetch_assoc()) {

    $courses[] = $row;
}

/* FETCH ASSIGNMENTS */
$assignments = [];

$sql = "

SELECT
    assignments.*,
    courses.title AS course_name

FROM assignments

JOIN courses
ON assignments.course_id = courses.id

ORDER BY assignments.due_date ASC

";

$a = $conn->query($sql);

while ($row = $a->fetch_assoc()) {

    $assignments[] = $row;
}

/* FETCH SUBMISSIONS */

$submittedAssignments = [];

$sub =
$conn->query("
SELECT assignment_id
FROM submissions
WHERE student_id = $student_id
");

while ($s = $sub->fetch_assoc()) {

    $submittedAssignments[] =
    $s['assignment_id'];
}

/* STATS */

$totalCourses = count($courses);

$totalAssignments = count($assignments);

$pending = 0;
$submitted = 0;
$overdue = 0;

$today = date('Y-m-d');

foreach ($assignments as $as) {

    $isSubmitted =
    in_array(
        $as['id'],
        $submittedAssignments
    );

    if ($isSubmitted) {

        $submitted++;

    } else {

        if ($as['due_date'] < $today) {

            $overdue++;

        } else {

            $pending++;
        }
    }
}

/* PROGRESS */

$progress =
$totalAssignments
? ($submitted / $totalAssignments) * 100
: 0;

?>

<!DOCTYPE html>
<html>

<head>

<title>Student Dashboard</title>

<style>

body {

    margin:0;
    font-family:Segoe UI;

    background:
    linear-gradient(
    rgba(10,25,47,0.75),
    rgba(2,12,27,0.9)),

    url('assets/images/bg (3).jpg');

    background-size:cover;

    color:#e6f1ff;
}

/* SIDEBAR */

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

.sidebar a:hover {

    color:#4da3ff;
}

/* MAIN */

.main {

    margin-left:250px;

    padding:40px;
}

/* CARD */

.card {

    background:#112240;

    padding:20px;

    border-radius:14px;

    margin-bottom:20px;

    box-shadow:
    0 10px 30px rgba(0,0,0,0.3);
}

/* GRID */

.grid {

    display:grid;

    grid-template-columns:2fr 1fr;

    gap:20px;
}

/* MINI GRID */

.mini-grid {

    display:grid;

    grid-template-columns:
    repeat(auto-fit,minmax(220px,1fr));

    gap:15px;
}

/* STATUS */

.status {

    display:inline-block;

    padding:5px 12px;

    border-radius:20px;

    font-size:12px;

    margin-top:10px;
}

.pending {

    background:#0072ff;
}

.submitted {

    background:#00c853;
}

.overdue {

    background:#ff1744;
}

/* BUTTON */

.submit-btn {

    display:inline-block;

    margin-top:10px;

    padding:8px 14px;

    border-radius:8px;

    text-decoration:none;

    background:#4da3ff;

    color:white;
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

<h1>
Welcome,
<?php echo htmlspecialchars($_SESSION['username']); ?>
👋
</h1>

<!-- STATS -->

<div class="mini-grid">

    <div class="card">

        <h3>📚 Courses</h3>

        <p style="font-size:28px;">
        <?php echo $totalCourses; ?>
        </p>

    </div>

    <div class="card">

        <h3>📝 Assignments</h3>

        <p style="font-size:28px;">
        <?php echo $totalAssignments; ?>
        </p>

    </div>

    <div class="card">

        <h3>⏳ Pending</h3>

        <p style="font-size:28px;">
        <?php echo $pending; ?>
        </p>

    </div>

    <div class="card">

        <h3>✅ Submitted</h3>

        <p style="font-size:28px;">
        <?php echo $submitted; ?>
        </p>

    </div>

</div>

<!-- MAIN GRID -->

<div class="grid">

<!-- LEFT -->

<div>

<!-- ASSIGNMENTS -->

<div class="card">

<h2>📝 Assignments</h2>

<?php if (empty($assignments)): ?>

<p>No assignments available</p>

<?php endif; ?>

<?php foreach ($assignments as $as):

$isSubmitted =
in_array(
$as['id'],
$submittedAssignments
);

?>

<div style="
margin-bottom:20px;
padding-bottom:15px;
border-bottom:1px solid rgba(255,255,255,0.08);
">

<strong style="font-size:18px;">
<?php echo htmlspecialchars($as['title']); ?>
</strong>

<p>
📘
<?php echo htmlspecialchars($as['course_name']); ?>
</p>

<p>
📅 Due:
<?php echo $as['due_date']; ?>
</p>

<?php

if ($isSubmitted) {

echo "<div class='status submitted'>
✅ Submitted
</div>";

} else {

if ($as['due_date'] < $today) {

echo "<div class='status overdue'>
❌ Overdue
</div>";

} else {

echo "<div class='status pending'>
⏳ Pending
</div>";
}

?>

<br>

<a
class="submit-btn"
href="submit_assignment.php?id=<?php echo $as['id']; ?>">
Upload Work
</a>

<?php } ?>

</div>

<?php endforeach; ?>

</div>

</div>

<!-- RIGHT -->

<div>

<!-- PROGRESS -->

<div class="card">

<h3>📊 Progress</h3>

<div style="
background:#0a192f;
height:10px;
border-radius:10px;
overflow:hidden;
">

<div style="
width:<?php echo $progress; ?>%;
height:100%;
background:#4da3ff;
">

</div>

</div>

<p style="margin-top:10px;">
<?php echo round($progress); ?>%
Completed
</p>

</div>

<!-- QUICK INFO -->

<div class="card">

<h3>🔥 Quick Info</h3>

<p>
Stay consistent and submit before deadlines.
</p>

<p>
Overdue assignments may affect grading.
</p>

</div>

</div>

</div>

</div>

</body>
</html>