<?php
session_start();
include '../config/koneksi.php';

if ($_SESSION['role'] != 'user') header("Location: ../login.php");
$id_user = $_SESSION['id_user'];
?>

<!DOCTYPE html>
<html>
<head>
    <title>Riwayat Sewa Saya</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <nav class="navbar navbar-dark bg-dark mb-4">
        <div class="container">
            <a class="navbar-brand" href="index.php">Rental App</a>
            <div class="d-flex">
                <a href="index.php" class="btn btn-outline-light btn-sm me-2">Katalog Mobil</a>
                <a href="sewa.php" class="btn btn-light btn-sm me-2">Riwayat Saya</a>
                <a href="../logout.php" class="btn btn-danger btn-sm">Logout</a>
            </div>
        </div>
    </nav>

    <div class="container">
        <div class="card shadow-sm">
            <div class="card-header bg-success text-white">
                <h5>Riwayat Penyewaan: <?= $_SESSION['nama'] ?></h5>
            </div>
            <div class="card-body">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Kendaraan</th>
                            <th>Tgl Sewa</th>
                            <th>Rencana Kembali</th>
                            <th>Tgl Kembali (Real)</th>
                            <th>Total Bayar</th>
                            <th>Denda</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $no = 1;
                        $query = mysqli_query($koneksi, "SELECT t.*, k.merk, k.nopol 
                                                         FROM transaksi t 
                                                         JOIN kendaraan k ON t.id_kendaraan = k.id_kendaraan 
                                                         WHERE t.id_user = '$id_user' 
                                                         ORDER BY t.id_transaksi DESC");
                        
                        while ($row = mysqli_fetch_assoc($query)):
                        ?>
                        <tr>
                            <td><?= $no++ ?></td>
                            <td><?= $row['merk'] ?> <br> <small class="text-muted"><?= $row['nopol'] ?></small></td>
                            <td><?= $row['tgl_sewa'] ?></td>
                            <td><?= $row['tgl_kembali_rencana'] ?></td>
                            <td>
                                <?= $row['tgl_kembali_real'] ? $row['tgl_kembali_real'] : '-' ?>
                            </td>
                            <td>Rp <?= number_format($row['total_bayar']) ?></td>
                            <td class="text-danger">Rp <?= number_format($row['denda']) ?></td>
                            <td>
                                <?php if($row['status'] == 'Berjalan'): ?>
                                    <span class="badge bg-warning text-dark">Sedang Disewa</span>
                                <?php else: ?>
                                    <span class="badge bg-success">Selesai</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
                
                <?php if(mysqli_num_rows($query) == 0): ?>
                    <div class="alert alert-info text-center">Belum ada riwayat penyewaan.</div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</body>
</html>