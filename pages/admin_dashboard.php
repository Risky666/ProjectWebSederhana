<?php
session_start();
include '../includes/db.php';

if ($_SESSION['role'] != 'admin') {
    header('Location: login.php');
    exit;
}

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

if (isset($_GET['delete_user'])) {
    $user_id = $_GET['delete_user'];
    $sql = "DELETE FROM users WHERE id = $user_id";
    if ($conn->query($sql) === TRUE) {
        $success = "User berhasil dihapus.";
    } else {
        $error = "Terjadi kesalahan: " . $conn->error;
    }
}

$sql = "SELECT * FROM users";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Admin Dashboard</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;500;600&display=swap" rel="stylesheet">

    <style>
    body {
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        margin: 0;
        padding: 0;
        background-color: #121212;
        color: #f5f5f5;
    }

    .container {
        width: 80%;
        max-width: 900px;
        margin: 50px auto;
        padding: 30px;
        background-color: #1e1e1e;
        border-radius: 10px;
        box-shadow: 0 0 15px rgba(255, 0, 0, 0.2);
    }

    h2, h3 {
        text-align: center;
        color: #ff3b3f;
    }

    table {
        width: 100%;
        border-collapse: collapse;
        margin-bottom: 30px;
    }

    table th, table td {
        border: 1px solid #ff3b3f;
        padding: 10px;
        text-align: center;
    }

    table th {
        background-color: #2a2a2a;
    }

    table tr:nth-child(even) {
        background-color: #181818;
    }

    input[type="text"],
    input[type="password"],
    select {
        width: 100%;
        padding: 10px;
        margin: 5px 0 15px;
        border: 1px solid #444;
        border-radius: 5px;
        background-color: #2a2a2a;
        color: white;
    }

    input[type="submit"] {
        background-color: #ff3b3f;
        color: white;
        padding: 10px 20px;
        border: none;
        border-radius: 5px;
        cursor: pointer;
        font-weight: bold;
    }

    input[type="submit"]:hover {
        background-color: #ff1a1e;
    }

    a {
        color: #ff3b3f;
        text-decoration: none;
    }

    a:hover {
        text-decoration: underline;
    }

    .logo {
        text-align: center;
        margin-bottom: 30px;
    }

    .logo img {
        max-width: 150px;
        height: auto;
    }

    .message {
        text-align: center;
        margin-bottom: 20px;
        font-weight: bold;
    }

    .message.success {
        color: #00e676;
    }

    .message.error {
        color: #ff3b3f;
    }
</style>

</head>
<body>
<div class="logo">
    <img src="../assets/Logo.jpg" alt="Logo"> <!-- Ganti path sesuai lokasi logomu -->
</div>
<div class="container">
    <h2>Admin Dashboard</h2>
    <p>Selamat datang, <strong>Chief</strong>!</p>

    <?php if (!empty($success)) echo "<div class='success'>$success</div>"; ?>
    <?php if (!empty($error)) echo "<div class='error'>$error</div>"; ?>

    <h3>Daftar User</h3>
    <table>
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

        <input type="submit" name="add_user" value="Tambah User">
    </form>

    <br>
    <a href="change_password.php">Ganti Password</a> |
  <a href="../logout.php">Logout</a>
</div>
</body>
</html>
