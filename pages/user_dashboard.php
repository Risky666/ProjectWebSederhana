<?php
session_start();
include '../includes/db.php';

if ($_SESSION['role'] != 'user') {
    header('Location: login.php');
    exit;
}

$user_id = $_SESSION['user_id'];

// Tambah pengumuman
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_announcement'])) {
    $title = $_POST['title'];
    $content = $_POST['content'];
    $category = $_POST['category'];
    $file_path = null;
    $image_path = null;

    // Upload file
    if (isset($_FILES['file']) && $_FILES['file']['error'] == 0) {
        $file_name = basename($_FILES['file']['name']);
        $file_tmp = $_FILES['file']['tmp_name'];
        $file_path = '../announcements/' . $file_name;
        move_uploaded_file($file_tmp, $file_path);
    }

    // Upload gambar
    if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
        $img_name = basename($_FILES['image']['name']);
        $img_tmp = $_FILES['image']['tmp_name'];
        $image_path = '../announcements/' . $img_name;
        move_uploaded_file($img_tmp, $image_path);
    }

    $stmt = $conn->prepare("INSERT INTO announcements (user_id, title, content, category, file_path, image_path) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("isssss", $user_id, $title, $content, $category, $file_path, $image_path);

    if ($stmt->execute()) {
        echo "<script>alert('Pengumuman berhasil ditambahkan!');</script>";
    } else {
        echo "<script>alert('Gagal menambahkan pengumuman: " . $stmt->error . "');</script>";
    }

    $stmt->close();
}

// Hapus pengumuman
if (isset($_GET['delete'])) {
    $announcement_id = $_GET['delete'];
    $stmt = $conn->prepare("DELETE FROM announcements WHERE id = ? AND user_id = ?");
    $stmt->bind_param("ii", $announcement_id, $user_id);
    $stmt->execute();
    $stmt->close();
    header('Location: user_dashboard.php');
    exit;
}

// Edit pengumuman
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['edit_announcement'])) {
    $announcement_id = $_POST['announcement_id'];
    $title = $_POST['title'];
    $content = $_POST['content'];

    $stmt = $conn->prepare("UPDATE announcements SET title = ?, content = ? WHERE id = ? AND user_id = ?");
    $stmt->bind_param("ssii", $title, $content, $announcement_id, $user_id);
    $stmt->execute();
    $stmt->close();
    header('Location: user_dashboard.php');
    exit;
}

// Ambil pengumuman
$stmt = $conn->prepare("SELECT * FROM announcements WHERE user_id = ? ORDER BY created_at DESC");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$announcements = $stmt->get_result();
?>

<!DOCTYPE html>
<html>
<head>
    <title>User Dashboard</title>
    <style>
        body {
            background: #121212;
            color: #f5f5f5;
            font-family: Arial, sans-serif;
        }
        .container {
            max-width: 800px;
            margin: 40px auto;
            padding: 30px;
            background-color: #1e1e1e;
            border-radius: 10px;
        }
        h2, h3 {
            color: #ff3b3f;
            text-align: center;
        }
        label {
            font-weight: bold;
        }
        input, select, textarea {
            width: 100%;
            padding: 8px;
            background-color: #2a2a2a;
            color: #fff;
            border: 1px solid #444;
            margin-bottom: 10px;
        }
        input[type="submit"], button {
            background-color: #ff3b3f;
            border: none;
            padding: 10px;
            cursor: pointer;
            color: white;
            font-weight: bold;
        }
        a {
            color: #ff3b3f;
        }
        li {
            background-color: #2a2a2a;
            margin-bottom: 20px;
            padding: 15px;
            border-radius: 8px;
        }
        img.preview {
            max-width: 100%;
            height: auto;
            margin-top: 10px;
            border-radius: 6px;
        }
    </style>
</head>
<body>
<div class="container">
    <h2>Dashboard</h2>
    <p>Selamat Datang di Forum Chief !!!! </p>
    <p>ini adalah forum sharing tentang CyberSecurity, tools Cyber, dan Attack!!! </p>
    <p>Selamat belajar chief!!!</p>

    <h3>Tambah Pengumuman</h3>
    <form method="POST" enctype="multipart/form-data">
        <label>Judul</label>
        <input type="text" name="title" required>

        <label>Isi Pengumuman</label>
        <textarea name="content" rows="4"></textarea>

        <label>Kategori</label>
        <select name="category">
            <option value="Informasi">Informasi</option>
            <option value="Attack!!!">Attack!!!</option>
            <option value="Tools">Tools</option>
        </select>

        <label>Upload File (Opsional)</label>
        <input type="file" name="file">

        <label>Upload Gambar</label>
        <input type="file" name="image" accept="image/*">

        <input type="submit" name="add_announcement" value="Tambah Pengumuman">
    </form>

    <h3>Daftar Pengumuman</h3>
    <ul>
        <?php while ($row = $announcements->fetch_assoc()): ?>
            <li>
                <h4><?= htmlspecialchars($row['title']) ?></h4>
                <p><?= nl2br(htmlspecialchars($row['content'])) ?></p>
                <p>Kategori: <?= $row['category'] ?></p>

                <?php if (!empty($row['image_path'])): ?>
                    <img src="<?= $row['image_path'] ?>" alt="Gambar Pengumuman" class="preview">
                <?php endif; ?>

                <?php if (!empty($row['file_path'])): ?>
                    <p><a href="<?= $row['file_path'] ?>" target="_blank">Download File</a></p>
                <?php endif; ?>

                <button onclick="openEditForm(<?= $row['id'] ?>, '<?= addslashes($row['title']) ?>', '<?= addslashes($row['content']) ?>')">Edit</button>
                <a href="?delete=<?= $row['id'] ?>" onclick="return confirm('Yakin ingin menghapus?')">Hapus</a>
            </li>
        <?php endwhile; ?>
    </ul>

    <div id="edit-form" style="display:none;">
        <h3>Edit Pengumuman</h3>
        <form method="POST">
            <input type="hidden" name="announcement_id" id="edit-id">
            <label>Judul</label>
            <input type="text" name="title" id="edit-title">
            <label>Isi</label>
            <textarea name="content" id="edit-content" rows="4"></textarea>
            <input type="submit" name="edit_announcement" value="Simpan">
            <button type="button" onclick="closeEditForm()">Batal</button>
        </form>
    </div>

    <br>
    <a href="change_password.php">Ganti Password</a> |
    <a href="../logout.php">Logout</a>
</div>

<script>
    function openEditForm(id, title, content) {
        document.getElementById("edit-id").value = id;
        document.getElementById("edit-title").value = title;
        document.getElementById("edit-content").value = content;
        document.getElementById("edit-form").style.display = "block";
        window.scrollTo(0, document.body.scrollHeight);
    }
    function closeEditForm() {
        document.getElementById("edit-form").style.display = "none";
    }
</script>
</body>
</html>
