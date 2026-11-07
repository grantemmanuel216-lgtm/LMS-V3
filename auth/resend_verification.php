<?php
require __DIR__ . '/../config/db.php';

if (!isset($_GET['email'])) {
    die("Invalid request.");
}

$email = $_GET['email'];

$sql = "SELECT * FROM users WHERE email = ? AND is_verified = 0";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();

if ($user = $result->fetch_assoc()) {

    $code = rand(100000, 999999);

    $update = "UPDATE users SET verification_code = ? WHERE email = ?";
    $stmt = $conn->prepare($update);
    $stmt->bind_param("is", $code, $email);
    $stmt->execute();

    $link = "http://localhost/lms/verify.php?code=$code";

    mail($email, "Verify your account", "Click here: $link");

    echo "✅ Verification email resent.";
} else {
    echo "❌ Email already verified or not found.";
}
