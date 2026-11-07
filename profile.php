<?php
require __DIR__ . '/config/innit.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: auth/login.php");
    exit();
}
?>
<?php
require __DIR__ . '/config/db.php';

if (isset($_POST['update'])) {
    $new_username = $_POST['username'];

    $sql = "UPDATE users SET username=? WHERE id=?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("si", $new_username, $_SESSION['user_id']);
    $stmt->execute();

    $_SESSION['username'] = $new_username;
}
?>

<?php if (!empty($_SESSION['profile_pic'])): ?>
    <img src="<?php echo $_SESSION['profile_pic']; ?>" class="avatar">
<?php else: ?>
    <div class="avatar-fallback">
        <?php echo strtoupper(substr($_SESSION['username'],0,1)); ?>
    </div>
<?php endif; ?>

<?php
require __DIR__ . '/config/db.php';

if (isset($_POST['upload']) && isset($_FILES['profile_pic'])) {

    $file = $_FILES['profile_pic'];

    if ($file['error'] === 0) {

        $filename = time() . "_" . basename($file['name']);
        $target = "uploads/" . $filename;

        if (move_uploaded_file($file['tmp_name'], $target)) {

            $sql = "UPDATE users SET profile_pic=? WHERE id=?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("si", $target, $_SESSION['user_id']);
            $stmt->execute();

            $_SESSION['profile_pic'] = $target;
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
<title>Profile</title>

<style>
body {
    margin: 0;
    font-family: Arial;
    background: 
        linear-gradient(rgba(10,25,47,0.6), rgba(2,12,27,0.75)),
        url('assets/images/bg (3).jpg');
    background-size: cover;
    background-position: center;
    color: white;
}

/* MAIN */
.container {
    max-width: 800px;
    margin: 60px auto;
    background: #112240;
    padding: 30px;
    border-radius: 15px;
    box-shadow: 0 10px 30px rgba(0,0,0,0.5);
}

.avatar img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    border-radius: 50%;
}

/* TOP */
.top {
    display: flex;
    align-items: center;
    gap: 20px;
    margin-bottom: 30px;
}

.avatar {
    width: 80px;
    height: 80px;
    border-radius: 50%;
    background: #4da3ff;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 30px;
}

/* FORM */
input {
    width: 100%;
    padding: 10px;
    margin: 10px 0;
    border-radius: 8px;
    border: none;
}

button {
    background: linear-gradient(135deg, #4da3ff, #1f78ff);
    border: none;
    padding: 10px 15px;
    border-radius: 8px;
    color: white;
    cursor: pointer;
}

/* BACK */
.back {
    display: inline-block;
    margin-top: 20px;
    color: #4da3ff;
    text-decoration: none;
}
</style>

</head>

<body>

<div class="container">

    <div class="top">
        <div class="avatar">
<?php if (!empty($_SESSION['profile_pic'])): ?>
    <img src="<?php echo $_SESSION['profile_pic']; ?>" style="width:100%; height:100%; border-radius:50%;">
<?php else: ?>
    <?php echo strtoupper(substr($_SESSION['username'],0,1)); ?>
<?php endif; ?>
</div>
    </div> 

    <!-- upload form -->
    <form method="POST" enctype="multipart/form-data">
    <input type="file" name="profile_pic" required>
    <button name="upload">Upload Picture</button>
</form>

    <!-- EDIT FORM -->
    <form method="POST">
        <label>Change Username</label>
        <input type="text" name="username" value="<?php echo $_SESSION['username']; ?>">

        <button name="update">Update Profile</button>
    </form>

    <a href="dashboard.php" class="back">← Back to Dashboard</a>

</div>

</body>
</html>