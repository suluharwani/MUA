<?= $this->extend('admin/layout/header') ?>

<?= $this->section('content') ?>
<div class="container-fluid">
    <!-- Page Heading -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800">Dashboard</h1>
        <div class="text-muted">
            <?= date('d F Y, H:i') ?>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="row">
        <!-- Total Pesanan -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary border-4 h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                Total Pesanan</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800"><?= $pesananStats['total'] ?? 0 ?></div>
                            <div class="mt-2">
                                <small class="text-success">
                                    <i class="bi bi-arrow-up"></i> 
                                    <?= $pesananStats['selesai'] ?? 0 ?> selesai
                                </small>
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="bi bi-cart-check fs-2 text-primary"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Total Pendapatan -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-success border-4 h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                Total Pendapatan</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                Rp <?= number_format($pesananStats['total_pendapatan'] ?? 0, 0, ',', '.') ?>
                            </div>
                            <div class="mt-2">
                                <small class="text-muted">Pesanan selesai</small>
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="bi bi-currency-dollar fs-2 text-success"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Total Paket -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-info border-4 h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                Total Paket</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800"><?= $paketStats['total'] ?? 0 ?></div>
                            <div class="mt-2">
                                <small class="<?= ($paketStats['active'] ?? 0) > 0 ? 'text-success' : 'text-danger' ?>">
                                    <i class="bi bi-circle-fill"></i> 
                                    <?= $paketStats['active'] ?? 0 ?> aktif
                                </small>
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="bi bi-box-seam fs-2 text-info"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Total Kostum -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-warning border-4 h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                Total Kostum</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800"><?= $kostumStats['total'] ?? 0 ?></div>
                            <div class="mt-2">
                                <small class="text-danger">
                                    <i class="bi bi-exclamation-triangle"></i> 
                                    <?= $kostumStats['low_stock'] ?? 0 ?> stok menipis
                                </small>
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="bi bi-person-badge fs-2 text-warning"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts and Tables Row -->
    <div class="row">
        <!-- Grafik Pesanan -->
        <div class="col-xl-8 col-lg-7">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">Statistik Pesanan (<?= date('Y') ?>)</h6>
                    <div class="dropdown no-arrow">
                        <a class="dropdown-toggle" href="#" role="button" id="dropdownMenuLink" 
                           data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="bi bi-three-dots-vertical"></i>
                        </a>
                        <div class="dropdown-menu dropdown-menu-end shadow">
                            <a class="dropdown-item" href="#" onclick="updateChart('<?= date('Y')-1 ?>')">Tahun <?= date('Y')-1 ?></a>
                            <a class="dropdown-item" href="#" onclick="updateChart('<?= date('Y') ?>')">Tahun <?= date('Y') ?></a>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="chart-area">
                        <canvas id="ordersChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- Status Pesanan -->
        <div class="col-xl-4 col-lg-5">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Status Pesanan</h6>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <span class="badge bg-warning me-2">Pending</span>
                        <span class="float-end"><?= $pesananStats['pending'] ?? 0 ?></span>
                    </div>
                    <div class="mb-3">
                        <span class="badge bg-info me-2">Dikonfirmasi</span>
                        <span class="float-end"><?= $pesananStats['dikonfirmasi'] ?? 0 ?></span>
                    </div>
                    <div class="mb-3">
                        <span class="badge bg-primary me-2">Diproses</span>
                        <span class="float-end"><?= $pesananStats['diproses'] ?? 0 ?></span>
                    </div>
                    <div class="mb-3">
                        <span class="badge bg-success me-2">Selesai</span>
                        <span class="float-end"><?= $pesananStats['selesai'] ?? 0 ?></span>
                    </div>
                    <div class="mb-3">
                        <span class="badge bg-danger me-2">Dibatalkan</span>
                        <span class="float-end"><?= $pesananStats['dibatalkan'] ?? 0 ?></span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Tables Row -->
    <div class="row">
        <!-- Pesanan Terbaru -->
        <div class="col-lg-6 mb-4">
            <div class="card shadow">
                <div class="card-header py-3 d-flex justify-content-between align-items-center">
                    <h6 class="m-0 font-weight-bold text-primary">Pesanan Terbaru</h6>
                    <a href="<?= base_url('admin/pesanan') ?>" class="btn btn-sm btn-outline-primary">
                        Lihat Semua
                    </a>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover table-sm">
                            <thead>
                                <tr>
                                    <th>Kode</th>
                                    <th>Nama</th>
                                    <th>Layanan</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($latestOrders)): ?>
                                    <tr>
                                        <td colspan="4" class="text-center">Belum ada pesanan</td>
                                    </tr>
                                <?php else: ?>
                                    <?php foreach ($latestOrders as $order): ?>
                                    <tr>
                                        <td>
                                            <small class="text-muted"><?= $order['kode_pesanan'] ?></small>
                                        </td>
                                        <td><?= $order['nama_lengkap'] ?></td>
                                        <td>
                                            <?php if ($order['paket_nama']): ?>
                                                <span class="badge bg-info">Makeup</span>
                                            <?php elseif ($order['kostum_nama']): ?>
                                                <span class="badge bg-warning">Kostum</span>
                                            <?php else: ?>
                                                <span class="badge bg-secondary">-</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <span class="badge bg-<?= getStatusColor($order['status']) ?>">
                                                <?= ucfirst($order['status']) ?>
                                            </span>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Acara Mendatang -->
        <div class="col-lg-6 mb-4">
            <div class="card shadow">
                <div class="card-header py-3 d-flex justify-content-between align-items-center">
                    <h6 class="m-0 font-weight-bold text-primary">Acara Mendatang (7 hari)</h6>
                    <a href="<?= base_url('admin/pesanan/calendar') ?>" class="btn btn-sm btn-outline-primary">
                        Kalender
                    </a>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover table-sm">
                            <thead>
                                <tr>
                                    <th>Tanggal</th>
                                    <th>Nama</th>
                                    <th>Layanan</th>
                                    <th>Lokasi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($upcomingEvents)): ?>
                                    <tr>
                                        <td colspan="4" class="text-center">Tidak ada acara mendatang</td>
                                    </tr>
                                <?php else: ?>
                                    <?php foreach ($upcomingEvents as $event): ?>
                                    <tr>
                                        <td>
                                            <small><?= date('d M', strtotime($event['tanggal_acara'])) ?></small>
                                        </td>
                                        <td><?= $event['nama_lengkap'] ?></td>
                                        <td>
                                            <?= $event['paket_nama'] ?? $event['kostum_nama'] ?? 'Layanan' ?>
                                        </td>
                                        <td>
                                            <small class="text-muted"><?= character_limiter($event['lokasi_acara'], 20) ?></small>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Kostum Stok Menipis -->
    <div class="row">
        <div class="col-12">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex justify-content-between align-items-center">
                    <h6 class="m-0 font-weight-bold text-danger">Kostum Stok Menipis</h6>
                    <a href="<?= base_url('admin/kostum') ?>" class="btn btn-sm btn-outline-danger">
                        Kelola Kostum
                    </a>
                </div>
                <div class="card-body">
                    <?php if (empty($lowStockCostumes)): ?>
                        <div class="alert alert-success">
                            <i class="bi bi-check-circle"></i> Semua kostum memiliki stok yang cukup.
                        </div>
                    <?php else: ?>
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Nama Kostum</th>
                                        <th>Kategori</th>
                                        <th>Stok Tersedia</th>
                                        <th>Stok Total</th>
                                        <th>Status</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($lowStockCostumes as $kostum): ?>
                                    <tr>
                                        <td>
                                            <strong><?= $kostum['nama_kostum'] ?></strong>
                                        </td>
                                        <td>
                                            <?php 
                                            $kategoriOptions = [
                                                'pengantin_wanita' => 'Pengantin Wanita',
                                                'pengantin_pria' => 'Pengantin Pria',
                                                'keluarga' => 'Keluarga',
                                                'lainnya' => 'Lainnya'
                                            ];
                                            echo $kategoriOptions[$kostum['kategori']] ?? $kostum['kategori'];
                                            ?>
                                        </td>
                                        <td>
                                            <span class="badge bg-<?= $kostum['stok_tersedia'] == 0 ? 'danger' : 'warning' ?>">
                                                <?= $kostum['stok_tersedia'] ?>
                                            </span>
                                        </td>
                                        <td><?= $kostum['stok'] ?></td>
                                        <td>
                                            <?php if ($kostum['is_active']): ?>
                                                <span class="badge bg-success">Aktif</span>
                                            <?php else: ?>
                                                <span class="badge bg-danger">Nonaktif</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <a href="<?= base_url('admin/kostum/edit/' . $kostum['id']) ?>" 
                                               class="btn btn-sm btn-outline-warning">
                                                <i class="bi bi-pencil"></i> Edit
                                            </a>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- System Info -->
    <div class="row">
        <div class="col-12">
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Informasi Sistem</h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3 mb-3">
                            <div class="card border-start border-primary border-3">
                                <div class="card-body">
                                    <div class="text-muted">CodeIgniter</div>
                                    <div class="fw-bold"><?= $systemInfo['ci_version'] ?></div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3 mb-3">
                            <div class="card border-start border-success border-3">
                                <div class="card-body">
                                    <div class="text-muted">PHP Version</div>
                                    <div class="fw-bold"><?= $systemInfo['php_version'] ?></div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3 mb-3">
                            <div class="card border-start border-info border-3">
                                <div class="card-body">
                                    <div class="text-muted">Environment</div>
                                    <div class="fw-bold"><?= $systemInfo['environment'] ?></div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3 mb-3">
                            <div class="card border-start border-warning border-3">
                                <div class="card-body">
                                    <div class="text-muted">Timezone</div>
                                    <div class="fw-bold"><?= $systemInfo['timezone'] ?></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Helper functions -->
