<?php
require __DIR__ . '/../config/db.php';

if (isset($_GET['code'])) {

    $code = $_GET['code'];

    $sql = "UPDATE users SET is_verified=1 WHERE verification_code=?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $code);

    if ($stmt->execute()) {
        echo "✅ Account verified! You can now log in.";
    } else {
        echo "❌ Verification failed.";
    }
}
?>
