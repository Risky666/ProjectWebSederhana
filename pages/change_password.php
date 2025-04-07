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
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Ganti Password</title>
    <style>
        body {
            margin: 0;
            padding: 0;
            background-color: #121212;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            color: #f5f5f5;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }

        .container {
            background-color: #1e1e1e;
            padding: 40px;
            border-radius: 12px;
            box-shadow: 0 0 20px rgba(255, 0, 0, 0.2);
            width: 100%;
            max-width: 400px;
        }

        h2 {
            color: #ff3b3f;
            margin-bottom: 20px;
            text-align: center;
        }

        label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
            color: #ccc;
        }

        input[type="password"] {
            width: 100%;
            padding: 12px;
            margin-bottom: 20px;
            border: none;
            border-radius: 8px;
            background-color: #2a2a2a;
            color: #f5f5f5;
            font-size: 16px;
        }

        input[type="submit"] {
            width: 100%;
            padding: 12px;
            background-color: #ff3b3f;
            color: white;
            border: none;
            border-radius: 8px;
            font-size: 16px;
            font-weight: bold;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        input[type="submit"]:hover {
            background-color: #e63237;
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
    <div class="container">
        <h2>Ganti Password</h2>
        <?php if (!empty($success)): ?>
            <p class="message success"><?= $success ?></p>
        <?php elseif (!empty($error)): ?>
            <p class="message error"><?= $error ?></p>
        <?php endif; ?>

        <form method="POST">
            <label for="new_password">Password Baru</label>
            <input type="password" name="new_password" id="new_password" required>
            <input type="submit" value="Ganti Password">
        </form>
    </div>
</body>
</html>
