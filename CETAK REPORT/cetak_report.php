<?php
include '../config/koneksi.php'; // Pastikan file ini mendefinisikan $conn



// Ambil no_afrn dari parameter GET
$no_afrn = isset($_GET['no_afrn']) ? $conn->real_escape_string($_GET['no_afrn']) : '';

// Validasi agar tidak kosong
if (empty($no_afrn)) {
    die("No AFRN tidak ditemukan.");
}

$query = "SELECT
    a.id_afrn, a.no_afrn, a.tgl_afrn, a.no_bpp, a.id_transportir, a.id_destinasi, a.id_tangki,a.rit,
    a.dibuat, a.diperiksa, a.disetujui,
    b.nama_trans, b.alamat_trans,
    d.nama_destinasi, d.alamat_destinasi,
    t.no_tangki, t.no_bacth, t.source, t.doc_url,
    t.test_report_no, t.test_report_let, t.test_report_date,
    t.density, t.temperature, t.cu, t.water_contamination_ter,
    br.no_polisi, br.tgl_serti_akhir, br.volume,
    bo.keluar_dppu, bo.mulai_pengisian, bo.selesai_pengisian, bo.water_cont_ter, bo.total_meter_akhir, bo.meter_awal, bo.jlh_pengisian,
    c.id_driver, c.nama_driver
FROM afrn a
JOIN BRIDGER br ON a.id_bridger = br.id_bridger
JOIN TRANSPORTIR b ON br.id_trans = b.id_trans
JOIN DESTINASI d ON a.id_destinasi = d.id_destinasi
JOIN TANGKI t ON a.id_tangki = t.id_tangki
LEFT JOIN DRIVER c ON br.id_driver = c.id_driver
LEFT JOIN BON bo ON a.id_bon = bo.id_bon -- Join BON melalui id_bon di AFRN
WHERE a.no_afrn = '$no_afrn'
LIMIT 1";

$result = $conn->query($query);

// Cek hasil query
if (!$result) {
    die("Query error: " . $conn->error);
}

$data = $result->fetch_assoc();

if (!$data) {
    die("Data tidak ditemukan untuk no_afrn: $no_afrn");
}

// Fungsi format tanggal
function formatDate($date) {
    if (!$date || $date === '0000-00-00' || $date === 'NULL') return '';
    return date('d F Y', strtotime($date));
}




