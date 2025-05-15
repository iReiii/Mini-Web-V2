<?php
session_start();
include 'include/db.php';
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}
$username = $_SESSION['username'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Shoes Store</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 flex items-center justify-center h-screen">
    <div class="bg-white p-8 rounded-lg shadow-lg w-full max-w-4xl">
        <h1 class="text-3xl font-bold text-center mb-6 text-gray-800">Hi, <?php echo htmlspecialchars($username); ?>!</h1>
        <h2 class="text-2xl font-semibold text-center mb-6 text-gray-700">Welcome to the Dashboard - Toko Sepatu</h2>

        <div class="grid grid-cols-2 md:grid-cols-3 gap-6">
            <a href="pages/pemasok.php" class="flex flex-col items-center bg-blue-500 text-white p-6 rounded-lg shadow-md hover:bg-blue-600 transform hover:scale-105 transition duration-300">
                <span class="text-lg font-semibold">Pemasok</span>
            </a>
            <a href="pages/barang.php" class="flex flex-col items-center bg-green-500 text-white p-6 rounded-lg shadow-md hover:bg-green-600 transform hover:scale-105 transition duration-300">
                <span class="text-lg font-semibold">Barang</span>
            </a>
            <a href="pages/pembelian.php" class="flex flex-col items-center bg-yellow-500 text-white p-6 rounded-lg shadow-md hover:bg-yellow-600 transform hover:scale-105 transition duration-300">
                <span class="text-lg font-semibold">Pembelian</span>
            </a>
            <a href="pages/det_pembelian.php" class="flex flex-col items-center bg-purple-500 text-white p-6 rounded-lg shadow-md hover:bg-purple-600 transform hover:scale-105 transition duration-300">
                <span class="text-lg font-semibold">Detail Pembelian</span>
            </a>
            <a href="pages/penjualan.php" class="flex flex-col items-center bg-red-500 text-white p-6 rounded-lg shadow-md hover:bg-red-600 transform hover:scale-105 transition duration-300">
                <span class="text-lg font-semibold">Penjualan</span>
            </a>
            <a href="pages/det_penjualan.php" class="flex flex-col items-center bg-indigo-500 text-white p-6 rounded-lg shadow-md hover:bg-indigo-600 transform hover:scale-105 transition duration-300">
                <span class="text-lg font-semibold">Detail Penjualan</span>
            </a>
            <div class="flex flex-col items-center bg-white rounded-lg"></div>
            <a href="pages/pelanggan.php" class="flex flex-col items-center bg-pink-500 text-white p-6 rounded-lg shadow-md hover:bg-pink-600 transform hover:scale-105 transition duration-300">
                <span class="text-lg font-semibold">Pelanggan</span>
            </a>
        </div>
        <div class="flex justify-end mb-6">
            <a href="auth/logout.php" class="flex items-center bg-red-500 text-white px-4 py-2 rounded-md shadow-md hover:bg-red-600 transform hover:scale-105 transition duration-300">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M3 3a1 1 0 00-1 1v12a1 1 0 102 0V4a1 1 0 00-1-1zm10.293 9.293a1 1 0 001.414 1.414l3-3a1 1 0 000-1.414l-3-3a1 1 0 10-1.414 1.414L14.586 9H7a1 1 0 100 2h7.586l-1.293 1.293z" clip-rule="evenodd" />
                </svg>
                <span class="font-semibold">Logout</span>
            </a>
        </div>
    </div>
</body>
</html>