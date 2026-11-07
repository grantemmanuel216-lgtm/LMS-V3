<?php
require __DIR__ . '/config/db.php';

if (isset($_POST['register'])) {

    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

    $sql = "INSERT INTO users (username, email, password)
            VALUES (?, ?, ?)";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sss", $username, $email, $password);

    if ($stmt->execute()) {
        $success = "Account created! You can now log in.";
    } else {
        $error = "Registration failed.";
    }
}
?>

<!DOCTYPE html>
<link rel="stylesheet" href="assets/css/style.css">
<html>
<head>
    <title>Register</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>

<script>
function togglePass() {
    const pass = document.getElementById("password");

    if (pass.type === "password") {
        pass.type = "text";
    } else {
        pass.type = "password";
    }
}
</script>
<body>

<div class="auth-box">
    <h2>Register</h2>

    <?php if (isset($error)) echo "<p class='error'>$error</p>"; ?>
    <?php if (isset($success)) echo "<p class='success'>$success</p>"; ?>

    <form method="POST">
        <input type="text" name="username" placeholder="Username" required>
        <input type="email" name="email" placeholder="Email" required>
        <div class="pass-wrap">
       <input type="password" id="password" name="password" placeholder="Password" required>
       <span class="eye" onclick="togglePass()">👁</span>
</div>
        <button type="submit" name="register">Register</button>
    </form>

    <p>Already have an account? <a href="auth/login.php">Login</a></p>
</div>

</body>
</html>