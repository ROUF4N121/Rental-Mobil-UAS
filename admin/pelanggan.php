<?php
session_start();
include '../config/koneksi.php';

if ($_SESSION['role'] != 'admin') {
    header("Location: ../login.php");
    exit;
}

// Hapus Pelanggan
if (isset($_GET['hapus'])) {
    $id = $_GET['hapus'];
    mysqli_query($koneksi, "DELETE FROM users WHERE id_user='$id'");
    header("Location: pelanggan.php");
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Data Pelanggan</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="../assets/css/style.css" rel="stylesheet">
</head>
<body>
    <div class="d-flex">
        <div class="sidebar p-3" style="width: 250px; min-height: 100vh;">
            <h4>Rental Admin</h4>
            <hr>
            <a href="index.php">Dashboard & Laporan</a>
            <a href="kendaraan.php">Master Kendaraan</a>
            <a href="transaksi.php">Transaksi & Denda</a>
            <a href="pelanggan.php" class="text-white bg-secondary rounded">Data Pelanggan</a>
            <a href="../logout.php" class="text-danger mt-5">Logout</a>
        </div>

        <div class="container-fluid p-4">
            <h3>Data Pelanggan (Penyewa)</h3>
            <p class="text-muted">Daftar akun user yang terdaftar di aplikasi.</p>
            <hr>

            <div class="card shadow-sm">
                <div class="card-body">
                    <table class="table table-hover table-striped">
                        <thead class="table-dark">
                            <tr>
                                <th>No</th>
                                <th>Nama Lengkap</th>
                                <th>Username</th>
                                <th>Role</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $no = 1;
                            // Kita hanya menampilkan role 'user' saja, admin jangan dihapus
                            $query = mysqli_query($koneksi, "SELECT * FROM users WHERE role='user'");
                            while ($row = mysqli_fetch_assoc($query)):
                            ?>
                            <tr>
                                <td><?= $no++ ?></td>
                                <td><?= $row['nama_lengkap'] ?></td>
                                <td><?= $row['username'] ?></td>
                                <td><span class="badge bg-info text-dark"><?= $row['role'] ?></span></td>
                                <td>
                                    <a href="pelanggan.php?hapus=<?= $row['id_user'] ?>" 
                                       class="btn btn-danger btn-sm"
                                       onclick="return confirm('Yakin ingin menghapus user ini? Semua riwayat transaksinya juga akan terhapus.')">
                                       Hapus User
                                    </a>
                                </td>
                            </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>

                    <?php if(mysqli_num_rows($query) == 0): ?>
                        <div class="alert alert-info text-center mt-3">Belum ada pelanggan terdaftar.</div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</body>
</html>