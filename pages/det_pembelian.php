<?php
include '../include/db.php';
session_start();
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

if (isset($_POST['add'])) {
    $id_sepatu = $_POST['id_sepatu'];
    $no_nota = $_POST['no_nota'];
    $jumlah = $_POST['jumlah'];
    $harga = $_POST['harga'];

    $stmt = $conn->prepare("INSERT INTO det_pembelian (Id_sepatu, No_nota, Jumlah, Harga) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssid", $id_sepatu, $no_nota, $jumlah, $harga);
    $stmt->execute();
    header("Location: det_pembelian.php");
    exit();
}

if (isset($_POST['edit'])) {
    $id_sepatu = $_POST['id_sepatu'];
    $no_nota = $_POST['no_nota'];
    $jumlah = $_POST['jumlah'];
    $harga = $_POST['harga'];

    $stmt = $conn->prepare("UPDATE det_pembelian SET Jumlah=?, Harga=? WHERE Id_sepatu=? AND No_nota=?");
    $stmt->bind_param("diss", $jumlah, $harga, $id_sepatu, $no_nota);
    $stmt->execute();
    header("Location: det_pembelian.php");
    exit();
}

if (isset($_GET['delete'])) {
    $id_sepatu = $_GET['id_sepatu'];
    $no_nota = $_GET['no_nota'];
    
    $stmt = $conn->prepare("DELETE FROM det_pembelian WHERE Id_sepatu=? AND No_nota=?");
    $stmt->bind_param("ss", $id_sepatu, $no_nota);
    $stmt->execute();
    header("Location: det_pembelian.php");
    exit();
}  

$sql = "SELECT dp.*, b.Merk_sepatu, p.Tanggal
        FROM det_pembelian dp
        LEFT JOIN barang b ON dp.Id_sepatu = b.Id_sepatu
        LEFT JOIN pembelian p ON dp.No_nota = p.No_nota";
$result = $conn->query($sql);

$sql_barang = "SELECT Id_sepatu, Merk_sepatu FROM barang";
$result_barang = $conn->query($sql_barang);

