<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Form Pemeriksaan</title>
    <script src="https://cdn.tailwindcss.com"></script>

    <style>
    body {
        font-family: Arial, sans-serif;
        font-size: 12px;
    }

    table {
        border-collapse: collapse;
        width: 100%;
        margin-bottom: 10px;
    }

    th,
    td {
        border: 1px solid black;
        padding: 4px 6px;
        text-align: left;
    }

    .no-border td {
        border: none;
    }

    .header-table td {
        padding: 2px 5px;
    }

    .section-title {
        text-align: center;
        font-weight: bold;
        margin: 10px 0;
    }

    .signature-box {
        border: 1px solid black;
        padding: 10px;
        width: 250px;
        text-align: center;
        float: right;
        margin-top: 10px;
    }

    .signature-box img {
        max-width: 80px;
        margin-bottom: 5px;
    }

    .clear {
        clear: both;
    }

    .oke-box {
        display: flex;
        justify-content: space-between;
        font-weight: bold;
        padding-top: 5px;
    }

    .catatan {
        font-size: 11px;
        margin-top: 10px;
    }

    .header-table {
        width: auto;
        margin-left: 0;
        font-size: 14px;
    }

    .header-table td:first-child {
        padding-right: 10px;
    }
    </style>
</head>

<body>

    <table class="header-table" style="width: auto; margin-left: 0; font-size: 14px;">
        <tr>
            <td style="padding-right: 10px;">Tanggal</td>
            <td>: Monday, 29 April 2024</td>
        </tr>
        <tr>
            <td style="padding-right: 10px;">No. Polisi</td>
            <td>: B 9380 PEJ</td>
        </tr>
        <tr>
            <td style="padding-right: 10px;">Volume Bridger</td>
            <td>: 32000</td>
        </tr>
        <tr>
            <td style="padding-right: 10px;">Masa Berlaku Tera Tangki Bridger</td>
            <td>: 2025-08-07</td>
        </tr>
    </table>


    <div class="section-title">
        PEMERIKSAAN DAN PENCATATAN MINIMAL 10 MENIT SETELAH SETTLING TIME
    </div>

    <table border="1" cellspacing="0" cellpadding="5">
        <tr>
            <th colspan="2" class="text-center">JARAK T1 PADA DOKUMEN KALIBRASI</th>
            <th colspan="3" class="text-center">JARAK CAIRAN TERHADAP T1 (ULLAGE) @ SUPPLY POINT</th>
            <th colspan="2" class="text-center">DIPERIKSA & DICATAT OLEH</th>
        </tr>
        <tr>
            <th>JARAK (MM)</th>
            <th>TEMP (°C)</th>
            <th>JARAK (MM)</th>
            <th>DENSITY OBA (Kg/L)</th>
            <th>TEMP (°C)</th>
            <th colspan="2" rowspan="7" style="text-align: center; vertical-align: top;">
                <div class="signature-box">
                    <img src="../image/pertamina.jpg" alt="Logo Pertamina" class="mx-auto" style="max-width: 80px;">
                    <div style="margin-top: 5px; font-size: 10px;">Jr Supervisor | Rec., Sto. & Dist.</div>
                    <strong>HERMANTO PURBA</strong><br>
                    <div style="margin-top: 10px;">Hermanto Purba</div>
                </div>
            </th>
        </tr>
        <tr>
            <td>KOMP. 1: 181</td>
            <td>30</td>
            <td>KOMP. 1: 30</td>
            <td>0.79</td>
            <td>30</td>
        </tr>
        <tr>
            <td>KOMP. 2: 200</td>
            <td>30</td>
            <td>KOMP. 2: 30</td>
            <td>0.79</td>
            <td>30</td>
        </tr>
        <tr>
            <td>KOMP. 3: 193</td>
            <td>30</td>
            <td>KOMP. 3: 30</td>
            <td>0.79</td>
            <td>30</td>
        </tr>
        <tr>
            <td>KOMP. 4: 189</td>
            <td>30</td>
            <td>KOMP. 4: 30</td>
            <td>0.79</td>
            <td>30</td>
        </tr>
        <tr>
            <td colspan="2" style=";"><strong>KETERANGAN :</strong></td>
            <td colspan="3" style=";"><strong>KETERANGAN :</strong></td>
        </tr>
    </table>




    <div class="clear"></div>

    <div class="section-title">PEMERIKSAAN OLEH SECURITY SEBELUM KELUAR LOKASI</div>

    <table class="text-center">
        <tr class="text-center">
            <th class="text-center">NOMOR/KODE SEGEL</th>
            <th class="text-center">JAM KELUAR</th>
            <th class="text-center">DIPERIKSA & DICATAT OLEH</th>
        </tr>
        <tr>
            <td colspan="1">
                <div class="w-full border border-black">
                    <div class="flex divide-x divide-black">
                        <!-- Kolom Kiri -->
                        <div class="w-1/2">
                            <div class="border-b border-black p-1">MAINHOLE 1 :</div>
                            <div class="border-b border-black p-1">MAINHOLE 2 :</div>
                            <div class="border-b border-black p-1">MAINHOLE 3 :</div>
                            <div class="border-b border-black p-1">MAINHOLE 4 :</div>
                            <div class="border-b border-black p-1">BOTTOM LOADER COVER :</div>
                            <div class="border-b border-black p-1">BOTTOM LOADER VALVE 1 :</div>
                            <div class="border-b border-black p-1">BOTTOM LOADER VALVE 2 :</div>
                            <div class="border-b border-black p-1">BOTTOM LOADER VALVE 3 :</div>
                            <div class="p-1">BOTTOM LOADER VALVE 4</div>
                        </div>

                        <!-- Kolom Kanan -->
                        <div class="w-1/2 text-center">
                            <div class="border-b border-black p-1">SKH-029209</div>
                            <div class="border-b border-black p-1">SKH-029210</div>
                            <div class="border-b border-black p-1">SKH-029211</div>
                            <div class="border-b border-black p-1">SKH-029212</div>
                            <div class="border-b border-black p-1">SKH-029213</div>
                            <div class="border-b border-black p-1">SKH-029214</div>
                            <div class="border-b border-black p-1">SKH-029215</div>
                            <div class="border-b border-black p-1">SKH-029216</div>
                            <div class="p-1">SKH-029217</div>
                        </div>
                    </div>
                </div>
            </td>


            <td style="text-align: center;">16:00:00</td>
            <td class="text-center align-middle">
                <div class="flex flex-col items-center justify-center">
                    <img src="../image/pertamina.jpg" alt="Logo Pertamina" class="mx-auto max-w-[80px]">
                    <div class="mt-1 text-[15px]">Jr Supervisor | Rec., Sto. & Dist.</div>
                    <strong class="font-bold">HERMANTO PURBA</strong>
                    <div class="mt-2">Hermanto Purba</div>
                </div>
            </td>

        </tr>

    </table>

    <table>
        <td colspan="1">
            <div class="catatan p-4">
                <strong>CATATAN :</strong>
                <ul>
                    <li>Pemeriksaan ini wajib dilakukan oleh Petugas RSD yang bertugas sebagai fungsi kontrol atas asset
                        milik
                        Negara Republik Indonesia yang diamanahkan kepada PT. Pertamina (Persero).</li>
                    <li>Bila pada saat bridger masuk terdapat ketidaksesuaian agar dilaporkan kepada pimpinan Receiving
                        Storage
                        & Distribution.</li>
                    <li>Bila pada saat pemeriksaan bagian dalam kompartemen masih terdapat produk bahan bakar, ataupun
                        hal
                        yang
                        mencurigakan agar bridger tidak diijinkan keluar terlebih dahulu sebelum isi kompartemen
                        benar-benar
                        kosong.</li>
                    <li>Jika terdapat kolom yang tidak ada pada Bridger pada saat pemeriksaan namun bukan sebagai
                        persyaratan
                        wajib maka dapat ditulis “N/A”.</li>
                    <li>Misalnya bridger kapasitas 16 KL.</li>
                </ul>
            </div>
        </td>

    </table>

</body>

</html> modiifkasi full implementasikan filednya tadi agar sesuai datanya yg muncul di database