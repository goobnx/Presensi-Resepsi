<?php

include("ceklogin.php");

require 'vendor/autoload.php'; // Pastikan path ini benar

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xls;
use PhpOffice\PhpSpreadsheet\Style;

// Periksa koneksi
if ($koneksi->connect_error) {
    die("Koneksi gagal: " . $koneksi->connect_error);
}

// Query untuk mengambil data kehadiran tamu
$sql_hadir = "SELECT nama_tamu, alamat, status, kehadiran, waktu FROM tamu WHERE kehadiran = 'Hadir' ORDER BY status DESC";
$sql_tidak_hadir = "SELECT nama_tamu, alamat, status, kehadiran, waktu FROM tamu WHERE kehadiran = 'Tidak Hadir' ORDER BY status DESC";

$result_hadir = $koneksi->query($sql_hadir);
$result_tidak_hadir = $koneksi->query($sql_tidak_hadir);

// Membuat array untuk menyimpan data
$data_hadir = array();
$data_tidak_hadir = array();

if ($result_hadir->num_rows > 0) {
    while($row = $result_hadir->fetch_assoc()) {
        $data_hadir[] = array(
            'nama_tamu' => $row['nama_tamu'],
            'status' => $row['status'],
            'alamat' => $row['alamat'],
            'kehadiran' => $row['kehadiran'],
            'waktu' => $row['waktu']
        );
    }
}

if ($result_tidak_hadir->num_rows > 0) {
    while($row = $result_tidak_hadir->fetch_assoc()) {
        $data_tidak_hadir[] = array(
            'nama_tamu' => $row['nama_tamu'],
            'status' => $row['status'],
            'alamat' => $row['alamat'],
            'kehadiran' => $row['kehadiran']
        );
    }
}

// Membuat Spreadsheet baru
$spreadsheet = new Spreadsheet();

// Menambahkan sheet pertama untuk data hadir
$sheet_hadir = $spreadsheet->getActiveSheet();
$sheet_hadir->setTitle('Hadir');

// Mengatur format judul tebal dan align center
$titleStyle = [
    'font' => ['bold' => true],
    'alignment' => ['horizontal' => Style\Alignment::HORIZONTAL_CENTER, 'vertical' => Style\Alignment::VERTICAL_CENTER],
    'borders' => [
        'outline' => [
            'borderStyle' => Style\Border::BORDER_THIN,
        ],
        'inside' => [
            'borderStyle' => Style\Border::BORDER_THIN,
        ],
    ],
];
$sheet_hadir->getStyle('A1:F1')->applyFromArray($titleStyle);
$sheet_hadir->setCellValue('A1', 'No.');
$sheet_hadir->setCellValue('B1', 'Nama');
$sheet_hadir->setCellValue('C1', 'Status');
$sheet_hadir->setCellValue('D1', 'Alamat');
$sheet_hadir->setCellValue('E1', 'Kehadiran');
$sheet_hadir->setCellValue('F1', 'Waktu');

// Mengatur alignment center dan border untuk data, kecuali kolom 'B' dan 'D'
$dataStyle = [
    'alignment' => ['horizontal' => Style\Alignment::HORIZONTAL_CENTER, 'vertical' => Style\Alignment::VERTICAL_CENTER],
    'borders' => [
        'allBorders' => [
            'borderStyle' => Style\Border::BORDER_THIN,
        ],
    ],
];

// Mengatur alignment kiri untuk kolom 'B' dan 'D'
$leftAlignStyle = [
    'alignment' => ['horizontal' => Style\Alignment::HORIZONTAL_LEFT],
    'borders' => [
        'allBorders' => [
            'borderStyle' => Style\Border::BORDER_THIN,
        ],
    ],
];

$row_num = 2;
foreach ($data_hadir as $key => $row) {
    $sheet_hadir->setCellValue('A' . $row_num, $key + 1);
    $sheet_hadir->setCellValue('B' . $row_num, $row['nama_tamu']);
    $sheet_hadir->setCellValue('C' . $row_num, $row['status']);
    $sheet_hadir->setCellValue('D' . $row_num, $row['alamat']);
    $sheet_hadir->setCellValue('E' . $row_num, $row['kehadiran']);
    $sheet_hadir->setCellValue('F' . $row_num, $row['waktu']);
    $row_num++;
}
$sheet_hadir->getStyle('A2:F' . ($row_num - 1))->applyFromArray($dataStyle);
$sheet_hadir->getStyle('B2:B' . ($row_num - 1))->applyFromArray($leftAlignStyle);
$sheet_hadir->getStyle('D2:D' . ($row_num - 1))->applyFromArray($leftAlignStyle);

// Mengatur lebar kolom otomatis
foreach (range('A', 'F') as $columnID) {
    $sheet_hadir->getColumnDimension($columnID)->setAutoSize(true);
}

// Menambahkan sheet kedua untuk data tidak hadir
$sheet_tidak_hadir = new \PhpOffice\PhpSpreadsheet\Worksheet\Worksheet($spreadsheet, 'Tidak Hadir');
$spreadsheet->addSheet($sheet_tidak_hadir, 1);

// Mengatur format judul tebal dan align center untuk sheet kedua
$sheet_tidak_hadir->getStyle('A1:E1')->applyFromArray($titleStyle);
$sheet_tidak_hadir->setCellValue('A1', 'No.');
$sheet_tidak_hadir->setCellValue('B1', 'Nama');
$sheet_tidak_hadir->setCellValue('C1', 'Status');
$sheet_tidak_hadir->setCellValue('D1', 'Alamat');
$sheet_tidak_hadir->setCellValue('E1', 'Kehadiran');

$row_num = 2;
foreach ($data_tidak_hadir as $key => $row) {
    $sheet_tidak_hadir->setCellValue('A' . $row_num, $key + 1);
    $sheet_tidak_hadir->setCellValue('B' . $row_num, $row['nama_tamu']);
    $sheet_tidak_hadir->setCellValue('C' . $row_num, $row['status']);
    $sheet_tidak_hadir->setCellValue('D' . $row_num, $row['alamat']);
    $sheet_tidak_hadir->setCellValue('E' . $row_num, $row['kehadiran']);
    $row_num++;
}
$sheet_tidak_hadir->getStyle('A2:E' . ($row_num - 1))->applyFromArray($dataStyle);
$sheet_tidak_hadir->getStyle('B2:B' . ($row_num - 1))->applyFromArray($leftAlignStyle);
$sheet_tidak_hadir->getStyle('D2:D' . ($row_num - 1))->applyFromArray($leftAlignStyle);

// Mengatur lebar kolom otomatis untuk sheet kedua
foreach (range('A', 'E') as $columnID) {
    $sheet_tidak_hadir->getColumnDimension($columnID)->setAutoSize(true);
}

// Mengatur header untuk unduhan file Excel
header('Content-Type: application/vnd.ms-excel');
header('Content-Disposition: attachment;filename="Laporan Kehadiran Tamu.xls"');
header('Cache-Control: max-age=0');

// Menulis file ke output
$writer = new Xls($spreadsheet);
$writer->save('php://output');

exit;
?>