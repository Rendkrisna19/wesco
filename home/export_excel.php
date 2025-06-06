<?php
require '../vendor/autoload.php'; // Penting: Ini memuat pustaka PhpSpreadsheet
include '../config/koneksi.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;

// Ambil tanggal dari parameter GET
$startDate = isset($_GET['start_date']) ? $_GET['start_date'] : date('Y-m-01');
$endDate = isset($_GET['end_date']) ? $_GET['end_date'] : date('Y-m-t');

// Validasi tanggal (opsional tapi disarankan)
if (!strtotime($startDate) || !strtotime($endDate)) {
    die("Format tanggal tidak valid.");
}

// Query untuk mengambil semua data yang diminta
$query = "SELECT
                a.no_afrn,
                a.tgl_afrn,
                a.no_bpp,
                b.nama_trans,
                b.alamat_trans,
                d.nama_destinasi,
                d.alamat_destinasi,
                t.no_tangki,        -- Kolom no_tangki ada di tabel tangki (alias 't')
                br.no_polisi,
                br.tgl_serti_akhir,
                br.volume,
                bon.keluar_dppu,
                bon.tgl_rekam
            FROM
                afrn a
            LEFT JOIN
                BRIDGER br ON a.id_bridger = br.id_bridger
            LEFT JOIN
                TRANSPORTIR b ON br.id_trans = b.id_trans
            LEFT JOIN
                DESTINASI d ON a.id_destinasi = d.id_destinasi
            LEFT JOIN
                bon ON a.id_bon = bon.id_bon
            LEFT JOIN
                TANGKI t ON t.id_tangki = t.id_tangki -- Join ke tabel TANGKI melalui id_tangki di BRIDGER
            WHERE
                a.tgl_afrn BETWEEN ? AND ?
            ORDER BY
                a.tgl_afrn ASC";

// Persiapan statement
if ($stmt = $conn->prepare($query)) {
    $stmt->bind_param("ss", $startDate, $endDate);
    $stmt->execute();
    $result = $stmt->get_result();
} else {
    die("Error preparing statement: " . $conn->error);
}

// Buat objek Spreadsheet baru
$spreadsheet = new Spreadsheet();
$sheet = $spreadsheet->getActiveSheet();

// Tulis header kolom
$headers = [
    'NO AFRN', 'TGL AFRN', 'NO BPP', 'NAMA TRANSPORTIR', 'ALAMAT TRANSPORTIR',
    'NAMA DESTINASI', 'ALAMAT DESTINASI', 'NO TANGKI', 'NO POLISI',
    'TGL SERTI AKHIR', 'VOLUME', 'KELUAR DPPU', 'TGL REKAM'
];
$sheet->fromArray($headers, NULL, 'A1');

// Styling untuk header
$sheet->getStyle('A1:' . $sheet->getHighestColumn() . '1')->applyFromArray([
    'font' => [
        'bold' => true,
        'color' => ['argb' => 'FFFFFFFF'],
    ],
    'fill' => [
        'fillType' => Fill::FILL_SOLID,
        'startColor' => ['argb' => 'FF4A7080'], // Warna biru gelap
    ],
    'borders' => [
        'allBorders' => [
            'borderStyle' => Border::BORDER_THIN,
            'color' => ['argb' => 'FF000000'],
        ],
    ],
    'alignment' => [
        'horizontal' => Alignment::HORIZONTAL_CENTER,
        'vertical' => Alignment::VERTICAL_CENTER,
    ],
]);

// Tulis data baris
$rowNum = 2; // Mulai dari baris kedua setelah header
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $sheet->setCellValue('A' . $rowNum, $row['no_afrn']);
        $sheet->setCellValue('B' . $rowNum, $row['tgl_afrn']);
        $sheet->setCellValue('C' . $rowNum, $row['no_bpp']);
        $sheet->setCellValue('D' . $rowNum, $row['nama_trans']);
        $sheet->setCellValue('E' . $rowNum, $row['alamat_trans']);
        $sheet->setCellValue('F' . $rowNum, $row['nama_destinasi']);
        $sheet->setCellValue('G' . $rowNum, $row['alamat_destinasi']);
        $sheet->setCellValue('H' . $rowNum, $row['no_tangki']);
        $sheet->setCellValue('I' . $rowNum, $row['no_polisi']);
        $sheet->setCellValue('J' . $rowNum, $row['tgl_serti_akhir']);
        $sheet->setCellValue('K' . $rowNum, $row['volume']);
        $sheet->setCellValue('L' . $rowNum, $row['keluar_dppu']);
        $sheet->setCellValue('M' . $rowNum, $row['tgl_rekam']);
        $rowNum++;
    }
}

// Auto-size kolom
foreach (range('A', $sheet->getHighestColumn()) as $col) {
    $sheet->getColumnDimension($col)->setAutoSize(true);
}

// Tentukan nama file
$filename = "export_afrn_" . $startDate . "_to_" . $endDate . ".xlsx";
$filename = str_replace('-', '_', $filename); // Ganti hyphen dengan underscore untuk nama file

// Set header untuk download file
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="' . $filename . '"');
header('Cache-Control: max-age=0');

// Buat writer dan simpan file
$writer = new Xlsx($spreadsheet);
$writer->save('php://output');

$conn->close();
exit;