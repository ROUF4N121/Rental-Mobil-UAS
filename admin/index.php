<?php
session_start();
if ($_SESSION['role'] != 'admin') header("Location: ../login.php");
?>
<!DOCTYPE html>
<html>
<head>
    <title>Admin Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="../assets/css/style.css" rel="stylesheet">
</head>
<body>
    <div class="d-flex">
        <div class="sidebar p-3" style="width: 250px;">
            <h4>Rental Admin</h4>
            <hr>
            <a href="index.php">Dashboard & Laporan</a>
            <a href="kendaraan.php">Master Kendaraan</a>
            <a href="transaksi.php">Transaksi & Denda</a>
            <a href="pelanggan.php">Data Pelanggan</a>
            <a href="../logout.php" class="text-danger mt-5">Logout</a>
        </div>

        <div class="container-fluid p-4">
            <h2>Selamat Datang, <?= $_SESSION['nama'] ?></h2>
            <hr>
            
            <div class="card shadow-sm mt-4">
                <div class="card-header bg-primary text-white">Laporan Kendaraan Sedang Disewa</div>
                <div class="card-body">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>Penyewa</th>
                                <th>Kendaraan</th>
                                <th>Tgl Kembali (Rencana)</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            include '../config/koneksi.php';
                            $query = mysqli_query($koneksi, "SELECT t.*, u.nama_lengkap, k.merk 
                                                             FROM transaksi t 
                                                             JOIN users u ON t.id_user = u.id_user 
                                                             JOIN kendaraan k ON t.id_kendaraan = k.id_kendaraan 
                                                             WHERE t.status = 'Berjalan'");
                            while($row = mysqli_fetch_assoc($query)):
                            ?>
                            <tr>
                                <td><?= $row['nama_lengkap'] ?></td>
                                <td><?= $row['merk'] ?></td>
                                <td><?= $row['tgl_kembali_rencana'] ?></td>
                                <td><span class="badge bg-warning text-dark"><?= $row['status'] ?></span></td>
                            </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</body>
</html>