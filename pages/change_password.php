<?php
session_start();
include '../includes/db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $new_password = $_POST['new_password'];
    $user_id = $_SESSION['user_id'];

    $sql = "UPDATE users SET password = '$new_password' WHERE id = $user_id";
    if ($conn->query($sql) === TRUE) {
        $success = "Password berhasil diganti.";
    } else {
        $error = "Terjadi kesalahan.";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <link rel="stylesheet" href="../css/style.css">
    <title>Ganti Password</title>
</head>
<body>
<div class="container">
    <h2>Ganti Password</h2>
    <?php
    if (!empty($success)) echo "<p style='color: green;'>$success</p>";
    if (!empty($error)) echo "<p style='color: red;'>$error</p>";
    ?>
    <form method="POST">
        <label>Password Baru</label>
        <input type="password" name="new_password" required>
        <input type="submit" value="Ganti Password">
    </form>
</div>
</body>
</html>
