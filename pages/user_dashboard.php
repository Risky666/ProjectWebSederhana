<?php
session_start(); // Memulai sesi .
include '../includes/db.php'; //  koneksi database.

if ($_SESSION['role'] != 'user') { // Mengecek apakah role pengguna bukan 'user'.
    header('Location: login.php'); // Jika bukan user maka akan diarahkan ke halaman login.
    exit; // Menghentikan script .
}

$user_id = $_SESSION['user_id']; // Mendapatkan user ID dari sesi.



// Tambahkan pengumuman
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_announcement'])) { // Mengecek jika form tambah pengumuman disubmit.
    $title = $_POST['title']; // Mengambil input judul.
    $content = $_POST['content']; // Mengambil input isi pengumuman.
    $category = $_POST['category']; // Mengambil input kategori.
    $file_path = null; // Inisialisasi file path sebagai null.

    if (isset($_FILES['file']) && $_FILES['file']['error'] == 0) { // Mengecek jika file diunggah tanpa error.
        $file_name = basename($_FILES['file']['name']); // Mendapatkan nama file.
        $file_tmp = $_FILES['file']['tmp_name']; // Mendapatkan path sementara file.
        $file_path = '../announcements/' . $file_name; // Menentukan path tujuan yaitu announcements untuk menyimpan file yang di upload.

        // Simpan file ke folder tujuan.
        move_uploaded_file($file_tmp, $file_path); // Memindahkan file yang diunggah ke path tujuan.
    }

    // Gunakan Prepared Statement untuk menambahkan pengumuman ke database.
    $stmt = $conn->prepare("INSERT INTO announcements (user_id, title, content, category, file_path) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("issss", $user_id, $title, $content, $category, $file_path); // Mengikat parameter.

    if ($stmt->execute()) { // Mengeksekusi query.
        echo "<script>alert('Pengumuman berhasil ditambahkan!');</script>"; // jika pengumuman berhasil di tambah maka akan Menampilkan pesan sukses.
    } else { // Jika terjadi error.
        echo "<script>alert('Gagal menambahkan pengumuman: " . $stmt->error . "');</script>"; // jika gagal Menampilkan pesan error.
    }

    $stmt->close(); // Menutup statement.
}



// Hapus pengumuman
if (isset($_GET['delete'])) { 
    $announcement_id = $_GET['delete']; // Mendapatkan ID pengumuman dari parameter 'delete'.
    $stmt = $conn->prepare("DELETE FROM announcements WHERE id = ? AND user_id = ?"); // Query untuk menghapus pengumuman.
    $stmt->bind_param("ii", $announcement_id, $user_id); // Mengikat parameter.

    if ($stmt->execute()) { // Mengeksekusi query.
        echo "<script>alert('Pengumuman berhasil dihapus!');</script>"; // Menampilkan pesan sukses.
    } else { // Jika terjadi error.
        echo "<script>alert('Gagal menghapus pengumuman: " . $stmt->error . "');</script>"; // Menampilkan pesan error.
    }

    $stmt->close(); // Menutup statement.
    header('Location: user_dashboard.php'); // Redirect ke dashboard.
    exit; // Menghentikan eksekusi script.
}

// Edit pengumuman
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['edit_announcement'])) { // Mengecek jika form edit pengumuman disubmit.
    $announcement_id = $_POST['announcement_id']; // Mendapatkan ID pengumuman yang ingin diubah.
    $title = $_POST['title']; // Mengambil input judul baru.
    $content = $_POST['content']; // Mengambil input isi pengumuman baru.

    // Query untuk memperbarui pengumuman berdasarkan ID dan user ID.
    $stmt = $conn->prepare("UPDATE announcements SET title = ?, content = ? WHERE id = ? AND user_id = ?");
    $stmt->bind_param("ssii", $title, $content, $announcement_id, $user_id); // Mengikat parameter.

    if ($stmt->execute()) { // Mengeksekusi query.
        echo "<script>alert('Pengumuman berhasil diupdate!');</script>"; // Menampilkan pesan sukses.
    } else { // Jika terjadi error.
        echo "<script>alert('Gagal mengupdate pengumuman: " . $stmt->error . "');</script>"; // Menampilkan pesan error.
    }

    $stmt->close(); // Menutup statement.
    header('Location: user_dashboard.php'); // Redirect ke dashboard.
    exit; // Menghentikan eksekusi script.
}



// Ambil semua pengumuman pengguna
$stmt = $conn->prepare("SELECT * FROM announcements WHERE user_id = ? ORDER BY created_at DESC"); // Query untuk mengambil pengumuman berdasarkan user ID.
$stmt->bind_param("i", $user_id); // Mengikat parameter.
$stmt->execute(); // Mengeksekusi query.
$announcements = $stmt->get_result(); // Mendapatkan hasil query.
?>

