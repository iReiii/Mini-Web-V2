<?php
require('../vendor/setasign/fpdf/fpdf.php');
include '../include/db.php'; // Koneksi database

// Query ambil data
$sql = "SELECT dp.*, b.Merk_sepatu, p.Tgl_nota, pl.Nama AS NamaPelanggan
        FROM det_penjualan dp
        LEFT JOIN barang b ON dp.Id_sepatu = b.Id_sepatu
        LEFT JOIN penjualan p ON dp.No_nota = p.No_nota
        LEFT JOIN pelanggan pl ON p.Id_pelanggan = pl.Id_pelanggan
        ORDER BY p.Tgl_nota DESC";
$result = $conn->query($sql);

// Membuat PDF
$pdf = new FPDF('L', 'mm', 'A4');
$pdf->AddPage();

// Judul
$pdf->SetFont('Arial', 'B', 16);
$pdf->Cell(0, 10, 'Laporan Detail Penjualan Shoes Store', 0, 1, 'C');
$pdf->Ln(5);

// Header Tabel
$pdf->SetFont('Arial', 'B', 10);
$pdf->SetFillColor(0, 122, 255); // warna biru terang
$pdf->SetTextColor(255);
$pdf->Cell(40, 10, 'Barang', 1, 0, 'C', true);
$pdf->Cell(30, 10, 'No Nota', 1, 0, 'C', true);
$pdf->Cell(30, 10, 'Tanggal', 1, 0, 'C', true);
$pdf->Cell(20, 10, 'Jumlah', 1, 0, 'C', true);
$pdf->Cell(30, 10, 'Harga Satuan', 1, 0, 'C', true);
$pdf->Cell(30, 10, 'Subtotal', 1, 1, 'C', true);

// Isi Tabel
$pdf->SetFont('Arial', '', 10);
$pdf->SetTextColor(0);
$totalSeluruh = 0;

while($row = $result->fetch_assoc()) {
    $subtotal = $row['Jumlah'] * $row['Harga'];
    $totalSeluruh += $subtotal;

    $pdf->Cell(40, 10, $row['Merk_sepatu'], 1, 0, 'C');
    $pdf->Cell(30, 10, $row['No_nota'], 1, 0, 'C');
    $pdf->Cell(30, 10, $row['Tgl_nota'], 1, 0, 'C');
    $pdf->Cell(20, 10, $row['Jumlah'], 1, 0, 'C');
    $pdf->Cell(30, 10, 'Rp' . number_format($row['Harga'], 0, ',', '.'), 1, 0, 'C');
    $pdf->Cell(30, 10, 'Rp' . number_format($subtotal, 0, ',', '.'), 1, 1, 'C');
}

// Baris Total
$pdf->SetFont('Arial', 'B', 10);
$pdf->Cell(150, 10, 'Total', 1, 0, 'R');
$pdf->Cell(30, 10, 'Rp' . number_format($totalSeluruh, 0, ',', '.'), 1, 1, 'C');

// Output
$pdf->Output('I', 'Detail_Penjualan_Shoes_Store.pdf');
?>
