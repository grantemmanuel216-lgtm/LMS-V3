<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: auth/login.php");
    exit();
}

require __DIR__ . '/config/db.php';

/* =========================
   POST COMMENT
========================= */

if (isset($_POST['comment_submit'])) {

    $assignment_id = (int) $_POST['assignment_id'];
    $comment = trim($_POST['comment']);

    if (!empty($comment)) {

        $stmt = $conn->prepare("
            INSERT INTO comments
            (assignment_id, user_id, comment)
            VALUES (?, ?, ?)
        ");

        $stmt->bind_param(
            "iis",
            $assignment_id,
            $_SESSION['user_id'],
            $comment
        );

        $stmt->execute();
    }
}

/* =========================
   GET ASSIGNMENTS
========================= */

$sql = "
SELECT
    assignments.id,
    assignments.title,
    assignments.due_date,
    courses.title AS course_title

FROM assignments

JOIN courses
ON assignments.course_id = courses.id

ORDER BY assignments.due_date ASC
";

$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">

<head>

<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Assignments</title>

<style>

*{
    margin:0;
    padding:0;
    box-sizing:border-box;
}

body{

    font-family:Arial, sans-serif;

    background:
    linear-gradient(
    rgba(10,25,47,0.75),
    rgba(2,12,27,0.9)),

    url('assets/images/bg (3).jpg');

    background-size:cover;
    background-position:center;
    background-attachment:fixed;

    color:white;
}

/* CONTAINER */

.container{

    width:90%;
    max-width:1200px;

    margin:auto;
    padding:40px 0;
}

/* TITLE */

.page-title{

    text-align:center;
    margin-bottom:40px;
}

.page-title h1{

    font-size:40px;
    margin-bottom:10px;
}

.page-title p{

    opacity:0.8;
}

/* GRID */

.grid{

    display:grid;

    grid-template-columns:
    repeat(auto-fit, minmax(320px, 1fr));

    gap:25px;
}

/* CARD */

.card{

    background:rgba(255,255,255,0.08);

    backdrop-filter:blur(12px);

    border-radius:18px;

    padding:22px;

    box-shadow:
    0 10px 25px rgba(0,0,0,0.35);

    transition:0.3s;
}

.card:hover{

    transform:translateY(-6px);

    box-shadow:
    0 14px 35px rgba(0,0,0,0.45);
}

/* ASSIGNMENT TITLE */

.assignment-title{

    font-size:24px;
    margin-bottom:14px;
}

/* INFO */

.info{

    margin-bottom:10px;
    opacity:0.9;
}

/* COMMENTS */

.comments-title{

    margin-top:20px;
    margin-bottom:15px;
}

.comment-box{

    background:#0a192f;

    padding:12px;

    border-radius:10px;

    margin-bottom:12px;
}

.comment-box strong{

    color:#4da3ff;
}

.comment-box p{

    margin:8px 0;
    line-height:1.5;
}

.comment-box small{

    opacity:0.7;
}

/* FORM */

textarea{

    width:100%;
    min-height:90px;

    margin-top:10px;
    padding:12px;

    border:none;
    border-radius:10px;

    resize:none;

    background:rgba(255,255,255,0.1);
    color:white;

    outline:none;
}

textarea::placeholder{
    color:#ccc;
}

/* BUTTON */

button{

    margin-top:12px;

    padding:12px 18px;

    border:none;
    border-radius:10px;

    cursor:pointer;

    background:
    linear-gradient(135deg,
    #00c6ff,
    #0072ff);

    color:white;

    font-weight:bold;

    transition:0.3s;
}

button:hover{

    transform:translateY(-2px);

    box-shadow:
    0 0 18px rgba(0,198,255,0.5);
}4

/* EMPTY */

.empty{

    text-align:center;

    background:rgba(255,255,255,0.08);

    padding:40px;

    border-radius:18px;
}

</style>

</head>

<body>

<div class="container">

    <div class="page-title">

        <h1>📝 Student Assignments</h1>

        <p>
            View all assignments uploaded by teachers
        </p>

    </div>

    <?php if ($result && $result->num_rows > 0) { ?>

    <div class="grid">

        <?php while ($a = $result->fetch_assoc()) { ?>

        <div class="card">

            <!-- ASSIGNMENT -->

            <h2 class="assignment-title">
                <?php echo htmlspecialchars($a['title']); ?>
            </h2>

            <p class="info">
                📘 Course:
                <?php echo htmlspecialchars($a['course_title']); ?>
            </p>

            <p class="info">
                📅 Due:
                <?php echo htmlspecialchars($a['due_date']); ?>
            </p>

            <!-- COMMENTS QUERY -->

            <?php

            $assignment_id = (int) $a['id'];

            $comments = $conn->query("
                SELECT
                    comments.*,
                    users.username

                FROM comments

                JOIN users
                ON comments.user_id = users.id

                WHERE comments.assignment_id = $assignment_id

                ORDER BY comments.created_at DESC
            ");

            ?>

            <!-- COMMENTS -->

            <h3 class="comments-title">💬 Comments</h3>

            <?php if ($comments && $comments->num_rows > 0) { ?>

                <?php while ($c = $comments->fetch_assoc()) { ?>

                <div class="comment-box">

                    <strong>
                        <?php echo htmlspecialchars($c['username']); ?>
                    </strong>

                    <p>
                        <?php echo htmlspecialchars($c['comment']); ?>
                    </p>

                    <small>
                        <?php echo htmlspecialchars($c['created_at']); ?>
                    </small>

                </div>

                <?php } ?>

            <?php } else { ?>

                <p>No comments yet.</p>

            <?php } ?>

            <!-- COMMENT FORM -->

            <form method="POST">

                <input
                type="hidden"
                name="assignment_id"
                value="<?php echo $a['id']; ?>">

                <textarea
                name="comment"
                placeholder="Write a comment..."
                required></textarea>

                <button type="submit" name="comment_submit">
                    Post Comment
                </button>

            </form>

        </div>

        <?php } ?>

    </div>

    <?php } else { ?>

    <div class="empty">

        <h2>No Assignments Found</h2>

        <p>
            There are currently no assignments available.
        </p>

    </div>

    <?php } ?>

</div>

</body>
</html>