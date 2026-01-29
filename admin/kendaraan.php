<?php
session_start();
include '../config/koneksi.php';

// Cek Admin
if ($_SESSION['role'] != 'admin') {
    header("Location: ../login.php");
    exit;
}

// Proses Tambah
if (isset($_POST['simpan'])) {
    $merk = $_POST['merk'];
    $jenis = $_POST['jenis'];
    $nopol = $_POST['nopol'];
    $harga = $_POST['harga'];
    
    // Upload Gambar
    $gambar = $_FILES['gambar']['name'];
    if ($gambar != "") {
        $target = "../assets/img/" . basename($gambar);
        move_uploaded_file($_FILES['gambar']['tmp_name'], $target);
    } else {
        $gambar = 'default.jpg'; 
    }

    mysqli_query($koneksi, "INSERT INTO kendaraan (merk, jenis, nopol, harga, gambar) 
                            VALUES ('$merk', '$jenis', '$nopol', '$harga', '$gambar')");
    header("Location: kendaraan.php");
}

// Proses Hapus
if (isset($_GET['hapus'])) {
    $id = $_GET['hapus'];
    mysqli_query($koneksi, "DELETE FROM kendaraan WHERE id_kendaraan='$id'");
    header("Location: kendaraan.php");
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Kelola Kendaraan</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="../assets/css/style.css" rel="stylesheet">
</head>
<body>
    <div class="d-flex">
        <div class="sidebar p-3" style="width: 250px; min-height: 100vh;">
            <h4>Rental Admin</h4>
            <hr>
            <a href="index.php">Dashboard & Laporan</a>
            <a href="kendaraan.php" class="text-white bg-secondary rounded">Master Kendaraan</a>
            <a href="transaksi.php">Transaksi & Denda</a>
            <a href="pelanggan.php">Data Pelanggan</a>
            <a href="../logout.php" class="text-danger mt-5">Logout</a>
        </div>

        <div class="container-fluid p-4">
            <h3>Master Data Kendaraan</h3>
            <hr>

            <button class="btn btn-primary mb-3" type="button" data-bs-toggle="collapse" data-bs-target="#formTambah">
                + Tambah Kendaraan Baru
            </button>
            
            <div class="collapse mb-4" id="formTambah">
                <div class="card card-body shadow-sm">
                    <form method="POST" enctype="multipart/form-data" class="row g-3">
                        <div class="col-md-3">
                            <input type="text" name="merk" class="form-control" placeholder="Merk Mobil/Motor" required>
                        </div>
                        <div class="col-md-2">
                            <select name="jenis" class="form-select">
                                <option value="Minibus">Minibus</option>
                                <option value="Bus">Bus</option>
                                <option value="Motor">Motor</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <input type="text" name="nopol" class="form-control" placeholder="Plat Nomor" required>
                        </div>
                        <div class="col-md-2">
                            <input type="number" name="harga" class="form-control" placeholder="Harga/Hari" required>
                        </div>
                        <div class="col-md-3">
                            <input type="file" name="gambar" class="form-control">
                        </div>
                        <div class="col-12 text-end">
                            <button type="submit" name="simpan" class="btn btn-success">Simpan Data</button>
                        </div>
                    </form>
                </div>
            </div>

            <div class="card shadow-sm">
                <div class="card-body">
                    <table class="table table-striped table-hover align-middle">
                        <thead class="table-dark">
                            <tr>
                                <th>No</th>
                                <th>Gambar</th>
                                <th>Info Kendaraan</th>
                                <th>Harga Sewa</th>
                                <th>Status</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $no = 1;
                            $query = mysqli_query($koneksi, "SELECT * FROM kendaraan ORDER BY id_kendaraan DESC");
                            while ($row = mysqli_fetch_assoc($query)):
                            ?>
                            <tr>
                                <td><?= $no++ ?></td>
                                <td>
                                    <img src="../assets/img/<?= $row['gambar'] ?>" width="80" class="rounded border">
                                </td>
                                <td>
                                    <strong><?= $row['merk'] ?></strong> <br>
                                    <span class="badge bg-secondary"><?= $row['jenis'] ?></span>
                                    <small class="text-muted ms-2"><?= $row['nopol'] ?></small>
                                </td>
                                <td>Rp <?= number_format($row['harga']) ?> /hari</td>
                                <td>
                                    <?php if($row['status'] == 'Tersedia'): ?>
                                        <span class="badge bg-success">Tersedia</span>
                                    <?php else: ?>
                                        <span class="badge bg-danger">Disewa</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <a href="edit_kendaraan.php?id=<?= $row['id_kendaraan'] ?>" class="btn btn-warning btn-sm">Edit</a>
                                    
                                    <a href="kendaraan.php?hapus=<?= $row['id_kendaraan'] ?>" 
                                       class="btn btn-danger btn-sm" 
                                       onclick="return confirm('Yakin hapus kendaraan ini?')">Hapus</a>
                                </td>
                            </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>