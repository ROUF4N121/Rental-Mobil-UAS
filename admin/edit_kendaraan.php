<?php
session_start();
include '../config/koneksi.php';

// Cek Admin
if ($_SESSION['role'] != 'admin') {
    header("Location: ../login.php");
    exit;
}

// Ambil ID dari URL
$id = $_GET['id'];
$query = mysqli_query($koneksi, "SELECT * FROM kendaraan WHERE id_kendaraan='$id'");
$data = mysqli_fetch_assoc($query);

// Proses Update Data
if (isset($_POST['update'])) {
    $merk = $_POST['merk'];
    $jenis = $_POST['jenis'];
    $nopol = $_POST['nopol'];
    $harga = $_POST['harga'];
    $status = $_POST['status'];
    
    // Logika Ganti Gambar
    $gambar_nama = $_FILES['gambar']['name'];
    
    if ($gambar_nama != "") {
        // Jika User Upload Gambar Baru
        $target = "../assets/img/" . basename($gambar_nama);
        move_uploaded_file($_FILES['gambar']['tmp_name'], $target);
        
        $query_update = "UPDATE kendaraan SET merk='$merk', jenis='$jenis', nopol='$nopol', harga='$harga', status='$status', gambar='$gambar_nama' WHERE id_kendaraan='$id'";
    } else {
        // Jika Tidak Ganti Gambar (Pakai gambar lama)
        $query_update = "UPDATE kendaraan SET merk='$merk', jenis='$jenis', nopol='$nopol', harga='$harga', status='$status' WHERE id_kendaraan='$id'";
    }

    mysqli_query($koneksi, $query_update);
    echo "<script>alert('Data Berhasil Diupdate!'); window.location='kendaraan.php';</script>";
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Edit Kendaraan</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <div class="container mt-5" style="max-width: 600px;">
        <div class="card shadow">
            <div class="card-header bg-warning">
                <h5 class="mb-0">Edit Data Kendaraan</h5>
            </div>
            <div class="card-body">
                <form method="POST" enctype="multipart/form-data">
                    <div class="mb-3">
                        <label>Merk Kendaraan</label>
                        <input type="text" name="merk" class="form-control" value="<?= $data['merk'] ?>" required>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label>Jenis</label>
                            <select name="jenis" class="form-select">
                                <option value="Minibus" <?= ($data['jenis'] == 'Minibus') ? 'selected' : '' ?>>Minibus</option>
                                <option value="Bus" <?= ($data['jenis'] == 'Bus') ? 'selected' : '' ?>>Bus</option>
                                <option value="Motor" <?= ($data['jenis'] == 'Motor') ? 'selected' : '' ?>>Motor</option>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label>Plat Nomor</label>
                            <input type="text" name="nopol" class="form-control" value="<?= $data['nopol'] ?>" required>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label>Harga Sewa / Hari</label>
                            <input type="number" name="harga" class="form-control" value="<?= $data['harga'] ?>" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label>Status</label>
                            <select name="status" class="form-select">
                                <option value="Tersedia" <?= ($data['status'] == 'Tersedia') ? 'selected' : '' ?>>Tersedia</option>
                                <option value="Disewa" <?= ($data['status'] == 'Disewa') ? 'selected' : '' ?>>Disewa</option>
                            </select>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label>Ganti Gambar (Opsional)</label><br>
                        <img src="../assets/img/<?= $data['gambar'] ?>" width="100" class="mb-2 border rounded">
                        <input type="file" name="gambar" class="form-control">
                        <small class="text-muted">Biarkan kosong jika tidak ingin mengganti gambar.</small>
                    </div>

                    <div class="d-flex justify-content-between">
                        <a href="kendaraan.php" class="btn btn-secondary">Batal</a>
                        <button type="submit" name="update" class="btn btn-primary">Simpan Perubahan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</body>
</html>