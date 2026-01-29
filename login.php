<?php
session_start();
include 'config/koneksi.php';

// Jika sudah login, langsung lempar ke halaman yang sesuai
if (isset($_SESSION['role'])) {
    if ($_SESSION['role'] == 'admin') header("Location: admin/index.php");
    else header("Location: user/index.php");
    exit;
}

if (isset($_POST['login'])) {
    $username = $_POST['username'];
    $password = $_POST['password'];

    $query = mysqli_query($koneksi, "SELECT * FROM users WHERE username='$username'");
    $data = mysqli_fetch_assoc($query);

    // Verifikasi password
    if ($data && password_verify($password, $data['password'])) {
        $_SESSION['id_user'] = $data['id_user'];
        $_SESSION['role'] = $data['role'];
        $_SESSION['nama'] = $data['nama_lengkap'];
        
        if ($data['role'] == 'admin') header("Location: admin/index.php");
        else header("Location: user/index.php");
    } else {
        echo "<script>alert('Login Gagal! Cek Username/Password.');</script>";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Login Rental</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #74b9ff, #a29bfe);
        }
    </style>
</head>
<body class="d-flex align-items-center justify-content-center" style="height: 100vh;">
    <div class="card p-4 shadow-lg border-0" style="width: 380px;">
        <h3 class="text-center mb-4 fw-bold text-primary">Login System</h3>
        
        <form method="POST">
            <div class="mb-3">
                <label class="form-label">Username</label>
                <input type="text" name="username" class="form-control" placeholder="Masukkan username" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Password</label>
                <input type="password" name="password" class="form-control" placeholder="Masukkan password" required>
            </div>
            <button type="submit" name="login" class="btn btn-primary w-100 py-2">Masuk Aplikasi</button>
        </form>

        <div class="alert alert-info mt-4 mb-0" style="font-size: 0.9rem;">
            <strong>ℹ️ Info Akun Demo:</strong><br>
            <table class="w-100 mt-1">
                <tr>
                    <td width="30%"><b>Admin:</b></td>
                    <td>admin / 123</td>
                </tr>
                <tr>
                    <td><b>User:</b></td>
                    <td>user1 / 123</td>
                </tr>
            </table>
        </div>
    </div>
</body>
</html>