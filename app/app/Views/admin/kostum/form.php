<?php
// Form template untuk tambah dan edit
// Anda bisa membuat dua file terpisah: tambah.php dan edit.php
// Atau menggunakan satu file dengan kondisi
?>

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
        <div class="col-md-8 mx-auto">
            <div class="card">
                <div class="card-body">
                    <?php if (isset($validation)): ?>
                        <div class="alert alert-danger">
                            <?= $validation->listErrors() ?>
                        </div>
                    <?php endif; ?>
                    
                    <form method="post" action="<?= $form_action ?>" enctype="multipart/form-data">
                        <?= csrf_field() ?>
                        
                        <div class="row">
                            <div class="col-md-8">
                                <div class="mb-3">
                                    <label for="nama_kostum" class="form-label">Nama Kostum <span class="text-danger">*</span></label>
                                    <input type="text" 
                                           class="form-control <?= (isset($validation) && $validation->hasError('nama_kostum')) ? 'is-invalid' : '' ?>" 
                                           id="nama_kostum" 
                                           name="nama_kostum" 
                                           value="<?= old('nama_kostum', $kostum['nama_kostum'] ?? '') ?>" 
                                           required>
                                    <?php if (isset($validation) && $validation->hasError('nama_kostum')): ?>
                                        <div class="invalid-feedback">
                                            <?= $validation->getError('nama_kostum') ?>
                                        </div>
                                    <?php endif; ?>
                                </div>
                                
                                <div class="mb-3">
                                    <label for="deskripsi" class="form-label">Deskripsi</label>
                                    <textarea class="form-control" 
                                              id="deskripsi" 
                                              name="deskripsi" 
                                              rows="4"><?= old('deskripsi', $kostum['deskripsi'] ?? '') ?></textarea>
                                </div>
                            </div>
                            
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="gambar" class="form-label">Gambar Kostum</label>
                                    <?php if (isset($kostum['gambar']) && !empty($kostum['gambar'])): ?>
                                        <div class="mb-2">
                                            <img src="<?= base_url('uploads/kostum/' . $kostum['gambar']) ?>" 
                                                 alt="Preview" 
                                                 class="img-thumbnail" 
                                                 style="max-height: 150px;">
                                        </div>
                                    <?php endif; ?>
                                    <input type="file" 
                                           class="form-control <?= (isset($validation) && $validation->hasError('gambar')) ? 'is-invalid' : '' ?>" 
                                           id="gambar" 
                                           name="gambar" 
                                           accept="image/*">
                                    <small class="text-muted">Ukuran maksimal 2MB. Format: JPG, PNG, WebP</small>
                                    <?php if (isset($validation) && $validation->hasError('gambar')): ?>
                                        <div class="invalid-feedback">
                                            <?= $validation->getError('gambar') ?>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="kategori" class="form-label">Kategori <span class="text-danger">*</span></label>
                                    <select class="form-select <?= (isset($validation) && $validation->hasError('kategori')) ? 'is-invalid' : '' ?>" 
                                            id="kategori" 
                                            name="kategori" 
                                            required>
                                        <option value="">Pilih Kategori</option>
                                        <?php foreach ($kategori_options as $value => $label): ?>
                                            <option value="<?= $value ?>" 
                                                <?= old('kategori', $kostum['kategori'] ?? '') == $value ? 'selected' : '' ?>>
                                                <?= $label ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                    <?php if (isset($validation) && $validation->hasError('kategori')): ?>
                                        <div class="invalid-feedback">
                                            <?= $validation->getError('kategori') ?>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="durasi_sewa" class="form-label">Durasi Sewa</label>
                                    <select class="form-select" id="durasi_sewa" name="durasi_sewa">
                                        <option value="24 Jam" <?= old('durasi_sewa', $kostum['durasi_sewa'] ?? '') == '24 Jam' ? 'selected' : '' ?>>24 Jam</option>
                                        <option value="12 Jam" <?= old('durasi_sewa', $kostum['durasi_sewa'] ?? '') == '12 Jam' ? 'selected' : '' ?>>12 Jam</option>
                                        <option value="1 Minggu" <?= old('durasi_sewa', $kostum['durasi_sewa'] ?? '') == '1 Minggu' ? 'selected' : '' ?>>1 Minggu</option>
                                        <option value="2 Minggu" <?= old('durasi_sewa', $kostum['durasi_sewa'] ?? '') == '2 Minggu' ? 'selected' : '' ?>>2 Minggu</option>
                                        <option value="1 Bulan" <?= old('durasi_sewa', $kostum['durasi_sewa'] ?? '') == '1 Bulan' ? 'selected' : '' ?>>1 Bulan</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="harga_sewa" class="form-label">Harga Sewa <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <span class="input-group-text">Rp</span>
                                        <input type="number" 
                                               class="form-control <?= (isset($validation) && $validation->hasError('harga_sewa')) ? 'is-invalid' : '' ?>" 
                                               id="harga_sewa" 
                                               name="harga_sewa" 
                                               value="<?= old('harga_sewa', $kostum['harga_sewa'] ?? '') ?>" 
                                               required 
                                               min="0">
                                    </div>
                                    <?php if (isset($validation) && $validation->hasError('harga_sewa')): ?>
                                        <div class="invalid-feedback">
                                            <?= $validation->getError('harga_sewa') ?>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                            
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="stok" class="form-label">Stok Total <span class="text-danger">*</span></label>
                                    <input type="number" 
                                           class="form-control <?= (isset($validation) && $validation->hasError('stok')) ? 'is-invalid' : '' ?>" 
                                           id="stok" 
                                           name="stok" 
                                           value="<?= old('stok', $kostum['stok'] ?? '') ?>" 
                                           required 
                                           min="0">
                                    <?php if (isset($validation) && $validation->hasError('stok')): ?>
                                        <div class="invalid-feedback">
                                            <?= $validation->getError('stok') ?>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                            
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="stok_tersedia" class="form-label">Stok Tersedia</label>
                                    <input type="number" 
                                           class="form-control" 
                                           id="stok_tersedia" 
                                           name="stok_tersedia" 
                                           value="<?= old('stok_tersedia', $kostum['stok_tersedia'] ?? $kostum['stok'] ?? '') ?>" 
                                           min="0" 
                                           max="<?= old('stok', $kostum['stok'] ?? 0) ?>">
                                </div>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" 
                                               type="checkbox" 
                                               id="is_active" 
                                               name="is_active" 
                                               value="1" 
                                               <?= old('is_active', $kostum['is_active'] ?? 1) ? 'checked' : '' ?>>
                                        <label class="form-check-label" for="is_active">Aktif</label>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" 
                                               type="checkbox" 
                                               id="is_featured" 
                                               name="is_featured" 
                                               value="1" 
                                               <?= old('is_featured', $kostum['is_featured'] ?? 0) ? 'checked' : '' ?>>
                                        <label class="form-check-label" for="is_featured">Featured</label>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="text-end">
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-save"></i> Simpan
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Set max stok_tersedia based on stok
document.getElementById('stok').addEventListener('input', function() {
    const stok = parseInt(this.value) || 0;
    const stokTersediaInput = document.getElementById('stok_tersedia');
    stokTersediaInput.max = stok;
    
    if (parseInt(stokTersediaInput.value) > stok) {
        stokTersediaInput.value = stok;
    }
});
</script>
<?= $this->endSection() ?>