<?php
include '../include/db.php';
session_start();
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

// CREATE
if (isset($_POST['add'])) {
    $nama = $_POST['nama'];
    $alamat = $_POST['alamat'];
    $no_telp = $_POST['no_telp'];

    $stmt = $conn->prepare("INSERT INTO pelanggan (Nama, Alamat, No_telp) VALUES (?, ?, ?)");
    $stmt->bind_param("sss", $nama, $alamat, $no_telp);
    $stmt->execute();
    header("Location: pelanggan.php");
    exit();
}

// UPDATE
if (isset($_POST['edit'])) {
    $id = $_POST['id'];
    $nama = $_POST['nama'];
    $alamat = $_POST['alamat'];
    $no_telp = $_POST['no_telp'];

    $stmt = $conn->prepare("UPDATE pelanggan SET Nama=?, Alamat=?, No_telp=? WHERE Id_pelanggan=?");
    $stmt->bind_param("sssi", $nama, $alamat, $no_telp, $id);
    $stmt->execute();
    header("Location: pelanggan.php");
    exit();
}

// DELETE
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    $stmt = $conn->prepare("DELETE FROM pelanggan WHERE Id_pelanggan=?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    header("Location: pelanggan.php");
    exit();
}

// READ
$sql = "SELECT * FROM pelanggan ORDER BY Id_pelanggan ASC";
$result = $conn->query($sql);

// Get last ID for display in add form
$last_id = 0;
$sql_last_id = "SELECT MAX(Id_pelanggan) as last_id FROM pelanggan";
$result_last_id = $conn->query($sql_last_id);
if ($result_last_id->num_rows > 0) {
    $row = $result_last_id->fetch_assoc();
    $last_id = $row['last_id'];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pelanggan - Shoes Store</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 p-10">
    <h1 class="text-2xl font-bold mb-4">Data Pelanggan</h1>

    <!-- Tombol Tambah Pelanggan -->
    <button onclick="openAddModal()" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded mb-6">
        Tambah Pelanggan
    </button>

    <!-- Table Pelanggan -->
    <table class="w-full bg-white shadow-md rounded-lg overflow-hidden">
        <thead class="bg-blue-500 text-white">
            <tr>
                <th class="p-3">ID Pelanggan</th>
                <th class="p-3">Nama</th>
                <th class="p-3">Alamat</th>
                <th class="p-3">No Telp</th>
                <th class="p-3">Aksi</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = $result->fetch_assoc()): ?>
            <tr class="border-b">
                <td class="p-3 text-center"><?php echo $row['Id_pelanggan']; ?></td>
                <td class="p-3 text-center"><?php echo $row['Nama']; ?></td>
                <td class="p-3 text-center"><?php echo $row['Alamat']; ?></td>
                <td class="p-3 text-center"><?php echo $row['No_telp']; ?></td>
                <td class="p-3 text-center space-x-2">
                    <button onclick="openEditModal(
                        <?php echo $row['Id_pelanggan']; ?>, 
                        '<?php echo addslashes($row['Nama']); ?>', 
                        '<?php echo addslashes($row['Alamat']); ?>', 
                        '<?php echo addslashes($row['No_telp']); ?>'
                    )" class="bg-yellow-400 hover:bg-yellow-500 text-white px-3 py-1 rounded">Edit</button>
                    <a href="?delete=<?php echo $row['Id_pelanggan']; ?>" onclick="return confirm('Yakin ingin hapus?')" class="bg-red-500 hover:bg-red-700 text-white px-3 py-1 rounded">Hapus</a>
                </td>
            </tr>
            <?php endwhile; ?>
        </tbody>
    </table>

    <!-- Modal untuk Tambah dan Edit -->
    <div id="dataModal" style="display: none;" class="fixed inset-0 bg-gray-800 bg-opacity-50 flex justify-center items-center">
        <div class="bg-white p-6 rounded shadow-md w-96">
            <h2 id="modalTitle" class="text-xl font-semibold mb-4">Tambah Pelanggan</h2>
            <form method="post" class="space-y-4">
                <input type="hidden" name="id" id="modal-id">
                <div>
                    <label class="block text-gray-700">ID Pelanggan</label>
                    <input type="text" id="display-id" readonly class="w-full p-2 border rounded bg-gray-100" value="AUTO">
                </div>
                <div>
                    <label class="block text-gray-700">Nama</label>
                    <input type="text" name="nama" id="modal-nama" placeholder="Nama Pelanggan" required class="w-full p-2 border rounded">
                </div>
                <div>
                    <label class="block text-gray-700">Alamat</label>
                    <input type="text" name="alamat" id="modal-alamat" placeholder="Alamat" required class="w-full p-2 border rounded">
                </div>
                <div>
                    <label class="block text-gray-700">No Telepon</label>
                    <input type="text" name="no_telp" id="modal-no_telp" placeholder="No Telepon" required class="w-full p-2 border rounded">
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
            document.getElementById('modalTitle').textContent = 'Tambah Pelanggan';
            document.getElementById('modal-id').value = '';
            document.getElementById('display-id').value = 'AUTO';
            document.getElementById('modal-nama').value = '';
            document.getElementById('modal-alamat').value = '';
            document.getElementById('modal-no_telp').value = '';
            document.getElementById('modal-submit').name = 'add';
            document.getElementById('modal-submit').textContent = 'Tambah';
            document.getElementById('dataModal').style.display = 'flex';
        }

        function openEditModal(id, nama, alamat, no_telp) {
            document.getElementById('modalTitle').textContent = 'Edit Pelanggan';
            document.getElementById('modal-id').value = id;
            document.getElementById('display-id').value = id;
            document.getElementById('modal-nama').value = nama;
            document.getElementById('modal-alamat').value = alamat;
            document.getElementById('modal-no_telp').value = no_telp;
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