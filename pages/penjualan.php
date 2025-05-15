<?php
include '../include/db.php';
session_start();
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

// Aktifkan error reporting untuk membantu debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// CREATE
if (isset($_POST['add'])) {
    $no_nota = $_POST['no_nota']; // Mendapatkan No Nota yang dikirimkan dari modal
    $id_pelanggan = $_POST['id_pelanggan'];
    $tgl_nota = date('Y-m-d'); // Tanggal otomatis hari ini

    $stmt = $conn->prepare("INSERT INTO penjualan (No_nota, Id_pelanggan, Tgl_nota) VALUES (?, ?, ?)");
    $stmt->bind_param("sss", $no_nota, $id_pelanggan, $tgl_nota);
    $stmt->execute();
    header("Location: penjualan.php");
    exit();
}

// UPDATE
if (isset($_POST['edit'])) {
    $no_nota = $_POST['no_nota'];
    $id_pelanggan = $_POST['id_pelanggan'];

    $stmt = $conn->prepare("UPDATE penjualan SET Id_pelanggan=? WHERE No_nota=?");
    $stmt->bind_param("ss", $id_pelanggan, $no_nota);
    $stmt->execute();
    header("Location: penjualan.php");
    exit();
}

// DELETE
if (isset($_GET['delete'])) {
    $no_nota = $_GET['delete'];
    $stmt = $conn->prepare("DELETE FROM penjualan WHERE No_nota=?");
    $stmt->bind_param("s", $no_nota);
    $stmt->execute();
    header("Location: penjualan.php");
    exit();
}

// READ
$sql = "SELECT p.*, pl.Nama as NamaPelanggan FROM penjualan p 
        LEFT JOIN pelanggan pl ON p.Id_pelanggan = pl.Id_pelanggan";
$result = $conn->query($sql);

// Get data pelanggan untuk dropdown
$sql_pelanggan = "SELECT Id_pelanggan, Nama FROM pelanggan";
$result_pelanggan = $conn->query($sql_pelanggan);

// Mendapatkan Auto Increment terakhir dari tabel penjualan untuk No Nota
$sql_last_no_nota = "SELECT MAX(No_nota) AS last_no_nota FROM penjualan";
$result_last_no_nota = $conn->query($sql_last_no_nota);
$last_no_nota = $result_last_no_nota->fetch_assoc()['last_no_nota'];
$next_no_nota = $last_no_nota + 1; // Increment untuk No Nota berikutnya
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Penjualan - Shoes Store</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 p-10">
    <h1 class="text-2xl font-bold mb-4">Data Penjualan</h1>

    <!-- Tombol Tambah Penjualan -->
    <button onclick="openAddModal()" class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded mb-6">
        Tambah Penjualan
    </button>

    <!-- Table Penjualan -->
    <table class="w-full bg-white shadow-md rounded-lg overflow-hidden">
        <thead class="bg-blue-500 text-white">
            <tr>
                <th class="p-3">No Nota</th>
                <th class="p-3">Pelanggan</th>
                <th class="p-3">Tanggal Nota</th>
                <th class="p-3">Aksi</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = $result->fetch_assoc()): ?>
            <tr class="border-b">
                <td class="p-3 text-center"><?php echo $row['No_nota']; ?></td>
                <td class="p-3 text-center">
                    <?php echo $row['NamaPelanggan'] ?? 'Tidak Ada Data'; ?>
                </td>
                <td class="p-3 text-center"><?php echo $row['Tgl_nota']; ?></td>
                <td class="p-3 text-center space-x-2">
                    <button onclick="openEditModal(
                        '<?php echo $row['No_nota']; ?>', 
                        '<?php echo $row['Id_pelanggan']; ?>'
                    )" class="bg-yellow-400 hover:bg-yellow-500 text-white px-3 py-1 rounded">Edit</button>
                    <a href="?delete=<?php echo $row['No_nota']; ?>" onclick="return confirm('Yakin ingin hapus?')" class="bg-red-500 hover:bg-red-700 text-white px-3 py-1 rounded">Hapus</a>
                    <a href="det_penjualan.php?no_nota=<?php echo $row['No_nota']; ?>" 
                       class="bg-green-500 hover:bg-green-700 text-white px-3 py-1 rounded">Detail</a>
                </td>
            </tr>
            <?php endwhile; ?>
        </tbody>
    </table>

    <!-- Modal untuk Tambah dan Edit -->
    <div id="dataModal" style="display: none;" class="fixed inset-0 bg-gray-800 bg-opacity-50 flex justify-center items-center">
        <div class="bg-white p-6 rounded shadow-md w-96">
            <h2 id="modalTitle" class="text-xl font-semibold mb-4">Tambah Penjualan</h2>
            <form method="post" class="space-y-4">
                <input type="hidden" name="no_nota" id="modal-no_nota">
                <div>
                    <label class="block text-gray-700">No Nota</label>
                    <input type="text" id="display-no_nota" readonly class="w-full p-2 border rounded bg-gray-100">
                </div>
                <div>
                    <label class="block text-gray-700">Pelanggan</label>
                    <select name="id_pelanggan" id="modal-id_pelanggan" required class="w-full p-2 border rounded">
                        <option value="">Pilih Pelanggan</option>
                        <?php while ($pelanggan = $result_pelanggan->fetch_assoc()): ?>
                            <option value="<?php echo $pelanggan['Id_pelanggan']; ?>">
                                <?php echo $pelanggan['Id_pelanggan'] . ' - ' . $pelanggan['Nama']; ?>
                            </option>
                        <?php endwhile; ?>
                    </select>
                </div>
                <div>
                    <label class="block text-gray-700">Tanggal Nota</label>
                    <input type="text" id="display-tgl_nota" readonly class="w-full p-2 border rounded bg-gray-100" value="<?php echo date('Y-m-d'); ?>">
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
            document.getElementById('modalTitle').textContent = 'Tambah Penjualan';
            
            // Set No Nota ke Auto Increment (ambil dari PHP)
            document.getElementById('modal-no_nota').value = '<?php echo $next_no_nota; ?>';
            document.getElementById('display-no_nota').value = '<?php echo $next_no_nota; ?>';

            document.getElementById('modal-id_pelanggan').value = '';
            document.getElementById('modal-submit').name = 'add';
            document.getElementById('modal-submit').textContent = 'Tambah';
            document.getElementById('dataModal').style.display = 'flex';
        }

        function openEditModal(no_nota, id_pelanggan) {
            document.getElementById('modalTitle').textContent = 'Edit Penjualan';
            document.getElementById('modal-no_nota').value = no_nota;
            document.getElementById('display-no_nota').value = no_nota;
            document.getElementById('modal-id_pelanggan').value = id_pelanggan;
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
