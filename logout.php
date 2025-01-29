<?php
session_start(); // Memulai sesi .

session_destroy(); // Menghancurkan semua data sesi, termasuk data login.

header('Location: pages/login.php'); // Mengarahkan pengguna ke halaman login setelah sesi dihancurkan.

exit; // Menghentikan eksekusi kode.
?>
