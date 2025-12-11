<?= $this->extend('admin/layout/header') ?>

<?= $this->section('content') ?>
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0"><?= $title ?></h1>
        <div>
            <a href="<?= base_url('admin/kostum/import') ?>" class="btn btn-outline-secondary me-2">
                <i class="bi bi-upload"></i> Import
            </a>
            <a href="<?= base_url('admin/kostum/export') ?>" class="btn btn-outline-success me-2">
                <i class="bi bi-download"></i> Export
            </a>
            <a href="<?= base_url('admin/kostum/tambah') ?>" class="btn btn-primary">
                <i class="bi bi-plus-circle"></i> Tambah Kostum
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
                            <div class="text-muted fw-semibold">Total Kostum</div>
                            <div class="fw-bold fs-4"><?= $stats['total'] ?></div>
                        </div>
                        <div class="align-self-center">
                            <i class="bi bi-person-standing-dress fs-1 text-primary"></i>
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
                            <div class="text-muted fw-semibold">Aktif</div>
                            <div class="fw-bold fs-4"><?= $stats['active'] ?></div>
                        </div>
                        <div class="align-self-center">
                            <i class="bi bi-check-circle fs-1 text-success"></i>
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
                            <div class="text-muted fw-semibold">Featured</div>
                            <div class="fw-bold fs-4"><?= $stats['featured'] ?></div>
                        </div>
                        <div class="align-self-center">
                            <i class="bi bi-star fs-1 text-warning"></i>
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
                            <div class="text-muted fw-semibold">Stok Habis</div>
                            <div class="fw-bold fs-4"><?= $stats['out_of_stock'] ?></div>
                        </div>
                        <div class="align-self-center">
                            <i class="bi bi-exclamation-triangle fs-1 text-danger"></i>
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
                            <div class="text-muted fw-semibold">Stok Sedikit</div>
                            <div class="fw-bold fs-4"><?= $stats['low_stock'] ?></div>
                        </div>
                        <div class="align-self-center">
                            <i class="bi bi-info-circle fs-1 text-info"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-xl-2 col-md-4 col-sm-6 mb-3">
            <div class="card border-start border-secondary border-4 h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <div class="text-muted fw-semibold">Per Kategori</div>
                            <div class="fw-bold fs-4"><?= count($stats['by_kategori']) ?></div>
                        </div>
                        <div class="align-self-center">
                            <i class="bi bi-tags fs-1 text-secondary"></i>
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
                <div class="col-md-3">
                    <input type="text" name="search" class="form-control" placeholder="Cari nama/deskripsi..." value="<?= $search_term ?? '' ?>">
                </div>
                <div class="col-md-3">
                    <select name="kategori" class="form-select">
                        <option value="">Semua Kategori</option>
                        <?php foreach ($kategori_options as $value => $label): ?>
                        <option value="<?= $value ?>" <?= ($kategori_filter == $value) ? 'selected' : '' ?>>
                            <?= $label ?>
                        </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-3">
                    <select name="status" class="form-select">
                        <option value="">Semua Status</option>
                        <option value="active" <?= ($status_filter == 'active') ? 'selected' : '' ?>>Aktif</option>
                        <option value="inactive" <?= ($status_filter == 'inactive') ? 'selected' : '' ?>>Nonaktif</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="bi bi-filter"></i> Filter
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Bulk Actions -->
    <form method="post" action="<?= base_url('admin/kostum/bulk-action') ?>" id="bulkForm">
        <div class="card mb-4">
            <div class="card-body">
                <div class="row align-items-center">
                    <div class="col-md-4">
                        <select name="action" class="form-select" id="bulkAction">
                            <option value="">Pilih Aksi</option>
                            <option value="activate">Aktifkan</option>
                            <option value="deactivate">Nonaktifkan</option>
                            <option value="feature">Tandai Featured</option>
                            <option value="unfeature">Hapus Featured</option>
                            <option value="delete">Hapus</option>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <button type="button" class="btn btn-outline-secondary" id="selectAll">
                            <i class="bi bi-check-square"></i> Pilih Semua
                        </button>
                        <button type="button" class="btn btn-outline-secondary" id="deselectAll">
                            <i class="bi bi-square"></i> Batalkan Pilihan
                        </button>
                    </div>
                    <div class="col-md-4 text-end">
                        <button type="submit" class="btn btn-primary" id="applyBulkAction">
                            <i class="bi bi-check-circle"></i> Terapkan
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tabel Kostum -->
        <div class="card">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th width="50">
                                    <input type="checkbox" id="checkAll">
                                </th>
                                <th>Gambar</th>
                                <th>Nama Kostum</th>
                                <th>Kategori</th>
                                <th>Harga Sewa</th>
                                <th>Stok</th>
                                <th>Status</th>
                                <th>Featured</th>
                                <th>Tanggal</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($kostum)): ?>
                                <tr>
                                    <td colspan="10" class="text-center">Tidak ada data kostum</td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($kostum as $k): ?>
                                <tr>
                                    <td>
                                        <input type="checkbox" name="ids[]" value="<?= $k['id'] ?>" class="row-checkbox">
                                    </td>
                                    <td>
                                        <?php if (!empty($k['gambar'])): ?>
                                            <img src="<?= base_url('uploads/kostum/' . $k['gambar']) ?>" 
                                                 alt="<?= $k['nama_kostum'] ?>" 
                                                 class="rounded" 
                                                 width="60" 
                                                 height="60"
                                                 style="object-fit: cover;">
                                        <?php else: ?>
                                            <div class="bg-light rounded d-flex align-items-center justify-content-center" 
                                                 style="width: 60px; height: 60px;">
                                                <i class="bi bi-image text-muted"></i>
                                            </div>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <strong><?= $k['nama_kostum'] ?></strong>
                                        <br>
                                        <small class="text-muted"><?= character_limiter($k['deskripsi'] ?? '', 50) ?></small>
                                    </td>
                                    <td>
                                        <span class="badge bg-light text-dark">
                                            <?= $kategori_options[$k['kategori']] ?? $k['kategori'] ?>
                                        </span>
                                    </td>
                                    <td>
                                        <strong>Rp <?= number_format($k['harga_sewa'], 0, ',', '.') ?></strong>
                                        <br>
                                        <small class="text-muted"><?= $k['durasi_sewa'] ?></small>
                                    </td>
                                    <td>
                                        <?php
                                        $stokClass = 'bg-success';
                                        if ($k['stok_tersedia'] == 0) {
                                            $stokClass = 'bg-danger';
                                        } elseif ($k['stok_tersedia'] <= 2) {
                                            $stokClass = 'bg-warning';
                                        }
                                        ?>
                                        <span class="badge <?= $stokClass ?>">
                                            <?= $k['stok_tersedia'] ?> / <?= $k['stok'] ?>
                                        </span>
                                    </td>
                                    <td>
                                        <?php if ($k['is_active']): ?>
                                            <span class="badge bg-success">Aktif</span>
                                        <?php else: ?>
                                            <span class="badge bg-danger">Nonaktif</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php if ($k['is_featured']): ?>
                                            <span class="badge bg-warning">Featured</span>
                                        <?php else: ?>
                                            <span class="badge bg-secondary">-</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?= date('d/m/Y', strtotime($k['created_at'])) ?>
                                    </td>
                                    <td>
                                        <div class="btn-group btn-group-sm">
                                            <a href="<?= base_url('admin/kostum/view/' . $k['id']) ?>" class="btn btn-outline-primary" title="Lihat">
                                                <i class="bi bi-eye"></i>
                                            </a>
                                            <a href="<?= base_url('admin/kostum/edit/' . $k['id']) ?>" class="btn btn-outline-warning" title="Edit">
                                                <i class="bi bi-pencil"></i>
                                            </a>
                                            <a href="<?= base_url('admin/kostum/toggle-status/' . $k['id']) ?>" class="btn btn-outline-<?= $k['is_active'] ? 'danger' : 'success' ?>" title="<?= $k['is_active'] ? 'Nonaktifkan' : 'Aktifkan' ?>">
                                                <i class="bi bi-power"></i>
                                            </a>
                                            <a href="<?= base_url('admin/kostum/toggle-featured/' . $k['id']) ?>" class="btn btn-outline-<?= $k['is_featured'] ? 'secondary' : 'warning' ?>" title="<?= $k['is_featured'] ? 'Hapus Featured' : 'Jadikan Featured' ?>">
                                                <i class="bi bi-star"></i>
                                            </a>
                                            <button type="button" class="btn btn-outline-danger" onclick="confirmDelete(<?= $k['id'] ?>)" title="Hapus">
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
    </form>
