<?php
session_start();

require __DIR__ . '/config/db.php';

/* GET STUDENTS */

$students = $conn->query("
SELECT * FROM users
WHERE role='student'
");

/* SAVE ATTENDANCE */

if (isset($_POST['save_attendance'])) {

    foreach ($_POST['status'] as $student_id => $status) {

        $date = date('Y-m-d');

        $stmt = $conn->prepare("
        INSERT INTO attendance
        (student_id, date, status)

        VALUES (?, ?, ?)
        ");

        $stmt->bind_param(
            "iss",
            $student_id,
            $date,
            $status
        );

        $stmt->execute();
    }

    echo "<script>alert('Attendance saved successfully');</script>";
}
?>

<!DOCTYPE html>
<html>

<head>

<title>Attendance</title>

<style>

body {

    background:#0a192f;
    color:white;
    font-family:Segoe UI;
    padding:20px;
}

.card {

    background:#112240;

    padding:25px;

    border-radius:15px;

    max-width:700px;

    margin:auto;
}

.student {

    background:#1b2d4a;

    padding:15px;

    margin-bottom:15px;

    border-radius:10px;
}

select {

    padding:10px;

    border:none;

    border-radius:8px;

    margin-top:10px;
}

button {

    margin-top:20px;

    padding:12px 20px;

    border:none;

    border-radius:10px;

    background:#00bfff;

    color:white;

    cursor:pointer;

    font-weight:bold;
}

button:hover {

    background:#0099cc;
}

</style>

</head>

<body>

<div class="card">

    <h1>📋 Attendance</h1>

    <form method="POST">

        <?php while ($s = $students->fetch_assoc()) { ?>

            <div class="student">

                <h3>
                    <?php echo htmlspecialchars($s['username']); ?>
                </h3>

                <select name="status[<?php echo $s['id']; ?>]">

                    <option value="Present">
                        Present
                    </option>

                    <option value="Absent">
                        Absent
                    </option>

                    <option value="Late">
                        Late
                    </option>

                </select>

            </div>

        <?php } ?>

        <button
        type="submit"
        name="save_attendance">

            Save Attendance

        </button>

    </form>

</div>

</body>
</html>