// Fungsi untuk render satu dokumen AFRN
function render_afrn($data) {
?>
<div class="afrn-copy bg-white">
    <div class="header-section flex justify-between items-start">
        <div class="title-block flex-1">
            <h1 class="text-xs-force leading-tight font-normal">AVIATION FUEL DELIVERY RELEASE</h1>
            <h2 class="text-xs-force leading-tight font-normal">NOTE</h2>
            <h3 class="text-xs-force leading-tight font-normal">(AFRN)</h3>
        </div>
        <div class="logo-block flex flex-col items-center">
            <span class="font-semibold text-sm mb-1">
                <?php echo htmlspecialchars($data['no_afrn'] ?? '-'); ?>
            </span>

            <img src="../image/pertamina.jpg" alt="Pertamina Logo" class="h-auto w-12">
        </div>
    </div>

    <div class="info-section ">
        <div class="grid grid-cols-2 gap-x-2 text-xxs-force">
            <div class="space-y-0.5">
                <div class="flex">
                    <span class="font-bold w-16">LOCATION</span>
                    <span class="mr-1">:</span>
                    <span class="">SOEKARNO-HATTA AVIATION FUEL TERMINAL & HYDRANT</span>
                </div>
                <div class="flex">
                    <span class="font-normal w-16">Vehicle Type</span>
                    <span class="mr-1">:</span>
                    <span class="">BRIDGER</span>
                </div>
                <div class="flex">
                    <span class="font-normal w-16">Vehicle No.</span>
                    <span class="mr-1">:</span>
                    <span class=""><?php echo htmlspecialchars($data['no_polisi'] ?? '-'); ?></span>
                </div>
                <div class="flex">
                    <span class="font-normal w-16">Trip No.</span>
                    <span class="mr-1">:</span>
                    <span class=""><?php echo htmlspecialchars($data['rit'] ?? '-'); ?></span>
                </div>
                <div class="flex">
                    <span class="font-normal w-16">Destination</span>
                    <span class="mr-1">:</span>
                    <span class=""><?php echo htmlspecialchars($data['nama_destinasi'] ?? '-'); ?></span>
                </div>
            </div>
            <div class="space-y-0.5">
                <div class="flex">
                    <span class="font-normal w-16">Date</span>
                    <span class="mr-1">:</span>
                    <span class=""><?php echo formatDate($data['tgl_afrn'] ?? ''); ?></span>
                </div>
                <div class="flex">
                    <span class="font-normal w-16">Installation</span>
                    <span class="mr-1">:</span>
                    <span class="">SHAFTHI</span>
                </div>
                <div class="flex">
                    <span class="font-normal w-16">Driver</span>
                    <span class="mr-1">:</span>
                    <span class=""><?php echo htmlspecialchars($data['nama_driver'] ?? ''); ?>Hermanto</span>
                </div>
                <div class="flex">
                    <span class="font-normal w-16">BPP/PNBP</span>
                    <span class="mr-1">:</span>
                    <span class=""><?php echo htmlspecialchars($data['no_bpp'] ?? '-'); ?></span>
                </div>
                <div class="flex">
                    <span class="font-normal w-16">Transportir</span>
                    <span class="mr-1">:</span>
                    <span class=""><?php echo htmlspecialchars($data['nama_trans'] ?? '-'); ?></span>
                </div>
            </div>
        </div>
    </div>

    <div class="main-content-tables flex h-auto">
        <div class="flex-1 left-column">
            <table class="w-full border-collapse">
                <thead>
                    <tr>
                        <th class="table-header text-left font-normal w-24">Tank Number</th>
                        <th class="table-header text-center font-bold w-16">
                            <?php echo htmlspecialchars($data['no_tangki'] ?? '-'); ?></th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td class="table-cell px-2 py-1">Filling Commenced</td>
                        <td class="table-cell"><?php echo htmlspecialchars($data['mulai_pengisian'] ?? ''); ?></td>
                    </tr>
                    <tr>
                        <td class="table-cell">Filling Completed</td>
                        <td class="table-cell"><?php echo htmlspecialchars($data['selesai_pengisian'] ?? ''); ?></td>
                    </tr>
                    <tr>
                        <td class="table-cell">Water/Contamination Test</td>
                        <td class="table-cell"><?php echo htmlspecialchars($data['water_cont_ter'] ?? ''); ?></td>
                    </tr>
                    <tr>
                        <td class="table-cell">Grade</td>
                        <td class="table-cell">JET A-1</td>
                    </tr>
                    <tr>
                        <td class="table-cell">Meter Totalizer After Filling</td>
                        <td class="table-cell"><?php echo number_format($data['meter_awal'] ?? 0); ?></td>
                    </tr>
                    <tr>
                        <td class="table-cell">Meter Totalizer Before Filling</td>
                        <td class="table-cell"><?php echo number_format($data['total_meter_akhir'] ?? 0); ?></td>
                    </tr>
                    <tr>
                        <td class="table-cell">Quantity</td>
                        <td class="table-cell"><?php echo number_format($data['jlh_pengisian'] ?? 0); ?>&nbsp; LITERS
                        </td>
                    </tr>
                    <tr>
                        <td class="table-cell">Batch Number</td>
                        <td class="table-cell"><?php echo htmlspecialchars($data['no_bacth'] ?? ''); ?></td>
                    </tr>
                    <tr>
                        <td class="table-cell">Source</td>
                        <td class="table-cell"><?php echo htmlspecialchars($data['source'] ?? ''); ?></td>
                    </tr>
                    <tr>
                        <td class="table-cell">No. Test Report & Date</td>
                        <td class="table-cell">
                            <?php
                            echo htmlspecialchars($data['test_report_no'] ?? '');
                            if (!empty($data['test_report_date']) && $data['test_report_date'] !== '0000-00-00') {
                                echo '<br>' . formatDate($data['test_report_date']);
                            }
                            ?>
                        </td>
                    </tr>
                    <tr>
                        <td class="table-cell">Quality Controller</td>
                        <td class="table-cell"><?php echo htmlspecialchars($data['disetujui'] ?? ''); ?></td>
                    </tr>
                    <tr>
                        <td class="table-cell">Left Installation</td>
                        <td class="table-cell"><?php echo htmlspecialchars($data['keluar_dppu'] ?? ''); ?> Hrs.</td>
                    </tr>
                    <tr>
                        <td class="table-cell">Arrived at Depot</td>
                        <td class="table-cell text-center">Hrs.</td>
                    </tr>
                    <tr>
                        <td class="table-cell">Water/Contamination Test</td>
                        <td class="table-cell text-center">Hrs.</td>
                    </tr>
                    <tr>
                        <td class="table-cell">Discharge Commenced</td>
                        <td class="table-cell text-center">Hrs.</td>
                    </tr>
                    <tr>
                        <td class="table-cell">Discharge Completed</td>
                        <td class="table-cell text-center">Hrs.</td>
                    </tr>
                    <tr>
                        <td class="table-cell">Quantity Arrived</td>
                        <td class="table-cell text-center">Liters</td>
                    </tr>
                    <tr>
                        <td class="table-cell">Quality Controller</td>
                        <td class="table-cell"></td>
                    </tr>
                    <tr>
                        <td class="table-cell">Left Depot</td>
                        <td class="table-cell text-center">Hrs.</td>
                    </tr>
                    <tr>
                        <td class="table-cell">Arrived at Installation/Depot Quality Controller</td>
                        <td class="table-cell"></td>
                    </tr>

                </tbody>
            </table>

            <table class="w-full border-collapse mt-1">
                <tbody>
                    <tr>
                        <td class="border border-black  p-2 flex flex-col justify-between h-28">
                            <span>Receive Free From Water</span>
                            <span>Quality Controller Sign & Mark</span>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <div class="right-column">
            <div class=" text-xxs-force mb-1">
                <h1 class="font-bold pb-0.5 mb-0.5">Vehicle Note</h1>
                <div class="mb-0.5">
                    <span class="font-normal"> Type of Product Filling :</span>
                    <span class="ml-1">JET A-1</span>
                </div>
                <div class="mb-0.5">Date of Cleaning/Draining :</div>
                <div class="mb-0.5">Checked By Quality Controller :</div>
                <div class="">Instruction Number :</div>
            </div>

            <div class="section-box certified-box w-42 border border-gray-400 p-3 text-center text-xs text-xxs-force">

                <div class="font-semibold ">Certified Water free by</div>

                <div class="h-20 my-2 flex items-center justify-center">

                    <span class="font-normal  text-gray-300 ">
                        for Deliver
                    </span>

                </div>

                <div class="font-normal">Quality Controller Sign & Mark</div>

            </div>

            <div class="text-xxs-force mb-1 leading-tight">
                <div class="font-semibold mb-0.5">Certified</div>
                <div class="mb-0.5">That this item enumerated hereon has been inspected</div>
                <div class="mb-0.5">and tested in accordance with contract condition and the</div>
                <div class="mb-0.5">Pertamina Quality Control Scheme C.F</div>
                <div class="mb-0.5">a. That it conforms to Specification.</div>
                <div class="mb-0.5">b. That Stand 31-94 latest issue.</div>
                <div class="mb-0.5">c. That has been released under Authority of quality</div>
                <div class="mb-0.5">Pertamina Quality Control Scheme of :</div>
            </div>

            <div class="section-box text-xxs-force mb-1">
                <div class="grid grid-cols-2 gap-x-1 gap-y-0.5">
                    <span class="font-medium">BPP/PNP Num.</span>
                    <span><?php echo htmlspecialchars($data['no_bpp'] ?? '-'); ?></span>
                    <span class="font-medium">DENSITY OBSD</span>
                    <span><?php echo htmlspecialchars($data['density'] ?? '-'); ?>&nbsp; Kg/liter </span>
                    <span class="font-medium">TEMP OBSD</span>
                    <span><?php echo htmlspecialchars($data['temperature'] ?? '-'); ?>&nbsp; &deg;C</span>
                    <span class="font-medium">CU</span>
                    <span><?php echo htmlspecialchars($data['cu'] ?? '-'); ?>&nbsp; p. S/m</span>
                </div>
            </div>
            <div class="p-1 mb-1 w-42  text-center">
                <div class="text-control p-8">Quality Control Sign</div>

                <div class="text-xs-force font-semibold">HERMANTO PURBA</div>
            </div>

            <div class="absolute bottom-8 left-8 text-xs text-gray-500">
                SF-117/2012 - AVS Rev.0
            </div>
        </div>
    </div>
</div>
<?php
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Aviation Fuel Delivery Release Note</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap"
        rel="stylesheet">
    <style>
    body {
        font-family: 'Poppins', sans-serif;
        margin: 0;
        padding: 0;
        background: #fff;
        color: #000;
        /* Ensure text is black */
    }

    .text-control {
        font-size: 8px;
        margin-bottom: 30px;
    }

    /* Custom utility classes for very small fonts */
    .text-xxs-force {
        font-size: 10px !important;
        /* Extremely small font */
        line-height: 1 !important;
    }

    .text-xs-force {
        font-size: 10px !important;
        /* Slightly larger for titles */
        line-height: 1.1 !important;
    }

    @media print {
        @page {
            size: A4 landscape;
            /* 297mm x 210mm */
            margin: 0;
            /* No page margins */
        }

        html,
        body {
            width: 297mm;
            height: 210mm;
            overflow: hidden;
            /* Prevent scrollbars */
        }

        .print-container {
            display: flex;
            /* Use flexbox for side-by-side */
            flex-direction: row;
            width: 297mm;
            height: 210mm;
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            border: none;
            /* No outer border for the whole page */
            background: #fff;
        }

        .afrn-copy {
            width: 50%;
            /* Each copy takes exactly half the page width */
            height: 100%;
            /* Each copy takes full page height */
            box-sizing: border-box;
            /* border: 0.25mm solid black; */
            /* Thin border between copies */
            padding: 2mm;
            /* Minimal padding inside each copy */
            display: flex;
            flex-direction: column;
            /* Stack sections vertically */
            justify-content: space-between;
            /* Distribute space vertically */
            page-break-inside: avoid;
            /* Keep each copy together */
        }

        .afrn-copy:first-child {
            border-right: none;
            /* No border in the middle if you prefer a single line */
        }

        .afrn-copy:last-child {
            border-left: none;
            /* No border in the middle if you prefer a single line */
        }


        .header-section {
            padding: 0.5mm 0;
            /* Minimal padding */
            margin-bottom: 0.5mm;
        }

        .header-section .title-block h1,
        .header-section .title-block h2,
        .header-section .title-block h3 {
            margin: 0;
            padding: 0;
        }

        .header-section .logo-block img {
            width: 35mm;
            /* Adjust logo size */
        }

        .info-section {
            padding: 0.5mm 0;
            /* Minimal padding */
            margin-bottom: 0.5mm;

            /* Thin border */
        }

        .info-section .grid {
            gap: 2mm;
            /* Reduce gap */
        }

        .info-section .flex span {
            margin-right: 0.5mm;
            /* Reduce gap */
        }

        .info-section .flex .w-16 {
            width: 15mm;
            /* Adjust fixed width for labels */
        }


        .main-content-tables {
            flex-grow: 1;
            /* Allow tables section to grow */
            display: flex;
            flex-direction: row;
            /* Columns side-by-side */
            gap: 1mm;
            /* Small gap between left and right columns */
        }

        .left-column {
            flex: 1;
            /* Left column takes available space */
            padding-right: 0.5mm;
        }

        .right-column {
            width: 45mm;
            /* Fixed width for right column */
            padding-left: 0.5mm;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            /* Space out sections vertically */
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 1mm;
            /* Reduce margin */
        }

        .table-header,
        .table-cell {
            /* Menggunakan warna abu-abu gelap (#333) agar tidak terlalu tajam saat dicetak */
            border: 0.25mm solid #333;

            /* Padding tetap sama, sudah cukup baik */
            padding: 0.5mm 1mm;

            /* Menggunakan 8pt (points), standar untuk cetak dan sedikit lebih mudah dibaca */
            font-size: 8pt;

            /* Memberi sedikit ruang napas antar baris agar tidak terlalu dempet */
            line-height: 1.2;

            /* vertical-align: top sudah sangat bagus untuk merapikan teks ke atas */
            vertical-align: top;

            /* Menambahkan properti untuk menangani teks yang sangat panjang */
            word-wrap: break-word;
        }

        .table-header {

            background-color: #f5f5f5;

        }

        .section-box {
            border: 0.25mm solid black;
            padding: 1mm;
            /* Minimal padding for boxes */
            margin-bottom: 1mm;
            /* Minimal margin */
        }

        .section-box h1 {
            font-size: 8px !important;
            /* Adjust font size for box titles */
            border-bottom: 0.25mm solid black;
            padding-bottom: 0.5mm;
            margin-bottom: 0.5mm;
        }

        .section-box img {
            width: 25mm;
            /* Adjust logo size in boxes */
        }

        .certified-box {
            height: 35mm;
            /* Fixed height for certified box */
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            font-size: 5px;
        }
    }
    </style>
</head>

<body>
    <div class="print-container">
        <?php render_afrn($data); ?>
        <?php render_afrn($data); ?>
    </div>
</body>
<script>
window.onload = function() {
    window.print();
};
</script>

</html>
<?php $conn->close(); ?>