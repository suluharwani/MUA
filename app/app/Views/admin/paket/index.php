<?= $this->extend('admin/layout/header') ?>

<?= $this->section('content') ?>
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0"><?= $title ?></h1>
        <a href="<?= base_url('admin/paket/tambah') ?>" class="btn btn-primary">
            <i class="bi bi-plus-circle"></i> Tambah Paket
        </a>
    </div>

    <!-- Stats Cards -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-3">
            <div class="card border-start border-primary border-4 h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <div class="text-muted fw-semibold">Total Paket</div>
                            <div class="fw-bold fs-4"><?= $stats['total'] ?? 0 ?></div>
                        </div>
                        <div class="align-self-center">
                            <i class="bi bi-box-seam fs-1 text-primary"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-xl-3 col-md-6 mb-3">
            <div class="card border-start border-success border-4 h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <div class="text-muted fw-semibold">Aktif</div>
                            <div class="fw-bold fs-4"><?= $stats['active'] ?? 0 ?></div>
                        </div>
                        <div class="align-self-center">
                            <i class="bi bi-check-circle fs-1 text-success"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-xl-3 col-md-6 mb-3">
            <div class="card border-start border-warning border-4 h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <div class="text-muted fw-semibold">Featured</div>
                            <div class="fw-bold fs-4"><?= $stats['featured'] ?? 0 ?></div>
                        </div>
                        <div class="align-self-center">
                            <i class="bi bi-star fs-1 text-warning"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-xl-3 col-md-6 mb-3">
            <div class="card border-start border-info border-4 h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <div class="text-muted fw-semibold">Rata-rata Harga</div>
                            <div class="fw-bold fs-4">Rp <?= number_format($stats['average_price'] ?? 0, 0, ',', '.') ?></div>
                        </div>
                        <div class="align-self-center">
                            <i class="bi bi-currency-dollar fs-1 text-info"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Tabel Paket -->
    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Nama Paket</th>
                            <th>Harga</th>
                            <th>Durasi</th>
                            <th>Fitur</th>
                            <th>Status</th>
                            <th>Featured</th>
                            <th>Urutan</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($paket)): ?>
                            <tr>
                                <td colspan="8" class="text-center">Belum ada data paket</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($paket as $p): ?>
                            <tr>
                                <td>
                                    <strong><?= $p['nama_paket'] ?></strong>
                                    <br>
                                    <small class="text-muted"><?= character_limiter($p['deskripsi'] ?? '', 60) ?></small>
                                </td>
                                <td>
                                    <strong>Rp <?= number_format($p['harga'], 0, ',', '.') ?></strong>
                                </td>
                                <td><?= $p['durasi'] ?? '-' ?></td>
                                <td>
                                    <?php if (!empty($p['features']) && is_array($p['features'])): ?>
                                        <small><?= count($p['features']) ?> fitur</small>
                                    <?php else: ?>
                                        <small>-</small>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php if ($p['is_active']): ?>
                                        <span class="badge bg-success">Aktif</span>
                                    <?php else: ?>
                                        <span class="badge bg-danger">Nonaktif</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php if ($p['is_featured']): ?>
                                        <span class="badge bg-warning">Featured</span>
                                    <?php else: ?>
                                        <span class="badge bg-secondary">-</span>
                                    <?php endif; ?>
                                </td>
                                <td><?= $p['urutan'] ?></td>
                                <td>
                                    <div class="btn-group btn-group-sm">
                                        <a href="<?= base_url('admin/paket/edit/' . $p['id']) ?>" class="btn btn-outline-warning">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                        <a href="<?= base_url('admin/paket/toggle-status/' . $p['id']) ?>" class="btn btn-outline-<?= $p['is_active'] ? 'danger' : 'success' ?>">
                                            <i class="bi bi-power"></i>
                                        </a>
                                        <a href="<?= base_url('admin/paket/toggle-featured/' . $p['id']) ?>" class="btn btn-outline-<?= $p['is_featured'] ? 'secondary' : 'warning' ?>">
                                            <i class="bi bi-star"></i>
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
    if (confirm('Apakah Anda yakin ingin menghapus paket ini?')) {
        window.location.href = '<?= base_url('admin/paket/hapus/') ?>' + id;
    }
}
</script>
<?= $this->endSection() ?>