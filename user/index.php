<?php
session_start();
include '../config/koneksi.php';

// Cek Login User
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'user') {
    header("Location: ../login.php");
    exit;
}

// Proses Booking Sederhana
if (isset($_POST['sewa'])) {
    $id_user = $_SESSION['id_user'];
    $id_k = $_POST['id_kendaraan'];
    $harga = $_POST['harga'];
    $hari = $_POST['hari'];
    
    $total = $harga * $hari;
    $tgl_sewa = date('Y-m-d');
    $tgl_kembali = date('Y-m-d', strtotime("+$hari days"));

    // Insert Transaksi
    mysqli_query($koneksi, "INSERT INTO transaksi (id_user, id_kendaraan, tgl_sewa, tgl_kembali_rencana, total_bayar) 
                            VALUES ('$id_user', '$id_k', '$tgl_sewa', '$tgl_kembali', '$total')");
    
    // Update Stok (Status Disewa)
    mysqli_query($koneksi, "UPDATE kendaraan SET status='Disewa' WHERE id_kendaraan='$id_k'");
    
    echo "<script>alert('Berhasil Sewa! Silakan cek menu Riwayat Saya.'); window.location='index.php';</script>";
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Sewa Kendaraan</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="../assets/css/style.css" rel="stylesheet">
    <style>
        /* CSS Tambahan agar gambar kartu seragam */
        .img-mobil {
            height: 200px;
            object-fit: cover;
            width: 100%;
        }
    </style>
</head>
<body>
    <nav class="navbar navbar-dark bg-dark sticky-top">
        <div class="container">
            <a class="navbar-brand" href="#">RopanRental App</a>
            <div class="d-flex align-items-center">
                <span class="navbar-text text-white me-3 d-none d-md-block">Halo, <?= $_SESSION['nama'] ?></span>
                <a href="sewa.php" class="btn btn-primary btn-sm me-2">Riwayat Saya</a>
                <a href="../logout.php" class="btn btn-outline-danger btn-sm">Logout</a>
            </div>
        </div>
    </nav>

    <div class="container mt-4 mb-5">
        <div class="text-center mb-5">
            <h3>Katalog Armada Kami</h3>
            <p class="text-muted">Pilih kendaraan terbaik untuk perjalanan Anda</p>
        </div>

        <div class="row">
            <?php
            $query = mysqli_query($koneksi, "SELECT * FROM kendaraan WHERE status='Tersedia' ORDER BY id_kendaraan DESC");
            while($row = mysqli_fetch_assoc($query)):
            ?>
            <div class="col-md-4 mb-4">
                <div class="card card-vehicle h-100 shadow-sm">
                    <img src="../assets/img/<?= $row['gambar'] ?>" class="card-img-top img-mobil" alt="Gambar Mobil">
                    
                    <div class="card-body d-flex flex-column">
                        <div class="d-flex justify-content-between align-items-start mb-2">
                            <h5 class="card-title fw-bold"><?= $row['merk'] ?></h5>
                            <span class="badge bg-secondary"><?= $row['jenis'] ?></span>
                        </div>
                        <p class="card-text text-muted small mb-1">No Polisi: <?= $row['nopol'] ?></p>
                        <h4 class="text-primary mb-3">Rp <?= number_format($row['harga']) ?> <span class="fs-6 text-muted">/hari</span></h4>
                        
                        <div class="mt-auto">
                            <form method="POST">
                                <input type="hidden" name="id_kendaraan" value="<?= $row['id_kendaraan'] ?>">
                                <input type="hidden" name="harga" value="<?= $row['harga'] ?>">
                                <div class="input-group mb-3">
                                    <span class="input-group-text">Durasi</span>
                                    <input type="number" name="hari" class="form-control" value="1" min="1" required>
                                    <span class="input-group-text">Hari</span>
                                </div>
                                <button type="submit" name="sewa" class="btn btn-success w-100" onclick="return confirm('Yakin ingin menyewa kendaraan ini?')">Sewa Sekarang</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
            <?php endwhile; ?>
        </div>
        
        <?php if(mysqli_num_rows($query) == 0): ?>
            <div class="alert alert-warning text-center">
                Mohon maaf, semua unit sedang disewa saat ini.
            </div>
        <?php endif; ?>
    </div>
</body>
</html>