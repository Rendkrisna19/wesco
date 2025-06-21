<?php
session_start();

// Redirect jika belum login (optional, tergantung alur aplikasi Anda)
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header("Location: ../auth/index.php");
    exit;
}

include '../config/koneksi.php'; // koneksi mysqli

// Ambil ID dari parameter GET
if (isset($_GET['id_afrn'])) { // Menggunakan id_afrn untuk pengambilan data
    $id_afrn = mysqli_real_escape_string($conn, $_GET['id_afrn']);

    $query = "SELECT
                segel.id_segel,
                segel.mainhole1,
                segel.mainhole2,
                segel.mainhole3,
                segel.mainhole4,
                segel.bottom_load_cov1,
                segel.bottom_load_cov2,
                segel.bottom_load_cov3,
                segel.bottom_load_cov4,
                segel.bottom_load_cov5,
                salib_ukur.ket_jarak_t1,
                salib_ukur.ket_jarak_cair_t1,
                salib_ukur.diperiksa_t1,
                salib_ukur.diperiksa_segel,
                jarak_t1.jarak_komp1,
                jarak_t1.jarak_komp2,
                jarak_t1.jarak_komp3,
                jarak_t1.jarak_komp4,
                jarak_t1.temp_komp1,
                jarak_t1.temp_komp2,
                jarak_t1.temp_komp3,
                jarak_t1.temp_komp4,
                jarak_cair_t1.jarak_cair_komp1,
                jarak_cair_t1.jarak_cair_komp2,
                jarak_cair_t1.jarak_cair_komp3,
                jarak_cair_t1.jarak_cair_komp4,
                jarak_cair_t1.dencity_cair_komp1,
                jarak_cair_t1.dencity_cair_komp2,
                jarak_cair_t1.dencity_cair_komp3,
                jarak_cair_t1.dencity_cair_komp4,
                jarak_cair_t1.temp_cair_komp_komp1,
                jarak_cair_t1.temp_cair_komp_komp2,
                jarak_cair_t1.temp_cair_komp_komp3,
                jarak_cair_t1.temp_cair_komp_komp4,
                afrn.no_afrn,
                afrn.tgl_afrn,
                afrn.no_bpp,
                afrn.dibuat,
                afrn.diperiksa,
                afrn.disetujui,
                bridger.no_polisi,
                bridger.volume,
                bridger.tgl_serti_akhir,
                bon.tgl_rekam,
                bon.keluar_dppu -- Pastikan kolom ini diambil dari tabel bon
            FROM afrn
            LEFT JOIN bridger ON afrn.id_bridger = bridger.id_bridger
            LEFT JOIN salib_ukur ON salib_ukur.id_afrn = afrn.id_afrn
            LEFT JOIN bon ON afrn.id_bon = bon.id_bon -- Join ke tabel bon
            LEFT JOIN segel ON segel.id_ukur = salib_ukur.id_ukur
            LEFT JOIN jarak_t1 ON salib_ukur.id_jarak_t1 = jarak_t1.id_jarak_t1
            LEFT JOIN jarak_cair_t1 ON salib_ukur.id_jarak_cair_t1 = jarak_cair_t1.id_jarak_cair_t1
            WHERE afrn.id_afrn = '$id_afrn'
            LIMIT 1";

    $result = mysqli_query($conn, $query);

    if ($result && mysqli_num_rows($result) > 0) {
        $data = mysqli_fetch_assoc($result);
    } else {
        die("Data tidak ditemukan untuk AFRN ID: " . htmlspecialchars($id_afrn));
    }
} else {
    die("Parameter id_afrn dibutuhkan.");
}

