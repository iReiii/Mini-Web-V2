<?php
require('../vendor/setasign/fpdf/fpdf.php');
include '../include/db.php';
session_start();

// Cek jika belum login
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

// Query data
$sql = "SELECT dp.*, b.Merk_sepatu, p.Tanggal
        FROM det_pembelian dp
        LEFT JOIN barang b ON dp.Id_sepatu = b.Id_sepatu
        LEFT JOIN pembelian p ON dp.No_nota = p.No_nota";
$result = $conn->query($sql);

// Buat PDF baru
$pdf = new FPDF('L', 'mm', 'A4'); // Landscape, millimeter, A4
$pdf->AddPage();

// Judul
$pdf->SetFont('Arial', 'B', 16);
$pdf->Cell(0, 10, 'Laporan Detail Pembelian', 0, 1, 'C');
$pdf->Ln(5);

// Header tabel
$pdf->SetFont('Arial', 'B', 12);
$pdf->SetFillColor(0, 123, 255);
$pdf->SetTextColor(255);

$pdf->Cell(50, 10, 'Barang', 1, 0, 'C', true);
$pdf->Cell(40, 10, 'No Nota', 1, 0, 'C', true);
$pdf->Cell(35, 10, 'Tanggal', 1, 0, 'C', true);
$pdf->Cell(30, 10, 'Jumlah', 1, 0, 'C', true);
$pdf->Cell(40, 10, 'Harga Satuan', 1, 0, 'C', true);
$pdf->Cell(40, 10, 'Subtotal', 1, 1, 'C', true);

// Data tabel
$pdf->SetFont('Arial', '', 11);
$pdf->SetTextColor(0);

$total = 0;
while ($row = $result->fetch_assoc()) {
    $subtotal = $row['Jumlah'] * $row['Harga'];
    $total += $subtotal;

    $pdf->Cell(50, 10, $row['Merk_sepatu'], 1);
    $pdf->Cell(40, 10, $row['No_nota'], 1);
    $pdf->Cell(35, 10, $row['Tanggal'], 1);
    $pdf->Cell(30, 10, $row['Jumlah'], 1, 0, 'C');
    $pdf->Cell(40, 10, number_format($row['Harga'], 0, ',', '.'), 1, 0, 'R');
    $pdf->Cell(40, 10, number_format($subtotal, 0, ',', '.'), 1, 1, 'R');
}

// Total
$pdf->SetFont('Arial', 'B', 12);
$pdf->Cell(195, 10, 'Total', 1);
$pdf->Cell(40, 10, number_format($total, 0, ',', '.'), 1, 1, 'R');

// Output PDF
$pdf->Output('I', 'Laporan_Detail_Pembelian.pdf'); // 'I' berarti tampilkan di browser
?>
