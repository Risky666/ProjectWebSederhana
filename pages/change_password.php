<?php
session_start(); // Memulai sesi .
include '../includes/db.php'; //  koneksi database.

if ($_SERVER['REQUEST_METHOD'] == 'POST') { // Mengecek apakah metode permintaan adalah POST.
    $new_password = $_POST['new_password']; // Mengambil input password baru dari form.
    $user_id = $_SESSION['user_id']; // Mengambil user ID dari sesi untuk menentukan user yang ingin diubah password-nya.

    // Query SQL untuk memperbarui password user berdasarkan ID.
    $sql = "UPDATE users SET password = '$new_password' WHERE id = $user_id";
    if ($conn->query($sql) === TRUE) { // Mengeksekusi query dan mengecek jika berhasil.
        $success = "Password berhasil diganti."; // menampilkan Pesan ketika berhasil diganti .
    } else { // Jika terjadi kesalahan pada query.
        $error = "Terjadi kesalahan."; // tampilkan Pesan error.
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
<div class="container"> <!-- Container utama untuk form ganti password. -->
    <h2>Ganti Password</h2> <!-- Header halaman. -->
    <?php
    // Menampilkan pesan sukses atau error jika ada.
    if (!empty($success)) echo "<p style='color: green;'>$success</p>";
    if (!empty($error)) echo "<p style='color: red;'>$error</p>";
    ?>
    <form method="POST"> <!-- Form untuk ganti password, menggunakan metode POST. -->
        <label>Password Baru</label> <!-- Label untuk input password baru. -->
        <input type="password" name="new_password" required> <!-- Input untuk password baru, wajib diisi. -->
        <input type="submit" value="Ganti Password"> <!-- Tombol submit untuk mengirim form. -->
    </form>
</div>
</body>
</html>
