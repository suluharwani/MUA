<?= $this->extend('admin/layout/header') ?>

<?= $this->section('content') ?>
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0"><?= $title ?></h1>
        <div>
            <a href="<?= base_url('admin/pesanan/calendar') ?>" class="btn btn-outline-primary me-2">
                <i class="bi bi-calendar-week"></i> Kalendar
            </a>
            <a href="<?= base_url('admin/pesanan/export/excel') ?>" class="btn btn-success">
                <i class="bi bi-file-earmark-excel"></i> Export Excel
            </a>
        </div>
    </div>

    <!-- Statistik -->
    <div class="row mb-4">
        <div class="col-xl-2 col-md-4 col-sm-6 mb-3">
            <div class="card border-start border-primary border-4 h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <div class="text-muted fw-semibold">Total Pesanan</div>
                            <div class="fw-bold fs-4"><?= $stats['total'] ?></div>
                        </div>
                        <div class="align-self-center">
                            <i class="bi bi-cart fs-1 text-primary"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-xl-2 col-md-4 col-sm-6 mb-3">
            <div class="card border-start border-warning border-4 h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <div class="text-muted fw-semibold">Pending</div>
                            <div class="fw-bold fs-4"><?= $stats['pending'] ?></div>
                        </div>
                        <div class="align-self-center">
                            <i class="bi bi-clock fs-1 text-warning"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-xl-2 col-md-4 col-sm-6 mb-3">
            <div class="card border-start border-info border-4 h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <div class="text-muted fw-semibold">Dikonfirmasi</div>
                            <div class="fw-bold fs-4"><?= $stats['dikonfirmasi'] ?></div>
                        </div>
                        <div class="align-self-center">
                            <i class="bi bi-check-circle fs-1 text-info"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-xl-2 col-md-4 col-sm-6 mb-3">
            <div class="card border-start border-primary border-4 h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <div class="text-muted fw-semibold">Diproses</div>
                            <div class="fw-bold fs-4"><?= $stats['diproses'] ?></div>
                        </div>
                        <div class="align-self-center">
                            <i class="bi bi-gear fs-1 text-primary"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-xl-2 col-md-4 col-sm-6 mb-3">
            <div class="card border-start border-success border-4 h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <div class="text-muted fw-semibold">Selesai</div>
                            <div class="fw-bold fs-4"><?= $stats['selesai'] ?></div>
                        </div>
                        <div class="align-self-center">
                            <i class="bi bi-check-circle-fill fs-1 text-success"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-xl-2 col-md-4 col-sm-6 mb-3">
            <div class="card border-start border-danger border-4 h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <div class="text-muted fw-semibold">Dibatalkan</div>
                            <div class="fw-bold fs-4"><?= $stats['dibatalkan'] ?></div>
                        </div>
                        <div class="align-self-center">
                            <i class="bi bi-x-circle fs-1 text-danger"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filter dan Search -->
    <div class="card mb-4">
        <div class="card-body">
            <form method="get" class="row g-3">
                <div class="col-md-4">
                    <input type="text" name="search" class="form-control" placeholder="Cari kode/nama/telepon..." value="<?= $search_term ?? '' ?>">
                </div>
                <div class="col-md-3">
                    <select name="status" class="form-select">
                        <option value="">Semua Status</option>
                        <option value="pending" <?= ($status_filter == 'pending') ? 'selected' : '' ?>>Pending</option>
                        <option value="dikonfirmasi" <?= ($status_filter == 'dikonfirmasi') ? 'selected' : '' ?>>Dikonfirmasi</option>
                        <option value="diproses" <?= ($status_filter == 'diproses') ? 'selected' : '' ?>>Diproses</option>
                        <option value="selesai" <?= ($status_filter == 'selesai') ? 'selected' : '' ?>>Selesai</option>
                        <option value="dibatalkan" <?= ($status_filter == 'dibatalkan') ? 'selected' : '' ?>>Dibatalkan</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <input type="date" name="date" class="form-control" value="<?= $this->request->getGet('date') ?>">
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="bi bi-filter"></i> Filter
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Tabel Pesanan -->
    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Kode</th>
                            <th>Nama</th>
                            <th>WhatsApp</th>
                            <th>Layanan</th>
                            <th>Tanggal Acara</th>
                            <th>Total</th>
                            <th>Status</th>
                            <th>Tanggal Pesan</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($pesanan)): ?>
                            <tr>
                                <td colspan="9" class="text-center">Tidak ada data pesanan</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($pesanan as $p): ?>
                            <tr>
                                <td>
                                    <strong><?= $p['kode_pesanan'] ?></strong>
                                </td>
                                <td><?= $p['nama_lengkap'] ?></td>
                                <td>
                                    <a href="https://wa.me/<?= $p['no_whatsapp'] ?>" target="_blank" class="text-success">
                                        <i class="bi bi-whatsapp"></i> <?= $p['no_whatsapp'] ?>
                                    </a>
                                </td>
                                <td>
                                    <span class="badge bg-light text-dark">
                                        <?= $p['jenis_layanan'] ?>
                                    </span>
                                </td>
                                <td>
                                    <?= date('d/m/Y', strtotime($p['tanggal_acara'])) ?>
                                </td>
                                <td>
                                    <strong>Rp <?= number_format($p['total_harga'] ?? 0, 0, ',', '.') ?></strong>
                                </td>
                                <td>
                                    <?php
                                    $badgeClass = [
                                        'pending' => 'bg-warning',
                                        'dikonfirmasi' => 'bg-info',
                                        'diproses' => 'bg-primary',
                                        'selesai' => 'bg-success',
                                        'dibatalkan' => 'bg-danger'
                                    ][$p['status']] ?? 'bg-secondary';
                                    ?>
                                    <span class="badge <?= $badgeClass ?>">
                                        <?= ucfirst($p['status']) ?>
                                    </span>
                                </td>
                                <td>
                                    <?= date('d/m/Y H:i', strtotime($p['created_at'])) ?>
                                </td>
                                <td>
                                    <div class="btn-group btn-group-sm">
                                        <a href="<?= base_url('admin/pesanan/detail/' . $p['id']) ?>" class="btn btn-outline-primary">
                                            <i class="bi bi-eye"></i>
                                        </a>
                                        <a href="https://wa.me/<?= $p['no_whatsapp'] ?>?text=Halo%20<?= urlencode($p['nama_lengkap']) ?>%20..." target="_blank" class="btn btn-outline-success">
                                            <i class="bi bi-whatsapp"></i>
                                        </a>
                                        <button type="button" class="btn btn-outline-danger" onclick="confirmDelete(<?= $p['id'] ?>)">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </div>
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

<script>
function confirmDelete(id) {
    if (confirm('Apakah Anda yakin ingin menghapus pesanan ini?')) {
        window.location.href = '<?= base_url('admin/pesanan/delete/') ?>' + id;
    }
}
</script>
<?= $this->endSection() ?>