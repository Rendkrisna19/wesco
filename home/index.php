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

// Include database connection
// Make sure this path is correct relative to where this dashboard.php file is.
include '../config/koneksi.php';

// --- Fetch data for "ACTUAL 5 LAST ORDER 2024" ---
$last_orders = [];
$sql_last_orders = "SELECT t.nama_trans, b.volume
                    FROM bridger b
                    JOIN transportir t ON b.id_trans = t.id_trans
                    ORDER BY b.tgl_serti_akhir DESC, b.id_bridger DESC
                    LIMIT 5";
$result_last_orders = $conn->query($sql_last_orders);

if ($result_last_orders->num_rows > 0) {
    while ($row = $result_last_orders->fetch_assoc()) {
        $last_orders[] = $row;
    }
}

// --- Fetch data for "GRAFIK PENJUALAN" (Sales Graph) ---
$sales_data = [];
$sql_sales_data = "SELECT DATE_FORMAT(tgl_serti_akhir, '%Y-%m-%d') as sales_date, SUM(volume) as total_volume
                   FROM bridger
                   GROUP BY sales_date
                   ORDER BY sales_date ASC";
$result_sales_data = $conn->query($sql_sales_data);

$labels = [];
$volumes = [];
if ($result_sales_data->num_rows > 0) {
    while ($row = $result_sales_data->fetch_assoc()) {
        $labels[] = $row['sales_date'];
        $volumes[] = $row['total_volume'];
    }
}


// tabel bawah
// Pagination settings
$limit = 3; // Jumlah record per halaman
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $limit;

// Filter tanggal
// Mengambil tanggal default dari gambar: 9 Mei 2025 - 7 Juni 2025
$startDate = isset($_GET['start_date']) ? $_GET['start_date'] : '2025-05-09';
$endDate = isset($_GET['end_date']) ? $_GET['end_date'] : '2025-06-07';

// Query untuk mengambil data AFRN
$query = "SELECT
                a.id_afrn,
                a.no_afrn,
                a.tgl_afrn, -- Menggunakan tgl_afrn sebagai 'Tanggal Isi'
                a.no_bpp,
                b.nama_trans,
                br.no_polisi,
                br.volume
            FROM
                afrn a
            LEFT JOIN
                BRIDGER br ON a.id_bridger = br.id_bridger
            LEFT JOIN
                TRANSPORTIR b ON br.id_trans = b.id_trans
            WHERE
                a.tgl_afrn BETWEEN ? AND ?
            ORDER BY
                a.id_afrn DESC
            LIMIT ? OFFSET ?";

// Persiapan statement
if ($stmt = $conn->prepare($query)) {
    // Bind parameter
    $stmt->bind_param("ssii", $startDate, $endDate, $limit, $offset);
    // Jalankan query
    $stmt->execute();
    // Ambil hasil
    $result = $stmt->get_result();
} else {
    echo "Error preparing statement: " . $conn->error;
    $result = false; // Set result to false to handle error gracefully
}

// Query untuk menghitung total records untuk pagination
$totalRecordsQuery = "SELECT COUNT(*) AS total
                      FROM afrn a
                      LEFT JOIN BRIDGER br ON a.id_bridger = br.id_bridger
                      LEFT JOIN TRANSPORTIR b ON br.id_trans = b.id_trans
                      WHERE a.tgl_afrn BETWEEN ? AND ?";

if ($totalStmt = $conn->prepare($totalRecordsQuery)) {
    $totalStmt->bind_param("ss", $startDate, $endDate);
    $totalStmt->execute();
    $totalRecordsResult = $totalStmt->get_result();
    $totalRecords = $totalRecordsResult->fetch_assoc()['total'];
    $totalPages = ceil($totalRecords / $limit);
} else {
    echo "Error preparing total records statement: " . $conn->error;
    $totalRecords = 0;
    $totalPages = 0;
}

// Close database connection
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Wesco</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
    /* Optional: Custom font if 'font-modify' is not defined elsewhere */
    @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap');

    .font-modify {
        font-family: 'Poppins', sans-serif;
    }

    /* Custom scrollbar for tables */
    .overflow-x-auto::-webkit-scrollbar {
        height: 8px;
    }

    .overflow-x-auto::-webkit-scrollbar-track {
        background: #f1f1f1;
        border-radius: 10px;
    }

    .overflow-x-auto::-webkit-scrollbar-thumb {
        background: #888;
        border-radius: 10px;
    }

    .overflow-x-auto::-webkit-scrollbar-thumb:hover {
        background: #555;
    }
    </style>
