<?php
session_start();
include '../includes/db.php';

// Periksa apakah pengguna adalah admin
if ($_SESSION['role'] != 'admin') {
    header('Location: login.php');
    exit;
}

// Proses penambahan user
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_user'])) {
    $username = $_POST['username'];
    $password = $_POST['password'];
    $role = $_POST['role'];

    $sql = "INSERT INTO users (username, password, role) VALUES ('$username', '$password', '$role')";
    if ($conn->query($sql) === TRUE) {
        $success = "User berhasil ditambahkan.";
    } else {
        $error = "Terjadi kesalahan: " . $conn->error;
    }
}

// Proses penghapusan user
if (isset($_GET['delete_user'])) {
    $user_id = $_GET['delete_user'];
    $sql = "DELETE FROM users WHERE id = $user_id";
    if ($conn->query($sql) === TRUE) {
        $success = "User berhasil dihapus.";
    } else {
        $error = "Terjadi kesalahan: " . $conn->error;
    }
}

// Ambil daftar semua user dari database
$sql = "SELECT * FROM users";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html>
<head>
    <link rel="stylesheet" href="../css/style.css">
    <title>Admin Dashboard</title>
</head>
<body>
<div class="container">
    <h2>Admin Dashboard</h2>
    <p>Selamat datang, Admin!</p>

    <?php
    if (!empty($success)) echo "<p style='color: green;'>$success</p>";
    if (!empty($error)) echo "<p style='color: red;'>$error</p>";
    ?>

    <h3>Daftar User</h3>
    <table border="1" cellpadding="8" cellspacing="0" style="width: 100%;">
        <thead>
            <tr>
                <th>ID</th>
                <th>Username</th>
                <th>Role</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = $result->fetch_assoc()) : ?>
            <tr>
                <td><?= $row['id'] ?></td>
                <td><?= $row['username'] ?></td>
                <td><?= $row['role'] ?></td>
                <td>
                    <a href="?delete_user=<?= $row['id'] ?>" onclick="return confirm('Yakin ingin menghapus user ini?')">Hapus</a>
                </td>
            </tr>
            <?php endwhile; ?>
        </tbody>
    </table>

    <h3>Tambah User</h3>
    <form method="POST">
        <label>Username</label>
        <input type="text" name="username" required>
        <label>Password</label>
        <input type="password" name="password" required>
        <label>Role</label>
        <select name="role" required>
            <option value="user">User</option>
            <option value="admin">Admin</option>
        </select>
        <br><br>
        <input type="submit" name="add_user" value="Tambah User">
    </form>

    <br>
    <a href="change_password.php">Ganti Password</a>
    <br><br>
    <a href="../logout.php">Logout</a>
</div>
</body>
</html>
