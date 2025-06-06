<?php
session_start(); 


if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    // If not logged in, redirect to login page
    header("Location: ../auth/index.php");
    exit;
}

$id_user = $_SESSION['id_user'];
$username = $_SESSION['username'];
// Assuming nama_lengkap is also set in session from login
$nama_lengkap = isset($_SESSION['nama_lengkap']) ? $_SESSION['nama_lengkap'] : $username;
include('backend_salib_ukur.php');
include('ajax_salibukur.php');
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pencatatan Hasil Pemeriksaan Bridger</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
    .form-section {
        background: #f8f9fa;
    }

    .input-disabled {
        background-color: #e9ecef !important;
        cursor: not-allowed !important;
        color: #6c757d !important;
    }
    </style>
</head>

<body class="bg-white font-modify">
    <div class="flex min-h-screen">

        <!-- Sidebar -->
        <div class="w-64 bg-white shadow-md">
            <?php include '../components/slidebar.php'; ?>
        </div>

        <!-- Main content -->
        <div class="flex-1 flex flex-col">
            <!-- Header -->
            <div class="bg-white shadow p-6 flex justify-between items-center">
                <h1 class="text-2xl font-bold text-cyan-700">Selamat Datang di Wesco,
                    <?= htmlspecialchars($nama_lengkap) ?>!</h1>
                <div class="flex items-center space-x-3">
                    <span class="text-gray-600"><?= htmlspecialchars($nama_lengkap) ?></span>
                    <img src="https://media.istockphoto.com/id/1300845620/id/vektor/ikon-pengguna-datar-terisolasi-pada-latar-belakang-putih-simbol-pengguna-ilustrasi-vektor.jpg?s=612x612&w=0&k=20&c=QN0LOsRwA1dHZz9lsKavYdSqUUnis3__FQLtZTQ--Ro="
                        alt="User" class="w-8 h-8 rounded-full">
                </div>
            </div>
            <div class="max-w-7xl mx-auto bg-white rounded-lg shadow-lg">
                <!-- Header -->
                <div class="text-gray-700 p-4 rounded-t-lg">
                    <h1 class="text-lg font-normal">PENCATATAN HASIL PEMERIKSAAN BRIDGER SEBELUM KELUAR LOKASI</h1>
                </div>

                <!-- Pilih AFRN Section -->
                <div class="p-6">


                    <!-- Form Data Utama -->
                    <form action="backend_salib_ukur.php" method="POST" class="p-4 bg-white shadow-md rounded"
                        id="salibForm">

                        <div class="grid grid-cols-5 gap-4 mb-6">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">No. AFRN</label>
                                <select id="no_afrn" onchange="loadAfrnData()" name="id_afrn"
                                    class="w-full bg-blue-700 text-white px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                                    <option value="">Pilih AFRN</option>
                                    <?php while ($row = mysqli_fetch_assoc($result_afrn)): ?>
                                    <option value="<?= $row['id_afrn'] ?>"><?= $row['no_afrn'] ?></option>
                                    <?php endwhile; ?>
                                </select>


                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal</label>
                                <input type="date" id="tanggal" name="tgl_afrn"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md input-disabled" readonly>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">No Polisi</label>
                                <input type="text" id="no_polisi" placeholder="BKCONTOH" name="no_polisi"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md input-disabled" readonly>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Volume Bridger</label>
                                <input type="text" id="volume_bridger" placeholder="123" name="volume"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md input-disabled" readonly>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Masa Berlaku Tangki</label>
                                <input type="date" id="tgl_serti_akhir" name=""
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md input-disabled" readonly>
                            </div>
                        </div>

                        <!-- Informasi Pemeriksaan -->
                        <div class="bg-gray-50 p-4 rounded-md mb-6">
                            <p class="text-center text-sm text-gray-600 font-medium">
                                PEMERIKSAAN DAN PENCATATAN MINIMAL 10 MENIT SETELAH SETTLING TIME
                            </p>
                        </div>

                        <!-- Jarak T1 dan Jarak Cairan -->
                        <div class="grid grid-cols-2 gap-8">
                            <!-- Jarak T1 Pada Dokumen -->
                            <div>
                                <h3 class="text-center font-normal text-gray-700 mb-4">JARAK T1 PADA DOKUMEN</h3>

                                <!-- Komp 1 -->
                                <div class="grid grid-cols-2 gap-2 mb-3">
                                    <div>
                                        <label class="block text-xs font-medium text-gray-600 mb-1">Jarak
                                            (mm)</label>
                                        <input id="jarak_komp1" name="jarak_komp1" type="number" step="0.1" value="0"
                                            class="w-full px-2 py-1 border border-gray-300 rounded text-sm input-disabled"
                                            placeholder="Data kosong" readonly>

                                    </div>
                                    <div>
                                        <label class="block text-xs font-medium text-gray-600 mb-1">Temp</label>
                                        <input name="temp_komp1" type="number" step="0.1"
                                            class="w-full px-2 py-1 border border-gray-300 rounded text-sm"
                                            placeholder="0">
                                    </div>
                                </div>
                                <div class="text-xs text-gray-500 mb-3">Komp 1.</div>

                                <!-- Komp 2 -->
                                <div class="grid grid-cols-2 gap-2 mb-3">
                                    <div>
                                        <label class="block text-xs font-medium text-gray-600 mb-1">Jarak (mm)</label>
                                        <input id="jarak_komp2" name="jarak_komp2" type="number" step="0.1"
                                            class="w-full px-2 py-1 border border-gray-300 rounded text-sm input-disabled"
                                            placeholder="Data kosong" readonly value="0">
                                    </div>
                                    <div>
                                        <label class="block text-xs font-medium text-gray-600 mb-1">Temp</label>
                                        <input name="temp_komp2" type="number" step="0.1" placeholder="0"
                                            class="w-full px-2 py-1 border border-gray-300 rounded text-sm ">
                                    </div>
                                </div>
                                <div class="text-xs text-gray-500 mb-3">Komp 2.</div>

                                <!-- Komp 3 -->
                                <div class="grid grid-cols-2 gap-2 mb-3">
                                    <div>
                                        <label class="block text-xs font-medium text-gray-600 mb-1">Jarak (mm)</label>
                                        <input id="jarak_komp3" name="jarak_komp3" type="number" step="0.1"
                                            class="w-full px-2 py-1 border border-gray-300 rounded text-sm input-disabled"
                                            placeholder="Data kosong" readonly value="0">
                                    </div>
                                    <div>
                                        <label class="block text-xs font-medium text-gray-600 mb-1">Temp</label>
                                        <input name="temp_komp3" type="number" step="0.1" placeholder="0"
                                            class="w-full px-2 py-1 border border-gray-300 rounded text-sm ">
                                    </div>
                                </div>
                                <div class="text-xs text-gray-500 mb-3">Komp 3.</div>

                                <!-- Komp 4 -->
                                <div class="grid grid-cols-2 gap-2 mb-3">
                                    <div>
                                        <label class="block text-xs font-medium text-gray-600 mb-1">Jarak (mm)</label>
                                        <input id="jarak_komp4" name="jarak_komp4" type="number" step="0.1"
                                            class="w-full px-2 py-1 border border-gray-300 rounded text-sm input-disabled"
                                            placeholder="Data kosong" readonly value="0">
                                    </div>
                                    <div>
                                        <label class="block text-xs font-medium text-gray-600 mb-1">Temp</label>
                                        <input name="temp_komp4" type="number" step="0.1" placeholder="0"
                                            class="w-full px-2 py-1 border border-gray-300 rounded text-sm ">
                                    </div>
                                </div>
                                <div class="text-xs text-gray-500 mb-4">Komp 4.</div>

                                <!-- Keterangan -->
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Keterangan</label>
                                    <textarea class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm"
                                        rows="3" name="ket_jarak_t1"
                                        placeholder="Tell us about your use case..."></textarea>
                                </div>
                            </div>

                            <!-- Jarak Cairan Terhadap T1 -->
                            <div>
                                <h3 class="text-center font-normal text-gray-700 mb-2">JARAK CAIRAN TERHADAP T1</h3>
                                <p class="text-center text-xs text-gray-500 mb-4">(ULLAGE) @ SUPPLY POINT</p>

                                <!-- Komp 1 -->
                                <div class="grid grid-cols-3 gap-2 mb-3">
                                    <div>
                                        <label class="block text-xs font-medium text-gray-600 mb-1">Jarak (mm)</label>
                                        <input type="number" step="0.1" placeholder="0" name="jarak_cair_komp1"
                                            class="w-full px-2 py-1 border border-gray-300 rounded text-sm ">
                                    </div>
                                    <div>
                                        <label class="block text-xs font-medium text-gray-600 mb-1">Density OBS
                                            (KG/L)</label>
                                        <input type="number" step="0.01" id="dencity_cair_komp1"
                                            name="dencity_cair_komp1" placeholder="Data kosong"
                                            class="w-full px-2 py-1 border border-gray-300 rounded text-sm input-disabled"
                                            readonly value="0">
                                    </div>
                                    <div>
                                        <label class="block text-xs font-medium text-gray-600 mb-1">TEMP (C)</label>
                                        <input type="number" step="0.1" id="temp_cair_komp_komp1"
                                            name="temp_cair_komp_komp1" placeholder="Data kosong"
                                            class="w-full px-2 py-1 border border-gray-300 rounded text-sm input-disabled"
                                            readonly value="0">
                                    </div>
                                </div>

                                <!-- Komp 2 -->
                                <div class="grid grid-cols-3 gap-2 mb-3">
                                    <div>
                                        <label class="block text-xs font-medium text-gray-600 mb-1">Jarak (mm)</label>
                                        <input type="number" step="0.1" placeholder="0" name="jarak_cair_komp2"
                                            class="w-full px-2 py-1 border border-gray-300 rounded text-sm ">
                                    </div>
                                    <div>
                                        <label class="block text-xs font-medium text-gray-600 mb-1">Density OBS
                                            (KG/L)</label>
                                        <input type="number" step="0.01" id="dencity_cair_komp2"
                                            name="dencity_cair_komp2" placeholder="Data kosong"
                                            class="w-full px-2 py-1 border border-gray-300 rounded text-sm input-disabled"
                                            readonly value="0">
                                    </div>
                                    <div>
                                        <label class="block text-xs font-medium text-gray-600 mb-1">TEMP (C)</label>
                                        <input type="number" step="0.1" id="temp_cair_komp_komp2"
                                            name="temp_cair_komp_komp2" placeholder="Data kosong"
                                            class="w-full px-2 py-1 border border-gray-300 rounded text-sm input-disabled"
                                            readonly value="0">
                                    </div>
                                </div>

                                <!-- Komp 3 -->
                                <div class="grid grid-cols-3 gap-2 mb-3">
                                    <div>
                                        <label class="block text-xs font-medium text-gray-600 mb-1">Jarak (mm)</label>
                                        <input type="number" step="0.1" placeholder="0" name="jarak_cair_komp3"
                                            class="w-full px-2 py-1 border border-gray-300 rounded text-sm ">
                                    </div>
                                    <div>
                                        <label class="block text-xs font-medium text-gray-600 mb-1">Density OBS
                                            (KG/L)</label>
                                        <input type="number" step="0.01" id="dencity_cair_komp3"
                                            name="dencity_cair_komp3" placeholder="Data kosong"
                                            class="w-full px-2 py-1 border border-gray-300 rounded text-sm input-disabled"
                                            readonly value="0">
                                    </div>
                                    <div>
                                        <label class="block text-xs font-medium text-gray-600 mb-1">TEMP (C)</label>
                                        <input type="number" step="0.1" id="temp_cair_komp_komp3"
                                            name="temp_cair_komp_komp3" placeholder="Data kosong"
                                            class="w-full px-2 py-1 border border-gray-300 rounded text-sm input-disabled"
                                            readonly value="0">
                                    </div>
                                </div>

                                <!-- Komp 4 -->
                                <div class="grid grid-cols-3 gap-2 mb-4">
                                    <div>
                                        <label class="block text-xs font-medium text-gray-600 mb-1">Jarak (mm)</label>
                                        <input type="number" step="0.1" placeholder="0" name="jarak_cair_komp4"
                                            class="w-full px-2 py-1 border border-gray-300 rounded text-sm ">
                                    </div>
                                    <div>
                                        <label class="block text-xs font-medium text-gray-600 mb-1">Density OBS
                                            (KG/L)</label>
                                        <input type="number" step="0.01" id="dencity_cair_komp4"
                                            name="dencity_cair_komp4" placeholder="Data kosong"
                                            class="w-full px-2 py-1 border border-gray-300 rounded text-sm input-disabled"
                                            readonly value="0">
                                    </div>
                                    <div>
                                        <label class="block text-xs font-medium text-gray-600 mb-1">TEMP (C)</label>
                                        <input type="number" step="0.1" id="temp_cair_komp_komp4"
                                            name="temp_cair_komp_komp4" placeholder="Data kosong"
                                            class="w-full px-2 py-1 border border-gray-300 rounded text-sm input-disabled"
                                            readonly value="0">
                                    </div>
                                </div>

                                <!-- Keterangan -->
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Keterangan</label>
                                    <textarea class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm"
                                        rows="3" name="ket_jarak_cair_t1"
                                        placeholder="Tell us about your use case..."></textarea>
                                </div>
                            </div>
                        </div>

                        <!-- Diperiksa & Dicatat Oleh -->
                        <div class="mt-6">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Diperiksa & Dicatat
                                    Oleh</label>
                                <input type="text" class="w-full px-3 py-2 border border-gray-300 rounded-md"
                                    name="diperiksa_t1" placeholder="Hermanto Purba">
                            </div>
                        </div>
                </div>



                <!-- pemeriksaan dan pencacatan minimal 10 menit setelah settiling time -->
                <div class="max-w-7xl mx-auto bg-white p-6 rounded-md shadow space-y-6">
                    <h2 class="text-center text-md font-normal text-gray-700 uppercase">
                        Pemeriksaan dan Pencatatan Minimal 10 Menit Setelah Settling Time
                    </h2>
                    <p class="text-center text-sm font-medium text-gray-600">Nomor/Kode Segel</p>

                    <!-- Grid Segel -->
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                        <div>
                            <label class="text-sm font-normal block mb-1">Mainhole 1</label>
                            <input type="text" class="w-full border rounded px-3 py-2 text-sm" name="mainhole1"
                                id="mainhole1" value="SKH-000023">
                        </div>
                        <div>
                            <label class="text-sm font-normal block mb-1">Mainhole 2</label>
                            <input type="text" class="w-full border rounded px-3 py-2 text-sm" name="mainhole2"
                                id="mainhole2" value="SKH-000023">
                        </div>
                        <div>
                            <label class="text-sm font-normal block mb-1">Mainhole 3</label>
                            <input type="text"" class=" w-full border rounded px-3 py-2 text-sm" name="mainhole3"
                                id="mainhole3" value="SKH-000023">
                        </div>
                        <div>
                            <label class="text-sm font-normal block mb-1">Mainhole 4</label>
                            <input type="text" class="w-full border rounded px-3 py-2 text-sm" name="mainhole4"
                                id="mainhole4" value="SKH-000023">
                        </div>

                        <div>
                            <label class="text-sm font-normal block mb-1">Bottom Loader Cover 1</label>
                            <input type="text" class="w-full border rounded px-3 py-2 text-sm" name="bottom_load_cov1"
                                id="bottom_load_cov1" value="SKH-000023">
                        </div>
                        <div>
                            <label class="text-sm font-normal block mb-1">Bottom Loader Cover 2</label>
                            <input type="text" class="w-full border rounded px-3 py-2 text-sm" name="bottom_load_cov2"
                                id="bottom_load_cov2" value="SKH-000023">
                        </div>
                        <div>
                            <label class="text-sm font-normal block mb-1">Bottom Loader Cover 3</label>
                            <input type="text" class="w-full border rounded px-3 py-2 text-sm" name="bottom_load_cov3"
                                id="bottom_load_cov3" value="SKH-000023">
                        </div>
                        <div>
                            <label class="text-sm font-normal block mb-1">Bottom Loader Cover 4</label>
                            <input type="text" class="w-full border rounded px-3 py-2 text-sm" name="bottom_load_cov4"
                                id="bottom_load_cov4" value="SKH-000023">
                        </div>
                        <div>
                            <label class="text-sm font-normal block mb-1">Bottom Loader Cover 5</label>
                            <input type="text" class="w-full border rounded px-3 py-2 text-sm" name="bottom_load_cov5"
                                id="bottom_load_cov5" value="SKH-000023">

                        </div>
                    </div>

                    <!-- Jam Keluar & Pemeriksa -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="text-sm font-normal block mb-1">Jam Keluar</label>
                            <input type="text" name="tgl_rekam" id="tgl_rekam"
                                class="w-full bg-gray-100 border rounded px-3 py-2 text-sm cursor-not-allowed" readonly
                                disabled>
                        </div>
                        <div>
                            <label class="text-sm font-normal block mb-1">Diperiksa & Dicatat Oleh</label>
                            <input type="text" class="w-full border rounded px-3 py-2 text-sm" name="diperiksa_segel"
                                value="Hermanto Purba">
                        </div>
                    </div>

                    <!-- Tombol -->
                    <div class="flex justify-end gap-2">
                        <a href="index.php" class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded">Batal</a>
                        <button type="submit" name="submit"
                            class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded">Submit</button>
                    </div>
                </div>
            </div>
            </form>


</body>

</html>