<?php 
function getStatusColor($status) {
    $colors = [
        'pending' => 'warning',
        'dikonfirmasi' => 'info',
        'diproses' => 'primary',
        'selesai' => 'success',
        'dibatalkan' => 'danger'
    ];
    return $colors[$status] ?? 'secondary';
}
?>

<!-- JavaScript for Charts -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
// Inisialisasi chart
let ordersChart;

function initChart(data) {
    const ctx = document.getElementById('ordersChart').getContext('2d');
    
    if (ordersChart) {
        ordersChart.destroy();
    }
    
    ordersChart = new Chart(ctx, {
        type: 'line',
        data: {
            labels: data.months,
            datasets: [{
                label: 'Jumlah Pesanan',
                data: data.data,
                backgroundColor: 'rgba(78, 115, 223, 0.05)',
                borderColor: 'rgba(78, 115, 223, 1)',
                pointBackgroundColor: 'rgba(78, 115, 223, 1)',
                pointBorderColor: '#fff',
                pointHoverBackgroundColor: '#fff',
                pointHoverBorderColor: 'rgba(78, 115, 223, 1)',
                tension: 0.3
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        precision: 0
                    }
                }
            }
        }
    });
}

// Load chart data
function updateChart(year) {
    fetch(`<?= base_url('admin/dashboard/chart-data/') ?>${year}`)
        .then(response => response.json())
        .then(data => {
            initChart(data);
        })
        .catch(error => {
            console.error('Error loading chart data:', error);
        });
}

// Initialize chart on page load
document.addEventListener('DOMContentLoaded', function() {
    // Initial data from PHP
    const initialData = {
        months: <?= json_encode($monthlyOrders['months'] ?? []) ?>,
        data: <?= json_encode($monthlyOrders['data'] ?? []) ?>
    };
    
    if (initialData.months.length > 0) {
        initChart(initialData);
    }
});
</script>

<?= $this->endSection() ?>