</div>

<script>
// Bulk actions
document.getElementById('checkAll').addEventListener('change', function() {
    const checkboxes = document.querySelectorAll('.row-checkbox');
    checkboxes.forEach(checkbox => {
        checkbox.checked = this.checked;
    });
});

document.getElementById('selectAll').addEventListener('click', function() {
    const checkboxes = document.querySelectorAll('.row-checkbox');
    checkboxes.forEach(checkbox => {
        checkbox.checked = true;
    });
    document.getElementById('checkAll').checked = true;
});

document.getElementById('deselectAll').addEventListener('click', function() {
    const checkboxes = document.querySelectorAll('.row-checkbox');
    checkboxes.forEach(checkbox => {
        checkbox.checked = false;
    });
    document.getElementById('checkAll').checked = false;
});

document.getElementById('bulkForm').addEventListener('submit', function(e) {
    const action = document.getElementById('bulkAction').value;
    const checkedBoxes = document.querySelectorAll('.row-checkbox:checked');
    
    if (!action) {
        e.preventDefault();
        alert('Pilih aksi terlebih dahulu!');
        return;
    }
    
    if (checkedBoxes.length === 0) {
        e.preventDefault();
        alert('Pilih minimal satu kostum!');
        return;
    }
    
    if (action === 'delete' && !confirm(`Apakah Anda yakin ingin menghapus ${checkedBoxes.length} kostum?`)) {
        e.preventDefault();
    }
});

function confirmDelete(id) {
    if (confirm('Apakah Anda yakin ingin menghapus kostum ini?')) {
        window.location.href = '<?= base_url('admin/kostum/hapus/') ?>' + id;
    }
}
</script>
<?= $this->endSection() ?>