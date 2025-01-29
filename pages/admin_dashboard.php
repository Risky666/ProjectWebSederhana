<?php
session_start(); // Memulai sesi
include '../includes/db.php'; // koneksi database

// Periksa apakah pengguna adalah admin
if ($_SESSION['role'] != 'admin') { // Jika role bukan admin
    header('Location: login.php'); // Arahkan ke halaman login
    exit; // Hentikan eksekusi
}

// Proses penambahan user
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_user'])) { // Periksa jika ada form untuk menambah user
    $username = $_POST['username']; // Ambil input username
    $password = $_POST['password']; // Ambil input password
    $role = $_POST['role']; // Ambil input role

    // Query SQL untuk menambah user ke database
    $sql = "INSERT INTO users (username, password, role) VALUES ('$username', '$password', '$role')";
    if ($conn->query($sql) === TRUE) { // Jika query berhasil
        $success = "User berhasil ditambahkan."; // tampilkan  pesan sukses
    } else { // Jika query gagal
        $error = "Terjadi kesalahan: " . $conn->error; // tampilkan pesan error
    }
}

// Proses penghapusan user
if (isset($_GET['delete_user'])) { // Periksa apakah ada parameter delete_user di URL
    $user_id = $_GET['delete_user']; // Ambil ID user yang akan dihapus
    $sql = "DELETE FROM users WHERE id = $user_id"; // Query untuk menghapus user berdasarkan ID
    if ($conn->query($sql) === TRUE) { // Jika query berhasil
        $success = "User berhasil dihapus."; // tampilkan pesan sukses
    } else { // Jika query gagal
        $error = "Terjadi kesalahan: " . $conn->error; // tampilkan pesan error
    }
}

// Ambil daftar semua user dari database
$sql = "SELECT * FROM users"; // Query untuk mengambil semua user
$result = $conn->query($sql); // Jalankan query
?>

<!DOCTYPE html>
<html>
<head>
    <link rel="stylesheet" href="../css/style.css"> 
    <title>Admin Dashboard</title> 
</head>
<body>
<div class="container"> <!-- Container utama -->
    <h2>Admin Dashboard</h2>
    <p>Selamat datang, Admin!</p> 

    <?php
    // Menampilkan pesan sukses atau error jika ada
    if (!empty($success)) echo "<p style='color: green;'>$success</p>";
    if (!empty($error)) echo "<p style='color: red;'>$error</p>";
    ?>

    <h3>Daftar User</h3> <!-- Header daftar user -->
    <table border="1" cellpadding="8" cellspacing="0" style="width: 100%;"> <!-- Tabel daftar user -->
        <thead>
            <tr>
                <th>ID</th> <!-- Kolom ID -->
                <th>Username</th> <!-- Kolom username -->
                <th>Role</th> <!-- Kolom role -->
                <th>Aksi</th> <!-- Kolom aksi -->
            </tr>
        </thead>
        <tbody>
            <?php while ($row = $result->fetch_assoc()) : ?> <!-- Looping data user dari database -->
            <tr>
                <td><?= $row['id'] ?></td> <!-- Menampilkan ID user -->
                <td><?= $row['username'] ?></td> <!-- Menampilkan username -->
                <td><?= $row['role'] ?></td> <!-- Menampilkan role -->
                <td>
                    <!-- Link untuk menghapus user -->
                    <a href="?delete_user=<?= $row['id'] ?>" onclick="return confirm('Yakin ingin menghapus user ini?')">Hapus</a>
                </td>
            </tr>
            <?php endwhile; ?> <!-- Akhir looping -->
        </tbody>
    </table>

    <h3>Tambah User</h3> <!-- Header form tambah user -->
    <form method="POST"> 
        <label>Username</label> <!-- Label input username -->
        <input type="text" name="username" required> <!-- Input username -->
        <label>Password</label> <!-- Label input password -->
        <input type="password" name="password" required> <!-- Input password -->
        <label>Role</label> <!-- Label input role -->
        <select name="role" required> <!-- Dropdown untuk memilih role -->
            <option value="user">User</option> <!-- Pilihan role user -->
            <option value="admin">Admin</option> <!-- Pilihan role admin -->
        </select>
        <br><br>
        <input type="submit" name="add_user" value="Tambah User"> <!-- Tombol submit -->
    </form>

    <br>
    <a href="change_password.php">Ganti Password</a> <!-- Link untuk ganti password -->
    <br><br>
    <a href="../logout.php">Logout</a> <!-- Link untuk logout -->
</div>
</body>
</html>
