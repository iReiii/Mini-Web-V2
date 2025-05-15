<?php
include '../include/db.php';
session_start();
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

// CREATE
if (isset($_POST['add'])) {
    $merk = $_POST['merk'];
    $jenis = $_POST['jenis'];
    $no_sepatu = $_POST['no_sepatu'];
    $stok = $_POST['stok'];
    $id_pemasok = $_POST['id_pemasok'];

    $stmt = $conn->prepare("INSERT INTO barang (Merk_sepatu, Jenis_sepatu, No_sepatu, Stok, Id_pemasok) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("sssii", $merk, $jenis, $no_sepatu, $stok, $id_pemasok);
    $stmt->execute();
    header("Location: barang.php");
    exit();
}

// UPDATE
if (isset($_POST['edit'])) {
    $id = $_POST['id'];
    $merk = $_POST['merk'];
    $jenis = $_POST['jenis'];
    $no_sepatu = $_POST['no_sepatu'];
    $stok = $_POST['stok'];
    $id_pemasok = $_POST['id_pemasok'];

    $stmt = $conn->prepare("UPDATE barang SET Merk_sepatu=?, Jenis_sepatu=?, No_sepatu=?, Stok=?, Id_pemasok=? WHERE Id_sepatu=?");
    $stmt->bind_param("sssiii", $merk, $jenis, $no_sepatu, $stok, $id_pemasok, $id);
    $stmt->execute();
    header("Location: barang.php");
    exit();
}

// DELETE
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    $stmt = $conn->prepare("DELETE FROM barang WHERE Id_sepatu=?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    header("Location: barang.php");
    exit();
}

// READ dengan JOIN ke tabel Pemasok
$sql = "SELECT b.*, p.Nama FROM barang b 
        LEFT JOIN Pemasok p ON b.Id_pemasok = p.Id_pemasok";
$result = $conn->query($sql);

// Get pemasok data for dropdown
$sql_pemasok = "SELECT Id_pemasok, Nama FROM Pemasok";
$result_pemasok = $conn->query($sql_pemasok);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Barang - Shoes Store</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 p-10">
    <h1 class="text-2xl font-bold mb-4">Data Barang</h1>

    <!-- Tombol Tambah Barang -->
    <button onclick="openAddModal()" class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded mb-6">
        Tambah Barang
    </button>

    <!-- Table Barang -->
    <table class="w-full bg-white shadow-md rounded-lg overflow-hidden">
        <thead class="bg-blue-500 text-white">
            <tr>
                <th class="p-3">ID</th>
                <th class="p-3">Merk Sepatu</th>
                <th class="p-3">Jenis Sepatu</th>
                <th class="p-3">No Sepatu</th>
                <th class="p-3">Stok</th>
                <th class="p-3">Pemasok</th>
                <th class="p-3">Aksi</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = $result->fetch_assoc()): ?>
                    <tr class="border-b">
                        <td class="p-3 text-center"><?php echo $row['Id_sepatu']; ?></td>
                        <td class="p-3 text-center"><?php echo $row['Merk_sepatu']; ?></td>
                        <td class="p-3 text-center"><?php echo $row['Jenis_sepatu']; ?></td>
                        <td class="p-3 text-center"><?php echo $row['No_sepatu']; ?></td>
                        <td class="p-3 text-center"><?php echo $row['Stok']; ?></td>
                        <td class="p-3 text-center">
                            <?php echo $row['Id_pemasok'] . ' - ' . $row['Nama']; ?>
                        </td>
                        <td class="p-3 text-center space-x-2">
                            <button onclick="openEditModal(
                                <?php echo $row['Id_sepatu']; ?>, 
                                '<?php echo addslashes($row['Merk_sepatu']); ?>', 
                                '<?php echo addslashes($row['Jenis_sepatu']); ?>', 
                                '<?php echo addslashes($row['No_sepatu']); ?>', 
                                <?php echo $row['Stok']; ?>, 
                                <?php echo $row['Id_pemasok']; ?>
                            )" class="bg-yellow-400 hover:bg-yellow-500 text-white px-3 py-1 rounded">Edit</button>
                            <a href="?delete=<?php echo $row['Id_sepatu']; ?>" onclick="return confirm('Yakin ingin hapus?')" class="bg-red-500 hover:bg-red-700 text-white px-3 py-1 rounded">Hapus</a>
                        </td>
                    </tr>
            <?php endwhile; ?>
        </tbody>
    </table>

    <!-- Modal untuk Tambah dan Edit -->
    <div id="dataModal" style="display: none;" class="fixed inset-0 bg-gray-800 bg-opacity-50 flex justify-center items-center">
        <div class="bg-white p-6 rounded shadow-md w-96">
            <h2 id="modalTitle" class="text-xl font-semibold mb-4">Tambah Barang</h2>
            <form method="post" class="space-y-4">
                <input type="hidden" name="id" id="modal-id">
                <div>
                    <input type="text" name="merk" id="modal-merk" placeholder="Merk Sepatu" required class="w-full p-2 border rounded">
                </div>
                <div>
                    <input type="text" name="jenis" id="modal-jenis" placeholder="Jenis Sepatu" required class="w-full p-2 border rounded">
                </div>
                <div>
                    <input type="text" name="no_sepatu" id="modal-no_sepatu" placeholder="No Sepatu" required class="w-full p-2 border rounded">
                </div>
                <div>
                    <input type="number" name="stok" id="modal-stok" placeholder="Stok" required class="w-full p-2 border rounded">
                </div>
                <div>
                    <select name="id_pemasok" id="modal-id_pemasok" required class="w-full p-2 border rounded">
                        <option value="">Pilih Pemasok</option>
                        <?php 
                        // Reset pointer result pemasok
                        $result_pemasok->data_seek(0);
                        while ($pemasok = $result_pemasok->fetch_assoc()): ?>
                            <option value="<?php echo $pemasok['Id_pemasok']; ?>">
                                <?php echo $pemasok['Id_pemasok'] . ' - ' . $pemasok['Nama']; ?>
                            </option>
                        <?php endwhile; ?>
                    </select>
                </div>
                <div class="flex justify-end space-x-2">
                    <button type="button" onclick="closeModal()" class="bg-gray-400 hover:bg-gray-500 text-white px-4 py-2 rounded">Batal</button>
                    <button type="submit" name="modal-submit" id="modal-submit" class="bg-green-500 hover:bg-green-700 text-white px-4 py-2 rounded">Simpan</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function openAddModal() {
            document.getElementById('modalTitle').textContent = 'Tambah Barang';
            document.getElementById('modal-id').value = '';
            document.getElementById('modal-merk').value = '';
            document.getElementById('modal-jenis').value = '';
            document.getElementById('modal-no_sepatu').value = '';
            document.getElementById('modal-stok').value = '';
            document.getElementById('modal-id_pemasok').value = '';
            document.getElementById('modal-submit').name = 'add';
            document.getElementById('modal-submit').textContent = 'Tambah';
            document.getElementById('dataModal').style.display = 'flex';
        }

        function openEditModal(id, merk, jenis, no_sepatu, stok, id_pemasok) {
            document.getElementById('modalTitle').textContent = 'Edit Barang';
            document.getElementById('modal-id').value = id;
            document.getElementById('modal-merk').value = merk;
            document.getElementById('modal-jenis').value = jenis;
            document.getElementById('modal-no_sepatu').value = no_sepatu;
            document.getElementById('modal-stok').value = stok;
            document.getElementById('modal-id_pemasok').value = id_pemasok;
            document.getElementById('modal-submit').name = 'edit';
            document.getElementById('modal-submit').textContent = 'Simpan';
            document.getElementById('dataModal').style.display = 'flex';
        }

        function closeModal() {
            document.getElementById('dataModal').style.display = 'none';
        }

        // Tutup modal ketika klik di luar area modal
        window.onclick = function(event) {
            const modal = document.getElementById('dataModal');
            if (event.target == modal) {
                closeModal();
            }
        }
    </script>
</body>
</html>