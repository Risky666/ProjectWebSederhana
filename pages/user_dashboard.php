<?php
session_start();
if ($_SESSION['role'] != 'user') {
    header('Location: login.php');
    exit;
}
?>

<!DOCTYPE html>
<html>
<head>
    <link rel="stylesheet" href="../css/style.css">
    <title>User Dashboard</title>
</head>
<body>
<div class="container">
    <h2>User Dashboard</h2>
    <p>Selamat datang, User!</p>
    <a href="change_password.php">Ganti Password</a>
    <br><br>
    <a href="../logout.php">Logout</a>
</div>
</body>
</html>
