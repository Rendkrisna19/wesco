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
function print_form($data) {
?>
<table class="header-table">
    <tr>
        <td>Tanggal</td>
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

<table class="main-table">
    <tr>
        <th colspan="2">JARAK T1 PADA DOKUMEN KALIBRASI</th>
        <th colspan="3">JARAK CAIRAN TERHADAP T1 (ULLAGE) @ SUPPLY POINT</th>
        <th colspan="2">
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
        <th colspan="2" rowspan="7" class="ttd">
            <img src="../image/stempel.png" alt="Logo Pertamina">
            <div class="nama"><?= htmlspecialchars($data['diperiksa_t1']) ?></div>
        </th>
    </tr>
    <?php for($i=1;$i<=4;$i++): ?>
    <tr>
        <td>KOMP. <?= $i ?>: <?= htmlspecialchars($data["jarak_komp$i"]) ?></td>
        <td><?= htmlspecialchars($data["temp_komp$i"]) ?></td>
        <td>KOMP. <?= $i ?>: <?= htmlspecialchars($data["jarak_cair_komp$i"]) ?></td>
        <td><?= htmlspecialchars($data["dencity_cair_komp$i"]) ?></td>
        <td><?= htmlspecialchars($data["temp_cair_komp_komp$i"]) ?></td>
    </tr>
    <?php endfor; ?>
    <tr>
        <td colspan="2"><strong>KETERANGAN :</strong></td>
        <td colspan="3"><strong>KETERANGAN :</strong></td>
    </tr>
    <tr>
        <td colspan="2"><?= nl2br(htmlspecialchars($data['ket_jarak_t1'])) ?></td>
        <td colspan="3"><?= nl2br(htmlspecialchars($data['ket_jarak_cair_t1'])) ?></td>
    </tr>
</table>

<div class="section-title">PEMERIKSAAN OLEH SECURITY SEBELUM KELUAR LOKASI</div>
<table class="main-table">
    <tr>
        <th>NOMOR/KODE SEGEL</th>
        <th>JAM KELUAR</th>
        <th colspan="2">
            <div>DIPERIKSA & DICATAT OLEH</div>
            <div class="sub">(Nama & Tanda Tangan)</div>
        </th>
    </tr>
    <tr>
        <td>
            <table class="segel-table">
                <tr>
                    <td>MAINHOLE 1</td>
                    <td><?= htmlspecialchars($data['mainhole1']) ?></td>
                </tr>
                <tr>
                    <td>MAINHOLE 2</td>
                    <td><?= htmlspecialchars($data['mainhole2']) ?></td>
                </tr>
                <tr>
                    <td>MAINHOLE 3</td>
                    <td><?= htmlspecialchars($data['mainhole3']) ?></td>
                </tr>
                <tr>
                    <td>MAINHOLE 4</td>
                    <td><?= htmlspecialchars($data['mainhole4']) ?></td>
                </tr>
                <tr>
                    <td>BOTTOM LOAD COVER 1</td>
                    <td><?= htmlspecialchars($data['bottom_load_cov1']) ?></td>
                </tr>
                <tr>
                    <td>BOTTOM LOAD COVER 2</td>
                    <td><?= htmlspecialchars($data['bottom_load_cov2']) ?></td>
                </tr>
                <tr>
                    <td>BOTTOM LOAD COVER 3</td>
                    <td><?= htmlspecialchars($data['bottom_load_cov3']) ?></td>
                </tr>
                <tr>
                    <td>BOTTOM LOAD COVER 4</td>
                    <td><?= htmlspecialchars($data['bottom_load_cov4']) ?></td>
                </tr>
                <tr>
                    <td>BOTTOM LOAD COVER 5</td>
                    <td><?= htmlspecialchars($data['bottom_load_cov5']) ?></td>
                </tr>
            </table>
        </td>
        <td style="text-align:center; vertical-align:middle;"><?= htmlspecialchars($data['keluar_dppu']) ?></td>
        <td colspan="2" class="ttd">
            <img src="../image/stempel.png" alt="Logo Pertamina">
            <div class="nama"><?= htmlspecialchars($data['diperiksa_segel']) ?></div>
        </td>
    </tr>
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
    <title>Form Pemeriksaan</title>
    <style>
    @media print {
        @page {
            size: A4 landscape;
            margin: 5mm;
            /* Mengurangi margin halaman */
        }

        body {
            margin: 0;
            padding: 0;
            font-size: 9px;
            /* Mengurangi ukuran font dasar */
            -webkit-print-color-adjust: exact;
            /* Pastikan warna latar belakang dicetak */
        }

        .wrapper {
            display: flex;
            flex-direction: row;
            justify-content: space-between;
            align-items: flex-start;
            width: 100%;
            gap: 5mm;
            /* Mengurangi jarak antar kontainer */
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            /* Pastikan padding dan border termasuk dalam lebar */
        }

        .print-container {
            width: 49%;
            /* Tetap 49% untuk dua kolom */
            border: 1px solid #000;
            box-sizing: border-box;
            padding: 5mm 6mm;
            /* Mengurangi padding internal */
            margin: 0;
            min-height: calc(210mm - 10mm);
            /* Tinggi A4 - 2x margin halaman */
            height: auto;
            /* Biarkan tinggi menyesuaikan konten */
            background: #fff;
            page-break-inside: avoid;
            /* Hindari pemotongan kontainer di tengah halaman */
        }

        .header-table {
            width: 100%;
            font-size: 10px;
            /* Font lebih kecil */
            margin-bottom: 4px;
            /* Margin lebih kecil */
        }

        .header-table td {
            padding: 1px 3px;
            /* Padding lebih kecil */
        }

        .main-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 4px;
            /* Margin lebih kecil */
            font-size: 10px;
            /* Font lebih kecil */
        }

        .main-table th,
        .main-table td {
            border: 1px solid #000;
            padding: 2px 3px;
            /* Padding lebih kecil */
            text-align: left;
        }

        .main-table th {
            background: #f3f3f3;
            font-weight: bold;
            text-align: center;
        }

        .main-table .ttd {
            text-align: center;
            vertical-align: top;
            padding-top: 5px;
            /* Padding lebih kecil */
            position: relative;
            /* Untuk posisi gambar stempel */
        }

        .main-table .ttd img {
            max-width: 50px;
            /* Ukuran stempel lebih kecil */
            margin-bottom: 2px;
            /* Margin lebih kecil */
            position: absolute;
            left: 50%;
            transform: translateX(-50%);
            top: 10px;
            /* Sesuaikan posisi vertikal stempel */
            opacity: 0.7;
            /* Membuat stempel sedikit transparan */
        }

        .main-table .ttd .nama {
            font-weight: bold;
            font-size: 0.9em;
            /* Font lebih kecil */
            margin-top: 50px;
            /* Sesuaikan dengan tinggi gambar stempel */
        }


        .main-table .sub {
            font-size: 0.8em;
            /* Font lebih kecil */
            font-style: italic;
        }

        .section-title {
            text-align: center;
            font-weight: bold;
            margin: 4px 0 3px 0;
            /* Margin lebih kecil */
            font-size: 11px;
            /* Font lebih kecil */
        }

        .catatan {
            font-size: 9px;
            /* Font lebih kecil */
            margin-top: 4px;
            /* Margin lebih kecil */
        }

        .catatan ul {
            margin: 0 0 0 10px;
            /* Margin lebih kecil */
            padding: 0;
        }

        .catatan li {
            margin-bottom: 1px;
            /* Margin lebih kecil */
        }

        .segel-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 9px;
            /* Font lebih kecil */
            margin-top: -3px;
            /* Sesuaikan jika ada spasi berlebih */
        }

        .segel-table td {
            border: none;
            padding: 0.5px 1px;
            /* Padding lebih kecil */
        }
    }
    </style>
</head>

<body>
    <div class="wrapper">
        <div class="print-container">
            <?php print_form($data); ?>
        </div>
        <div class="print-container">
            <?php print_form($data); ?>
        </div>
    </div>
    <script>
    window.onload = function() {
        window.print();
    };
    </script>
</body>

</html>