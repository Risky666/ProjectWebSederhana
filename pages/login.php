<?php
session_start(); // Memulai sesi untuk menyimpan data pengguna yang login.
include '../includes/db.php'; // Menghubungkan file PHP ini dengan file database untuk koneksi ke database.

if ($_SERVER['REQUEST_METHOD'] == 'POST') { // Mengecek apakah metode permintaan adalah POST (form telah disubmit).
    $username = $_POST['username']; // Mendapatkan nilai input username dari form.
    $password = $_POST['password']; // Mendapatkan nilai input password dari form.

    // Query SQL untuk mengambil pengguna berdasarkan username dan password.
    $sql = "SELECT * FROM users WHERE username = '$username' AND password = '$password'";
    $result = $conn->query($sql); // Menjalankan query ke database.

    if ($result->num_rows > 0) { // Mengecek apakah ada hasil dari query (user ditemukan).
        $user = $result->fetch_assoc(); // Mengambil data user sebagai array asosiatif.
        $_SESSION['user_id'] = $user['id']; // Menyimpan ID user dalam session.
        $_SESSION['role'] = $user['role']; // Menyimpan peran (role) user dalam session.

        if ($user['role'] == 'admin') { // Mengecek apakah user adalah admin.
            header('Location: admin_dashboard.php'); // Redirect ke halaman dashboard admin.
        } else { // Jika bukan admin.
            header('Location: user_dashboard.php'); // Redirect ke halaman dashboard user.
        }
    } else { // Jika username atau password tidak cocok.
        $error = "Username atau password salah."; // Menyimpan pesan kesalahan.
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <link rel="stylesheet" href="../css/style.css"> <!-- Css -->
    <title>Login</title> 
</head>
<body>
<div class="container"> <!-- Container utama untuk form login. -->
    <h2>Login</h2> 
    <?php if (!empty($error)) echo "<p style='color: red;'>$error</p>"; ?> <!-- Menampilkan pesan error jika ada eror -->
    <form method="POST"> 
        <label>Username</label> <!-- Label untuk input username. -->
        <input type="text" name="username" required> <!-- Input username, wajib diisi. -->
        <label>Password</label> <!-- Label untuk input password. -->
        <input type="password" name="password" required> <!-- Input password, wajib diisi. -->
        <input type="submit" value="Login"> <!-- Tombol submit untuk mengirim form. -->
    </form>
</div>
</body>
</html>
