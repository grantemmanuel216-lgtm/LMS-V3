<?php
require __DIR__ . '/config/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $email = $_POST['email'];
    $code = rand(100000, 999999);
    $expires = date("Y-m-d H:i:s", time() + 900); // 15 mins

    $sql = "UPDATE users SET reset_code=?, reset_expires=? WHERE email=?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("iss", $code, $expires, $email);
    $stmt->execute();

    $link = "http://localhost/lms/reset_password.php?code=$code";

    mail($email, "Password Reset", "Reset here: $link");

    echo " Reset link sent to your email if it exists in our system.";
}
?>

<form method="POST">
    <input type="email" name="email" placeholder="Your email" required>
    <button type="submit">Send reset link</button>
</form>
