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

/* UPDATE GRADE */

if (isset($_POST['grade_submit'])) {

    $submission_id = $_POST['submission_id'];

    $grade = $_POST['grade'];

    $feedback = $_POST['feedback'];

    $stmt = $conn->prepare("
    UPDATE submissions

    SET
    grade = ?,
    feedback = ?,
    status = 'graded'

    WHERE id = ?
    ");

    $stmt->bind_param(
        "ssi",
        $grade,
        $feedback,
        $submission_id
    );

    $stmt->execute();
}

/* FETCH SUBMISSIONS */

$sql = "

SELECT

submissions.*,

users.username,

assignments.title AS assignment_title,

courses.title AS course_name

FROM submissions

JOIN users
ON submissions.student_id = users.id

JOIN assignments
ON submissions.assignment_id = assignments.id

JOIN courses
ON assignments.course_id = courses.id

ORDER BY submissions.submitted_at DESC

";

$result = $conn->query($sql);

?>

<!DOCTYPE html>
<html>

<head>

<title>Student Submissions</title>

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

.sidebar a:hover {

    color:#4da3ff;
}

/* MAIN */

.main {

    margin-left:260px;

    padding:40px;
}

/* CARD */

.card {

    background:#112240;

    padding:20px;

    border-radius:14px;

    margin-bottom:20px;

    box-shadow:
    0 10px 30px rgba(0,0,0,0.35);
}

/* INPUTS */

input,
textarea {

    width:100%;

    padding:10px;

    margin-top:10px;

    border:none;

    border-radius:8px;

    background:#0a192f;

    color:white;
}

button {

    margin-top:10px;

    padding:10px 16px;

    border:none;

    border-radius:8px;

    background:#4da3ff;

    color:white;

    cursor:pointer;
}

/* DOWNLOAD */

.download {

    display:inline-block;

    margin-top:10px;

    padding:8px 12px;

    background:#00c853;

    color:white;

    text-decoration:none;

    border-radius:8px;
}

/* STATUS */

.status {

    display:inline-block;

    padding:5px 12px;

    border-radius:20px;

    font-size:12px;

    margin-top:10px;
}

.graded {

    background:#00c853;
}

.submitted {

    background:#0072ff;
}

</style>

</head>

<body>

<div class="sidebar">

<h2>Teacher Panel</h2>

<a href="teacher_dashboard.php">
Dashboard
</a>

<a href="teacher_assignments.php">
Assignments
</a>

<a href="teacher_submissions.php">
Submissions
</a>

<a href="auth/logout.php">
Logout
</a>

</div>

<div class="main">

<h1>📥 Student Submissions</h1>

<?php if ($result->num_rows == 0): ?>

<div class="card">

<p>No submissions yet.</p>

</div>

<?php endif; ?>

<?php while ($s = $result->fetch_assoc()): ?>

<div class="card">

<h2>
<?php echo htmlspecialchars($s['assignment_title']); ?>
</h2>

<p>
👨‍🎓 Student:
<strong>
<?php echo htmlspecialchars($s['username']); ?>
</strong>
</p>

<p>
📘 Course:
<?php echo htmlspecialchars($s['course_name']); ?>
</p>

<p>
📅 Submitted:
<?php echo $s['submitted_at']; ?>
</p>

<!-- STATUS -->

<?php

if ($s['status'] === 'graded') {

echo "
<div class='status graded'>
✅ Graded
</div>";

} else {

echo "
<div class='status submitted'>
📥 Submitted
</div>";
}

?>

<br>

<!-- DOWNLOAD -->

<a
class="download"
href="uploads/<?php echo $s['file_name']; ?>"
download>

⬇ Download Submission

</a>

<!-- CURRENT GRADE -->

<?php if ($s['grade']): ?>

<p>
🏆 Grade:
<strong>
<?php echo $s['grade']; ?>
</strong>
</p>

<?php endif; ?>

<!-- FEEDBACK -->

<?php if ($s['feedback']): ?>

<p>
💬 Feedback:
<?php echo htmlspecialchars($s['feedback']); ?>
</p>

<?php endif; ?>

<!-- GRADE FORM -->

<form method="POST">

<input
type="hidden"
name="submission_id"
value="<?php echo $s['id']; ?>">

<input
type="text"
name="grade"
placeholder="Grade (A, 90%, etc)"
required>

<textarea
name="feedback"
placeholder="Feedback"></textarea>

<button name="grade_submit">

Submit Grade

</button>

</form>

</div>

<?php endwhile; ?>

</div>

</body>
</html>