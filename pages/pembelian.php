<?php
include '../include/db.php';
session_start();
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

// Fungsi untuk ambil next No_nota
function getNextNoNota($conn) {
    $sql_last = "SELECT MAX(No_nota) AS last_nota FROM pembelian";
    $result_last = $conn->query($sql_last);
    $row_last = $result_last->fetch_assoc();
    return $row_last['last_nota'] + 1;
}

$next_nota = getNextNoNota($conn);

// CREATE
if (isset($_POST['add'])) {
    $no_nota = $_POST['no_nota'];
    $tanggal = date('Y-m-d'); 
    $id_pemasok = $_POST['id_pemasok'];

    $stmt = $conn->prepare("INSERT INTO pembelian (No_nota, Tanggal, Id_pemasok) VALUES (?, ?, ?)");
    $stmt->bind_param("sss", $no_nota, $tanggal, $id_pemasok);
    $stmt->execute();
    header("Location: pembelian.php");
    exit();
}

// UPDATE
if (isset($_POST['edit'])) {
    $no_nota = $_POST['no_nota'];
    $id_pemasok = $_POST['id_pemasok'];

    $stmt = $conn->prepare("UPDATE pembelian SET Id_pemasok=? WHERE No_nota=?");
    $stmt->bind_param("ss", $id_pemasok, $no_nota);
    $stmt->execute();
    header("Location: pembelian.php");
    exit();
}

// DELETE
if (isset($_GET['delete'])) {
    $no_nota = $_GET['delete'];

    $stmt = $conn->prepare("DELETE FROM det_pembelian WHERE No_nota=?");
    $stmt->bind_param("s", $no_nota);
    $stmt->execute();

    $stmt = $conn->prepare("DELETE FROM pembelian WHERE No_nota=?");
    $stmt->bind_param("s", $no_nota);
    $stmt->execute();

    header("Location: pembelian.php");
    exit();
}

// READ dengan JOIN ke tabel pemasok
$sql = "SELECT p.*, pm.Nama as NamaPemasok 
        FROM pembelian p
        LEFT JOIN pemasok pm ON p.Id_pemasok = pm.Id_pemasok";
$result = $conn->query($sql);

// Get data pemasok untuk dropdown
$sql_pemasok = "SELECT Id_pemasok, Nama FROM pemasok";
$result_pemasok = $conn->query($sql_pemasok);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pembelian - Shoes Store</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 p-10">
    <h1 class="text-2xl font-bold mb-4">Data Pembelian</h1>

    <!-- Tombol Tambah Pembelian -->
    <button onclick="openAddModal()" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded mb-6">
        Tambah Pembelian
    </button>

    <!-- Table Pembelian -->
    <table class="w-full bg-white shadow-md rounded-lg overflow-hidden">
        <thead class="bg-blue-500 text-white">
            <tr>
                <th class="p-3">No Nota</th>
                <th class="p-3">Tanggal</th>
                <th class="p-3">Pemasok</th>
                <th class="p-3">Aksi</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = $result->fetch_assoc()): ?>
            <tr class="border-b">
                <td class="p-3 text-center"><?php echo $row['No_nota']; ?></td>
                <td class="p-3 text-center"><?php echo $row['Tanggal']; ?></td>
                <td class="p-3 text-center">
                    <?php echo $row['Id_pemasok'] . ' - ' . ($row['NamaPemasok'] ?? 'Tidak Ada Data'); ?>
                </td>
                <td class="p-3 text-center space-x-2">
                    <button onclick="openEditModal(
                        '<?php echo $row['No_nota']; ?>',
                        '<?php echo $row['Id_pemasok']; ?>'
                    )" class="bg-yellow-400 hover:bg-yellow-500 text-white px-3 py-1 rounded">Edit</button>
                    <a href="?delete=<?php echo $row['No_nota']; ?>" 
                       onclick="return confirm('Yakin ingin hapus pembelian ini? Semua detail pembelian terkait juga akan dihapus.')" 
                       class="bg-red-500 hover:bg-red-700 text-white px-3 py-1 rounded">Hapus</a>
                    <a href="det_pembelian.php?no_nota=<?php echo $row['No_nota']; ?>" 
                       class="bg-green-500 hover:bg-green-700 text-white px-3 py-1 rounded">Detail</a>
                </td>
            </tr>
            <?php endwhile; ?>
        </tbody>
    </table>

    <!-- Modal untuk Tambah dan Edit -->
    <div id="dataModal" style="display: none;" class="fixed inset-0 bg-gray-800 bg-opacity-50 flex justify-center items-center">
        <div class="bg-white p-6 rounded shadow-md w-96">
            <h2 id="modalTitle" class="text-xl font-semibold mb-4">Tambah Pembelian</h2>
            <form method="post" class="space-y-4">
                <input type="hidden" name="no_nota" id="modal-no_nota" value="<?php echo $next_nota; ?>">

                <div>
                    <label class="block text-gray-700">No Nota</label>
                    <input type="text" id="display-no_nota" readonly class="w-full p-2 border rounded bg-gray-100" value="<?php echo $next_nota; ?>">
                </div>

                <div>
                    <label class="block text-gray-700">Tanggal</label>
                    <input type="text" id="display-tanggal" readonly class="w-full p-2 border rounded bg-gray-100" value="<?php echo date('Y-m-d'); ?>">
                </div>

                <div>
                    <label class="block text-gray-700">Pemasok</label>
                    <select name="id_pemasok" id="modal-id_pemasok" required class="w-full p-2 border rounded">
                        <option value="">Pilih Pemasok</option>
                        <?php 
                        $result_pemasok->data_seek(0); // Reset pointer hasil query
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
            document.getElementById('modalTitle').textContent = 'Tambah Pembelian';
            document.getElementById('modal-submit').name = 'add';
            document.getElementById('modal-submit').textContent = 'Tambah';

            document.getElementById('modal-id_pemasok').value = '';

            const nextNota = '<?php echo $next_nota; ?>';
            document.getElementById('display-no_nota').value = nextNota;
            document.getElementById('modal-no_nota').value = nextNota;

            document.getElementById('dataModal').style.display = 'flex';
        }

        function openEditModal(no_nota, id_pemasok) {
            document.getElementById('modalTitle').textContent = 'Edit Pembelian';
            document.getElementById('modal-no_nota').value = no_nota;
            document.getElementById('display-no_nota').value = no_nota;
            document.getElementById('modal-id_pemasok').value = id_pemasok;
            document.getElementById('modal-submit').name = 'edit';
            document.getElementById('modal-submit').textContent = 'Simpan';
            document.getElementById('dataModal').style.display = 'flex';
        }

        function closeModal() {
            document.getElementById('dataModal').style.display = 'none';
        }

        window.onclick = function(event) {
            const modal = document.getElementById('dataModal');
            if (event.target == modal) {
                closeModal();
            }
        }
    </script>
</body>
</html>
