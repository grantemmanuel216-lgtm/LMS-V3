<?php
require __DIR__ . '/config/db.php';

if (!isset($_GET['code'])) {
    die("Invalid reset link.");
}

$code = $_GET['code'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $newPassword = password_hash($_POST['password'], PASSWORD_DEFAULT);

    $sql = "UPDATE users 
            SET password=?, reset_code=NULL, reset_expires=NULL
            WHERE reset_code=? AND reset_expires > NOW()";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("si", $newPassword, $code);
    $stmt->execute();

    if ($stmt->affected_rows === 1) {
        echo "✅ Password reset successful. You can log in.";
    } else {
        echo "❌ Invalid or expired reset link.";
    }
}
?>

<form method="POST">
    <input type="password" name="password" placeholder="New password" required>
    <button type="submit">Reset password</button>
</form>
