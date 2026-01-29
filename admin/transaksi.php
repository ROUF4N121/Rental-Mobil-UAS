<?php
session_start();
include '../config/koneksi.php';

// Cek Admin
if ($_SESSION['role'] != 'admin') {
    header("Location: ../login.php");
    exit;
}

// --- LOGIKA PROSES PENGEMBALIAN ---
if (isset($_GET['selesai_id'])) {
    $id_transaksi = $_GET['selesai_id'];
    $id_mobil = $_GET['id_mobil'];
    
    // Ambil data transaksi untuk hitung denda
    $cek = mysqli_query($koneksi, "SELECT * FROM transaksi WHERE id_transaksi='$id_transaksi'");
    $data = mysqli_fetch_assoc($cek);
    
    $tgl_rencana = new DateTime($data['tgl_kembali_rencana']);
    $tgl_real = new DateTime(); // Waktu saat ini (hari pengembalian)
    $now = date('Y-m-d');

    // Hitung Denda (Jika telat)
    $denda = 0;
    if ($tgl_real > $tgl_rencana) {
        $selisih = $tgl_rencana->diff($tgl_real)->days;
        // Rumus: Telat 1 hari = Denda Rp 100.000
        $denda = $selisih * 100000;
    }

    // Update Transaksi (Isi tgl_kembali_real, denda, ubah status jadi Selesai)
    $query_update = "UPDATE transaksi SET tgl_kembali_real='$now', denda='$denda', status='Selesai' WHERE id_transaksi='$id_transaksi'";
    mysqli_query($koneksi, $query_update);
    
    // Update Stok Kendaraan (Ubah status jadi Tersedia lagi)
    mysqli_query($koneksi, "UPDATE kendaraan SET status='Tersedia' WHERE id_kendaraan='$id_mobil'");
    
    // Refresh halaman agar data terupdate
    header("Location: transaksi.php");
    exit;
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Manajemen Transaksi</title>
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
            <a href="transaksi.php" class="text-white bg-secondary rounded">Transaksi & Denda</a>
            <a href="pelanggan.php">Data Pelanggan</a>
            <a href="../logout.php" class="text-danger mt-5">Logout</a>
        </div>

        <div class="container-fluid p-4">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2>Manajemen Transaksi</h2>
                <span class="badge bg-info text-dark">Hari ini: <?= date('d-m-Y') ?></span>
            </div>
            
            <div class="card shadow-sm">
                <div class="card-header bg-dark text-white">
                    <h5 class="mb-0">Daftar Penyewaan & Pengembalian</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped table-hover align-middle">
                            <thead class="table-secondary">
                                <tr>
                                    <th>ID</th>
                                    <th>Penyewa</th>
                                    <th>Mobil</th>
                                    <th>Tgl Pinjam</th>
                                    <th>Rencana Kembali</th>
                                    <th>Tgl Kembali (Real)</th>
                                    <th>Denda</th>
                                    <th>Status</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                // Query gabungan (Join) untuk mengambil nama user dan merk mobil
                                $query = mysqli_query($koneksi, "SELECT t.*, u.nama_lengkap, k.merk, k.nopol, k.id_kendaraan as id_k 
                                                                 FROM transaksi t 
                                                                 JOIN users u ON t.id_user = u.id_user 
                                                                 JOIN kendaraan k ON t.id_kendaraan = k.id_kendaraan 
                                                                 ORDER BY t.status ASC, t.id_transaksi DESC");
                                
                                while($row = mysqli_fetch_assoc($query)):
                                ?>
                                <tr>
                                    <td>#<?= $row['id_transaksi'] ?></td>
                                    <td>
                                        <strong><?= $row['nama_lengkap'] ?></strong>
                                    </td>
                                    <td>
                                        <?= $row['merk'] ?> <br>
                                        <small class="text-muted"><?= $row['nopol'] ?></small>
                                    </td>
                                    <td><?= date('d/m/Y', strtotime($row['tgl_sewa'])) ?></td>
                                    <td><?= date('d/m/Y', strtotime($row['tgl_kembali_rencana'])) ?></td>
                                    <td>
                                        <?php if($row['tgl_kembali_real']): ?>
                                            <?= date('d/m/Y', strtotime($row['tgl_kembali_real'])) ?>
                                        <?php else: ?>
                                            -
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php if($row['denda'] > 0): ?>
                                            <span class="text-danger fw-bold">Rp <?= number_format($row['denda']) ?></span>
                                        <?php else: ?>
                                            Rp 0
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php if($row['status'] == 'Berjalan'): ?>
                                            <span class="badge bg-warning text-dark">Sedang Disewa</span>
                                        <?php else: ?>
                                            <span class="badge bg-success">Selesai</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php if($row['status'] == 'Berjalan'): ?>
                                            <a href="transaksi.php?selesai_id=<?= $row['id_transaksi'] ?>&id_mobil=<?= $row['id_k'] ?>" 
                                               class="btn btn-primary btn-sm"
                                               onclick="return confirm('Apakah mobil sudah dikembalikan? Denda akan dihitung otomatis jika telat.')">
                                               Terima Kembali
                                            </a>
                                        <?php else: ?>
                                            <button class="btn btn-secondary btn-sm" disabled>Selesai</button>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                                <?php endwhile; ?>