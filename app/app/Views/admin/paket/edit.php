<?= $this->extend('admin/layout/header') ?>

<?= $this->section('content') ?>
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0"><?= $title ?></h1>
        <a href="<?= base_url('admin/paket') ?>" class="btn btn-secondary">
            <i class="bi bi-arrow-left"></i> Kembali
        </a>
    </div>

    <?php if (session()->getFlashdata('errors')): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <h5 class="alert-heading"><i class="bi bi-exclamation-triangle"></i> Terjadi Kesalahan</h5>
            <ul class="mb-0">
                <?php foreach (session()->getFlashdata('errors') as $error): ?>
                    <li><?= $error ?></li>
                <?php endforeach; ?>
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <?php if (session()->getFlashdata('error')): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="bi bi-exclamation-triangle"></i> <?= session()->getFlashdata('error') ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <div class="row">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Form Edit Paket</h5>
                </div>
                <div class="card-body">
                    <form action="<?= base_url('admin/paket/update/' . $paket['id']) ?>" method="POST" id="paketForm">
                        <?= csrf_field() ?>
                        
                        <div class="mb-3">
                            <label for="nama_paket" class="form-label">Nama Paket <span class="text-danger">*</span></label>
                            <input type="text" class="form-control <?= session('errors.nama_paket') ? 'is-invalid' : '' ?>" 
                                   id="nama_paket" name="nama_paket" 
                                   value="<?= old('nama_paket', $paket['nama_paket']) ?>" required>
                            <?php if (session('errors.nama_paket')): ?>
                                <div class="invalid-feedback">
                                    <?= session('errors.nama_paket') ?>
                                </div>
                            <?php endif; ?>
                        </div>
                        
                        <div class="mb-3">
                            <label for="deskripsi" class="form-label">Deskripsi <span class="text-danger">*</span></label>
                            <textarea class="form-control <?= session('errors.deskripsi') ? 'is-invalid' : '' ?>" 
                                      id="deskripsi" name="deskripsi" rows="4" required><?= old('deskripsi', $paket['deskripsi']) ?></textarea>
                            <?php if (session('errors.deskripsi')): ?>
                                <div class="invalid-feedback">
                                    <?= session('errors.deskripsi') ?>
                                </div>
                            <?php endif; ?>
                            <small class="text-muted">Jelaskan detail paket makeup yang ditawarkan.</small>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="harga" class="form-label">Harga <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text">Rp</span>
                                    <input type="number" class="form-control <?= session('errors.harga') ? 'is-invalid' : '' ?>" 
                                           id="harga" name="harga" 
                                           value="<?= old('harga', $paket['harga']) ?>" min="0" required>
                                    <?php if (session('errors.harga')): ?>
                                        <div class="invalid-feedback">
                                            <?= session('errors.harga') ?>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label for="durasi" class="form-label">Durasi</label>
                                <input type="text" class="form-control <?= session('errors.durasi') ? 'is-invalid' : '' ?>" 
                                       id="durasi" name="durasi" 
                                       value="<?= old('durasi', $paket['durasi']) ?>" placeholder="Contoh: 3 jam, 1 hari">
                                <?php if (session('errors.durasi')): ?>
                                    <div class="invalid-feedback">
                                        <?= session('errors.durasi') ?>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="features" class="form-label">Fitur & Layanan</label>
                            <textarea class="form-control" id="features" name="features" rows="5"><?= old('features', implode("\n", $paket['features'] ?? [])) ?></textarea>
                            <small class="text-muted">Satu fitur per baris.</small>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="urutan" class="form-label">Urutan Tampil</label>
                                <input type="number" class="form-control" id="urutan" name="urutan" 
                                       value="<?= old('urutan', $paket['urutan']) ?>" min="0">
                                <small class="text-muted">Angka lebih kecil akan ditampilkan lebih awal.</small>
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Status</label>
                                <div class="d-flex gap-3">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="is_active" name="is_active" value="1" 
                                            <?= $paket['is_active'] ? 'checked' : '' ?>>
                                        <label class="form-check-label" for="is_active">
                                            Aktif
                                        </label>
                                    </div>
                                    
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="is_featured" name="is_featured" value="1"
                                            <?= $paket['is_featured'] ? 'checked' : '' ?>>
                                        <label class="form-check-label" for="is_featured">
                                            Featured
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="d-flex justify-content-between mt-4">
                            <a href="<?= base_url('admin/paket') ?>" class="btn btn-secondary">
                                <i class="bi bi-x-circle"></i> Batal
                            </a>
                            <div class="btn-group">
                                <button type="submit" class="btn btn-primary">
                                    <i class="bi bi-save"></i> Update Paket
                                </button>
                                <a href="<?= base_url('admin/paket/hapus/' . $paket['id']) ?>" 
                                   class="btn btn-danger" onclick="return confirm('Hapus paket ini?')">
                                    <i class="bi bi-trash"></i> Hapus
                                </a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        
        <div class="col-lg-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Informasi Paket</h5>
                </div>
                <div class="card-body">
                    <div class="alert alert-info">
                        <h6><i class="bi bi-info-circle"></i> Info Paket</h6>
                        <ul class="mb-0">
                            <li>ID: #<?= $paket['id'] ?></li>
                            <li>Dibuat: <?= date('d/m/Y', strtotime($paket['created_at'])) ?></li>
                            <li>Diupdate: <?= date('d/m/Y', strtotime($paket['updated_at'])) ?></li>
                        </ul>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Status Saat Ini</label>
                        <div>
                            <?php if ($paket['is_active']): ?>
                                <span class="badge bg-success">Aktif</span>
                            <?php else: ?>
                                <span class="badge bg-danger">Nonaktif</span>
                            <?php endif; ?>
                            
                            <?php if ($paket['is_featured']): ?>
                                <span class="badge bg-warning ms-2">Featured</span>
                            <?php endif; ?>
                        </div>
                    </div>
                    
                    <h6>Fitur Saat Ini:</h6>
                    <div class="bg-light p-3 rounded">
                        <?php if (!empty($paket['features'])): ?>
                            <ul class="mb-0">
                                <?php foreach ($paket['features'] as $feature): ?>
                                    <li><small><?= $feature ?></small></li>
                                <?php endforeach; ?>
                            </ul>
                        <?php else: ?>
                            <small class="text-muted">Belum ada fitur</small>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const hargaInput = document.getElementById('harga');
    const pricePreview = document.getElementById('pricePreview');
    
    // Format harga real-time
    if (hargaInput) {
        hargaInput.addEventListener('input', function() {
            const harga = this.value.replace(/\D/g, '');
            const formattedHarga = new Intl.NumberFormat('id-ID').format(harga);
            
            if (pricePreview) {
                if (harga > 0) {
                    pricePreview.innerHTML = `
                        <h4 class="text-primary">Rp ${formattedHarga}</h4>
                        <small class="text-muted">Harga per paket</small>
                    `;
                } else {
                    pricePreview.innerHTML = `
                        <h4 class="text-primary">Rp 0</h4>
                        <small class="text-muted">Harga akan muncul di sini</small>
                    `;
                }
            }
        });
        
        // Trigger initial formatting
        if (hargaInput.value) {
            hargaInput.dispatchEvent(new Event('input'));
        }
    }
    
    // Form validation
    const form = document.getElementById('paketForm');
    if (form) {
        form.addEventListener('submit', function(e) {
            const harga = document.getElementById('harga').value;
            
            if (harga <= 0) {
                e.preventDefault();
                alert('Harga harus lebih dari 0');
                return false;
            }
            
            return true;
        });
    }
});
</script>

<?= $this->endSection() ?>