// Fungsi untuk menampilkan isi form (agar tidak duplikasi kode)
function print_form($data)
{
?>
<table class="header-table">
    <tr>
        <td style="width: 200px;">Tanggal</td>
        <td>: <?= date('l, d F Y', strtotime($data['tgl_rekam'])) ?></td>
    </tr>
    <tr>
        <td>No. Polisi</td>
        <td>: <?= htmlspecialchars($data['no_polisi']) ?></td>
    </tr>
    <tr>
        <td>Volume Bridger</td>
        <td>: <?= number_format($data['volume']) ?></td>
    </tr>
    <tr>
        <td>Masa Berlaku Tera Tangki Bridger</td>
        <td>: <?= date('Y-m-d', strtotime($data['tgl_serti_akhir'])) ?></td>
    </tr>
</table>

<div class="section-title">PEMERIKSAAN DAN PENCATATAN MINIMAL 10 MENIT SETELAH SETTLING TIME</div>
<table class="main-table">
    <thead>
        <tr>
            <th colspan="2">JARAK T1 PADA DOKUMEN KALIBRASI</th>
            <th colspan="3">JARAK CAIRAN TERHADAP T1 (ULLAGE) @ SUPPLY POINT</th>
            <th rowspan="2" colspan="1" style="width: 200px;">
                <div>DIPERIKSA & DICATAT OLEH</div>
                <div class="sub">(Nama & Tanda Tangan)</div>
            </th>
        </tr>
        <tr>
            <th>JARAK (MM)</th>
            <th>TEMP (°C)</th>
            <th>JARAK (MM)</th>
            <th>DENSITY OBA (Kg/L)</th>
            <th>TEMP (°C)</th>
        </tr>
    </thead>
    <tbody>
        <?php for ($i = 1; $i <= 4; $i++) : ?>
        <tr>
            <td>KOMP <?= $i ?>: <?= htmlspecialchars($data["jarak_komp$i"] ?? '') ?></td>
            <td><?= htmlspecialchars($data["temp_komp$i"] ?? '') ?></td>
            <td>KOMP <?= $i ?>: <?= htmlspecialchars($data["jarak_cair_komp$i"] ?? '') ?></td>
            <td><?= htmlspecialchars($data["dencity_cair_komp$i"] ?? '') ?></td>
            <td><?= htmlspecialchars($data["temp_cair_komp_komp$i"] ?? '') ?></td>
            <?php if ($i === 1) : ?>
            <td rowspan="6" class="ttd">
                <img src="https://i.pinimg.com/originals/bc/f2/11/bcf211ef4cbf4f8883079941942ccb9c.png"
                    alt="Logo Pertamina">
                <div class="nama"><?= htmlspecialchars($data['diperiksa_t1']) ?></div>
            </td>
            <?php endif; ?>
        </tr>
        <?php endfor; ?>
        <tr>
            <td colspan="5" style="border-right: none;">&nbsp;</td>
        </tr>
        <tr>
            <td colspan="2" style="border-right: none; font-weight:600;">KETERANGAN
                :<br><?= nl2br(htmlspecialchars($data['ket_jarak_t1'])) ?></td>
            <td colspan="3" style="border-right: none; font-weight:600;">KETERANGAN
                :<br><?= nl2br(htmlspecialchars($data['ket_jarak_cair_t1'])) ?></td>
        </tr>
    </tbody>
</table>

<div class="section-title main-table">PEMERIKSAAN OLEH SECURITY SEBELUM KELUAR LOKASI</div>
<table class="main-table">
    <thead>
        <tr>
            <th>NOMOR/KODE SEGEL</th>
            <th style="width: 100px;">JAM KELUAR</th>
            <th style="width: 200px;">
                <div>DIPERIKSA & DICATAT OLEH</div>
                <div class="sub">(Nama & Tanda Tangan)</div>
            </th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td>
                <table class="segel-table border-collapse ">
                    <tbody>
                        <tr>
                            <td class="border border-black p-2 font-medium">MAINHOLE 1</td>
                            <td class="border border-black p-2 w-6 text-center">:</td>
                            <td class="border border-black p-2">SKH-000024</td>
                        </tr>

                        <tr>
                            <td class="border border-black p-2 font-medium">MAINHOLE 2</td>
                            <td class="border border-black p-2 w-6 text-center">:</td>
                            <td class="border border-black p-2">SKH-000024</td>
                        </tr>

                        <tr>
                            <td class="border border-black p-2 font-medium">MAINHOLE 3</td>
                            <td class="border border-black p-2 w-6 text-center">:</td>
                            <td class="border border-black p-2">SKH-000024</td>
                        </tr>

                        <tr>
                            <td class="border border-black p-2 font-medium">BOTTOM LOADER COVER</td>
                            <td class="border border-black p-2 w-6 text-center">:</td>
                            <td class="border border-black p-2">SKH-000024</td>
                        </tr>

                        <tr>
                            <td class="border border-black p-2 font-medium">BOTTOM LOADER VALVE 1</td>
                            <td class="border border-black p-2 w-6 text-center">:</td>
                            <td class="border border-black p-2">SKH-000024</td>
                        </tr>

                        <tr>
                            <td class="border border-black p-2 font-medium">BOTTOM LOADER VALVE 2</td>
                            <td class="border border-black p-2 w-6 text-center">:</td>
                            <td class="border border-black p-2">SKH-000024</td>
                        </tr>

                        <tr>
                            <td class="border border-black p-2 font-medium">BOTTOM LOADER VALVE 3</td>
                            <td class="border border-black p-2 w-6 text-center">:</td>
                            <td class="border border-black p-2">SKH-000024</td>
                        </tr>

                        <tr>
                            <td class="border border-black p-2 font-medium">BOTTOM LOADER VALVE 4</td>
                            <td class="border border-black p-2 w-6 text-center">:</td>
                            <td class="border border-black p-2">SKH-000024</td>
                        </tr>

                    </tbody>
                </table>
            </td>
            <td style="text-align:center; vertical-align:middle;"><?= htmlspecialchars($data['keluar_dppu']) ?></td>
            <td class="ttd" style="vertical-align: middle;">
                <img src="https://i.pinimg.com/originals/bc/f2/11/bcf211ef4cbf4f8883079941942ccb9c.png"
                    alt="Logo Pertamina">
                <div class="nama"><?= htmlspecialchars($data['diperiksa_segel']) ?></div>
            </td>
        </tr>
    </tbody>
</table>

<div class="catatan">
    <strong>CATATAN :</strong>
    <ul>
        <li>Pemeriksaan ini wajib dilakukan oleh Petugas RSD yang bertugas sebagai fungsi kontrol atas asset milik
            Negara Republik Indonesia yang diamanahkan kepada PT. Pertamina (Persero).</li>
        <li>Bila pada saat bridger masuk terdapat ketidaksesuaian agar dilaporkan kepada pimpinan Receiving Storage &
            Distribution.</li>
        <li>Bila pada saat pemeriksaan bagian dalam kompartemen masih terdapat produk bahan bakar, ataupun hal yang
            mencurigakan agar bridger tidak diijinkan keluar terlebih dahulu sebelum isi kompartemen benar-benar kosong.
        </li>
        <li>Jika terdapat kolom yang tidak ada pada Bridger pada saat pemeriksaan namun bukan sebagai persyaratan wajib
            maka dapat ditulis “N/A”.</li>
        <li>Misalnya bridger kapasitas 16 KL.</li>
    </ul>
</div>
<?php
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Form Pemeriksaan - Hasil Modifikasi</title>
    <style>
    body {
        font-family: Arial, sans-serif;
        margin: 0;
        padding: 0;
    }

    .print-container {
        width: 100%;
        border: 1px solid #000;
        box-sizing: border-box;
        padding: 10mm;
        margin: 0 auto;
        background: #fff;
    }

    .header-table {
        border-collapse: collapse;
        /* width dihapus agar lebar tabel otomatis sesuai konten */
        font-size: 11px;
        margin-bottom: 10px;
        border: 1px solid #000;
        /* Warna border diubah menjadi hitam */
    }

    .header-table td {
        padding: 4px 6px;
        /* Padding diperkecil agar lebih rapat */
        border: 1px solid #000;
        /* Warna border diubah menjadi hitam */
    }

    .header-table .label {
        font-weight: bold;
        /* width dihapus agar lebar kolom menyesuaikan otomatis */
    }

    .main-table {
        width: 100%;
        border-collapse: collapse;
        margin-bottom: 5px;
        font-size: 11px;
    }

    .main-table th,
    .main-table td {
        border: 1px solid #000;
        padding: 2px 5px;
        text-align: left;
        vertical-align: top;
    }

    .main-table th {
        font-weight: 600;
        /* Font bold tidak terlalu tebal */
        text-align: center;
        vertical-align: middle;
        background: #fff;
        /* Tidak ada background abu-abu */
    }

    .main-table tbody td {
        height: 20px;
        /* Memberi tinggi minimum pada sel data */
    }

    .main-table .ttd {
        text-align: center;
        vertical-align: top;
        padding-top: 5px;
        position: relative;
        height: 150px;
        /* Menentukan tinggi area TTD */
    }

    .main-table .ttd img {
        max-width: 60px;
        margin-bottom: 2px;
        position: absolute;
        left: 50%;
        transform: translateX(-50%);
        top: 15px;
        /* Posisi vertikal logo */
        opacity: 0.8;
    }

    .main-table .ttd .nama {
        font-weight: 600;
        font-size: 1em;
        position: absolute;
        bottom: 10px;
        /* Posisi nama di bawah */
        left: 50%;
        transform: translateX(-50%);
        text-decoration: underline;
    }

    .main-table .sub {
        font-size: 0.9em;
        font-weight: normal;
        font-style: italic;
    }

    .section-title {
        text-align: center;
        font-weight: 600;
        margin: 10px 0 3px 0;
        font-size: 12px;
        text-decoration: underline;
    }

    .catatan {
        font-size: 10px;
        margin-top: 10px;
    }

    .catatan ul {
        margin: 2px 0 0 15px;
        padding: 0;
        list-style-type: disc;
    }

    .catatan li {
        margin-bottom: 2px;
    }

    .segel-table {
        width: 100%;
        border-collapse: collapse;
        font-size: 11px;
    }

    .segel-table td {
        /* border-collapse: collapse; */
    }

    .segel-table td:first-child {
        width: 150px;
        /* Lebar kolom nama segel */
    }

    @media print {
        @page {
            size: A4 portrait;
            margin: 8mm;
        }

        body {
            margin: 0;
            font-size: 10px;
            -webkit-print-color-adjust: exact;
        }

        .print-container {
            border: 1px solid #000;
            width: 100%;
            height: calc(297mm - 16mm);
            /* Tinggi A4 - 2x margin halaman */
            padding: 5mm;
            margin: 0;
            page-break-inside: avoid;
        }

        /* Menyesuaikan ukuran font dan padding untuk print */
        .header-table,
        .main-table,
        .segel-table {
            font-size: 10px;

        }

        .catatan {
            font-size: 9px;
        }

        .main-table th,
        .main-table td {
            padding: 1px 4px;
        }

        .main-table tbody td {
            height: 18px;
        }

    }
    </style>
</head>

<body>
    <div class="print-container">
        <?php print_form($data); ?>
    </div>

    <script>
    window.onload = function() {
        window.print();
    };
    </script>
</body>

</html>