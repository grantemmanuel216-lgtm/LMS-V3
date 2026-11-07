<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require __DIR__ . '/../config/db.php';

session_start();

/* EMAIL VERIFIED MESSAGE */
if (isset($_GET['verified']) && $_GET['verified'] == 1) {

    echo "✅ Email verified successfully.
          You can log in now.<br><br>";
}

/* LOGIN */
if (isset($_POST['login'])) {

    $username = $_POST['username'];
    $password = $_POST['password'];
    $role = $_POST['role'];

    $sql = "SELECT id, username, password, role
            FROM users
            WHERE username = ? AND role = ?";

    $stmt = mysqli_prepare($conn, $sql);

    mysqli_stmt_bind_param(
        $stmt,
        "ss",
        $username,
        $role
    );

    mysqli_stmt_execute($stmt);

    $result = mysqli_stmt_get_result($stmt);

    $user = mysqli_fetch_assoc($result);

    $error = "";

    if ($user && isset($user['password'])) {

        if (password_verify($password, $user['password'])) {

            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['role'] = $user['role'];

            /* REDIRECT BASED ON ROLE */

            if ($user['role'] === 'teacher') {

                header("Location: ../teacher_dashboard.php");

            } else {

                header("Location: ../dashboard.php");
            }

            exit();

        } else {

            $error = "❌ Wrong password";
        }

    } else {

        $error = "❌ Invalid login credentials";
    }
}
?>

<!DOCTYPE html>
<html>

<head>

<link rel="stylesheet"
href="../assets/css/style.css">

<title>Login</title>

</head>

<script>

function togglePass() {

    const pass =
    document.getElementById("password");

    if (pass.type === "password") {

        pass.type = "text";

    } else {

        pass.type = "password";
    }
}

</script>

<body>

<div class="auth-box">

    <h2>Login</h2>

    <?php
    if (!empty($error)) {

        echo "<div class='error-box'>
              $error
              </div>";
    }
    ?>

    <form method="POST">

        <!-- USERNAME -->
        <input
        type="text"
        name="username"
        placeholder="Username"
        required>

        <!-- PASSWORD -->
        <div class="pass-wrap">

            <input
            type="password"
            id="password"
            name="password"
            placeholder="Password"
            required>

            <span class="eye"
            onclick="togglePass()">
            👁️
            </span>

        </div>

        <!-- ROLE SELECT -->
        <select name="role" required>

            <option value="">
                Select Role
            </option>

            <option value="student">
                👨‍🎓 Student
            </option>

            <option value="teacher">
                👨‍🏫 Teacher
            </option>

        </select>

        <!-- BUTTON -->
        <button
        type="submit"
        name="login">

        Login

        </button>

    </form>

</div>

</body>
</html>