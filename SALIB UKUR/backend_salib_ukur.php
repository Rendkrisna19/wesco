<?php
include '../config/koneksi.php';

// ============================
// 1. AJAX: Ambil data AFRN + relasi jika idSegel kosong
// ============================
if (isset($_GET['get_data']) && isset($_GET['id_afrn']) && empty($_GET['idSegel'])) {
  header('Content-Type: application/json');
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
                bon.tgl_rekam
            FROM afrn
        LEFT JOIN bridger ON afrn.id_bridger = bridger.id_bridger
        LEFT JOIN salib_ukur ON salib_ukur.id_afrn = afrn.id_afrn
     LEFT JOIN bon ON afrn.id_bon = bon.id_bon
        LEFT JOIN segel ON segel.id_ukur = salib_ukur.id_ukur
        LEFT JOIN jarak_t1 ON salib_ukur.id_jarak_t1 = jarak_t1.id_jarak_t1
        LEFT JOIN jarak_cair_t1 ON salib_ukur.id_jarak_cair_t1 = jarak_cair_t1.id_jarak_cair_t1
        WHERE afrn.id_afrn = '$id_afrn'
        LIMIT 1";

  $result = mysqli_query($conn, $query);
  if ($result && mysqli_num_rows($result) > 0) {
    $data = mysqli_fetch_assoc($result);
    echo json_encode($data);
  } else {
    echo json_encode(['error' => 'Data tidak ditemukan']);
  }
  exit;
}

// ============================
// 2. Ambil data AFRN untuk dropdown
// ============================
$query_afrn = "SELECT id_afrn, no_afrn FROM afrn ORDER BY id_afrn DESC LIMIT 1";
$result_afrn = mysqli_query($conn, $query_afrn);

    // ============================
    // 3. Proses Insert Data Lengkap
    // ============================
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit'])) {
        mysqli_begin_transaction($conn);
        try {
            // 1. INSERT ke jarak_t1
            $stmt1 = mysqli_prepare($conn, "INSERT INTO jarak_t1 (jarak_komp1, jarak_komp2, jarak_komp3, jarak_komp4, temp_komp1, temp_komp2, temp_komp3, temp_komp4) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
            if (!$stmt1) throw new Exception("Query jarak_t1 gagal: " . mysqli_error($conn));
            mysqli_stmt_bind_param($stmt1, "dddddddd",
                $_POST['jarak_komp1'], $_POST['jarak_komp2'], $_POST['jarak_komp3'], $_POST['jarak_komp4'],
                $_POST['temp_komp1'], $_POST['temp_komp2'], $_POST['temp_komp3'], $_POST['temp_komp4']
            );
            if (!mysqli_stmt_execute($stmt1)) throw new Exception("Eksekusi jarak_t1 gagal: " . mysqli_stmt_error($stmt1));
            $id_jarak_t1 = mysqli_insert_id($conn);
            mysqli_stmt_close($stmt1);

            // 2. INSERT ke jarak_cair_t1
            $stmt2 = mysqli_prepare($conn, "INSERT INTO jarak_cair_t1 (jarak_cair_komp1, jarak_cair_komp2, jarak_cair_komp3, jarak_cair_komp4, dencity_cair_komp1, dencity_cair_komp2, dencity_cair_komp3, dencity_cair_komp4, temp_cair_komp_komp1, temp_cair_komp_komp2, temp_cair_komp_komp3, temp_cair_komp_komp4) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
            if (!$stmt2) throw new Exception("Query jarak_cair_t1 gagal: " . mysqli_error($conn));
            mysqli_stmt_bind_param($stmt2, "dddddddddddd",
                $_POST['jarak_cair_komp1'], $_POST['jarak_cair_komp2'], $_POST['jarak_cair_komp3'], $_POST['jarak_cair_komp4'],
                $_POST['dencity_cair_komp1'], $_POST['dencity_cair_komp2'], $_POST['dencity_cair_komp3'], $_POST['dencity_cair_komp4'],
                $_POST['temp_cair_komp_komp1'], $_POST['temp_cair_komp_komp2'], $_POST['temp_cair_komp_komp3'], $_POST['temp_cair_komp_komp4']
            );
            if (!mysqli_stmt_execute($stmt2)) throw new Exception("Eksekusi jarak_cair_t1 gagal: " . mysqli_stmt_error($stmt2));
            $id_jarak_cair_t1 = mysqli_insert_id($conn);
            mysqli_stmt_close($stmt2);

            // 3. INSERT ke salib_ukur
            $stmt3 = mysqli_prepare($conn, "INSERT INTO salib_ukur (id_afrn, id_jarak_t1, id_jarak_cair_t1, ket_jarak_t1, ket_jarak_cair_t1, diperiksa_t1, diperiksa_segel) VALUES (?, ?, ?, ?, ?, ?, ?)");
            if (!$stmt3) throw new Exception("Query salib_ukur gagal: " . mysqli_error($conn));
            mysqli_stmt_bind_param($stmt3, "iiissss",
                $_POST['id_afrn'], $id_jarak_t1, $id_jarak_cair_t1,
                $_POST['ket_jarak_t1'], $_POST['ket_jarak_cair_t1'],
                $_POST['diperiksa_t1'], $_POST['diperiksa_segel']
            );
            if (!mysqli_stmt_execute($stmt3)) throw new Exception("Eksekusi salib_ukur gagal: " . mysqli_stmt_error($stmt3));
            $id_ukur = mysqli_insert_id($conn);
            mysqli_stmt_close($stmt3);

            // 4. INSERT ke segel
            $stmt4 = mysqli_prepare($conn, "INSERT INTO segel (id_ukur, mainhole1, mainhole2, mainhole3, mainhole4, bottom_load_cov1, bottom_load_cov2, bottom_load_cov3, bottom_load_cov4, bottom_load_cov5) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
            if (!$stmt4) throw new Exception("Query segel gagal: " . mysqli_error($conn));
            mysqli_stmt_bind_param($stmt4, "isssssssss", $id_ukur,
                $_POST['mainhole1'], $_POST['mainhole2'], $_POST['mainhole3'], $_POST['mainhole4'],
                $_POST['bottom_load_cov1'], $_POST['bottom_load_cov2'], $_POST['bottom_load_cov3'], $_POST['bottom_load_cov4'], $_POST['bottom_load_cov5']
            );
            
            if (!mysqli_stmt_execute($stmt4)) throw new Exception("Eksekusi segel gagal: " . mysqli_stmt_error($stmt4));
            mysqli_stmt_close($stmt4);

            mysqli_commit($conn);
            echo "<script>alert('Data berhasil disimpan.'); window.location.href='insert_salibukur.php';</script>";
            exit;
        } catch (Exception $e) {
            mysqli_rollback($conn);
            $msg = htmlspecialchars($e->getMessage(), ENT_QUOTES);
            echo "<script>alert('Gagal menyimpan data: $msg');</script>";
        }
        exit;
    }

    