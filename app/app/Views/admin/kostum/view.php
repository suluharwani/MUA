<?= $this->extend('admin/layout/header') ?>

<?= $this->section('content') ?>
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0"><?= $title ?></h1>
        <div>
            <a href="<?= base_url('admin/kostum') ?>" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left"></i> Kembali
            </a>
        </div>
    </div>

    <div class="row">
        <div class="col-md-4">
            <div class="card mb-4">
                <div class="card-body text-center">
                    <?php if (!empty($kostum['gambar'])): ?>
                        <img src="<?= base_url('uploads/kostum/' . $kostum['gambar']) ?>" 
                             alt="<?= $kostum['nama_kostum'] ?>" 
                             class="img-fluid rounded mb-3">
                    <?php else: ?>
                        <div class="bg-light rounded d-flex align-items-center justify-content-center mb-3" 
                             style="height: 200px;">
                            <i class="bi bi-image text-muted fs-1"></i>
                        </div>
                    <?php endif; ?>
                    
                    <h4><?= $kostum['nama_kostum'] ?></h4>
                    <div class="d-flex justify-content-center gap-2 mb-3">
                        <span class="badge bg-<?= $kostum['is_active'] ? 'success' : 'danger' ?>">
                            <?= $kostum['is_active'] ? 'Aktif' : 'Nonaktif' ?>
                        </span>
                        <?php if ($kostum['is_featured']): ?>
                            <span class="badge bg-warning">Featured</span>
                        <?php endif; ?>
                    </div>
                    
                    <div class="btn-group">
                        <a href="<?= base_url('admin/kostum/edit/' . $kostum['id']) ?>" class="btn btn-warning">
                            <i class="bi bi-pencil"></i> Edit
                        </a>
                        <a href="<?= base_url('admin/kostum/hapus/' . $kostum['id']) ?>" 
                           class="btn btn-danger" 
                           onclick="return confirm('Apakah Anda yakin?')">
                            <i class="bi bi-trash"></i> Hapus
                        </a>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-8">
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">Informasi Kostum</h5>
                </div>
                <div class="card-body">
                    <table class="table table-striped">
                        <tr>
                            <th width="30%">Kategori</th>
                            <td>
                                <span class="badge bg-light text-dark">
                                    <?= $kategori_options[$kostum['kategori']] ?? $kostum['kategori'] ?>
                                </span>
                            </td>
                        </tr>
                        <tr>
                            <th>Harga Sewa</th>
                            <td><strong>Rp <?= number_format($kostum['harga_sewa'], 0, ',', '.') ?></strong></td>
                        </tr>
                        <tr>
                            <th>Durasi Sewa</th>
                            <td><?= $kostum['durasi_sewa'] ?></td>
                        </tr>
                        <tr>
                            <th>Stok</th>
                            <td>
                                <span class="badge <?= $kostum['stok_tersedia'] == 0 ? 'bg-danger' : ($kostum['stok_tersedia'] <= 2 ? 'bg-warning' : 'bg-success') ?>">
                                    <?= $kostum['stok_tersedia'] ?> / <?= $kostum['stok'] ?>
                                </span>
                            </td>
                        </tr>
                        <tr>
                            <th>Dibuat</th>
                            <td><?= date('d/m/Y H:i', strtotime($kostum['created_at'])) ?></td>
                        </tr>
                        <tr>
                            <th>Diperbarui</th>
                            <td><?= date('d/m/Y H:i', strtotime($kostum['updated_at'])) ?></td>
                        </tr>
                    </table>
                </div>
            </div>
            
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Deskripsi</h5>
                </div>
                <div class="card-body">
                    <?= nl2br(esc($kostum['deskripsi'])) ?>
                </div>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>