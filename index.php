<?php
/**
 * Aplikasi CRUD
 * Pemrograman Web II
 * Style: mysqli Object-Oriented (OOP) + Prepared Statement
 */

// KONEKSI DATABASE (mysqli OOP) 
$koneksi = new mysqli("localhost", "root", "", "akademik");

// Tampilkan pesan error jika koneksi gagal
if ($koneksi->connect_errno) {
    die("Koneksi database gagal : " . $koneksi->connect_error);
}

$pesan = "";

//  PROSES TAMBAH (CREATE) 
if (isset($_POST['simpan'])) {
    $nama   = $_POST['nama'];
    $nim    = $_POST['nim'];
    $alamat = $_POST['alamat'];

    // Prepared statement (keamanan dari SQL Injection)
    $stmt = $koneksi->prepare("INSERT INTO mahasiswa (nama, nim, alamat) VALUES (?, ?, ?)");
    $stmt->bind_param("sis", $nama, $nim, $alamat);
    if ($stmt->execute()) {
        $pesan = "Data berhasil ditambahkan!";
    } else {
        $pesan = "Gagal menyimpan data: " . $stmt->error;
    }
    $stmt->close();
}

//  PROSES UPDATE 
if (isset($_POST['update'])) {
    $id     = $_POST['id'];
    $nama   = $_POST['nama'];
    $nim    = $_POST['nim'];
    $alamat = $_POST['alamat'];

    $stmt = $koneksi->prepare("UPDATE mahasiswa SET nama=?, nim=?, alamat=? WHERE id=?");
    $stmt->bind_param("sisi", $nama, $nim, $alamat, $id);
    if ($stmt->execute()) {
        $pesan = "Data berhasil diperbarui!";
    } else {
        $pesan = "Gagal memperbarui data: " . $stmt->error;
    }
    $stmt->close();
}

//  PROSES HAPUS (DELETE) 
if (isset($_GET['hapus'])) {
    $id = $_GET['hapus'];

    $stmt = $koneksi->prepare("DELETE FROM mahasiswa WHERE id = ?");
    $stmt->bind_param("i", $id);
    if ($stmt->execute()) {
        $pesan = "Data berhasil dihapus!";
    } else {
        $pesan = "Gagal menghapus data: " . $stmt->error;
    }
    $stmt->close();
}

//  AMBIL DATA UNTUK EDIT 
$edit_data = null;
if (isset($_GET['edit'])) {
    $id = $_GET['edit'];
    $stmt = $koneksi->prepare("SELECT * FROM mahasiswa WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $edit_data = $stmt->get_result()->fetch_assoc();
    $stmt->close();
}

//  READ: AMBIL SEMUA DATA 
$data = $koneksi->query("SELECT * FROM mahasiswa");
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>CRUD PHP dan MySQLi - OOP Style</title>
</head>
<body>
    <h2>CRUD DATA MAHASISWA</h2>

    <?php if ($pesan): ?>
        <p style="color:green;"><b><?php echo $pesan; ?></b></p>
    <?php endif; ?>

    <!--  FORM TAMBAH / EDIT  -->
    <h3><?php echo $edit_data ? "EDIT DATA MAHASISWA" : "TAMBAH DATA MAHASISWA"; ?></h3>
    <form method="post" action="index.php">
        <?php if ($edit_data): ?>
            <input type="hidden" name="id" value="<?php echo $edit_data['id']; ?>">
        <?php endif; ?>
        <table>
            <tr>
                <td>Nama</td>
                <td><input type="text" name="nama" required
                    value="<?php echo $edit_data ? $edit_data['nama'] : ''; ?>"></td>
            </tr>
            <tr>
                <td>NIM</td>
                <td><input type="number" name="nim" required
                    value="<?php echo $edit_data ? $edit_data['nim'] : ''; ?>"></td>
            </tr>
            <tr>
                <td>Alamat</td>
                <td><input type="text" name="alamat" required
                    value="<?php echo $edit_data ? $edit_data['alamat'] : ''; ?>"></td>
            </tr>
            <tr>
                <td></td>
                <td>
                    <?php if ($edit_data): ?>
                        <input type="submit" name="update" value="UPDATE">
                        <a href="index.php">BATAL</a>
                    <?php else: ?>
                        <input type="submit" name="simpan" value="SIMPAN">
                    <?php endif; ?>
                </td>
            </tr>
        </table>
    </form>

    <br/>

    <!--  TABEL DATA  -->
    <table border="1" cellpadding="8">
        <tr>
            <th>NO</th>
            <th>Nama</th>
            <th>NIM</th>
            <th>Alamat</th>
            <th>OPSI</th>
        </tr>
        <?php
        if (!$data) {
            echo "<tr><td colspan='5'>Query gagal: " . $koneksi->error . "</td></tr>";
        } else {
            $no = 1;
            while ($d = $data->fetch_array()) {
        ?>
            <tr>
                <td><?php echo $no++; ?></td>
                <td><?php echo $d['nama']; ?></td>
                <td><?php echo $d['nim']; ?></td>
                <td><?php echo $d['alamat']; ?></td>
                <td>
                    <a href="index.php?edit=<?php echo $d['id']; ?>">EDIT</a> |
                    <a href="index.php?hapus=<?php echo $d['id']; ?>"
                       onclick="return confirm('Yakin hapus data ini?')">HAPUS</a>
                </td>
            </tr>
        <?php
            }
        }
        ?>
    </table>
</body>
</html>
<?php $koneksi->close(); ?>