$sql_nota = "SELECT No_nota FROM pembelian";
$result_nota = $conn->query($sql_nota);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detail Pembelian - Shoes Store</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 p-10">
    <h1 class="text-2xl font-bold mb-4">Data Detail Pembelian</h1>

    <!-- Tombol Tambah Detail Pembelian -->
    <button onclick="openAddModal()" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded mb-6">
        Tambah Detail Pembelian
    </button>

    <a href="report_pembelian.php" class="bg-purple-500 hover:bg-purple-700 text-white font-bold py-2 px-4 rounded">
    Cetak Laporan
    </a>

    <table class="w-full bg-white shadow-md rounded-lg overflow-hidden">
        <thead class="bg-blue-500 text-white">
            <tr>
                <th class="p-3">Barang</th>
                <th class="p-3">No Nota</th>
                <th class="p-3">Tanggal</th>
                <th class="p-3">Jumlah</th>
                <th class="p-3">Harga</th>
                <th class="p-3">Subtotal</th>
                <th class="p-3">Aksi</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = $result->fetch_assoc()): ?>
            <tr class="border-b">
                <td class="p-3 text-center">
                    <?php echo $row['Merk_sepatu']; ?>
                </td>
                <td class="p-3 text-center"><?php echo $row['No_nota']; ?></td>
                <td class="p-3 text-center"><?php echo $row['Tanggal']; ?></td>
                <td class="p-3 text-center"><?php echo $row['Jumlah']; ?></td>
                <td class="p-3 text-center"><?php echo number_format($row['Harga'], 0, ',', '.'); ?></td>
                <td class="p-3 text-center"><?php echo number_format($row['Jumlah'] * $row['Harga'], 0, ',', '.'); ?></td>
                <td class="p-3 text-center space-x-2">
                    <button onclick="openEditModal(
                        '<?php echo $row['Id_sepatu']; ?>',
                        '<?php echo $row['No_nota']; ?>',
                        <?php echo $row['Jumlah']; ?>,
                        <?php echo $row['Harga']; ?>
                    )" class="bg-yellow-400 hover:bg-yellow-500 text-white px-3 py-1 rounded">Edit</button>
                    <a href="?delete&id_sepatu=<?php echo $row['Id_sepatu']; ?>&no_nota=<?php echo $row['No_nota']; ?>" 
                       onclick="return confirm('Yakin ingin hapus?')" 
                       class="bg-red-500 hover:bg-red-700 text-white px-3 py-1 rounded">Hapus</a>
                </td>
            </tr>
            <?php endwhile; ?>
        </tbody>
    </table>

    <div id="dataModal" style="display: none;" class="fixed inset-0 bg-gray-800 bg-opacity-50 flex justify-center items-center">
        <div class="bg-white p-6 rounded shadow-md w-96">
            <h2 id="modalTitle" class="text-xl font-semibold mb-4">Tambah Detail Pembelian</h2>
            <form method="post" class="space-y-4">
                <input type="hidden" name="id_sepatu" id="modal-id_sepatu">
                <input type="hidden" name="no_nota" id="modal-no_nota">
                
                <div>
                    <label class="block text-gray-700">Barang</label>
                    <select name="id_sepatu" id="modal-barang" required class="w-full p-2 border rounded">
                        <option value="">Pilih Barang</option>
                        <?php while ($barang = $result_barang->fetch_assoc()): ?>
                            <option value="<?php echo $barang['Id_sepatu']; ?>">
                                <?php echo $barang['Merk_sepatu']; ?>
                            </option>
                        <?php endwhile; ?>
                    </select>
                </div>
                
                <div>
                    <label class="block text-gray-700">No Nota Pembelian</label>
                    <select name="no_nota" id="modal-nota" required class="w-full p-2 border rounded">
                        <option value="">Pilih Nota Pembelian</option>
                        <?php 
                        $result_nota->data_seek(0);
                        while ($nota = $result_nota->fetch_assoc()): ?>
                            <option value="<?php echo $nota['No_nota']; ?>">
                                <?php echo $nota['No_nota']; ?>
                            </option>
                        <?php endwhile; ?>
                    </select>
                </div>
                
                <div>
                    <label class="block text-gray-700">Jumlah</label>
                    <input type="number" name="jumlah" id="modal-jumlah" min="1" required class="w-full p-2 border rounded">
                </div>
                
                <div>
                    <label class="block text-gray-700">Harga Satuan</label>
                    <input type="number" name="harga" id="modal-harga" min="0" required class="w-full p-2 border rounded">
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
        document.getElementById('modalTitle').textContent = 'Tambah Detail Pembelian';
        document.getElementById('modal-id_sepatu').value = '';
        document.getElementById('modal-no_nota').value = '';
        document.getElementById('modal-barang').value = '';
        document.getElementById('modal-nota').value = '';
        document.getElementById('modal-jumlah').value = '';
        document.getElementById('modal-harga').value = '';
        document.getElementById('modal-submit').name = 'add';
        document.getElementById('modal-submit').textContent = 'Tambah';
        document.getElementById('dataModal').style.display = 'flex';
    }

    function openEditModal(id_sepatu, no_nota, jumlah, harga) {
        document.getElementById('modalTitle').textContent = 'Edit Detail Pembelian';
        document.getElementById('modal-id_sepatu').value = id_sepatu;
        document.getElementById('modal-no_nota').value = no_nota;
        document.getElementById('modal-jumlah').value = jumlah;
        document.getElementById('modal-harga').value = harga;

        // Mengatur pilihan pada dropdown barang sesuai dengan id_sepatu
        var selectBarang = document.getElementById('modal-barang');
        for (var i = 0; i < selectBarang.options.length; i++) {
            if (selectBarang.options[i].value == id_sepatu) {
                selectBarang.selectedIndex = i;
                break;
            }
        }

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