</head>

<body class="bg-white font-modify">
    <div class="flex min-h-screen">

        <div class="w-64 bg-white shadow-lg z-10">
            <?php
            // Ensure the path to slidebar.php is correct.
            // If slidebar.php is in the same directory as this dashboard.php, use 'slidebar.php'.
            // If it's in a 'components' folder relative to this file, use '../components/slidebar.php'.
            include '../components/slidebar.php';
            ?>
        </div>
        <div class="flex-1 flex flex-col ">
            <div class="bg-white shadow p-6 flex justify-between items-center">
                <h1 class="text-2xl font-bold text-cyan-700">Selamat Datang di Wesco,
                    <?= htmlspecialchars($nama_lengkap) ?>!</h1>
                <div class="flex items-center space-x-3">
                    <span class="text-gray-600"><?= htmlspecialchars($nama_lengkap) ?></span>
                    <img src="https://media.istockphoto.com/id/1300845620/id/vektor/ikon-pengguna-datar-terisolasi-pada-latar-belakang-putih-simbol-pengguna-ilustrasi-vektor.jpg?s=612x612&w=0&k=20&c=QN0LOsRwA1dHZz9lsKavYdSqUUnis3__FQLtZTQ--Ro="
                        alt="User" class="w-8 h-8 rounded-full">
                </div>
            </div>
            <div class="flex-1 flex flex-col p-6 ">

                <h1 class="text-3xl font-normal mb-8 text-gray-600">Dashboard</h1>

                <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                    <div class="bg-white rounded-xl shadow-lg p-7">
                        <h2 class="text-xl font-semibold mb-6 text-blue-700 border-b pb-3">ACTUAL 5 LAST ORDER 2024</h2>
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-blue-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th
                                            class="py-3 px-4 text-left text-xs font-semibold text-blue-600 uppercase tracking-wider rounded-tl-lg">
                                            No</th>
                                        <th
                                            class="py-3 px-4 text-left text-xs font-semibold text-blue-600 uppercase tracking-wider">
                                            Transportir</th>
                                        <th
                                            class="py-3 px-4 text-left text-xs font-semibold text-blue-600 uppercase tracking-wider rounded-tr-lg">
                                            Jumlah</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    <?php if (empty($last_orders)) : ?>
                                    <tr>
                                        <td colspan="3"
                                            class="px-4 py-4 whitespace-nowrap text-sm text-gray-500 text-center">
                                            Tidak ada data order terbaru.
                                        </td>
                                    </tr>
                                    <?php else : ?>
                                    <?php foreach ($last_orders as $index => $order) : ?>
                                    <tr
                                        class="<?php echo ($index % 2 == 0) ? 'bg-white' : 'bg-gray-50'; ?> hover:bg-gray-100 transition-colors duration-200">
                                        <td class="py-3 px-4 whitespace-nowrap text-sm font-medium text-gray-800">
                                            <?php echo $index + 1; ?>
                                        </td>
                                        <td class="py-3 px-4 whitespace-nowrap text-sm text-gray-700">
                                            <?php echo htmlspecialchars($order['nama_trans']); ?>
                                        </td>
                                        <td class="py-3 px-4 whitespace-nowrap text-sm text-gray-700">
                                            <?php echo number_format($order['volume']); ?>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <div class="bg-white rounded-xl shadow-lg p-7">
                        <h2 class="text-xl font-semibold mb-6 text-blue-700 border-b pb-3">GRAFIK PENJUALAN</h2>
                        <div class="h-80 w-full">
                            <canvas id="salesChart"></canvas>
                        </div>
                        <?php if (empty($labels)) : ?>
                        <p class="text-center text-gray-500 mt-4">Tidak ada data penjualan untuk ditampilkan.</p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <div class="container mx-auto bg-white p-8 rounded-lg shadow-md">
                <h1 class="text-2xl font-bold mb-6 text-gray-800">AFRN DATA</h1>

                <div
                    class="flex flex-col md:flex-row items-start md:items-center space-y-4 md:space-y-0 md:space-x-4 mb-6">
                    <form action="" method="GET"
                        class="flex flex-col md:flex-row items-start md:items-center space-y-4 md:space-y-0 md:space-x-4 w-full">
                        <div
                            class="relative flex items-center border border-gray-300 rounded-md focus-within:ring-2 focus-within:ring-blue-500 w-full md:w-auto">
                            <i class="fas fa-calendar-alt absolute left-3 text-gray-400"></i>
                            <input type="date" name="start_date" value="<?php echo htmlspecialchars($startDate); ?>"
                                class="pl-10 pr-3 py-2 rounded-md outline-none w-full">
                        </div>
                        <span class="text-gray-500 hidden md:block">-</span>
                        <div
                            class="relative flex items-center border border-gray-300 rounded-md focus-within:ring-2 focus-within:ring-blue-500 w-full md:w-auto">
                            <i class="fas fa-calendar-alt absolute left-3 text-gray-400"></i>
                            <input type="date" name="end_date" value="<?php echo htmlspecialchars($endDate); ?>"
                                class="pl-10 pr-3 py-2 rounded-md outline-none w-full">
                        </div>
                        <button type="submit"
                            class="bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 px-6 rounded-md shadow-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-opacity-50 w-full md:w-auto">
                            Filter
                        </button>
                    </form>
                </div>

                <div class="overflow-x-auto">
                    <table class="min-w-full bg-white border border-gray-200 rounded-lg">
                        <thead>
                            <tr class="bg-gray-50 table-header">
                                <th
                                    class="px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">
                                    No</th>
                                <th
                                    class="px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">
                                    Transportir</th>
                                <th
                                    class="px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">
                                    Tanggal Isi</th>
                                <th
                                    class="px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">
                                    Jumlah</th>
                                <th
                                    class="px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">
                                    Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            <?php
                    if ($result && $result->num_rows > 0) {
                        $no = $offset + 1; // Nomor urut dimulai dari offset + 1
                        while ($row = $result->fetch_assoc()) {
                            echo '<tr class="table-row hover:bg-gray-50">';
                            echo '<td class="px-6 py-4 whitespace-nowrap">' . $no++ . '</td>';
                            echo '<td class="px-6 py-4 whitespace-nowrap">' . htmlspecialchars($row['nama_trans']) . '</td>';
                            echo '<td class="px-6 py-4 whitespace-nowrap">' . htmlspecialchars($row['tgl_afrn']) . '</td>'; // Menggunakan tgl_afrn
                            echo '<td class="px-6 py-4 whitespace-nowrap">' . number_format($row['volume'], 0, ',', '.') . '</td>'; // Format volume
                            echo '<td class="px-6 py-4 whitespace-nowrap">';
 echo '<a href="export_excel.php?start_date=' . htmlspecialchars($startDate) . '&end_date=' . htmlspecialchars($endDate) . '" class="text-blue-600 hover:underline" target="_blank">Download Excel File</a>';                            echo '</td>';
                            echo '</tr>';
                        }
                    } else {
                        echo '<tr><td colspan="5" class="px-6 py-4 text-center text-gray-500">Tidak ada data ditemukan.</td></tr>';
                    }
                    ?>
                        </tbody>
                    </table>
                </div>

                <?php if ($totalPages > 1): ?>
                <div class="flex justify-between items-center mt-6">
                    <nav class="relative z-0 inline-flex rounded-md shadow-sm -space-x-px" aria-label="Pagination">
                        <?php if ($page > 1): ?>
                        <a href="?page=<?php echo $page - 1; ?>&start_date=<?php echo htmlspecialchars($startDate); ?>&end_date=<?php echo htmlspecialchars($endDate); ?>"
                            class="relative inline-flex items-center px-2 py-2 rounded-l-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50">
                            <span class="sr-only">Previous</span>
                            <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"
                                fill="currentColor" aria-hidden="true">
                                <path fill-rule="evenodd"
                                    d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z"
                                    clip-rule="evenodd" />
                            </svg>
                        </a>
                        <?php endif; ?>

                        <?php
                    // Display page numbers
                    for ($i = 1; $i <= $totalPages; $i++) {
                        $activeClass = ($i == $page) ? 'z-10 bg-blue-50 border-blue-500 text-blue-600' : 'bg-white border-gray-300 text-gray-700 hover:bg-gray-50';
                        echo '<a href="?page=' . $i . '&start_date=' . htmlspecialchars($startDate) . '&end_date=' . htmlspecialchars($endDate) . '" class="relative inline-flex items-center px-4 py-2 border text-sm font-medium ' . $activeClass . '">';
                        echo $i;
                        echo '</a>';
                    }
                    ?>

                        <?php if ($page < $totalPages): ?>
                        <a href="?page=<?php echo $page + 1; ?>&start_date=<?php echo htmlspecialchars($startDate); ?>&end_date=<?php echo htmlspecialchars($endDate); ?>"
                            class="relative inline-flex items-center px-2 py-2 rounded-r-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50">
                            <span class="sr-only">Next</span>
                            <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"
                                fill="currentColor" aria-hidden="true">
                                <path fill-rule="evenodd"
                                    d="M7.293 14.707a1 1 0 010-1.414L10.586 10l-3.293-3.293a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z"
                                    clip-rule="evenodd" />
                            </svg>
                        </a>
                        <?php endif; ?>
                    </nav>
                </div>
                <?php endif; ?>

            </div>
        </div>





        <script>
        // Chart.js configuration
        const ctx = document.getElementById('salesChart').getContext('2d');
        const salesChart = new Chart(ctx, {
            type: 'line', // Use 'line' type for the area graph
            data: {
                labels: <?php echo json_encode($labels); ?>,
                datasets: [{
                    label: 'Volume',
                    data: <?php echo json_encode($volumes); ?>,
                    fill: true, // Fill the area under the line
                    // Using Tailwind's cyan-500 color with transparency
                    backgroundColor: 'rgba(6, 182, 212, 0.4)', // cyan-500 with 40% opacity
                    borderColor: 'rgb(6, 182, 212)', // cyan-500
                    tension: 0.4, // Slightly more curve for a smoother look
                    pointRadius: 0, // Hide data points
                    borderWidth: 3, // Slightly thicker line
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false, // Allow chart to fit its container's height
                plugins: {
                    legend: {
                        display: true,
                        position: 'top',
                        labels: {
                            font: {
                                family: 'Poppins', // Match the custom font
                                size: 14,
                            },
                            color: '#374151' // gray-700
                        }
                    },
                    tooltip: {
                        mode: 'index',
                        intersect: false,
                        backgroundColor: 'rgba(15, 23, 42, 0.9)', // slate-900 for tooltips
                        titleFont: {
                            family: 'Poppins',
                            size: 14,
                            weight: 'bold'
                        },
                        bodyFont: {
                            family: 'Poppins',
                            size: 12
                        },
                        padding: 10,
                        cornerRadius: 6,
                    }
                },
                scales: {
                    x: {
                        grid: {
                            display: false, // Hide x-axis grid lines
                            drawBorder: false, // Hide x-axis border
                        },
                        ticks: {
                            autoSkip: true,
                            maxRotation: 0,
                            minRotation: 0,
                            font: {
                                family: 'Poppins',
                                size: 10
                            },
                            color: '#4b5563', // gray-600
                            callback: function(val, index) {
                                // Display labels every few points to avoid clutter,
                                // adjust '5' based on your data density.
                                const dataLength = this.getLabels().length;
                                if (dataLength > 10) { // Only skip if many labels
                                    return index % Math.ceil(dataLength / 6) === 0 ? this.getLabelForValue(
                                        val) : '';
                                }
                                return this.getLabelForValue(val);
                            }
                        },
                        title: {
                            display: true,
                            text: 'Tanggal',
                            font: {
                                family: 'Poppins',
                                size: 12,
                                weight: '500'
                            },
                            color: '#4b5563'
                        }
                    },
                    y: {
                        beginAtZero: true,
                        grid: {
                            color: 'rgba(200, 200, 200, 0.2)', // Light gray grid lines
                            drawBorder: false, // Hide y-axis border
                        },
                        ticks: {
                            font: {
                                family: 'Poppins',
                                size: 10
                            },
                            color: '#FFFFFFFF', // gray-600
                            callback: function(value) {
                                return value.toLocaleString(); // Format numbers with commas
                            }
                        },
                        title: {
                            display: true,
                            text: 'Volume',
                            font: {
                                family: 'Poppins',
                                size: 12,
                                weight: '500'
                            },
                            color: '#4b5563'
                        }
                    }
                }
            }
        });
        </script>
</body>

</html>