<!DOCTYPE html>
<html>
<head>
    <link rel="stylesheet" href="../css/style.css"> <!-- CSS  -->
    <title>User Dashboard</title> 
</head>
<body>

<div class="container"> 
    <h2>User Dashboard</h2> 
    <p>Selamat datang, User!</p> 

    <!-- Tambah Pengumuman -->
    <h3>Tambah Pengumuman</h3>
    <form method="POST" enctype="multipart/form-data"> <!-- Form untuk menambahkan pengumuman,  upload file. -->

        <label>Judul</label> <!-- Label untuk input judul. -->

        <input type="text" name="title" required> <!-- Input untuk judul pengumuman. -->

        <label>Isi Pengumuman</label> <!-- Label untuk input isi pengumuman. -->

        <textarea name="content" rows="4"></textarea> <!-- Textarea untuk isi pengumuman. -->

        <label>Kategori</label> <!-- Label untuk input kategori. -->

        <select name="category" required> <!-- Dropdown untuk memilih kategori. -->
            <option value="Informasi">Informasi</option>
            <option value="Pengumuman">Pengumuman</option>
            <option value="Lainnya">Lainnya</option>
        </select>

        <label>Upload File</label> <!-- Label untuk input file. -->

        <input type="file" name="file"> <!-- Input untuk file yang akan diupload. -->

        <input type="submit" name="add_announcement" value="Tambah Pengumuman"> <!-- Tombol submit. -->
    </form>

    <!-- Daftar Pengumuman -->
    <h3>Daftar Pengumuman</h3>
    <?php if ($announcements->num_rows > 0): ?> <!-- Mengecek jika ada pengumuman. -->
        <ul>
            <?php while ($row = $announcements->fetch_assoc()): ?> <!-- Loop untuk setiap pengumuman. -->
                <li>
                    <h4><?= $row['title'] ?></h4> <!-- Menampilkan judul pengumuman. -->
                    <p><?= nl2br($row['content']) ?></p> <!-- Menampilkan isi pengumuman dengan line break. -->
                    <p>Kategori: <?= $row['category'] ?></p> <!-- Menampilkan kategori pengumuman. -->
                    <?php if (!empty($row['file_path'])): ?> <!-- Mengecek jika ada file terkait pengumuman. -->
                        <a href="<?= $row['file_path'] ?>" target="_blank">Download File</a> <!-- Link untuk mendownload file. -->
                    <?php endif; ?>
                    <br>
                    <!-- Tombol Edit -->
                    <button onclick="openEditForm(<?= $row['id'] ?>, '<?= $row['title'] ?>', '<?= $row['content'] ?>')">Edit</button>
                    <!-- Tombol Hapus -->
                    <a href="?delete=<?= $row['id'] ?>" onclick="return confirm('Yakin ingin menghapus pengumuman ini?')">Hapus</a>
                    <hr>
                </li>
            <?php endwhile; ?>
        </ul>
    <?php else: ?> <!-- Jika tidak ada pengumuman. -->
        <p>Belum ada pengumuman.</p>
    <?php endif; ?>

    
    <!-- Form Edit Pengumuman -->
    <div id="edit-form" style="display: none;"> <!-- Form untuk mengedit pengumuman. -->
        <h3>Edit Pengumuman</h3>
        <form method="POST">
            <input type="hidden" name="announcement_id" id="edit-announcement-id"> <!-- Input tersembunyi untuk ID pengumuman. -->
            <label>Judul</label> <!-- Label untuk input judul. -->
            <input type="text" name="title" id="edit-title" required> <!-- Input untuk judul baru. -->
            <label>Isi Pengumuman</label> <!-- Label untuk input isi pengumuman baru. -->
            <textarea name="content" id="edit-content" rows="4"></textarea> <!-- Textarea untuk isi pengumuman baru. -->
            <input type="submit" name="edit_announcement" value="Simpan Perubahan"> <!-- Tombol submit untuk menyimpan perubahan. -->
            <button type="button" onclick="closeEditForm()">Batal</button> <!-- Tombol untuk membatalkan edit. -->
        </form>
    </div>

    <script>
        function openEditForm(id, title, content) { // Fungsi untuk membuka form edit dengan data pengumuman.
            document.getElementById('edit-form').style.display = 'block';
            document.getElementById('edit-announcement-id').value = id;
            document.getElementById('edit-title').value = title;
            document.getElementById('edit-content').value = content;
        }

        function closeEditForm() { // Fungsi untuk menutup form edit.
            document.getElementById('edit-form').style.display = 'none';
        }
    </script>

    <br>
    <a href="change_password.php">Ganti Password</a> <!-- Link ke halaman ganti password. -->
    <br><br>
    <a href="../logout.php">Logout</a> <!-- Link untuk logout. -->
</div>
</body>
</html>
